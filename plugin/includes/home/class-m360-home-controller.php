<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Home_Controller
{
    public static function template_include(string $template): string
    {
        if (!is_front_page() || is_feed() || is_admin() || !self::enabled()) { return $template; }
        return M360_CORE_PATH . 'templates/home.php';
    }

    public static function render(): void
    {
        if (!self::enabled()) { return; }
        $lang = self::language();
        $configuration = self::configuration();
        $label = $lang === 'pt' ? 'Últimas notícias' : 'Latest news';
        $featured = $lang === 'pt' ? 'destaque' : 'featured-en';
        $international = $lang === 'pt' ? 'internacional' : 'international';
        echo '<main id="m360-editorial-home" class="m360-editorial-home" data-m360-home><div class="m360-editorial-home__container">';
        echo '<div class="m360-core-home-nav">';
        if (shortcode_exists('m360_main_navigation')) {
            echo M360_Navigation_Shortcodes::main_navigation(['menu' => $configuration['menu'] ?? '']);
        }
        if (shortcode_exists('m360_search_toggle')) { echo M360_Search_Form_Component::toggle_shortcode(); }
        echo '</div>';
        if (M360_Runtime_Profile::enabled('ads_runtime')) { echo do_shortcode('[m360_ad_slot id="home-top"]'); }
        if (!empty($configuration['widget_ids'])) {
            echo M360_Editorial_Layout_Module::ticker(['lang' => $lang, 'label' => $label, 'limit' => 8]);
            $widgets = M360_Editorial_Widgets::all();
            foreach ($configuration['widget_ids'] as $id) {
                if (!isset($widgets[$id]) || $widgets[$id]['lang'] !== $lang) { continue; }
                echo M360_Editorial_Widgets::shortcode(['id' => $id]);
            }
            echo '</div></main>';
            return;
        }
        echo M360_Editorial_Layout_Module::ticker(['lang' => $lang, 'label' => $label, 'limit' => 8]);
        echo M360_Editorial_Layout_Module::newsroom(['lang' => $lang, 'featured_category' => $featured, 'international_category' => $international, 'include_international' => 'true', 'cards' => 4, 'show_title' => 'false']);
        echo M360_Editorial_Layout_Module::section(['title' => $lang === 'pt' ? 'Internacional' : 'International', 'lang' => $lang, 'category' => $international, 'layout' => 'grid', 'limit' => 4]);
        if (M360_Runtime_Profile::enabled('ads_runtime')) { echo do_shortcode('[m360_ad_slot id="home-middle"]'); }
        echo M360_Editorial_Layout_Module::section(['title' => $label, 'lang' => $lang, 'layout' => 'compact', 'limit' => 8]);
        echo '</div></main>';
    }

    public static function enqueue_assets(): void
    {
        if (!is_front_page() || !self::enabled()) { return; }
        foreach (['m360-core-foundation','m360-core-navigation-components','m360-core-language-switcher','m360-core-search-form','m360-core-editorial','m360-core-editorial-carousel','m360-core-editorial-layout','m360-core-editorial-polish','m360-core-editorial-newsroom-adjustments','m360-core-editorial-sections','m360-core-editorial-widgets','m360-core-editorial-ticker'] as $handle) {
            wp_enqueue_style($handle);
        }
        foreach (['m360-core-navigation','m360-core-language-switcher','m360-core-search-form','m360-core-editorial'] as $handle) {
            wp_enqueue_script($handle);
        }
    }

    private static function enabled(): bool
    {
        if (!class_exists('M360_Editorial_Layout_Module') || (M360_Editorial_Layout_Module::settings()['mode'] ?? 'off') !== 'public') { return false; }
        $stored = get_option('m360_site_profile', []);
        if (is_array($stored) && !array_key_exists('home', $stored) && get_option('m360_core_legacy_home_ownership', '0') === '1') { return true; }
        $configuration = self::configuration();
        if ($configuration === null) { return false; }
        if (empty($configuration['enabled']) || empty($configuration['widget_ids'])) { return false; }
        $widgets = M360_Editorial_Widgets::all();
        $lang = self::language();
        foreach ($configuration['widget_ids'] as $id) {
            if (isset($widgets[$id]) && $widgets[$id]['lang'] === $lang) { return true; }
        }
        return false;
    }

    private static function configuration(): ?array
    {
        $profile = M360_Site_Profile::get();
        $locale = self::locale();
        return $profile['home']['locales'][$locale] ?? null;
    }

    private static function locale(): string
    {
        $locale = function_exists('pll_current_language') ? (string) pll_current_language('locale') : (string) get_locale();
        $locale = str_replace('_', '-', $locale);
        $parts = explode('-', $locale, 2);
        return strtolower($parts[0]) . (isset($parts[1]) ? '-' . strtoupper($parts[1]) : '');
    }

    private static function language(): string
    {
        if (function_exists('pll_current_language')) { $language = sanitize_key((string) pll_current_language('slug')); if ($language !== '') { return substr($language, 0, 8); } }
        return str_starts_with((string) get_locale(), 'en') ? 'en' : 'pt';
    }
}
