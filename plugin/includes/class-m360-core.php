<?php
/** Runtime foundation for M360 Core. */
if (!defined('ABSPATH')) { exit; }

require_once M360_CORE_PATH . 'includes/ViewEngine/class-m360-view-registry.php';
require_once M360_CORE_PATH . 'includes/ViewEngine/class-m360-view-loader.php';
require_once M360_CORE_PATH . 'includes/ViewEngine/class-m360-view-renderer.php';
require_once M360_CORE_PATH . 'includes/navigation/class-m360-navigation-shortcodes.php';
require_once M360_CORE_PATH . 'includes/language/class-m360-language-switcher.php';
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
require_once M360_CORE_PATH . 'includes/search/class-m360-search-form-component.php';
require_once M360_CORE_PATH . 'includes/header/class-m360-header-orchestrator.php';
require_once M360_CORE_PATH . 'includes/author/class-m360-author-controller.php';
require_once M360_CORE_PATH . 'includes/category/class-m360-category-controller.php';
require_once M360_CORE_PATH . 'includes/tag/class-m360-tag-controller.php';
require_once M360_CORE_PATH . 'includes/date/class-m360-date-archive-controller.php';
require_once M360_CORE_PATH . 'includes/privacy/class-m360-consent-manager.php';
require_once M360_CORE_PATH . 'includes/newsletter/interface-m360-newsletter-provider.php';
require_once M360_CORE_PATH . 'includes/newsletter/class-m360-newsletter-settings.php';
require_once M360_CORE_PATH . 'includes/newsletter/class-m360-mailpoet-adapter.php';
require_once M360_CORE_PATH . 'includes/newsletter/class-m360-newsletter-db.php';
require_once M360_CORE_PATH . 'includes/newsletter/class-m360-newsletter-service.php';
require_once M360_CORE_PATH . 'includes/newsletter/class-m360-newsletter-audit.php';
require_once M360_CORE_PATH . 'includes/newsletter/class-m360-newsletter-admin.php';
require_once M360_CORE_PATH . 'includes/newsletter/class-m360-newsletter-component.php';
require_once M360_CORE_PATH . 'includes/platform/interface-m360-module.php';
require_once M360_CORE_PATH . 'includes/platform/class-m360-runtime-profile.php';
require_once M360_CORE_PATH . 'includes/platform/class-m360-site-profile.php';
require_once M360_CORE_PATH . 'includes/platform/class-m360-module-registry.php';
require_once M360_CORE_PATH . 'includes/platform/class-m360-publisher-foundation-module.php';
require_once M360_CORE_PATH . 'includes/editorial/class-m360-editorial-widgets.php';
require_once M360_CORE_PATH . 'includes/editorial/class-m360-editorial-layout-module.php';
require_once M360_CORE_PATH . 'includes/discovery/interface-m360-catalog-provider.php';
require_once M360_CORE_PATH . 'includes/discovery/interface-m360-relation-repository.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-legacy-semantic-adapter.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-discovery-locale-resolver.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-wordpress-catalog-provider.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-discovery-db.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-shadow-generator.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-discovery-scheduler.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-semantic-comparator.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-canary-renderer.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-discovery-content-injector.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-content-discovery-module.php';
require_once M360_CORE_PATH . 'includes/discovery/class-m360-content-discovery-admin.php';
require_once M360_CORE_PATH . 'includes/platform/class-m360-platform-admin.php';
require_once M360_CORE_PATH . 'includes/platform/class-m360-platform.php';

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
        M360_Runtime_Profile::activate();
        update_option('m360_core_version', M360_CORE_VERSION, false);
        update_option('m360_core_activated_at', current_time('mysql'), false);
        M360_Ads_DB::install(M360_Runtime_Profile::is_legacy_compatible());
        M360_Consent_Manager::activate();
        M360_Newsletter_DB::install();
        M360_Newsletter_Settings::activate();
        M360_Platform::activate();
        if (M360_Runtime_Profile::enabled('newsletter_runtime')) {
            M360_Newsletter_Component::register_rewrite_rules();
        }
        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        update_option('m360_core_deactivated_at', current_time('mysql'), false);
        wp_clear_scheduled_hook('m360_newsletter_sync_pending');
        wp_clear_scheduled_hook('m360_newsletter_daily_cleanup');
        wp_clear_scheduled_hook(M360_Discovery_Scheduler::PROCESS_HOOK);
        wp_clear_scheduled_hook(M360_Discovery_Scheduler::BACKFILL_HOOK);
        M360_Platform::deactivate();
        flush_rewrite_rules();
    }

    public function boot(): void
    {
        load_plugin_textdomain('m360-core', false, dirname(plugin_basename(M360_CORE_FILE)) . '/languages');
        M360_Runtime_Profile::activate();
        update_option('m360_core_version', M360_CORE_VERSION, false);
        M360_Ads_DB::maybe_upgrade();
        M360_Newsletter_DB::maybe_upgrade();
        M360_Newsletter_Settings::activate();
        M360_Platform::instance()->register();
        M360_Ads_Admin::register();
        M360_Ads_Creatives_Admin::register();
        M360_Consent_Manager::register_admin();
        M360_Newsletter_Admin::register();
        if (M360_Runtime_Profile::enabled('consent_runtime')) {
            M360_Consent_Manager::register_frontend();
        }
        if (M360_Runtime_Profile::enabled('newsletter_runtime')) {
            M360_Newsletter_Audit::register();
            M360_Newsletter_Component::register();
        }
        M360_Language_Switcher::register();
        M360_Header_Orchestrator::register();
        if (M360_Runtime_Profile::enabled('ads_auto_insert')) {
            M360_Ads_Inline_Engine::register();
        }
        $this->init_view_engine();
        add_action('init', [$this, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_action('admin_enqueue_scripts', [$this, 'register_admin_assets']);
        add_filter('widget_text', 'do_shortcode', 11);
        add_filter('widget_text_content', 'do_shortcode', 11);
        add_filter('widget_custom_html_content', 'do_shortcode', 11);
        if (M360_Runtime_Profile::enabled('public_views')) {
            add_filter('template_include', ['M360_Search_Controller', 'template_include'], 30);
            add_filter('template_include', ['M360_Author_Controller', 'template_include'], 31);
            add_filter('template_include', ['M360_Category_Controller', 'template_include'], 32);
            add_filter('template_include', ['M360_Tag_Controller', 'template_include'], 33);
            add_filter('template_include', ['M360_Date_Archive_Controller', 'template_include'], 34);
        }
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
        wp_register_script('m360-core-navigation', M360_CORE_URL . 'assets/js/m360-navigation.js', [], M360_CORE_VERSION, true);
        wp_register_style('m360-core-language-switcher', M360_CORE_URL . 'assets/css/m360-language-switcher.css', ['m360-core-foundation'], M360_CORE_VERSION);
        wp_register_script('m360-core-language-switcher', M360_CORE_URL . 'assets/js/m360-language-switcher.js', [], M360_CORE_VERSION, true);
        wp_register_style('m360-core-post-info', M360_CORE_URL . 'assets/css/m360-post-info.css', ['m360-core-foundation'], M360_CORE_VERSION);
        wp_register_style('m360-core-latest-news', M360_CORE_URL . 'assets/css/m360-latest-news.css', ['m360-core-ui-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-ads', M360_CORE_URL . 'assets/css/m360-ads.css', ['m360-core-ui-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-search', M360_CORE_URL . 'assets/css/search.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-search-form', M360_CORE_URL . 'assets/css/m360-search-form.css', ['m360-core-foundation'], M360_CORE_VERSION);
        wp_register_script('m360-core-search-form', M360_CORE_URL . 'assets/js/m360-search-form.js', [], M360_CORE_VERSION, true);
        wp_register_style('m360-core-header-orchestrator', M360_CORE_URL . 'assets/css/m360-header-orchestrator.css', ['m360-core-ads', 'm360-core-search-form'], M360_CORE_VERSION);
        wp_register_style('m360-core-author', M360_CORE_URL . 'assets/css/author.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-category', M360_CORE_URL . 'assets/css/category.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-tag', M360_CORE_URL . 'assets/css/tag.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-date', M360_CORE_URL . 'assets/css/date.css', ['m360-core-ui-polish', 'm360-core-ui-components', 'm360-core-navigation-components'], M360_CORE_VERSION);
        wp_register_style('m360-core-editorial', M360_CORE_URL . 'assets/css/m360-editorial.css', ['m360-core-foundation'], M360_CORE_VERSION);
        wp_register_style('m360-core-editorial-carousel', M360_CORE_URL . 'assets/css/m360-editorial-carousel.css', ['m360-core-editorial'], M360_CORE_VERSION);
        wp_register_style('m360-core-editorial-layout', M360_CORE_URL . 'assets/css/m360-editorial-layout.css', ['m360-core-editorial-carousel'], M360_CORE_VERSION);
        wp_register_style('m360-core-editorial-polish', M360_CORE_URL . 'assets/css/m360-editorial-polish.css', ['m360-core-editorial-layout'], M360_CORE_VERSION);
        wp_register_style('m360-core-editorial-sections', M360_CORE_URL . 'assets/css/m360-editorial-sections.css', ['m360-core-editorial-polish'], M360_CORE_VERSION);
        wp_register_style('m360-core-editorial-widgets', M360_CORE_URL . 'assets/css/m360-editorial-widgets.css', ['m360-core-editorial-polish'], M360_CORE_VERSION);
        wp_register_style('m360-core-editorial-ticker', M360_CORE_URL . 'assets/css/m360-editorial-ticker.css', ['m360-core-editorial-polish'], M360_CORE_VERSION);
        wp_register_style('m360-core-discovery-canary', M360_CORE_URL . 'assets/css/m360-discovery-canary.css', [], M360_CORE_VERSION);
        wp_register_script('m360-core-editorial', M360_CORE_URL . 'assets/js/m360-editorial.js', [], M360_CORE_VERSION, true);
        if (is_singular() && M360_Platform::instance()->registry()->is_enabled('content-discovery-seo')) {
            wp_enqueue_style('m360-core-discovery-canary');
        }
    }

    public function register_admin_assets(string $hook = ''): void
    {
        $hook = (string) $hook;
        if (strpos($hook, 'm360-dashboard') !== false || strpos($hook, 'm360-platform') !== false) {
            wp_enqueue_style('m360-core-dashboard-admin', M360_CORE_URL . 'assets/css/m360-dashboard-admin.css', [], M360_CORE_VERSION);
        }
        if (strpos($hook, 'm360-editorial-widgets') !== false) {
            wp_enqueue_style('m360-core-editorial-admin', M360_CORE_URL . 'assets/css/m360-editorial-admin.css', [], M360_CORE_VERSION);
        }
        if (strpos($hook, 'm360-content-discovery') !== false) {
            wp_enqueue_style('m360-core-discovery-admin', M360_CORE_URL . 'assets/css/m360-discovery-admin.css', [], M360_CORE_VERSION);
        }
        if (strpos($hook, 'm360-ads') !== false || strpos($hook, 'm360-newsletter') !== false) {
            wp_enqueue_style('m360-core-ads-admin', M360_CORE_URL . 'assets/css/m360-ads-admin.css', [], M360_CORE_VERSION);
            wp_enqueue_style('m360-core-ads-slots-manager', M360_CORE_URL . 'assets/css/m360-ads-slots-manager.css', ['m360-core-ads-admin'], M360_CORE_VERSION);
            wp_enqueue_media();
            wp_enqueue_script('m360-core-ads-admin', M360_CORE_URL . 'assets/js/m360-ads-admin.js', ['jquery', 'media-editor'], M360_CORE_VERSION, true);
        }
    }

    public function register_shortcodes(): void
    {
        M360_Navigation_Shortcodes::register();
        M360_Language_Switcher::register_shortcodes();
        M360_Post_Info_Component::register_shortcodes();
        M360_Search_Form_Component::register_shortcodes();
        M360_Header_Orchestrator::register_shortcodes();
        M360_UI_Components::register_shortcodes();
        M360_Latest_News_Component::register_shortcodes();
        if (M360_Runtime_Profile::enabled('ads_runtime')) {
            M360_Slot_Renderer::register_shortcodes();
            M360_Ads_Context_Renderer::register_shortcodes();
        }
        if (M360_Runtime_Profile::enabled('newsletter_runtime')) {
            M360_Newsletter_Component::register_shortcodes();
        }
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
