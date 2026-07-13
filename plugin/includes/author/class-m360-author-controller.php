<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Author_Controller
{
    public static function template_include(string $template): string
    {
        if (!is_author() || is_admin()) { return $template; }
        $m360_template = M360_CORE_PATH . 'templates/author.php';
        return is_readable($m360_template) ? $m360_template : $template;
    }

    public static function render(): string
    {
        self::enqueue_assets();
        $author = get_queried_object();
        if (!$author instanceof WP_User) { return ''; }
        $is_en = self::is_en();
        $posts_count = count_user_posts((int) $author->ID, 'post', true);
        $bio = get_the_author_meta('description', (int) $author->ID);
        $role = get_the_author_meta('m360_role', (int) $author->ID);
        $role = $role !== '' ? $role : ($is_en ? 'Author' : 'Autor');
        ob_start();
        echo '<main class="m360-dynamic-view m360-author-view"><div class="m360-dynamic-view__container">';
        echo do_shortcode('[m360_breadcrumb]');
        echo '<section class="m360-author-hero"><div class="m360-author-hero__avatar">' . get_avatar((int) $author->ID, 128) . '</div><div class="m360-author-hero__body"><span class="m360-author-hero__eyebrow">' . esc_html($is_en ? 'Mengão 360 Author' : 'Autor Mengão 360') . '</span><h1>' . esc_html($author->display_name) . '</h1><p class="m360-author-hero__role">' . esc_html($role) . '</p>';
        if ($bio !== '') { echo '<p class="m360-author-hero__bio">' . esc_html($bio) . '</p>'; }
        echo '<div class="m360-author-stats"><span><strong>' . esc_html((string) $posts_count) . '</strong> ' . esc_html($is_en ? 'articles' : 'artigos') . '</span></div></div></section>';
        if (have_posts()) {
            echo '<section class="m360-author-results" aria-label="' . esc_attr($is_en ? 'Author articles' : 'Artigos do autor') . '">';
            $index = 0;
            while (have_posts()) { the_post(); $index++; echo self::render_card(); echo M360_Ads_Archive_Engine::after_item('author', $index); }
            echo '</section>' . self::pagination();
        } else { echo '<section class="m360-author-empty"><h2>' . esc_html($is_en ? 'No articles found' : 'Nenhum artigo encontrado') . '</h2></section>'; }
        echo '</div></main>';
        wp_reset_postdata();
        return (string) ob_get_clean();
    }

    public static function render_shell(): void
    {
        if (!self::is_en()) { get_header(); echo self::render(); get_footer(); return; }
        echo '<!doctype html><html ' . get_language_attributes() . '><head><meta charset="' . esc_attr(get_bloginfo('charset')) . '"><meta name="viewport" content="width=device-width, initial-scale=1">';
        wp_head();
        echo '</head><body ' . get_body_class_string('m360-author-en-shell m360-author-en-elementor-shell') . '>';
        wp_body_open(); echo do_shortcode('[elementor-template id="4123"]'); echo self::render(); echo do_shortcode('[elementor-template id="3765"]'); wp_footer(); echo '</body></html>';
    }

    private static function render_card(): string
    {
        $categories = get_the_category(); $category = !empty($categories) ? $categories[0]->name : '';
        $excerpt = get_the_excerpt(); if ($excerpt === '') { $excerpt = wp_trim_words(wp_strip_all_tags((string) get_the_content()), 26); }
        ob_start(); echo '<article class="m360-author-card">';
        if (has_post_thumbnail()) { echo '<a class="m360-author-card__thumb" href="' . esc_url(get_permalink()) . '">'; the_post_thumbnail('medium_large'); echo '</a>'; }
        echo '<div class="m360-author-card__body">'; if ($category !== '') { echo '<span class="m360-author-card__category">' . esc_html($category) . '</span>'; }
        echo '<h2><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h2><time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time><p>' . esc_html($excerpt) . '</p></div></article>';
        return (string) ob_get_clean();
    }

    private static function pagination(): string
    {
        $links = paginate_links(['type'=>'list','prev_text'=>self::is_en() ? 'Previous' : 'Anterior','next_text'=>self::is_en() ? 'Next' : 'Próxima']);
        return is_string($links) && $links !== '' ? '<nav class="m360-author-pagination">' . $links . '</nav>' : '';
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-author', 'registered')) { wp_enqueue_style('m360-core-author'); }
        if (wp_style_is('m360-core-foundation', 'registered')) { wp_enqueue_style('m360-core-foundation'); }
    }
    private static function is_en(): bool { return str_starts_with((string) get_locale(), 'en'); }
}

if (!function_exists('get_body_class_string')) {
    function get_body_class_string(string $class = ''): string { return 'class="' . esc_attr(implode(' ', get_body_class($class))) . '"'; }
}
