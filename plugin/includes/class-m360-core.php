<?php
/** Runtime foundation for M360 Core. */
if (!defined('ABSPATH')) { exit; }

require_once M360_CORE_PATH . 'includes/ViewEngine/class-m360-view-registry.php';
require_once M360_CORE_PATH . 'includes/ViewEngine/class-m360-view-loader.php';
require_once M360_CORE_PATH . 'includes/ViewEngine/class-m360-view-renderer.php';
require_once M360_CORE_PATH . 'includes/navigation/class-m360-navigation-shortcodes.php';
require_once M360_CORE_PATH . 'includes/post/class-m360-post-info-component.php';
require_once M360_CORE_PATH . 'includes/ui/class-m360-ui-components.php';
require_once M360_CORE_PATH . 'includes/latest-news/class-m360-latest-news-component.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-ads-inventory-library.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-ads-db.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-ads-runtime-map.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-slot-renderer.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-ad-slot-component.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-ads-context-renderer.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-ads-inline-engine.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-ads-archive-engine.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-ads-admin.php';
require_once M360_CORE_PATH . 'includes/ads/class-m360-ads-creatives-admin.php';
require_once M360_CORE_PATH . 'includes/search/class-m360-search-controller.php';
require_once M360_CORE_PATH . 'includes/author/class-m360-author-controller.php';
require_once M360_CORE_PATH . 'includes/category/class-m360-category-controller.php';
require_once M360_CORE_PATH . 'includes/tag/class-m360-tag-controller.php';
require_once M360_CORE_PATH . 'includes/date/class-m360-date-archive-controller.php';

final class M360_Core_Runtime_034
{
    private static ?M360_Core_Runtime_034 $instance = null;
    private ?M360_View_Registry $view_registry = null;
    private ?M360_View_Renderer $view_renderer = null;

    public static function instance(): M360_Core_Runtime_034
    {
        if (self::$instance === null) { self::$instance = new self(); }
        return self::$instance;
    }

    private function __construct() {}

    public static function activate(): void
    {
        update_option('m360_core_version', M360_CORE_VERSION, false);
        update_option('m360_core_activated_at', current_time('mysql'), false);
        M360_Ads_DB::install();
    }

    public static function deactivate(): void
    {
        update_option('m360_core_deactivated_at', current_time('mysql'), false);
    }

    public function boot(): void
    {
        load_plugin_textdomain('m360-core', false, dirname(plugin_basename(M360_CORE_FILE)) . '/languages');
        M360_Ads_DB::maybe_upgrade();
        M360_Ads_Admin::register();
        M360_Ads_Creatives_Admin::register();
        M360_Ads_Inline_Engine::register();
        $this->init_view_engine();
        add_action('init', [$this, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_action('admin_enqueue_scripts', [$this, 'register_admin_assets']);
        add_filter('widget_text', 'do_shortcode', 11);
        add_filter('widget_text_content', 'do_shortcode', 11);
        add_filter('widget_custom_html_content', 'do_shortcode', 11);
        add_filter('template_include', ['M360_Search_Controller', 'template_include'], 30);
        add_filter('template_include', ['M360_Author_Controller', 'template_include'], 31);
        add_filter('template_include', ['M360_Category_Controller', 'template_include'], 32);
        add_filter('template_include', ['M360_Tag_Controller', 'template_include'], 33);
        add_filter('template_include', ['M360_Date_Archive_Controller', 'template_include'], 34);
    }

    private function init_view_engine(): void
    {
        $loader = new M360_View_Loader();
        $this->view_registry = new M360_View_Registry();
        $this->view_renderer = new M360_View_Renderer($loader);
        $this->view_registry->register('status', ['template' => 'status', 'public' => false]);
        $this->view_registry->register('latest', ['template' => 'latest', 'public' => true]);
        $this->view_registry->register('author', ['template' => 'author', 'public' => true]);
        $this->view_registry->register('search', ['template' => 'search', 'public' => true]);
        $this->view_registry->register('category', ['template' => 'category', 'public' => true]);
        $this->view_registry->register('tag', ['template' => 'tag', 'public' => true]);
        $this->view_registry->register('date', ['template' => 'date', 'public' => true]);
    }

    public function register_assets(): void
    {
        wp_register_style('m360-core-foundation', M360_CORE_URL . 'assets/css/m360-core.css', [], M360_CORE_VERSION);
        wp_register_style('m360-core-ui-polish', M360_CORE_URL . 'assets/css/m360-ui-polish.css', ['m360-core-foundation'], M360_CORE_VERSION);
        wp_register_style('m360-core-ui-components', M360_CORE_URL . 'assets/css/m360-ui-components.css', ['m360-core-foundation'], M360_CORE_VERSION);
        wp_register_style('m360-core-navigation-components', M360_CORE_URL . 'assets/css/m360-navigation-components.css', ['m360-core-foundation', 'm360-core-ui-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-post-info', M360_CORE_URL . 'assets/css/m360-post-info.css', ['m360-core-foundation'], M360_CORE_VERSION);
        wp_register_style('m360-core-latest-news', M360_CORE_URL . 'assets/css/m360-latest-news.css', ['m360-core-ui-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-ads', M360_CORE_URL . 'assets/css/m360-ads.css', ['m360-core-ui-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-search', M360_CORE_URL . 'assets/css/search.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-author', M360_CORE_URL . 'assets/css/author.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-category', M360_CORE_URL . 'assets/css/category.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-tag', M360_CORE_URL . 'assets/css/tag.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-date', M360_CORE_URL . 'assets/css/date.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
    }

    public function register_admin_assets(string $hook = ''): void
    {
        $hook = (string) $hook;
        if (strpos($hook, 'm360-ads') !== false) {
            wp_enqueue_style('m360-core-ads-admin', M360_CORE_URL . 'assets/css/m360-ads-admin.css', [], M360_CORE_VERSION);
            wp_enqueue_style('m360-core-ads-slots-manager', M360_CORE_URL . 'assets/css/m360-ads-slots-manager.css', ['m360-core-ads-admin'], M360_CORE_VERSION);
            wp_enqueue_media();
            wp_enqueue_script('m360-core-ads-admin', M360_CORE_URL . 'assets/js/m360-ads-admin.js', ['jquery', 'media-editor'], M360_CORE_VERSION, true);
        }
    }

    public function register_shortcodes(): void
    {
        M360_Navigation_Shortcodes::register();
        M360_Post_Info_Component::register_shortcodes();
        M360_UI_Components::register_shortcodes();
        M360_Latest_News_Component::register_shortcodes();
        M360_Slot_Renderer::register_shortcodes();
        M360_Ads_Context_Renderer::register_shortcodes();
        add_shortcode('m360_core_status', [$this, 'render_status_shortcode']);
        add_shortcode('m360_view', [$this, 'render_view_shortcode']);
    }

    public function render_status_shortcode(): string
    {
        if (!current_user_can('manage_options')) { return ''; }
        return $this->render_registered_view('status');
    }

    public function render_view_shortcode(array $atts = []): string
    {
        $atts = shortcode_atts(['view' => 'status'], $atts, 'm360_view');
        $view = sanitize_key((string) $atts['view']);
        if (!current_user_can('manage_options')) { return ''; }
        return $this->render_registered_view($view);
    }

    private function render_registered_view(string $view): string
    {
        if (!$this->view_registry || !$this->view_renderer) { return ''; }
        $definition = $this->view_registry->get($view);
        $template = $definition['template'] ?? $view;
        return $this->view_renderer->render((string) $template, ['view_name' => $view, 'definition' => $definition]);
    }
}
