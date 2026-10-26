<?php
if (!defined('ABSPATH')) { exit; }

get_header();
if (class_exists('M360_Date_Archive_Controller')) { echo M360_Date_Archive_Controller::render(); }
get_footer();
