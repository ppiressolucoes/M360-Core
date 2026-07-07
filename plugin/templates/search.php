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
<style>
body.m360-search-en-shell{margin:0;background:#f6f7f9;color:#111}.m360-en-header-top{background:#d71920;color:#fff;padding:10px 48px;font-size:13px;display:flex;justify-content:space-between}.m360-en-header-top a{color:#fff;text-decoration:none;margin-left:16px}.m360-en-brand{background:#f5f5f5;padding:28px 48px;display:flex;align-items:center;gap:40px}.m360-en-brand img{max-height:105px;width:auto}.m360-en-nav{background:#d71920;padding:0 48px}.m360-en-nav .m360-navigation-shell{max-width:1180px}.m360-en-footer{background:#050505;color:#fff;padding:28px 48px;font-size:13px}.m360-en-footer a{color:#fff}.m360-en-footer__cols{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px;max-width:1180px;margin:0 auto}.m360-en-footer h3{background:#333;padding:10px 14px;font-size:15px;text-transform:uppercase}.m360-en-footer ul{list-style:none;margin:0;padding:0}.m360-en-footer li{margin:10px 0}@media(max-width:768px){.m360-en-header-top,.m360-en-brand,.m360-en-nav,.m360-en-footer{padding-left:20px;padding-right:20px}.m360-en-brand{display:block}.m360-en-footer__cols{grid-template-columns:1fr}}
</style>
</head>
<body <?php body_class('m360-search-en-shell'); ?>>
<?php wp_body_open(); ?>
<header class="m360-en-header" role="banner">
    <div class="m360-en-header-top">
        <span><?php echo esc_html(date_i18n('l, F j, Y')); ?></span>
        <span><a href="<?php echo esc_url(home_url('/en/about-us/')); ?>">About Us</a><a href="<?php echo esc_url(home_url('/en/contact/')); ?>">Contact</a></span>
    </div>
    <div class="m360-en-brand">
        <a href="<?php echo esc_url(home_url('/en/')); ?>" aria-label="Mengão 360">
            <?php if (has_custom_logo()) { the_custom_logo(); } else { bloginfo('name'); } ?>
        </a>
    </div>
    <div class="m360-en-nav">
        <?php echo do_shortcode('[m360_main_navigation menu_en="primary menu English"]'); ?>
    </div>
</header>
<?php if (class_exists('M360_Search_Controller')) { echo M360_Search_Controller::render(); } ?>
<footer class="m360-en-footer" role="contentinfo">
    <div class="m360-en-footer__cols">
        <section><h3>Categories</h3><ul><li>Radar News</li><li>Flamengo EN</li><li>Brazilian Team</li><li>Transfers</li></ul></section>
        <section><h3>Competitions</h3><ul><li>Brazilian Serie A</li><li>CONMEBOL Libertadores</li><li>World Cup 2026</li></ul></section>
        <section><h3>Support</h3><ul><li>About Us</li><li>Masthead</li><li>Advertising</li><li>Privacy Policy</li><li>Terms of Use</li><li>Contact</li></ul></section>
        <section><h3>Tags</h3><p>Brazilian Football · Flamengo · Libertadores · World Cup</p></section>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
