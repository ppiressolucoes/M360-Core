<?php
if (!defined('ABSPATH')) { exit; }

M360_Elementor_Dynamic_Shell::render('archive', static function (): void {
    if (class_exists('M360_Author_Controller')) { echo M360_Author_Controller::render(); }
});
