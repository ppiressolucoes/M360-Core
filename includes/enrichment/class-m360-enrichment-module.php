<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Enrichment_Module implements M360_Module_Interface
{
    public function id(): string { return 'editorial-dw-enrichment'; }
    public function label(): string { return 'Enriquecimento Editorial DW'; }
    public function version(): string { return '0.1.0-dev'; }
    public function schema_version(): string { return '1'; }
    public function dependencies(): array { return ['publisher-foundation']; }
    public function capabilities(): array { return ['manage_options']; }
    public function settings_schema(): array { return []; }
    public function asset_handles(): array { return ['styles' => [], 'scripts' => []]; }
    public function is_required(): bool { return false; }
    public function default_enabled(): bool { return false; }
    public function activate(): void {}
    public function deactivate(): void {}
    public function boot(): void {}
    public function health(): array
    {
        return ['status' => 'warning', 'message' => 'Fundação em desenvolvimento; provedor DW e apresentação pública ainda não configurados.'];
    }
}
