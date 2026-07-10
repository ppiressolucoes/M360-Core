<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ads_Context_Renderer
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_ad_context', [self::class, 'shortcode']);
        add_shortcode('m360_ads_context', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'context' => '',
            'position' => '',
            'limit' => 0,
            'class' => '',
        ], $atts, 'm360_ad_context');

        $context = sanitize_key((string) $atts['context']);
        if ($context === '' || $context === 'auto') { $context = self::detect_context(); }

        return self::render_context($context, [
            'position' => sanitize_key((string) $atts['position']),
            'limit' => absint($atts['limit']),
            'class' => sanitize_html_class((string) $atts['class']),
        ]);
    }

    public static function render_context(string $context = '', array $args = []): string
    {
        $context = sanitize_key($context ?: self::detect_context());
        if ($context === '') { return ''; }

        $slots = self::slots_for_context($context, $args);
        if (!$slots) { return ''; }

        $html = '<div class="m360-ads-context m360-ads-context--' . esc_attr($context) . '" data-m360-ads-context="' . esc_attr($context) . '">';
        foreach ($slots as $slot) {
            $html .= M360_Slot_Renderer::render((string) $slot['slot_key'], [
                'class' => trim('m360-ads-context__slot ' . (string) ($args['class'] ?? '')),
                'context' => $context,
                'source' => 'context-renderer',
            ]);
        }
        $html .= '</div>';
        return $html;
    }

    public static function render_position(string $context, string $position, array $args = []): string
    {
        $args['position'] = sanitize_key($position);
        $args['limit'] = absint($args['limit'] ?? 1) ?: 1;
        return self::render_context($context, $args);
    }

    public static function slots_for_context(string $context, array $args = []): array
    {
        global $wpdb;
        $context = sanitize_key($context);
        $position = sanitize_key((string) ($args['position'] ?? ''));
        $limit = absint($args['limit'] ?? 0);
        $table = M360_Ads_DB::table('ad_slots');

        $where = ['page_context = %s', 'is_active = 1'];
        $params = [$context];
        if ($position !== '') {
            $where[] = 'position = %s';
            $params[] = $position;
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $where) . ' ORDER BY position ASC, slot_key ASC';
        if ($limit > 0) { $sql .= ' LIMIT ' . $limit; }

        $prepared = $wpdb->prepare($sql, ...$params);
        $rows = $wpdb->get_results($prepared, ARRAY_A);
        return is_array($rows) ? $rows : [];
    }

    public static function detect_context(): string
    {
        if (is_front_page() || is_home()) { return 'home'; }
        if (is_singular('post')) { return 'post'; }
        if (is_search()) { return 'search'; }
        if (is_category()) { return 'category'; }
        if (is_tag()) { return 'tag'; }
        if (is_author()) { return 'author'; }
        if (is_archive()) { return 'archive'; }
        return 'global';
    }
}

if (!function_exists('m360_ads_render_context')) {
    function m360_ads_render_context(string $context = '', array $args = []): string
    {
        return M360_Ads_Context_Renderer::render_context($context, $args);
    }
}

if (!function_exists('m360_ads_render_position')) {
    function m360_ads_render_position(string $context, string $position, array $args = []): string
    {
        return M360_Ads_Context_Renderer::render_position($context, $position, $args);
    }
}
