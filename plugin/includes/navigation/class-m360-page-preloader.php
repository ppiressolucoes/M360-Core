<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Page_Preloader
{
    private static bool $rendered = false;

    public static function register(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_automatic_assets'], 20);
        add_action('wp_head', [self::class, 'prime_document'], 1);
        add_action('wp_body_open', [self::class, 'render_automatic'], 1);
        add_action('wp_footer', [self::class, 'render_fallback'], 1);
    }

    public static function register_shortcodes(): void
    {
        add_shortcode('m360_preloader', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        if (is_admin() && !wp_doing_ajax()) { return ''; }
        self::enqueue_assets();
        return self::markup();
    }

    public static function enqueue_automatic_assets(): void
    {
        if (!self::automatic_enabled()) { return; }
        self::enqueue_assets();
    }

    public static function prime_document(): void
    {
        if (!self::automatic_enabled()) { return; }
        echo "<script>document.documentElement.classList.add('m360-preloader-active');</script>\n";
    }

    public static function render_automatic(): void
    {
        if (!self::automatic_enabled()) { return; }
        echo self::markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    public static function render_fallback(): void
    {
        if (self::$rendered || !self::automatic_enabled()) { return; }
        echo self::markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    private static function automatic_enabled(): bool
    {
        if (is_admin() || wp_doing_ajax() || is_feed() || is_robots() || is_trackback()) { return false; }
        $profile = M360_Site_Profile::get();
        $enabled = !empty($profile['navigation']['preloader_enabled']);
        if (!$enabled) { return false; }

        $core_request = false;
        if (
            class_exists('M360_Editorial_Layout_Module')
            && M360_Editorial_Layout_Module::settings()['mode'] === 'public'
            && is_front_page()
        ) {
            $core_request = true;
        }
        if (
            M360_Runtime_Profile::enabled('public_views')
            && (is_search() || is_author() || is_category() || is_tag() || is_date())
        ) {
            $core_request = true;
        }
        if (self::queried_content_uses_core()) {
            $core_request = true;
        }

        return (bool) apply_filters('m360_preloader_enabled', $core_request);
    }

    private static function queried_content_uses_core(): bool
    {
        if (!is_singular()) { return false; }
        $post_id = (int) get_queried_object_id();
        if ($post_id <= 0) { return false; }

        $post = get_post($post_id);
        if ($post instanceof WP_Post && str_contains((string) $post->post_content, '[m360_')) {
            return true;
        }

        $elementor_data = get_post_meta($post_id, '_elementor_data', true);
        return is_string($elementor_data) && str_contains($elementor_data, 'm360_');
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-preloader', 'registered')) {
            wp_enqueue_style('m360-core-preloader');
        }
        if (wp_script_is('m360-core-preloader', 'registered')) {
            wp_enqueue_script('m360-core-preloader');
        }
    }

    private static function markup(): string
    {
        if (self::$rendered) { return ''; }
        self::$rendered = true;
        $is_en = function_exists('pll_current_language')
            ? str_starts_with(strtolower((string) pll_current_language('slug')), 'en')
            : str_starts_with(strtolower((string) determine_locale()), 'en');
        $label = $is_en ? 'Loading content' : 'Carregando conteúdo';

        return '<div class="m360-page-preloader" data-m360-preloader role="status" aria-live="polite" aria-label="' . esc_attr($label) . '">'
            . '<span class="m360-page-preloader__dots" aria-hidden="true"><i></i><i></i><i></i></span>'
            . '<span class="m360-page-preloader__label">' . esc_html($label) . '</span>'
            . '</div>';
    }
}
