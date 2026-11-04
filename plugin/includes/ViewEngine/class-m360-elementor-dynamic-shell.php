<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Renders one Elementor shell for Core-owned dynamic views.
 * The active language is resolved by WordPress/Polylang and Elementor's
 * conditions select the matching translated Header, Archive and Footer.
 */
final class M360_Elementor_Dynamic_Shell
{
    public static function render(string $location, callable $fallback): void
    {
        $use_elementor = function_exists('elementor_theme_do_location')
            && (bool) apply_filters('m360_dynamic_view_use_elementor_shell', true, $location);
        if (!$use_elementor) {
            get_header();
            $fallback();
            get_footer();
            return;
        }

        echo '<!doctype html><html ' . get_language_attributes() . '><head><meta charset="' . esc_attr(get_bloginfo('charset')) . '"><meta name="viewport" content="width=device-width, initial-scale=1">';
        wp_head();
        echo '</head><body class="' . esc_attr(implode(' ', get_body_class('m360-core-elementor-dynamic-shell'))) . '">';
        wp_body_open();
        elementor_theme_do_location('header');
        $rendered = (bool) elementor_theme_do_location($location);
        if (!$rendered) { $fallback(); }
        elementor_theme_do_location('footer');
        wp_footer();
        echo '</body></html>';
    }
}
