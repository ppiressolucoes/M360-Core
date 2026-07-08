<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ad_Slot_Component
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_ad_slot', [self::class, 'shortcode']);
        add_shortcode('m360_ads_slot', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts(['id' => '', 'class' => '', 'fallback' => ''], $atts, 'm360_ad_slot');
        return self::render((string) $atts['id'], ['class' => (string) $atts['class'], 'fallback' => (string) $atts['fallback']]);
    }

    public static function render(string $slot_key, array $args = []): string
    {
        self::enqueue_assets();
        $slot_key = sanitize_key($slot_key);
        if ($slot_key === '') { return ''; }

        $slot = self::find_slot($slot_key);
        if (!$slot) { return self::fallback($slot_key, $args); }

        $campaign = self::find_campaign((int) $slot['id']);
        if (!$campaign) { return self::fallback($slot_key, $args, $slot); }

        $creative = self::find_creative((int) $campaign['id'], $slot_key, $slot);
        $classes = trim('m360-ad m360-ad-slot m360-ad-slot--' . sanitize_html_class($slot_key) . ' ' . sanitize_html_class((string) ($args['class'] ?? '')));
        $html = '<aside class="' . esc_attr($classes) . '" data-m360-ad-slot="' . esc_attr($slot_key) . '">';
        $html .= $creative ? self::render_creative($creative) : self::render_campaign($campaign);
        $html .= '</aside>';
        return $html;
    }

    private static function find_slot(string $slot_key): ?array
    {
        global $wpdb;
        $table = M360_Ads_DB::table('ad_slots');
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE slot_key = %s AND is_active = 1 LIMIT 1", $slot_key), ARRAY_A);
        return is_array($row) ? $row : null;
    }

    private static function find_campaign(int $slot_id): ?array
    {
        global $wpdb;
        $campaigns = M360_Ads_DB::table('ad_campaigns');
        $relations = M360_Ads_DB::table('ad_slot_campaigns');
        $language = self::language();
        $device = self::device();
        $now = current_time('mysql');
        $sql = $wpdb->prepare("SELECT c.*, r.priority AS slot_priority FROM {$relations} r INNER JOIN {$campaigns} c ON c.id = r.campaign_id WHERE r.slot_id = %d AND r.is_active = 1 AND c.status = 'active' AND (c.language = %s OR c.language = 'all') AND (c.device = %s OR c.device = 'all') AND (c.start_at IS NULL OR c.start_at <= %s) AND (c.end_at IS NULL OR c.end_at >= %s) ORDER BY r.priority DESC, c.priority DESC, c.id DESC LIMIT 1", $slot_id, $language, $device, $now, $now);
        $row = $wpdb->get_row($sql, ARRAY_A);
        return is_array($row) ? $row : null;
    }

    private static function find_creative(int $campaign_id, string $slot_key, array $slot): ?array
    {
        global $wpdb;
        $table = M360_Ads_DB::table('ad_creatives');
        $language = self::language();
        $device = self::device();

        $slug_like = 'm360-pilot-' . $wpdb->esc_like($slot_key) . '%';
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table}
             WHERE campaign_id = %d
               AND status = 'active'
               AND (language = %s OR language = 'all')
               AND (device = %s OR device = 'all')
               AND slug LIKE %s
             ORDER BY id DESC
             LIMIT 1",
            $campaign_id,
            $language,
            $device,
            $slug_like
        ), ARRAY_A);
        if (is_array($row)) { return $row; }

        $width = absint($slot['max_width'] ?? 0);
        $height = absint($slot['max_height'] ?? 0);
        if ($width && $height) {
            $row = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$table}
                 WHERE campaign_id = %d
                   AND status = 'active'
                   AND (language = %s OR language = 'all')
                   AND (device = %s OR device = 'all')
                   AND width = %d
                   AND height = %d
                 ORDER BY id DESC
                 LIMIT 1",
                $campaign_id,
                $language,
                $device,
                $width,
                $height
            ), ARRAY_A);
            if (is_array($row)) { return $row; }
        }

        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table}
             WHERE campaign_id = %d
               AND status = 'active'
               AND (language = %s OR language = 'all')
               AND (device = %s OR device = 'all')
             ORDER BY id ASC
             LIMIT 1",
            $campaign_id,
            $language,
            $device
        ), ARRAY_A);
        return is_array($row) ? $row : null;
    }

    private static function render_creative(array $creative): string
    {
        return self::render_payload(sanitize_key((string) ($creative['creative_type'] ?? 'image')), $creative);
    }

    private static function render_campaign(array $campaign): string
    {
        return self::render_payload(sanitize_key((string) ($campaign['campaign_type'] ?? 'image')), $campaign);
    }

    private static function render_payload(string $type, array $payload): string
    {
        $title = (string) ($payload['title'] ?? '');
        $target = (string) ($payload['target_url'] ?? '');
        $alt = (string) ($payload['alt_text'] ?? $title);
        if (in_array($type, ['image','house','sponsor','affiliate'], true)) {
            $image = (string) ($payload['image_url'] ?? '');
            if ($image === '') { return self::render_html_payload($payload); }
            $img = '<img class="m360-ad__image" src="' . esc_url($image) . '" alt="' . esc_attr($alt) . '" loading="lazy">';
            return $target !== '' ? '<a class="m360-ad__link" href="' . esc_url($target) . '" target="_blank" rel="nofollow sponsored noopener">' . $img . '</a>' : $img;
        }
        if ($type === 'html') { return self::render_html_payload($payload); }
        if ($type === 'adsense' || $type === 'gam' || $type === 'script') {
            if (!current_user_can('unfiltered_html')) { return '<div class="m360-ad__script m360-ad__script--blocked"></div>'; }
            return '<div class="m360-ad__script m360-ad__script--' . esc_attr($type) . '">' . (string) ($payload['script_code'] ?? '') . '</div>';
        }
        return '';
    }

    private static function render_html_payload(array $payload): string
    {
        return '<div class="m360-ad__html">' . wp_kses_post((string) ($payload['html_code'] ?? '')) . '</div>';
    }

    private static function fallback(string $slot_key, array $args = [], ?array $slot = null): string
    {
        $fallback = trim((string) ($args['fallback'] ?? ''));
        if ($fallback === '') { return ''; }
        $classes = 'm360-ad m360-ad-slot m360-ad-slot--' . sanitize_html_class($slot_key) . ' m360-ad-slot--fallback';
        return '<aside class="' . esc_attr($classes) . '">' . wp_kses_post($fallback) . '</aside>';
    }

    private static function language(): string
    {
        if (function_exists('pll_current_language')) { $lang = pll_current_language('slug'); if ($lang === 'en') { return 'en-us'; } if ($lang === 'pt') { return 'pt-br'; } }
        return str_starts_with((string) get_locale(), 'en') ? 'en-us' : 'pt-br';
    }

    private static function device(): string { return wp_is_mobile() ? 'mobile' : 'desktop'; }
    public static function enqueue_assets(): void { if (wp_style_is('m360-core-ads', 'registered')) { wp_enqueue_style('m360-core-ads'); } }
}

if (!function_exists('m360_ad_slot')) { function m360_ad_slot(string $slot_key, array $args = []): string { return M360_Ad_Slot_Component::render($slot_key, $args); } }
if (!function_exists('m360_ads_render_slot')) { function m360_ads_render_slot(string $slot_key, array $args = []): string { return M360_Ad_Slot_Component::render($slot_key, $args); } }
