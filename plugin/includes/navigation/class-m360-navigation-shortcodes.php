<?php
/**
 * Production navigation shortcodes for M360 Core.
 */

if (!defined('ABSPATH')) {
    exit;
}

final class M360_Navigation_Shortcodes
{
    public static function register(): void
    {
        add_shortcode('m360_main_navigation', [self::class, 'main_navigation']);
        add_shortcode('m360_breadcrumb', [self::class, 'breadcrumb']);
        add_shortcode('m360_section_navigation', [self::class, 'section_navigation']);
    }

    public static function main_navigation(array $atts = []): string
    {
        self::enqueue_assets();

        $atts = shortcode_atts([
            'menu_pt' => '',
            'menu_en' => '',
            'menu' => '',
        ], $atts, 'm360_main_navigation');

        $locale = function_exists('get_locale') ? get_locale() : 'pt_BR';
        $menu = (string) $atts['menu'];

        if ($menu === '') {
            $menu = str_starts_with($locale, 'en') ? (string) $atts['menu_en'] : (string) $atts['menu_pt'];
        }

        if ($menu === '') {
            $menu = str_starts_with($locale, 'en') ? 'main-menu-en' : 'main-menu-pt';
        }

        $html = wp_nav_menu([
            'menu' => $menu,
            'container' => 'nav',
            'container_class' => 'm360-main-navigation',
            'menu_class' => 'm360-main-navigation__menu',
            'echo' => false,
            'fallback_cb' => false,
            'depth' => 3,
        ]);

        return is_string($html) && $html !== '' ? $html : '';
    }

    public static function breadcrumb(array $atts = []): string
    {
        self::enqueue_assets();

        $items = [];
        $home_label = self::is_en() ? 'Home' : 'Início';
        $items[] = '<a href="' . esc_url(home_url('/')) . '">' . esc_html($home_label) . '</a>';

        if (is_singular()) {
            $categories = get_the_category();
            if (!empty($categories)) {
                $cat = $categories[0];
                $items[] = '<a href="' . esc_url(get_category_link($cat)) . '">' . esc_html($cat->name) . '</a>';
            }
            $items[] = '<span aria-current="page">' . esc_html(get_the_title()) . '</span>';
        } elseif (is_search()) {
            $label = self::is_en() ? 'Search' : 'Busca';
            $items[] = '<span aria-current="page">' . esc_html($label . ': ' . get_search_query()) . '</span>';
        } elseif (is_author()) {
            $items[] = '<span aria-current="page">' . esc_html(get_the_author()) . '</span>';
        } elseif (is_category() || is_tag() || is_archive()) {
            $items[] = '<span aria-current="page">' . esc_html(wp_get_document_title()) . '</span>';
        } elseif (is_page()) {
            $items[] = '<span aria-current="page">' . esc_html(get_the_title()) . '</span>';
        }

        return '<nav class="m360-breadcrumb" aria-label="Breadcrumb"><ol><li>' . implode('</li><li>', $items) . '</li></ol></nav>';
    }

    public static function section_navigation(array $atts = []): string
    {
        self::enqueue_assets();

        $atts = shortcode_atts([
            'menu' => '',
            'title' => '',
        ], $atts, 'm360_section_navigation');

        if ((string) $atts['menu'] !== '') {
            $menu_html = wp_nav_menu([
                'menu' => (string) $atts['menu'],
                'container' => false,
                'menu_class' => 'm360-section-navigation__menu',
                'echo' => false,
                'fallback_cb' => false,
                'depth' => 2,
            ]);

            if (is_string($menu_html) && $menu_html !== '') {
                return '<nav class="m360-section-navigation">' . $menu_html . '</nav>';
            }
        }

        $items = [];
        if (is_singular()) {
            $categories = get_the_category();
            if (!empty($categories)) {
                foreach (array_slice($categories, 0, 5) as $cat) {
                    $items[] = '<a href="' . esc_url(get_category_link($cat)) . '">' . esc_html($cat->name) . '</a>';
                }
            }
        }

        if (empty($items)) {
            return '';
        }

        $title = (string) $atts['title'];
        if ($title === '') {
            $title = self::is_en() ? 'Sections' : 'Seções';
        }

        return '<nav class="m360-section-navigation"><strong>' . esc_html($title) . '</strong><ul><li>' . implode('</li><li>', $items) . '</li></ul></nav>';
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-foundation', 'registered')) {
            wp_enqueue_style('m360-core-foundation');
        }
    }

    private static function is_en(): bool
    {
        return str_starts_with((string) get_locale(), 'en');
    }
}
