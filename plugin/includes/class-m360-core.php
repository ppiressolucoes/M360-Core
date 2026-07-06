<?php
/**
 * Runtime foundation for M360 Core.
 */

if (!defined('ABSPATH')) {
    exit;
}

final class M360_Core
{
    private static ?M360_Core $instance = null;

    public static function instance(): M360_Core
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    public static function activate(): void
    {
        update_option('m360_core_version', M360_CORE_VERSION, false);
        update_option('m360_core_activated_at', current_time('mysql'), false);
    }

    public static function deactivate(): void
    {
        update_option('m360_core_deactivated_at', current_time('mysql'), false);
    }

    public function boot(): void
    {
        load_plugin_textdomain('m360-core', false, dirname(plugin_basename(M360_CORE_FILE)) . '/languages');

        add_action('init', [$this, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
    }

    public function register_assets(): void
    {
        wp_register_style(
            'm360-core-foundation',
            M360_CORE_URL . 'assets/css/m360-core.css',
            [],
            M360_CORE_VERSION
        );
    }

    public function register_shortcodes(): void
    {
        add_shortcode('m360_core_status', [$this, 'render_status_shortcode']);
        add_shortcode('m360_view', [$this, 'render_view_placeholder']);
    }

    public function render_status_shortcode(): string
    {
        if (!current_user_can('manage_options')) {
            return '';
        }

        return sprintf(
            '<div class="m360-core-status"><strong>M360 Core</strong><br>Version: %s<br>Status: GitHub packaging foundation active.</div>',
            esc_html(M360_CORE_VERSION)
        );
    }

    public function render_view_placeholder(array $atts = []): string
    {
        $atts = shortcode_atts([
            'view' => 'default',
        ], $atts, 'm360_view');

        if (!current_user_can('manage_options')) {
            return '';
        }

        return sprintf(
            '<div class="m360-core-placeholder">M360 View Engine placeholder: <code>%s</code></div>',
            esc_html((string) $atts['view'])
        );
    }
}
