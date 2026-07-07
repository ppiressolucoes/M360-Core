<?php
if (!defined('ABSPATH')) { exit; }

get_header();

if (class_exists('M360_Search_Controller')) {
    echo M360_Search_Controller::render();
}

get_footer();
