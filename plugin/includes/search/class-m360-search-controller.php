<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Search_Controller
{
    public static function template_include(string $template): string
    {
        if (!is_search() || is_admin()) { return $template; }
        $m360_template = M360_CORE_PATH . 'templates/search.php';
        return is_readable($m360_template) ? $m360_template : $template;
    }

    public static function render(): string
    {
        self::enqueue_assets();
        $query = get_search_query(false);
        $is_en = self::is_en();
        $title = $is_en ? 'Search results' : 'Resultados da pesquisa';
        $empty_title = $is_en ? 'No results found' : 'Nenhum resultado encontrado';
        $empty_text = $is_en ? 'Try searching again with different keywords.' : 'Tente pesquisar novamente com outros termos.';

        ob_start();
        echo '<main class="m360-dynamic-view m360-search-view"><div class="m360-dynamic-view__container">';
        echo do_shortcode('[m360_breadcrumb]');
        echo '<header class="m360-search-view__header"><span class="m360-search-view__eyebrow">' . esc_html($is_en ? 'Mengão 360 Search' : 'Busca Mengão 360') . '</span><h1>' . esc_html($title) . '</h1><p>' . esc_html($is_en ? 'Search term:' : 'Termo pesquisado:') . ' <strong>' . esc_html($query) . '</strong></p></header>';

        if (have_posts()) {
            echo '<section class="m360-search-results" aria-label="' . esc_attr($title) . '">';
            $index = 0;
            while (have_posts()) {
                the_post();
                $index++;
                echo self::render_card();
                echo M360_Ads_Archive_Engine::after_item('search', $index);
            }
            echo '</section>' . self::pagination();
        } else {
            echo '<section class="m360-search-empty"><h2>' . esc_html($empty_title) . '</h2><p>' . esc_html($empty_text) . '</p>';
            get_search_form();
            echo M360_Ads_Archive_Engine::empty_state('search');
            echo '</section>';
        }

        echo '</div></main>';
        wp_reset_postdata();
        return (string) ob_get_clean();
    }

    private static function render_card(): string
    {
        $categories = get_the_category();
        $category = !empty($categories) ? $categories[0]->name : '';
        $excerpt = get_the_excerpt();
        if ($excerpt === '') { $excerpt = wp_trim_words(wp_strip_all_tags((string) get_the_content()), 26); }
        ob_start();
        echo '<article class="m360-search-card">';
        if (has_post_thumbnail()) { echo '<a class="m360-search-card__thumb" href="' . esc_url(get_permalink()) . '">'; the_post_thumbnail('medium_large'); echo '</a>'; }
        echo '<div class="m360-search-card__body">';
        if ($category !== '') { echo '<span class="m360-search-card__category">' . esc_html($category) . '</span>'; }
        echo '<h2><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h2><time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time><p>' . esc_html($excerpt) . '</p></div></article>';
        return (string) ob_get_clean();
    }

    private static function pagination(): string
    {
        $links = paginate_links(['type'=>'list','prev_text'=>self::is_en() ? 'Previous' : 'Anterior','next_text'=>self::is_en() ? 'Next' : 'Próxima']);
        return is_string($links) && $links !== '' ? '<nav class="m360-search-pagination">' . $links . '</nav>' : '';
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-search', 'registered')) { wp_enqueue_style('m360-core-search'); }
        if (wp_style_is('m360-core-foundation', 'registered')) { wp_enqueue_style('m360-core-foundation'); }
    }

    private static function is_en(): bool { return str_starts_with((string) get_locale(), 'en'); }
}
