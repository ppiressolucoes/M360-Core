<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Language_Switcher
{
    private static bool $registered = false;

    public static function register(): void
    {
        if (self::$registered) { return; }
        self::$registered = true;
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_assets'], 20);
    }

    public static function register_shortcodes(): void
    {
        add_shortcode('m360_language_switcher', [self::class, 'render']);
    }

    public static function enqueue_assets(): void
    {
        if (is_admin()) { return; }

        $target = self::target();
        if (wp_style_is('m360-core-language-switcher', 'registered')) {
            wp_enqueue_style('m360-core-language-switcher');
        }
        if (!wp_script_is('m360-core-language-switcher', 'registered')) { return; }

        wp_enqueue_script('m360-core-language-switcher');
        wp_localize_script('m360-core-language-switcher', 'M360LanguageSwitcherConfig', [
            'available' => (bool) $target['available'],
            'singular' => is_singular(),
            'url' => (string) $target['url'],
            'locale' => (string) $target['locale'],
            'code' => (string) $target['code'],
            'flag' => (string) $target['flag'],
            'title' => (string) $target['title'],
            'ariaLabel' => (string) $target['aria_label'],
        ]);
    }

    public static function render(): string
    {
        $target = self::target();
        if (!$target['available']) { return ''; }

        return '<div class="m360-floating-lang-toggle" data-m360-language-switcher="shortcode">'
            . '<a class="m360-lang-toggle" href="' . esc_url((string) $target['url']) . '"'
            . ' hreflang="' . esc_attr((string) $target['locale']) . '"'
            . ' title="' . esc_attr((string) $target['title']) . '"'
            . ' aria-label="' . esc_attr((string) $target['aria_label']) . '">'
            . '<span class="m360-lang-toggle__flag" aria-hidden="true">' . esc_html((string) $target['flag']) . '</span>'
            . '<span class="m360-lang-toggle__code">' . esc_html((string) $target['code']) . '</span>'
            . '</a></div>';
    }

    private static function target(): array
    {
        $empty = [
            'available' => false,
            'url' => '',
            'locale' => '',
            'code' => '',
            'flag' => '',
            'title' => '',
            'aria_label' => '',
        ];

        if (!function_exists('pll_current_language')) { return $empty; }

        $current = strtolower((string) pll_current_language('slug'));
        $target_slug = str_starts_with($current, 'en') ? 'pt' : 'en';
        $url = '';

        if (is_singular()) {
            if (!function_exists('pll_get_post')) { return $empty; }
            $translated_id = (int) pll_get_post((int) get_queried_object_id(), $target_slug);
            if ($translated_id <= 0 || get_post_status($translated_id) !== 'publish') { return $empty; }
            $url = (string) get_permalink($translated_id);
        } elseif ((is_category() || is_tag() || is_tax()) && function_exists('pll_get_term')) {
            $term = get_queried_object();
            if ($term instanceof WP_Term) {
                $translated_id = (int) pll_get_term((int) $term->term_id, $target_slug);
                if ($translated_id > 0) {
                    $translated = get_term($translated_id, $term->taxonomy);
                    if ($translated instanceof WP_Term) {
                        $term_link = get_term_link($translated);
                        if (!is_wp_error($term_link)) { $url = (string) $term_link; }
                    }
                }
            }
        } elseif (function_exists('pll_the_languages')) {
            $languages = pll_the_languages(['raw' => 1, 'hide_if_empty' => 0]);
            if (is_array($languages)) {
                foreach ($languages as $language) {
                    if (!is_array($language) || (string) ($language['slug'] ?? '') !== $target_slug) { continue; }
                    $url = (string) ($language['url'] ?? '');
                    break;
                }
            }
        }

        if ($url === '' && !is_singular() && function_exists('pll_home_url')) {
            $url = (string) pll_home_url($target_slug);
        }
        if ($url === '') { return $empty; }

        $is_en_target = $target_slug === 'en';
        return [
            'available' => true,
            'url' => $url,
            'locale' => $is_en_target ? 'en-US' : 'pt-BR',
            'code' => $is_en_target ? 'EN' : 'PT',
            'flag' => $is_en_target ? '🇺🇸' : '🇧🇷',
            'title' => $is_en_target ? 'English' : 'Português',
            'aria_label' => $is_en_target ? 'Switch language to English' : 'Trocar idioma para português',
        ];
    }
}
