<?php
if (!defined('ABSPATH')) { exit; }

get_header();

// Let an active Elementor Search Results/Archive template provide the body
// when the portal has one configured. The Core renderer remains the fallback
// for installations without an Elementor dynamic-view template.
$elementor_rendered = false;
if (function_exists('elementor_theme_do_location')) {
    $elementor_rendered = (bool) elementor_theme_do_location('archive');
}
if (!$elementor_rendered && class_exists('M360_Search_Controller')) {
    echo M360_Search_Controller::render();
}

get_footer();
