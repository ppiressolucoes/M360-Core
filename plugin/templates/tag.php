<?php
if (!defined('ABSPATH')) { exit; }

get_header();
if (class_exists('M360_Tag_Controller')) { echo M360_Tag_Controller::render(); }
get_footer();
