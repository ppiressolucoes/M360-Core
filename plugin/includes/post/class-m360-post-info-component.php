<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Post_Info_Component
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_post_info', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'id'       => 0,
            'avatar'   => 'true',
            'author'   => 'true',
            'category' => 'true',
            'date'     => 'true',
            'time'     => 'true',
            'updated'  => 'true',
            'class'    => '',
        ], $atts, 'm360_post_info');

        $post_id = absint($atts['id']);
        if ($post_id === 0) { $post_id = (int) get_the_ID(); }
        $post = get_post($post_id);
        if (!$post instanceof WP_Post || $post->post_type !== 'post') { return ''; }

        self::enqueue_assets();
        $is_en = self::is_en();
        $items = [];

        if (self::enabled($atts['author'])) {
            $author_id = (int) $post->post_author;
            $author_name = get_the_author_meta('display_name', $author_id);
            $author_url = get_author_posts_url($author_id);
            $avatar = self::enabled($atts['avatar'])
                ? '<span class="m360-post-info__avatar-frame">'
                    . get_avatar($author_id, 48, '', $author_name, ['class' => 'm360-post-info__avatar', 'loading' => 'lazy'])
                    . '</span>'
                : '';
            $items[] = '<span class="m360-post-info__item m360-post-info__item--author">'
                . $avatar
                . '<span class="m360-post-info__label">' . esc_html($is_en ? 'By' : 'Por') . ':</span>'
                . '<a rel="author" href="' . esc_url($author_url) . '">' . esc_html($author_name) . '</a>'
                . '</span>';
        }

        if (self::enabled($atts['category'])) {
            $categories = get_the_category($post_id);
            if (!empty($categories)) {
                $category = $categories[0];
                $items[] = '<span class="m360-post-info__item m360-post-info__item--category">'
                    . self::icon('category')
                    . '<a rel="category tag" href="' . esc_url(get_category_link($category)) . '">' . esc_html($category->name) . '</a>'
                    . '</span>';
            }
        }

        if (self::enabled($atts['date'])) {
            $published_iso = get_the_date(DATE_W3C, $post_id);
            $published_date = get_the_date(get_option('date_format'), $post_id);
            $published_year = (int) get_the_date('Y', $post_id);
            $published_month = (int) get_the_date('m', $post_id);
            $published_day = (int) get_the_date('d', $post_id);
            $date_archive_url = get_day_link($published_year, $published_month, $published_day);
            $date_link_label = $is_en
                ? 'View posts published on ' . $published_date
                : 'Ver posts publicados em ' . $published_date;
            $items[] = '<span class="m360-post-info__item m360-post-info__item--date">'
                . self::icon('calendar')
                . '<a class="m360-post-info__date-link" href="' . esc_url($date_archive_url) . '" aria-label="' . esc_attr($date_link_label) . '">'
                . '<time datetime="' . esc_attr($published_iso) . '">' . esc_html($published_date) . '</time>'
                . '</a>'
                . '</span>';
        }

        if (self::enabled($atts['time'])) {
            $published_iso = get_the_date(DATE_W3C, $post_id);
            $published_time = get_the_time(get_option('time_format'), $post_id);
            $items[] = '<span class="m360-post-info__item m360-post-info__item--time">'
                . self::icon('clock')
                . '<span class="m360-post-info__label">' . esc_html($is_en ? 'at' : 'às') . '</span> '
                . '<time datetime="' . esc_attr($published_iso) . '">' . esc_html($published_time) . '</time>'
                . '</span>';
        }

        if (self::enabled($atts['updated'])) {
            $modified_timestamp = (int) get_post_modified_time('U', true, $post_id);
            $published_timestamp = (int) get_post_time('U', true, $post_id);
            if ($modified_timestamp > $published_timestamp + MINUTE_IN_SECONDS) {
                $modified_iso = get_the_modified_date(DATE_W3C, $post_id);
                $relative = human_time_diff($modified_timestamp, (int) current_time('timestamp', true));
                $text = $is_en ? 'Updated ' . $relative . ' ago' : 'Atualizado há ' . $relative;
                $items[] = '<span class="m360-post-info__item m360-post-info__item--updated">'
                    . self::icon('updated')
                    . '<time datetime="' . esc_attr($modified_iso) . '">' . esc_html($text) . '</time>'
                    . '</span>';
            }
        }

        if (empty($items)) { return ''; }
        $custom_class = sanitize_html_class((string) $atts['class']);
        $classes = 'm360-post-info' . ($custom_class !== '' ? ' ' . $custom_class : '');
        $label = $is_en ? 'Post information' : 'Informações do post';

        return '<div class="' . esc_attr($classes) . '" aria-label="' . esc_attr($label) . '">'
            . implode('', $items)
            . '</div>';
    }

    private static function enabled(mixed $value): bool
    {
        return !in_array(strtolower(trim((string) $value)), ['0', 'false', 'no', 'off'], true);
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-post-info', 'registered')) { wp_enqueue_style('m360-core-post-info'); }
    }

    private static function is_en(): bool
    {
        if (function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
            if (is_string($language) && $language !== '') { return strtolower($language) === 'en'; }
        }
        return str_starts_with((string) determine_locale(), 'en');
    }

    private static function icon(string $name): string
    {
        $paths = [
            'category' => '<path d="M2.5 5.5v-3h3l8.8 8.8a1.7 1.7 0 0 1 0 2.4l-.6.6a1.7 1.7 0 0 1-2.4 0L2.5 5.5Zm2-1.5a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z"/>',
            'calendar' => '<path d="M5 1v2m6-2v2M2.5 6h11M3 2.5h10a1 1 0 0 1 1 1V13a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1Z"/>',
            'clock'    => '<circle cx="8" cy="8" r="6"/><path d="M8 4.5V8l2.5 1.5"/>',
            'updated'  => '<path d="M13 5V2.5L11.2 4.3A6 6 0 1 0 14 9"/><path d="M13 2.5h-2.5"/>',
        ];
        if (!isset($paths[$name])) { return ''; }
        return '<svg class="m360-post-info__icon" aria-hidden="true" viewBox="0 0 16 16" focusable="false">' . $paths[$name] . '</svg>';
    }
}
