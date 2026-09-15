<?php
if (!defined('ABSPATH')) { exit; }

/** Theme-independent renderer for WordPress menus placed in M360 footers. */
final class M360_Footer_Menu
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_footer_menu', [self::class, 'shortcode']);
        add_shortcode('m360_navigation_menu', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'menu' => '',
            'title' => '',
            'surface' => 'dark',
            'primary' => '#d71920',
            'secondary' => '#b81218',
            'depth' => 1,
        ], $atts, 'm360_footer_menu');

        $menu_reference = sanitize_text_field((string) $atts['menu']);
        if ($menu_reference === '') { return ''; }
        $menu_object = wp_get_nav_menu_object($menu_reference);
        if (!$menu_object instanceof WP_Term) { return ''; }

        $items = wp_nav_menu([
            'menu' => $menu_object,
            'container' => false,
            'menu_class' => 'm360-footer-menu__list',
            'menu_id' => '',
            'echo' => false,
            'fallback_cb' => false,
            'depth' => max(1, min(3, absint($atts['depth']))),
            'item_spacing' => 'discard',
        ]);
        if (!is_string($items) || trim($items) === '') { return ''; }

        self::enqueue_assets();
        $title = sanitize_text_field((string) $atts['title']);
        if ($title === '') { $title = (string) $menu_object->name; }
        $surface = strtolower((string) $atts['surface']) === 'light' ? 'light' : 'dark';
        $primary = self::color((string) $atts['primary'], '#d71920');
        $secondary = self::color((string) $atts['secondary'], '#b81218');

        return '<nav class="m360-footer-menu m360-footer-menu--' . esc_attr($surface) . '" style="--m360-footer-menu-primary:' . esc_attr($primary) . ';--m360-footer-menu-secondary:' . esc_attr($secondary) . '" aria-label="' . esc_attr($title) . '">'
            . '<h2 class="m360-footer-menu__title">' . esc_html($title) . '</h2>'
            . $items
            . '</nav>';
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-footer-menu', 'registered')) { wp_enqueue_style('m360-core-footer-menu'); }
    }

    private static function color(string $value, string $fallback): string
    {
        $value = sanitize_hex_color($value);
        return $value ?: $fallback;
    }
}
