<?php if (!defined('ABSPATH')) { exit; } ?><!doctype html>
<html <?php language_attributes(); ?>><head>
<meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?></head><body <?php body_class('m360-core-home-shell'); ?>>
<?php wp_body_open(); ?>
<header class="m360-core-home-header"><div class="m360-core-home-header__container">
<a class="m360-core-home-header__brand" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(get_bloginfo('name')); ?></a>
<?php if (shortcode_exists('m360_language_switcher')) { echo do_shortcode('[m360_language_switcher]'); } ?>
</div></header>
<?php M360_Home_Controller::render(); ?>
<footer class="m360-core-home-footer"><div class="m360-core-home-footer__container"><?php echo esc_html(get_bloginfo('name')); ?></div></footer>
<?php wp_footer(); ?></body></html>
