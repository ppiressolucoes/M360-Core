<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Latest_News_Component
{
    private const CURSOR_PARAM = 'm360_news_cursor';
    private const LEGACY_PAGE_PARAM = 'm360_news_page';
    private const CACHE_TTL = 600;
    private static bool $request_hooks_registered = false;
    /** @var array<string,string> */
    private static array $archive_url_cache = [];

    public static function register_shortcodes(): void
    {
        add_shortcode('m360_latest_news', [self::class, 'shortcode']);
        if (self::$request_hooks_registered) { return; }
        self::$request_hooks_registered = true;
        add_action('template_redirect', [self::class, 'redirect_legacy_request'], 1);
        add_filter('wp_robots', [self::class, 'cursor_robots']);
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
            'navigation' => '',
            'archive_url' => 'auto',
            'archive_label' => '',
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
            'navigation' => '',
            'archive_url' => 'auto',
            'archive_label' => '',
            'layout' => 'list',
            'cache' => true,
        ]);

        $limit = max(1, min(12, absint($args['limit'])));
        $is_en = self::is_en();
        $language = self::language_key($is_en);
        $title = trim((string) $args['title']);
        if ($title === '') { $title = $is_en ? 'Latest News' : 'Últimas Notícias'; }
        $layout = sanitize_key((string) $args['layout']);
        if (!in_array($layout, ['list', 'compact', 'sidebar'], true)) { $layout = 'list'; }

        $cache_enabled = filter_var($args['cache'], FILTER_VALIDATE_BOOLEAN);
        $show_image = filter_var($args['show_image'], FILTER_VALIDATE_BOOLEAN);
        $show_category = filter_var($args['show_category'], FILTER_VALIDATE_BOOLEAN);
        $show_date = filter_var($args['show_date'], FILTER_VALIDATE_BOOLEAN);
        $show_ads = filter_var($args['show_ads'], FILTER_VALIDATE_BOOLEAN);
        $exclude_current = filter_var($args['exclude_current'], FILTER_VALIDATE_BOOLEAN);
        $current_post_id = $exclude_current && is_singular('post') ? absint(get_queried_object_id()) : 0;
        $navigation = self::navigation_mode($args, $layout);
        $cursor = $navigation === 'cursor' ? self::request_cursor($language) : null;
        $archive_url = $navigation === 'none' ? self::resolve_archive_url($args, $language, $is_en) : '';
        $archive_label = trim((string) $args['archive_label']);
        if ($archive_label === '') { $archive_label = $is_en ? 'View all news' : 'Ver todas as notícias'; }

        $cache_key = 'm360_latest_news_' . md5(wp_json_encode([
            $limit, $language, $layout, $show_image, $show_category, $show_date, $show_ads,
            $exclude_current, $current_post_id, $navigation, $cursor['token'] ?? '', $archive_url,
            M360_CORE_VERSION,
        ]));
        if ($cache_enabled) {
            $cached = get_transient($cache_key);
            if (is_string($cached) && $cached !== '') { return $cached; }
        }

        $query = self::query($limit, $language, $current_post_id, $cursor);
        $has_more = false;
        if ($navigation === 'cursor' && count($query->posts) > $limit) {
            array_pop($query->posts);
            $query->post_count = count($query->posts);
            $query->current_post = -1;
            $has_more = true;
        }

        ob_start();
        echo '<section id="m360-latest-news" class="m360-ui m360-latest-news m360-latest-news--' . esc_attr($layout) . '" aria-label="' . esc_attr($title) . '">';
        echo '<header class="m360-latest-news__header"><h2>' . esc_html($title) . '</h2></header>';
        $last_post = null;
        if ($query->have_posts()) {
            echo '<div class="m360-latest-news__items">';
            $index = 0;
            while ($query->have_posts()) {
                $query->the_post();
                $index++;
                $last_post = get_post();
                echo self::render_item($index, $show_image, $show_category, $show_date, $layout, $is_en);
                if ($show_ads) { echo M360_Ads_Archive_Engine::after_item('latest-news', $index); }
            }
            echo '</div>';

            if ($navigation === 'cursor' && $has_more && $last_post instanceof WP_Post) {
                echo self::render_cursor_navigation($last_post, $language, $is_en);
            }
        } else {
            echo '<p class="m360-latest-news__empty">' . esc_html($is_en ? 'No recent news found.' : 'Nenhuma notícia recente encontrada.') . '</p>';
        }
        if ($navigation === 'none' && $archive_url !== '') {
            echo '<footer class="m360-latest-news__footer"><a class="m360-latest-news__archive-link" href="' . esc_url($archive_url) . '">' . esc_html($archive_label) . '</a></footer>';
        }
        echo '</section>';
        wp_reset_postdata();

        $html = (string) ob_get_clean();
        if ($cache_enabled) { set_transient($cache_key, $html, self::CACHE_TTL); }
        return $html;
    }

    /**
     * Cursor pagination deliberately fetches one extra post and never asks
     * MySQL for a total row count or a deep OFFSET/page number.
     */
    private static function query(int $limit, string $language, int $current_post_id, ?array $cursor): WP_Query
    {
        $query_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $cursor ? $limit + 1 : $limit,
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
            'orderby' => ['date' => 'DESC', 'ID' => 'DESC'],
            'order' => 'DESC',
        ];
        if ($current_post_id > 0) { $query_args['post__not_in'] = [$current_post_id]; }
        if (function_exists('pll_current_language')) {
            $lang = pll_current_language('slug');
            if (is_string($lang) && $lang !== '') { $query_args['lang'] = $lang; }
        }

        $where_filter = null;
        if ($cursor) {
            $query_args['m360_latest_news_cursor'] = $cursor;
            $where_filter = static function (string $where, WP_Query $wp_query) use ($cursor): string {
                if ($wp_query->get('m360_latest_news_cursor') !== $cursor) { return $where; }
                global $wpdb;
                $condition = $wpdb->prepare(
                    " AND (({$wpdb->posts}.post_date < %s) OR ({$wpdb->posts}.post_date = %s AND {$wpdb->posts}.ID < %d))",
                    $cursor['date'],
                    $cursor['date'],
                    $cursor['id']
                );
                return $where . $condition;
            };
            add_filter('posts_where', $where_filter, 10, 2);
        }

        try {
            return new WP_Query($query_args);
        } finally {
            if ($where_filter) { remove_filter('posts_where', $where_filter, 10); }
        }
    }

    private static function navigation_mode(array $args, string $layout): string
    {
        if ($layout !== 'list') { return 'none'; }
        $mode = sanitize_key((string) ($args['navigation'] ?? ''));
        if ($mode === '') {
            // Compatibility: old dedicated pages used pagination="true".
            // It now enables bounded cursor navigation, never numeric pages.
            $mode = filter_var($args['pagination'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'cursor' : 'none';
        }
        return $mode === 'cursor' ? 'cursor' : 'none';
    }

    private static function render_cursor_navigation(WP_Post $last_post, string $language, bool $is_en): string
    {
        $cursor = self::encode_cursor((string) get_post_field('post_date', $last_post->ID), $last_post->ID, $language);
        if ($cursor === '') { return ''; }
        $base = self::current_url_without([self::CURSOR_PARAM, self::LEGACY_PAGE_PARAM]);
        $url = add_query_arg(self::CURSOR_PARAM, rawurlencode($cursor), $base) . '#m360-latest-news';
        $label = $is_en ? 'Load more news' : 'Carregar mais notícias';
        return '<nav class="m360-latest-news__cursor" aria-label="' . esc_attr($is_en ? 'More latest news' : 'Mais últimas notícias') . '"><a class="m360-latest-news__more-link" href="' . esc_url($url) . '" rel="next">' . esc_html($label) . '</a></nav>';
    }

    private static function request_cursor(string $language): ?array
    {
        if (!isset($_GET[self::CURSOR_PARAM]) || !is_scalar($_GET[self::CURSOR_PARAM])) { return null; }
        return self::decode_cursor((string) wp_unslash($_GET[self::CURSOR_PARAM]), $language);
    }

    private static function encode_cursor(string $date, int $id, string $language): string
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $date) || $id < 1 || $language === '') { return ''; }
        $payload = rtrim(strtr(base64_encode(wp_json_encode(['v' => 1, 'd' => $date, 'i' => $id, 'l' => $language])), '+/', '-_'), '=');
        $signature = substr(hash_hmac('sha256', $payload, wp_salt('m360_latest_news_cursor')), 0, 20);
        return $payload . '.' . $signature;
    }

    private static function decode_cursor(string $raw, string $language): ?array
    {
        $raw = trim($raw);
        if ($raw === '' || strlen($raw) > 256 || substr_count($raw, '.') !== 1) { return null; }
        [$payload, $signature] = explode('.', $raw, 2);
        if ($payload === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $payload) || !preg_match('/^[a-f0-9]{20}$/', $signature)) { return null; }
        $expected = substr(hash_hmac('sha256', $payload, wp_salt('m360_latest_news_cursor')), 0, 20);
        if (!hash_equals($expected, $signature)) { return null; }
        $encoded = strtr($payload, '-_', '+/');
        $decoded = base64_decode($encoded . str_repeat('=', (4 - strlen($encoded) % 4) % 4), true);
        $data = is_string($decoded) ? json_decode($decoded, true) : null;
        if (!is_array($data) || ($data['v'] ?? null) !== 1 || !is_string($data['d'] ?? null) || !is_numeric($data['i'] ?? null) || !is_string($data['l'] ?? null)) { return null; }
        $id = absint($data['i']);
        if ($id < 1 || $data['l'] !== $language || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $data['d'])) { return null; }
        return ['date' => $data['d'], 'id' => $id, 'token' => $raw];
    }

    public static function redirect_legacy_request(): void
    {
        if (is_admin() || wp_doing_ajax() || wp_doing_cron() || (defined('REST_REQUEST') && REST_REQUEST)) { return; }
        $has_legacy = array_key_exists(self::LEGACY_PAGE_PARAM, $_GET);
        $has_cursor = array_key_exists(self::CURSOR_PARAM, $_GET);
        if (!$has_legacy && !$has_cursor) { return; }

        $language = self::language_key(self::is_en());
        $cursor_is_valid = !$has_cursor || (is_scalar($_GET[self::CURSOR_PARAM]) && self::decode_cursor((string) wp_unslash($_GET[self::CURSOR_PARAM]), $language));
        if (!$has_legacy && $cursor_is_valid) { return; }

        $target = self::current_url_without([self::LEGACY_PAGE_PARAM, self::CURSOR_PARAM]);
        if (!headers_sent()) { wp_safe_redirect($target, 301, 'M360 Core'); }
        exit;
    }

    /** @param array<string,mixed> $robots */
    public static function cursor_robots(array $robots): array
    {
        if (isset($_GET[self::CURSOR_PARAM])) {
            $robots['noindex'] = true;
            $robots['follow'] = true;
        }
        return $robots;
    }

    private static function current_url_without(array $params): string
    {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '/';
        $relative = remove_query_arg($params, $request_uri);
        return home_url($relative ?: '/');
    }

    private static function resolve_archive_url(array $args, string $language, bool $is_en): string
    {
        $requested = trim((string) ($args['archive_url'] ?? 'auto'));
        if ($requested !== '' && $requested !== 'auto') { return esc_url_raw($requested); }
        $cache_key = $language . '|' . ($is_en ? 'en' : 'pt');
        if (array_key_exists($cache_key, self::$archive_url_cache)) { return self::$archive_url_cache[$cache_key]; }

        $configured = (array) get_option('m360_latest_news_archive_urls', []);
        $url = (string) ($configured[$language] ?? $configured[$is_en ? 'en' : 'pt'] ?? '');
        $url = (string) apply_filters('m360_latest_news_archive_url', $url, $language, $args);
        if ($url === '' && function_exists('get_page_by_path')) {
            $slugs = $is_en ? ['latest-news', 'news'] : ['ultimas-noticias', 'ultimas-noticias-news'];
            foreach ($slugs as $slug) {
                $page = get_page_by_path($slug, OBJECT, 'page');
                if (!$page instanceof WP_Post) { continue; }
                $page_id = $page->ID;
                if (function_exists('pll_get_post')) {
                    $translated = pll_get_post($page_id, $language);
                    if (is_numeric($translated) && (int) $translated > 0) { $page_id = (int) $translated; }
                }
                $url = (string) get_permalink($page_id);
                if ($url !== '') { break; }
            }
        }
        return self::$archive_url_cache[$cache_key] = $url !== '' ? esc_url_raw($url) : '';
    }

    private static function render_item(int $index, bool $show_image, bool $show_category, bool $show_date, string $layout, bool $is_en): string
    {
        $classes = 'm360-latest-news__item' . ($index === 1 ? ' is-featured' : '');
        $category = self::primary_category((int) get_the_ID());
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
        if ($show_date) { echo '<time class="m360-latest-news__date" datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . ($is_en ? 'ago' : 'atrás')) . '</time>'; }
        echo '</div></article>';
        return (string) ob_get_clean();
    }

    /**
     * Resolves the editorial category selected by M360, Yoast, Rank Math or
     * a portal integration. The fallback remains the first assigned category.
     */
    public static function primary_category(int $post_id): ?WP_Term
    {
        if ($post_id <= 0) { return null; }
        $categories = get_the_category($post_id);
        if (!is_array($categories) || $categories === []) { return null; }

        $assigned_ids = array_map('intval', wp_list_pluck($categories, 'term_id'));
        $primary_id = (int) get_post_meta($post_id, '_m360_primary_category_id', true);
        if ($primary_id <= 0 && class_exists('WPSEO_Primary_Term')) {
            $primary_id = (int) (new WPSEO_Primary_Term('category', $post_id))->get_primary_term();
        }
        if ($primary_id <= 0) { $primary_id = (int) get_post_meta($post_id, '_yoast_wpseo_primary_category', true); }
        if ($primary_id <= 0) { $primary_id = (int) get_post_meta($post_id, 'rank_math_primary_category', true); }

        $primary_id = (int) apply_filters('m360_latest_news_primary_category_id', $primary_id, $post_id, $categories);
        if ($primary_id > 0 && in_array($primary_id, $assigned_ids, true)) {
            $term = get_term($primary_id, 'category');
            if ($term instanceof WP_Term) { return $term; }
        }
        foreach ($categories as $category) {
            if ($category instanceof WP_Term) { return $category; }
        }
        return null;
    }
    public static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-latest-news', 'registered')) { wp_enqueue_style('m360-core-latest-news'); }
    }

    private static function language_key(bool $is_en): string
    {
        if (function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
            if (is_string($language) && $language !== '') { return sanitize_key($language); }
        }
        return $is_en ? 'en' : 'pt';
    }

    private static function is_en(): bool { return str_starts_with((string) get_locale(), 'en'); }
}

if (!function_exists('m360_latest_news')) {
    function m360_latest_news(array $args = []): string { return M360_Latest_News_Component::render($args); }
}