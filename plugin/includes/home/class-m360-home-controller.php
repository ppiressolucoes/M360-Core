<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Home_Controller
{
    public static function template_include(string $template): string
    {
        if (!self::enabled() || (!is_front_page() && !is_home()) || is_feed() || is_admin()) { return $template; }
        return M360_CORE_PATH . 'templates/home.php';
    }

    public static function render(): void
    {
        if (!self::enabled()) { return; }
        foreach (['m360-core-editorial','m360-core-editorial-carousel','m360-core-editorial-layout','m360-core-editorial-polish','m360-core-editorial-sections','m360-core-editorial-widgets','m360-core-editorial-ticker'] as $handle) { wp_enqueue_style($handle); }
        wp_enqueue_script('m360-core-editorial');
        $lang = self::language();
        $label = $lang === 'pt' ? 'Últimas notícias' : 'Latest news';
        $featured = $lang === 'pt' ? 'destaque' : 'featured-en';
        $international = $lang === 'pt' ? 'internacional' : 'international';
        echo '<main id="m360-editorial-home" class="m360-editorial-home" data-m360-home><div class="m360-editorial-home__container">';
        if (shortcode_exists('m360_main_navigation')) { echo do_shortcode('[m360_main_navigation]'); }
        if (M360_Runtime_Profile::enabled('ads_runtime')) { echo do_shortcode('[m360_ad_slot id="home-top"]'); }
        echo M360_Editorial_Layout_Module::ticker(['lang' => $lang, 'label' => $label, 'limit' => 8]);
        echo M360_Editorial_Layout_Module::newsroom(['lang' => $lang, 'featured_category' => $featured, 'international_category' => $international, 'include_international' => 'true', 'cards' => 4, 'show_title' => 'false']);
        echo M360_Editorial_Layout_Module::section(['title' => $lang === 'pt' ? 'Internacional' : 'International', 'lang' => $lang, 'category' => $international, 'layout' => 'grid', 'limit' => 4]);
        if (M360_Runtime_Profile::enabled('ads_runtime')) { echo do_shortcode('[m360_ad_slot id="home-middle"]'); }
        echo M360_Editorial_Layout_Module::section(['title' => $label, 'lang' => $lang, 'layout' => 'compact', 'limit' => 8]);
        echo '</div></main>';
    }

    private static function enabled(): bool
    {
        return class_exists('M360_Editorial_Layout_Module') && (M360_Editorial_Layout_Module::settings()['mode'] ?? 'off') === 'public';
    }

    private static function language(): string
    {
        if (function_exists('pll_current_language')) { $language = sanitize_key((string) pll_current_language('slug')); if ($language !== '') { return substr($language, 0, 8); } }
        return str_starts_with((string) get_locale(), 'en') ? 'en' : 'pt';
    }
}
