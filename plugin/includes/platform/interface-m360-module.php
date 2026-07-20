<?php
if (!defined('ABSPATH')) { exit; }

interface M360_Module_Interface
{
    public function id(): string;
    public function label(): string;
    public function version(): string;
    public function schema_version(): string;
    /** @return string[] */
    public function dependencies(): array;
    /** @return string[] */
    public function capabilities(): array;
    /** @return array<string,array<string,mixed>> */
    public function settings_schema(): array;
    /** @return array{styles:string[],scripts:string[]} */
    public function asset_handles(): array;
    public function is_required(): bool;
    public function default_enabled(): bool;
    public function activate(): void;
    public function deactivate(): void;
    public function boot(): void;
    /** @return array{status:string,message:string} */
    public function health(): array;
}
