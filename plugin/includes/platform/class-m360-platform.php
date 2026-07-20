<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Platform
{
    private static ?M360_Platform $instance = null;
    private M360_Module_Registry $registry;
    private bool $registered = false;

    private function __construct()
    {
        $this->registry = new M360_Module_Registry();
    }

    public static function instance(): M360_Platform
    {
        if (self::$instance === null) { self::$instance = new self(); }
        return self::$instance;
    }

    public function register(): void
    {
        if ($this->registered) { return; }
        $this->registered = true;
        $this->registry->register(new M360_Publisher_Foundation_Module());
        do_action('m360_platform_register_modules', $this->registry);
        M360_Platform_Admin::register($this->registry);
        add_action('init', [$this, 'boot_modules'], -100);
    }

    public function boot_modules(): void
    {
        $this->registry->boot_enabled();
        do_action('m360_platform_ready', $this->registry, M360_Site_Profile::get());
    }

    public function registry(): M360_Module_Registry { return $this->registry; }

    public static function activate(): void
    {
        M360_Site_Profile::activate();
        $platform = self::instance();
        if (!$platform->registered) { $platform->register(); }
        $platform->registry->activate_registered();
        update_option('m360_platform_activated_at', current_time('mysql'), false);
    }

    public static function deactivate(): void
    {
        $platform = self::instance();
        if (!$platform->registered) { $platform->register(); }
        $platform->registry->deactivate_registered();
        update_option('m360_platform_deactivated_at', current_time('mysql'), false);
    }
}
