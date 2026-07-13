<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Category_Controller
{
    public static function template_include(string $template): string
    {
        if (!is_category() || is_admin()) { return $template; }
        $m360_template = M360_CORE_PATH . 'templates/category.php';
        return is_readable($m360_template) ? $m360_template : $template;
    }

    public static function render(): string
    {
        self::enqueue_assets();
        $term = get_queried_object();
        if (!$term instanceof WP_Term) { return ''; }
        $is_en = self::is_en();
        $description = term_description($term, 'category');
        $count_label = $is_en ? 'articles' : 'artigos';
        ob_start();
        echo '<main class="m360-dynamic-view m360-category-view"><div class="m360-dynamic-view__container">';
        echo do_shortcode('[m360_breadcrumb]');
        echo '<section class="m360-category-hero"><span class="m360-category-hero__eyebrow">' . esc_html($is_en ? 'Mengão 360 Category' : 'Categoria Mengão 360') . '</span><h1>' . esc_html(single_cat_title('', false)) . '</h1>';
        if ($description !== '') { echo '<div class="m360-category-hero__description">' . wp_kses_post($description) . '</div>'; }
        echo '<div class="m360-category-stats"><span><strong>' . esc_html((string) $term->count) . '</strong> ' . esc_html($count_label) . '</span></div></section>';
        if (have_posts()) {
            echo '<section class="m360-category-results" aria-label="' . esc_attr($is_en ? 'Category articles' : 'Artigos da categoria') . '">';
            $index = 0;
            while (have_posts()) { the_post(); $index++; echo self::render_card(); echo M360_Ads_Archive_Engine::after_item('category', $index); }
            echo '</section>' . self::pagination();
        } else { echo '<section class="m360-category-empty"><h2>' . esc_html($is_en ? 'No articles found' : 'Nenhum artigo encontrado') . '</h2></section>'; }
        echo '</div></main>';
        wp_reset_postdata();
        return (string) ob_get_clean();
    }

    public static function render_shell(): void
    {
        if (!self::is_en()) { get_header(); echo self::render(); get_footer(); return; }
        echo '<!doctype html><html ' . get_language_attributes() . '><head><meta charset="' . esc_attr(get_bloginfo('charset')) . '"><meta name="viewport" content="width=device-width, initial-scale=1">';
        wp_head();
        echo '</head><body ' . self::body_class_string('m360-category-en-shell m360-category-en-elementor-shell') . '>';
        wp_body_open(); echo do_shortcode('[elementor-template id="4123"]'); echo self::render(); echo do_shortcode('[elementor-template id="3765"]'); wp_footer(); echo '</body></html>';
    }

    private static function render_card(): string
    {
        $excerpt = get_the_excerpt(); if ($excerpt === '') { $excerpt = wp_trim_words(wp_strip_all_tags((string) get_the_content()), 26); }
        ob_start(); echo '<article class="m360-category-card">';
        if (has_post_thumbnail()) { echo '<a class="m360-category-card__thumb" href="' . esc_url(get_permalink()) . '">'; the_post_thumbnail('medium_large'); echo '</a>'; }
        echo '<div class="m360-category-card__body"><span class="m360-category-card__category">' . esc_html(single_cat_title('', false)) . '</span><h2><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h2><time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time><p>' . esc_html($excerpt) . '</p></div></article>';
        return (string) ob_get_clean();
    }

    private static function pagination(): string
    {
        $links = paginate_links(['type'=>'list','prev_text'=>self::is_en() ? 'Previous' : 'Anterior','next_text'=>self::is_en() ? 'Next' : 'Próxima']);
        return is_string($links) && $links !== '' ? '<nav class="m360-category-pagination">' . $links . '</nav>' : '';
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-category', 'registered')) { wp_enqueue_style('m360-core-category'); }
        if (wp_style_is('m360-core-foundation', 'registered')) { wp_enqueue_style('m360-core-foundation'); }
    }
    private static function is_en(): bool { return str_starts_with((string) get_locale(), 'en'); }
    private static function body_class_string(string $class = ''): string { return 'class="' . esc_attr(implode(' ', get_body_class($class))) . '"'; }
}
