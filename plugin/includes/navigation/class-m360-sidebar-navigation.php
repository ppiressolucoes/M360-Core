<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Sidebar_Navigation
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_categories_archives', [self::class, 'shortcode']);
        add_shortcode('m360_sidebar_categories_archives', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'categories_limit' => 12,
            'archives_limit' => 6,
            'title_categories' => '',
            'title_archives' => '',
            'show_counts' => 'true',
            'primary' => '#d71920',
            'secondary' => '#b81218',
        ], $atts, 'm360_categories_archives');
        self::enqueue_assets();
        $is_en = self::is_en();
        $categories_title = sanitize_text_field((string) $atts['title_categories']) ?: ($is_en ? 'Categories' : 'Categorias');
        $archives_title = sanitize_text_field((string) $atts['title_archives']) ?: ($is_en ? 'Archives' : 'Arquivos');
        $show_counts = filter_var($atts['show_counts'], FILTER_VALIDATE_BOOLEAN);
        $primary = self::color((string) $atts['primary'], '#d71920');
        $secondary = self::color((string) $atts['secondary'], '#b81218');
        $categories = get_categories(['hide_empty' => true, 'number' => max(1, min(30, absint($atts['categories_limit']))), 'orderby' => 'name', 'order' => 'ASC']);
        $archives = wp_get_archives(['type' => 'monthly', 'limit' => max(1, min(24, absint($atts['archives_limit']))), 'show_post_count' => $show_counts, 'echo' => false]);

        ob_start();
        echo '<aside class="m360-sidebar-navigation" style="--m360-sidebar-primary:' . esc_attr($primary) . ';--m360-sidebar-secondary:' . esc_attr($secondary) . '">';
        if (is_array($categories) && $categories) {
            echo '<section class="m360-sidebar-navigation__section m360-sidebar-navigation__section--categories"><h2>' . esc_html($categories_title) . '</h2><ul>';
            foreach ($categories as $category) {
                echo '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>' . ($show_counts ? ' <span>(' . esc_html((string) $category->count) . ')</span>' : '') . '</li>';
            }
            echo '</ul></section>';
        }
        if (is_string($archives) && trim($archives) !== '') {
            echo '<section class="m360-sidebar-navigation__section m360-sidebar-navigation__section--archives"><h2>' . esc_html($archives_title) . '</h2><ul>' . wp_kses_post($archives) . '</ul></section>';
        }
        echo '</aside>';
        return (string) ob_get_clean();
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-sidebar-navigation', 'registered')) { wp_enqueue_style('m360-core-sidebar-navigation'); }
    }

    private static function color(string $value, string $fallback): string
    {
        $value = sanitize_hex_color($value);
        return $value ?: $fallback;
    }

    private static function is_en(): bool
    {
        if (function_exists('pll_current_language')) {
            $language = strtolower((string) pll_current_language('slug'));
            if ($language !== '') { return str_starts_with($language, 'en'); }
        }
        return str_starts_with(strtolower((string) determine_locale()), 'en');
    }
}
