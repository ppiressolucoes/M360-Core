<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Latest_News_Component
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_latest_news', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'limit' => 6,
            'title' => '',
            'show_image' => 'true',
            'show_category' => 'true',
            'show_date' => 'true',
            'show_ads' => 'true',
            'exclude_current' => 'true',
            'pagination' => 'false',
            'layout' => 'list',
            'cache' => 'true',
        ], $atts, 'm360_latest_news');
        return self::render($atts);
    }

    public static function render(array $args = []): string
    {
        self::enqueue_assets();
        $args = wp_parse_args($args, [
            'limit' => 6,
            'title' => '',
            'show_image' => true,
            'show_category' => true,
            'show_date' => true,
            'show_ads' => true,
            'exclude_current' => true,
            'pagination' => false,
            'layout' => 'list',
            'cache' => true,
        ]);
        $limit = max(1, min(12, absint($args['limit'])));
        $is_en = self::is_en();
        $title = trim((string) $args['title']);
        if ($title === '') { $title = $is_en ? 'Latest News' : 'Últimas Notícias'; }
        $layout = sanitize_key((string) $args['layout']);
        if (!in_array($layout, ['list', 'compact', 'sidebar'], true)) {
            $layout = 'list';
        }
        $cache_enabled = filter_var($args['cache'], FILTER_VALIDATE_BOOLEAN);
        $show_image = filter_var($args['show_image'], FILTER_VALIDATE_BOOLEAN);
        $show_category = filter_var($args['show_category'], FILTER_VALIDATE_BOOLEAN);
        $show_date = filter_var($args['show_date'], FILTER_VALIDATE_BOOLEAN);
        $show_ads = filter_var($args['show_ads'], FILTER_VALIDATE_BOOLEAN);
        $exclude_current = filter_var($args['exclude_current'], FILTER_VALIDATE_BOOLEAN);
        $current_post_id = $exclude_current && is_singular('post')
            ? absint(get_queried_object_id())
            : 0;
        $pagination_enabled = $layout === 'list' && filter_var($args['pagination'], FILTER_VALIDATE_BOOLEAN);
        $requested_page = isset($_GET['m360_news_page']) && is_scalar($_GET['m360_news_page'])
            ? wp_unslash($_GET['m360_news_page'])
            : 1;
        $current_page = $pagination_enabled ? max(1, absint($requested_page)) : 1;
        $pagination_base = is_singular() ? get_permalink(get_queried_object_id()) : home_url('/');
        if (!is_string($pagination_base) || $pagination_base === '') { $pagination_base = home_url('/'); }

        $cache_key = 'm360_latest_news_' . md5(wp_json_encode([$limit, $is_en, $layout, $show_image, $show_category, $show_date, $show_ads, $exclude_current, $current_post_id, $pagination_enabled, $current_page, $pagination_base, M360_CORE_VERSION]));
        if ($cache_enabled) {
            $cached = get_transient($cache_key);
            if (is_string($cached) && $cached !== '') { return $cached; }
        }

        $query_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'ignore_sticky_posts' => true,
            'no_found_rows' => !$pagination_enabled,
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => $current_page,
        ];
        if ($current_post_id > 0) {
            $query_args['post__not_in'] = [$current_post_id];
        }
        if (function_exists('pll_current_language')) {
            $lang = pll_current_language('slug');
            if (is_string($lang) && $lang !== '') { $query_args['lang'] = $lang; }
        }
        $query = new WP_Query($query_args);

        ob_start();
        echo '<section id="m360-latest-news" class="m360-ui m360-latest-news m360-latest-news--' . esc_attr($layout) . '" aria-label="' . esc_attr($title) . '">';
        echo '<header class="m360-latest-news__header"><h2>' . esc_html($title) . '</h2></header>';
        if ($query->have_posts()) {
            echo '<div class="m360-latest-news__items">';
            $index = 0;
            while ($query->have_posts()) {
                $query->the_post();
                $index++;
                echo self::render_item($index, $show_image, $show_category, $show_date, $layout);
                if ($show_ads) {
                    echo M360_Ads_Archive_Engine::after_item('latest-news', $index);
                }
            }
            echo '</div>';
            if ($pagination_enabled && $query->max_num_pages > 1) {
                echo self::render_pagination($current_page, (int) $query->max_num_pages, $pagination_base, $is_en);
            }
        } else {
            echo '<p class="m360-latest-news__empty">' . esc_html($is_en ? 'No recent news found.' : 'Nenhuma notícia recente encontrada.') . '</p>';
        }
        echo '</section>';
        wp_reset_postdata();
        $html = (string) ob_get_clean();
        if ($cache_enabled) { set_transient($cache_key, $html, 10 * MINUTE_IN_SECONDS); }
        return $html;
    }

    private static function render_pagination(int $current_page, int $total_pages, string $base_url, bool $is_en): string
    {
        $placeholder = 999999999;
        $base = str_replace(
            (string) $placeholder,
            '%#%',
            esc_url_raw(add_query_arg('m360_news_page', $placeholder, $base_url))
        );
        $links = paginate_links([
            'base' => $base,
            'format' => '',
            'current' => $current_page,
            'total' => $total_pages,
            'mid_size' => 1,
            'end_size' => 1,
            'prev_text' => $is_en ? 'Previous' : 'Anterior',
            'next_text' => $is_en ? 'Next' : 'Próxima',
            'type' => 'list',
            'add_fragment' => '#m360-latest-news',
        ]);
        if (!is_string($links) || $links === '') { return ''; }
        $label = $is_en ? 'Latest news pagination' : 'Paginação das últimas notícias';
        return '<nav class="m360-latest-news__pagination" aria-label="' . esc_attr($label) . '">' . $links . '</nav>';
    }

    private static function render_item(int $index, bool $show_image, bool $show_category, bool $show_date, string $layout): string
    {
        $classes = 'm360-latest-news__item' . ($index === 1 ? ' is-featured' : '');
        $categories = get_the_category();
        $category = !empty($categories) ? $categories[0] : null;
        ob_start();
        echo '<article class="' . esc_attr($classes) . '">';
        if ($show_image && has_post_thumbnail()) {
            $image_size = ($layout === 'sidebar' || $index > 1) ? 'thumbnail' : 'medium_large';
            echo '<a class="m360-latest-news__thumb" href="' . esc_url(get_permalink()) . '">';
            the_post_thumbnail($image_size, ['loading' => 'lazy']);
            echo '</a>';
        }
        echo '<div class="m360-latest-news__body">';
        if ($show_category && $category instanceof WP_Term) { echo '<a class="m360-latest-news__category" href="' . esc_url(get_category_link($category)) . '">' . esc_html($category->name) . '</a>'; }
        echo '<h3 class="m360-latest-news__title"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>';
        if ($show_date) { echo '<time class="m360-latest-news__date" datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . ($is_en = self::is_en() ? 'ago' : 'atrás')) . '</time>'; }
        echo '</div></article>';
        return (string) ob_get_clean();
    }

    public static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-latest-news', 'registered')) { wp_enqueue_style('m360-core-latest-news'); }
    }

    private static function is_en(): bool { return str_starts_with((string) get_locale(), 'en'); }
}

if (!function_exists('m360_latest_news')) {
    function m360_latest_news(array $args = []): string { return M360_Latest_News_Component::render($args); }
}
