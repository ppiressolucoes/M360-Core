<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Sidebar_Navigation
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_categories', [self::class, 'categories_shortcode']);
        add_shortcode('m360_archives', [self::class, 'archives_shortcode']);
        add_shortcode('m360_tag_cloud', [self::class, 'tags_shortcode']);
        add_shortcode('m360_tags', [self::class, 'tags_shortcode']);
        add_shortcode('m360_categories_archives', [self::class, 'shortcode']);
        add_shortcode('m360_sidebar_categories_archives', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'categories_limit' => 12,
            'archives_limit' => 6,
            'title_categories' => '',
            'title_archives' => '',
            'show_counts' => 'true',
            'primary' => '#d71920',
            'secondary' => '#b81218',
        ], $atts, 'm360_categories_archives');
        return '<div class="m360-sidebar-navigation-group">'
            . self::categories_shortcode([
                'limit' => $atts['categories_limit'],
                'title' => $atts['title_categories'],
                'show_counts' => $atts['show_counts'],
                'primary' => $atts['primary'],
                'secondary' => $atts['secondary'],
            ])
            . self::archives_shortcode([
                'limit' => $atts['archives_limit'],
                'title' => $atts['title_archives'],
                'show_counts' => $atts['show_counts'],
                'primary' => $atts['primary'],
                'secondary' => $atts['secondary'],
            ])
            . '</div>';
    }

    public static function categories_shortcode(array $atts = []): string
    {
        $atts = self::block_atts($atts, 'm360_categories');
        $categories = get_categories(['hide_empty' => true, 'number' => $atts['limit'], 'orderby' => 'name', 'order' => 'ASC']);
        if (!is_array($categories) || !$categories) { return ''; }
        $items = '';
        foreach ($categories as $category) {
            $items .= '<li><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>' . ($atts['show_counts'] ? ' <span>(' . esc_html((string) $category->count) . ')</span>' : '') . '</li>';
        }
        return self::block('categories', $atts['title'] ?: (self::is_en() ? 'Categories' : 'Categorias'), $items, $atts);
    }

    public static function archives_shortcode(array $atts = []): string
    {
        $atts = self::block_atts($atts, 'm360_archives');
        $archives = wp_get_archives(['type' => 'monthly', 'limit' => $atts['limit'], 'show_post_count' => $atts['show_counts'], 'echo' => false]);
        if (!is_string($archives) || trim($archives) === '') { return ''; }
        return self::block('archives', $atts['title'] ?: (self::is_en() ? 'Archives' : 'Arquivos'), wp_kses_post($archives), $atts);
    }

    public static function tags_shortcode(array $atts = []): string
    {
        $atts = shortcode_atts(['limit' => 24, 'title' => '', 'show_counts' => 'false', 'primary' => '#d71920', 'secondary' => '#b81218', 'min_size' => 13, 'max_size' => 19, 'surface' => 'light'], $atts, 'm360_tag_cloud');
        $limit = max(1, min(80, absint($atts['limit'])));
        $min_size = max(11, min(32, (int) $atts['min_size']));
        $max_size = max($min_size, min(40, (int) $atts['max_size']));
        $show_counts = filter_var($atts['show_counts'], FILTER_VALIDATE_BOOLEAN);
        $terms = get_terms(['taxonomy' => 'post_tag', 'hide_empty' => true, 'number' => $limit, 'orderby' => 'count', 'order' => 'DESC']);
        if (is_wp_error($terms) || !is_array($terms) || !$terms) { return ''; }
        $max_count = max(1, (int) max(array_map(static fn($term): int => (int) $term->count, $terms)));
        $items = '';
        foreach ($terms as $term) {
            if (!$term instanceof WP_Term) { continue; }
            $url = get_term_link($term);
            if (is_wp_error($url)) { continue; }
            $ratio = $max_count > 1 ? (($term->count - 1) / ($max_count - 1)) : 0;
            $size = (int) round($min_size + (($max_size - $min_size) * $ratio));
            $label = $term->name . ($show_counts ? ' (' . $term->count . ')' : '');
            $items .= '<li><a href="' . esc_url($url) . '" style="font-size:' . esc_attr($size . 'px') . '">' . esc_html($label) . '</a></li>';
        }
        if ($items === '') { return ''; }
        $block_atts = [
            'limit' => $limit,
            'title' => sanitize_text_field((string) $atts['title']),
            'show_counts' => $show_counts,
            'primary' => self::color((string) $atts['primary'], '#d71920'),
            'secondary' => self::color((string) $atts['secondary'], '#b81218'),
            'surface' => strtolower((string) $atts['surface']) === 'dark' ? 'dark' : 'light',
        ];
        return self::block('tags', $block_atts['title'] ?: (self::is_en() ? 'Tags' : 'Tags'), $items, $block_atts);
    }

    private static function block_atts(array $atts, string $shortcode): array
    {
        $atts = shortcode_atts(['limit' => 12, 'title' => '', 'show_counts' => 'true', 'primary' => '#d71920', 'secondary' => '#b81218'], $atts, $shortcode);
        return [
            'limit' => max(1, min(30, absint($atts['limit']))),
            'title' => sanitize_text_field((string) $atts['title']),
            'show_counts' => filter_var($atts['show_counts'], FILTER_VALIDATE_BOOLEAN),
            'primary' => self::color((string) $atts['primary'], '#d71920'),
            'secondary' => self::color((string) $atts['secondary'], '#b81218'),
        ];
    }

    private static function block(string $type, string $title, string $items, array $atts): string
    {
        self::enqueue_assets();
        $surface = ($atts['surface'] ?? 'light') === 'dark' ? ' m360-sidebar-navigation--surface-dark' : '';
        return '<aside class="m360-sidebar-navigation m360-sidebar-navigation--' . esc_attr($type) . $surface . '" style="--m360-sidebar-primary:' . esc_attr($atts['primary']) . ';--m360-sidebar-secondary:' . esc_attr($atts['secondary']) . '">'
            . '<section class="m360-sidebar-navigation__section m360-sidebar-navigation__section--' . esc_attr($type) . '"><h2>' . esc_html($title) . '</h2><ul>' . $items . '</ul></section>'
            . '</aside>';
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-sidebar-navigation', 'registered')) { wp_enqueue_style('m360-core-sidebar-navigation'); }
    }

    private static function color(string $value, string $fallback): string
    {
        $value = sanitize_hex_color($value);
        return $value ?: $fallback;
    }

    private static function is_en(): bool
    {
        if (function_exists('pll_current_language')) {
            $language = strtolower((string) pll_current_language('slug'));
            if ($language !== '') { return str_starts_with($language, 'en'); }
        }
        return str_starts_with(strtolower((string) determine_locale()), 'en');
    }
}
