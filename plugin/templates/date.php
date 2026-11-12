<?php
if (!defined('ABSPATH')) { exit; }

M360_Elementor_Dynamic_Shell::render('archive', static function (): void {
    if (class_exists('M360_Date_Archive_Controller')) { echo M360_Date_Archive_Controller::render(); }
});
