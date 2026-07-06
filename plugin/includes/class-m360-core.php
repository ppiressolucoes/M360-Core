<?php
/**
 * Runtime foundation for M360 Core.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once M360_CORE_PATH . 'includes/ViewEngine/class-m360-view-registry.php';
require_once M360_CORE_PATH . 'includes/ViewEngine/class-m360-view-loader.php';
require_once M360_CORE_PATH . 'includes/ViewEngine/class-m360-view-renderer.php';
require_once M360_CORE_PATH . 'includes/navigation/class-m360-navigation-shortcodes.php';

final class M360_Core_Runtime_034
{
    private static ?M360_Core_Runtime_034 $instance = null;
    private ?M360_View_Registry $view_registry = null;
    private ?M360_View_Renderer $view_renderer = null;

    public static function instance(): M360_Core_Runtime_034
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

        $this->init_view_engine();

        add_action('init', [$this, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
    }

    private function init_view_engine(): void
    {
        $loader = new M360_View_Loader();
        $this->view_registry = new M360_View_Registry();
        $this->view_renderer = new M360_View_Renderer($loader);

        $this->view_registry->register('status', ['template' => 'status', 'public' => false]);
        $this->view_registry->register('latest', ['template' => 'latest', 'public' => true]);
        $this->view_registry->register('author', ['template' => 'author', 'public' => true]);
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
        M360_Navigation_Shortcodes::register();

        add_shortcode('m360_core_status', [$this, 'render_status_shortcode']);
        add_shortcode('m360_view', [$this, 'render_view_shortcode']);
    }

    public function render_status_shortcode(): string
    {
        if (!current_user_can('manage_options')) {
            return '';
        }

        return $this->render_registered_view('status');
    }

    public function render_view_shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'view' => 'status',
        ], $atts, 'm360_view');

        $view = sanitize_key((string) $atts['view']);

        if (!current_user_can('manage_options')) {
            return '';
        }

        return $this->render_registered_view($view);
    }

    private function render_registered_view(string $view): string
    {
        if (!$this->view_registry || !$this->view_renderer) {
            return '';
        }

        $definition = $this->view_registry->get($view);
        $template = $definition['template'] ?? $view;

        return $this->view_renderer->render((string) $template, [
            'view_name' => $view,
            'definition' => $definition,
        ]);
    }
}
