<?php
if (!defined('ABSPATH')) { exit; }

$is_en = str_starts_with((string) get_locale(), 'en');

if (!$is_en) {
    get_header();
    if (class_exists('M360_Search_Controller')) {
        echo M360_Search_Controller::render();
    }
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
<body <?php body_class('m360-shell m360-shell--en m360-shell--search'); ?>>
<?php wp_body_open(); ?>
<header class="m360-shell-header" role="banner">
    <div class="m360-shell-header__brand">
        <a href="<?php echo esc_url(home_url('/en/')); ?>" class="m360-shell-header__logo" aria-label="Mengão 360">
            <?php
            if (has_custom_logo()) {
                the_custom_logo();
            } else {
                bloginfo('name');
            }
            ?>
        </a>
    </div>
    <?php echo do_shortcode('[m360_main_navigation menu_en="primary menu English"]'); ?>
</header>
<?php
if (class_exists('M360_Search_Controller')) {
    echo M360_Search_Controller::render();
}
?>
<footer class="m360-shell-footer" role="contentinfo">
    <span>© <?php echo esc_html(date('Y')); ?> Mengão 360</span>
</footer>
<?php wp_footer(); ?>
</body>
</html>
