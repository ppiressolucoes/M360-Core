<?php
if (!defined('ABSPATH')) { exit; }

get_header();
if (class_exists('M360_Category_Controller')) { echo M360_Category_Controller::render(); }
get_footer();
