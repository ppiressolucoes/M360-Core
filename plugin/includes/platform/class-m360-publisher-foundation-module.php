<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Publisher_Foundation_Module implements M360_Module_Interface
{
    public function id(): string { return 'publisher-foundation'; }
    public function label(): string { return 'Publisher Platform Foundation'; }
    public function version(): string { return M360_CORE_VERSION; }
    public function schema_version(): string { return '1'; }
    public function dependencies(): array { return []; }
    public function capabilities(): array { return ['manage_options']; }
    public function settings_schema(): array
    {
        return [
            'site_key' => ['type' => 'string', 'portable' => true],
            'site_name' => ['type' => 'string', 'portable' => true],
            'vertical' => ['type' => 'string', 'portable' => true],
            'default_locale' => ['type' => 'locale', 'portable' => true],
            'supported_locales' => ['type' => 'locale[]', 'portable' => true],
        ];
    }
    public function asset_handles(): array { return ['styles' => [], 'scripts' => []]; }
    public function is_required(): bool { return true; }
    public function default_enabled(): bool { return true; }
    public function activate(): void { M360_Site_Profile::activate(); }
    public function deactivate(): void {}
    public function boot(): void {}

    public function health(): array
    {
        $profile = M360_Site_Profile::get();
        $ok = !empty($profile['site_key']) && !empty($profile['default_locale']) && !empty($profile['supported_locales']);
        return [
            'status' => $ok ? 'healthy' : 'error',
            'message' => $ok ? 'Kernel modular e Site Profile disponíveis.' : 'Site Profile incompleto.',
        ];
    }
}
