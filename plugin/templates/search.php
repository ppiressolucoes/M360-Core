<?php
if (!defined('ABSPATH')) { exit; }

$is_en = str_starts_with((string) get_locale(), 'en');

if (!$is_en) {
    get_header();
    if (class_exists('M360_Search_Controller')) { echo M360_Search_Controller::render(); }
    get_footer();
    return;
}

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class('m360-search-en-shell m360-search-en-elementor-shell'); ?>>
<?php wp_body_open(); ?>
<?php echo do_shortcode('[elementor-template id="4123"]'); ?>
<?php if (class_exists('M360_Search_Controller')) { echo M360_Search_Controller::render(); } ?>
<?php echo do_shortcode('[elementor-template id="3765"]'); ?>
<?php wp_footer(); ?>
</body>
</html>
