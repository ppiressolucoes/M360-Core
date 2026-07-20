<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Module_Registry
{
    private const STATES_OPTION = 'm360_platform_module_states';
    private const SCHEMAS_OPTION = 'm360_platform_module_schemas';
    /** @var array<string,M360_Module_Interface> */
    private array $modules = [];
    /** @var array<string,string> */
    private array $runtime_errors = [];

    public function register(M360_Module_Interface $module): void
    {
        $id = sanitize_key($module->id());
        if ($id === '' || $id !== $module->id()) {
            throw new InvalidArgumentException('O ID do módulo deve ser um sanitize_key válido.');
        }
        if (isset($this->modules[$id])) {
            throw new LogicException('Módulo duplicado: ' . $id);
        }
        $this->modules[$id] = $module;
    }

    /** @return array<string,M360_Module_Interface> */
    public function all(): array { return $this->modules; }

    public function get(string $id): ?M360_Module_Interface
    {
        return $this->modules[sanitize_key($id)] ?? null;
    }

    public function is_enabled(string $id): bool
    {
        $module = $this->get($id);
        if (!$module) { return false; }
        if ($module->is_required()) { return true; }
        $states = get_option(self::STATES_OPTION, []);
        if (is_array($states) && array_key_exists($module->id(), $states)) {
            return (bool) $states[$module->id()];
        }
        return $module->default_enabled();
    }

    public function activate_registered(): void
    {
        foreach ($this->ordered_enabled_modules() as $module) {
            try {
                $module->activate();
                $this->record_schema($module);
            } catch (Throwable $error) {
                $this->record_runtime_error($module->id(), $error);
            }
        }
    }

    public function boot_enabled(): void
    {
        foreach ($this->ordered_enabled_modules() as $module) {
            try {
                $this->migrate_if_needed($module);
                $module->boot();
                do_action('m360_platform_module_booted', $module->id(), $module);
            } catch (Throwable $error) {
                $this->record_runtime_error($module->id(), $error);
            }
        }
    }

    public function deactivate_registered(): void
    {
        $modules = array_reverse($this->ordered_enabled_modules());
        foreach ($modules as $module) {
            try { $module->deactivate(); } catch (Throwable $error) {
                $this->record_runtime_error($module->id(), $error);
            }
        }
    }

    public function set_enabled(string $id, bool $enabled)
    {
        $module = $this->get($id);
        if (!$module) { return new WP_Error('m360_module_missing', 'Módulo não registrado.'); }
        if (!$enabled && $module->is_required()) {
            return new WP_Error('m360_module_required', 'Este módulo é obrigatório e não pode ser desativado.');
        }

        if ($enabled) {
            foreach ($module->dependencies() as $dependency) {
                if (!$this->get($dependency) || !$this->is_enabled($dependency)) {
                    return new WP_Error('m360_module_dependency', 'Dependência indisponível: ' . sanitize_key($dependency));
                }
            }
        } else {
            foreach ($this->modules as $candidate) {
                if ($this->is_enabled($candidate->id()) && in_array($module->id(), $candidate->dependencies(), true)) {
                    return new WP_Error('m360_module_dependent', 'O módulo é exigido por: ' . $candidate->label());
                }
            }
        }

        try {
            if ($enabled) { $module->activate(); } else { $module->deactivate(); }
        } catch (Throwable $error) {
            return new WP_Error('m360_module_lifecycle', sanitize_text_field($error->getMessage()));
        }

        $states = get_option(self::STATES_OPTION, []);
        $states = is_array($states) ? $states : [];
        $states[$module->id()] = $enabled;
        update_option(self::STATES_OPTION, $states, false);
        if ($enabled) { $this->record_schema($module); }
        return true;
    }

    public function health_report(): array
    {
        $report = [];
        foreach ($this->modules as $module) {
            $enabled = $this->is_enabled($module->id());
            $health = ['status' => $enabled ? 'unknown' : 'disabled', 'message' => $enabled ? 'Sem diagnóstico.' : 'Módulo desativado.'];
            if ($enabled) {
                if (isset($this->runtime_errors[$module->id()])) {
                    $health = ['status' => 'error', 'message' => $this->runtime_errors[$module->id()]];
                } else {
                    try { $health = $module->health(); } catch (Throwable $error) {
                        $health = ['status' => 'error', 'message' => sanitize_text_field($error->getMessage())];
                    }
                }
            }
            $report[$module->id()] = [
                'id' => $module->id(),
                'label' => $module->label(),
                'version' => $module->version(),
                'schema_version' => $module->schema_version(),
                'dependencies' => array_values(array_map('sanitize_key', $module->dependencies())),
                'capabilities' => array_values(array_map('sanitize_key', $module->capabilities())),
                'settings' => array_values(array_map('sanitize_key', array_keys($module->settings_schema()))),
                'assets' => $this->sanitize_asset_handles($module->asset_handles()),
                'required' => $module->is_required(),
                'enabled' => $enabled,
                'status' => sanitize_key((string) ($health['status'] ?? 'unknown')),
                'message' => sanitize_text_field((string) ($health['message'] ?? '')),
            ];
        }
        return $report;
    }

    private function sanitize_asset_handles(array $assets): array
    {
        return [
            'styles' => array_values(array_filter(array_map('sanitize_key', (array) ($assets['styles'] ?? [])))),
            'scripts' => array_values(array_filter(array_map('sanitize_key', (array) ($assets['scripts'] ?? [])))),
        ];
    }

    /** @return M360_Module_Interface[] */
    private function ordered_enabled_modules(): array
    {
        $ordered = [];
        $visiting = [];
        $visited = [];
        $visit = function (M360_Module_Interface $module) use (&$visit, &$ordered, &$visiting, &$visited): void {
            $id = $module->id();
            if (isset($visited[$id])) { return; }
            if (isset($visiting[$id])) { throw new LogicException('Dependência circular envolvendo: ' . $id); }
            $visiting[$id] = true;
            foreach ($module->dependencies() as $dependency_id) {
                $dependency = $this->get($dependency_id);
                if (!$dependency || !$this->is_enabled($dependency_id)) {
                    throw new LogicException('Dependência não atendida para ' . $id . ': ' . $dependency_id);
                }
                $visit($dependency);
            }
            unset($visiting[$id]);
            $visited[$id] = true;
            $ordered[] = $module;
        };

        foreach ($this->modules as $module) {
            if (!$this->is_enabled($module->id())) { continue; }
            try {
                $visit($module);
            } catch (Throwable $error) {
                $this->record_runtime_error($module->id(), $error);
                $visiting = [];
            }
        }
        return $ordered;
    }

    private function record_runtime_error(string $module_id, Throwable $error): void
    {
        $module_id = sanitize_key($module_id);
        $this->runtime_errors[$module_id] = sanitize_text_field($error->getMessage());
        do_action('m360_platform_module_failed', $module_id, $error);
    }

    private function migrate_if_needed(M360_Module_Interface $module): void
    {
        $schemas = get_option(self::SCHEMAS_OPTION, []);
        $schemas = is_array($schemas) ? $schemas : [];
        if (($schemas[$module->id()] ?? null) !== $module->schema_version()) {
            $module->activate();
            $this->record_schema($module);
        }
    }

    private function record_schema(M360_Module_Interface $module): void
    {
        $schemas = get_option(self::SCHEMAS_OPTION, []);
        $schemas = is_array($schemas) ? $schemas : [];
        $schemas[$module->id()] = $module->schema_version();
        update_option(self::SCHEMAS_OPTION, $schemas, false);
    }
}
