<?php
if (!defined('ABSPATH')) { exit; }

get_header();

// In the PEL profile the Elementor shell owns all three locations. Do not
// call get_header()/get_footer() there, otherwise the active theme shell is
// emitted in addition to the Elementor Header/Footer.
$use_elementor_shell = function_exists('elementor_theme_do_location')
    && (bool) apply_filters('m360_search_use_elementor_theme_locations', false);
if ($use_elementor_shell) {
    echo '<!doctype html><html ' . get_language_attributes() . '><head><meta charset="' . esc_attr(get_bloginfo('charset')) . '"><meta name="viewport" content="width=device-width, initial-scale=1">';
    wp_head();
    echo '</head><body class="' . esc_attr(implode(' ', get_body_class('m360-core-elementor-search-shell'))) . '">';
    wp_body_open();
    elementor_theme_do_location('header');
    $elementor_rendered = (bool) elementor_theme_do_location('archive');
    if (!$elementor_rendered && class_exists('M360_Search_Controller')) {
        echo M360_Search_Controller::render();
    }
    elementor_theme_do_location('footer');
    wp_footer();
    echo '</body></html>';
    return;
}

if (class_exists('M360_Search_Controller')) { echo M360_Search_Controller::render(); }
get_footer();
