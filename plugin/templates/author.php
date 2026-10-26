<?php
if (!defined('ABSPATH')) { exit; }

get_header();
if (class_exists('M360_Author_Controller')) { echo M360_Author_Controller::render(); }
get_footer();
