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
        if (!$slot) { return self::empty_result($slot_key, $args, null); }

        $strict_provider = !empty($args['provider_strict']) ? sanitize_key((string) ($args['provider'] ?? '')) : '';
        $campaign = self::find_campaign((int) $slot['id'], $strict_provider);
        if (!$campaign) { return self::empty_result($slot_key, $args, $slot); }

        $creative = self::find_creative((int) $campaign['id'], $slot_key, $slot);
        $payload = $creative ?: $campaign;
        $provider = self::provider_from_payload($payload);
        $content = $creative ? self::render_creative($creative) : self::render_campaign($campaign);
        $status = trim($content) !== '' ? 'filled' : 'empty';
        if ($status === 'empty') { return self::empty_result($slot_key, $args, $slot); }

        return self::render_slot_shell($slot_key, $provider, $status, $provider, $content, $args, $slot);
    }

    private static function empty_result(string $slot_key, array $args, ?array $slot): string
    {
        $show_placeholder = (bool) ($args['show_placeholder'] ?? false);
        $show_placeholder = (bool) apply_filters('m360_ads_show_empty_placeholder', $show_placeholder, $slot_key, $args, $slot);
        if (!$show_placeholder) { return ''; }
        return self::render_slot_shell($slot_key, '', 'empty', 'internal', self::placeholder($slot_key, $args), $args, $slot);
    }

    private static function render_slot_shell(string $slot_key, string $format, string $status, string $provider, string $content, array $args = [], ?array $slot = null): string
    {
        $language = self::language();
        $label = self::label($language);
        $format = $format !== '' ? sanitize_key($format) : self::format_from_slot($slot_key, $slot);
        $provider = self::normalize_provider($provider);
        $status = sanitize_key($status ?: 'empty');
        $width = absint($slot['max_width'] ?? 0);
        $height = absint($slot['max_height'] ?? 0);
        $extra_class = sanitize_html_class((string) ($args['class'] ?? ''));
        $classes = trim(implode(' ', array_filter([
            'm360-ad',
            'm360-ad-slot',
            'm360-ad-slot--' . sanitize_html_class($slot_key),
            'm360-ad-slot--provider-' . sanitize_html_class($provider),
            'm360-ad-slot--format-' . sanitize_html_class($format),
            'm360-ad-slot--status-' . sanitize_html_class($status),
            $extra_class,
        ])));

        if (trim($content) === '') { $content = self::placeholder($slot_key, $args); $status = 'empty'; }

        $html = '<section id="m360-ad-slot-' . esc_attr($slot_key) . '" class="' . esc_attr($classes) . '" data-m360-ad-slot="' . esc_attr($slot_key) . '" data-m360-ad-provider="' . esc_attr($provider) . '" data-m360-ad-format="' . esc_attr($format) . '" data-m360-ad-lang="' . esc_attr($language) . '" data-m360-ad-status="' . esc_attr($status) . '"';
        if ($width > 0) { $html .= ' data-m360-ad-width="' . esc_attr((string) $width) . '"'; }
        if ($height > 0) { $html .= ' data-m360-ad-height="' . esc_attr((string) $height) . '"'; }
        $html .= ' aria-label="' . esc_attr($label) . '">';
        $html .= '<!-- M360 ADS SLOT: ' . esc_html($slot_key) . ' | provider: ' . esc_html($provider) . ' | format: ' . esc_html($format) . ' | lang: ' . esc_html($language) . ' | status: ' . esc_html($status) . ' -->';
        $html .= '<span class="m360-ad-slot__label">' . esc_html($label) . '</span>';
        $html .= '<div class="m360-ad-slot__content">' . $content . '</div>';
        $html .= '</section>';
        return $html;
    }

    private static function find_slot(string $slot_key): ?array
    {
        global $wpdb;
        $table = M360_Ads_DB::table('ad_slots');
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE slot_key = %s AND is_active = 1 LIMIT 1", $slot_key), ARRAY_A);
        return is_array($row) ? $row : null;
    }

    private static function find_campaign(int $slot_id, string $strict_provider = ''): ?array
    {
        global $wpdb;
        $campaigns = M360_Ads_DB::table('ad_campaigns');
        $relations = M360_Ads_DB::table('ad_slot_campaigns');
        $language = self::language();
        $device = self::device();
        $now = current_time('mysql');
        $provider_types = [
            'adsense' => ['adsense'],
            'google-ad-manager' => ['gam'],
            'house' => ['house'],
            'affiliate' => ['affiliate'],
            'sponsor' => ['sponsor'],
            'internal' => ['image', 'html', 'script'],
        ];
        $provider_sql = '';
        $params = [$slot_id, $language, $device, $now, $now];
        if (isset($provider_types[$strict_provider])) {
            $placeholders = implode(',', array_fill(0, count($provider_types[$strict_provider]), '%s'));
            $provider_sql = " AND c.campaign_type IN ({$placeholders})";
            array_push($params, ...$provider_types[$strict_provider]);
        }
        $params[] = $language;
        $sql = $wpdb->prepare("SELECT c.*, r.priority AS slot_priority FROM {$relations} r INNER JOIN {$campaigns} c ON c.id = r.campaign_id WHERE r.slot_id = %d AND r.is_active = 1 AND c.status = 'active' AND (c.language = %s OR c.language = 'all') AND (c.device = %s OR c.device = 'all') AND (c.start_at IS NULL OR c.start_at <= %s) AND (c.end_at IS NULL OR c.end_at >= %s){$provider_sql} ORDER BY CASE WHEN c.language = %s THEN 0 ELSE 1 END ASC, r.priority DESC, c.priority DESC, c.id DESC LIMIT 1", ...$params);
        $row = $wpdb->get_row($sql, ARRAY_A);
        return is_array($row) ? $row : null;
    }

    private static function find_creative(int $campaign_id, string $slot_key, array $slot): ?array
    {
        $language = self::language();
        $device = self::device();

        $strategies = self::creative_strategies($slot_key, $slot);
        foreach ($strategies as $strategy) {
            $row = self::query_creative($campaign_id, $language, $device, $strategy);
            if (is_array($row)) { return $row; }
        }

        return self::query_creative($campaign_id, $language, $device, ['mode' => 'campaign']);
    }

    private static function creative_strategies(string $slot_key, array $slot): array
    {
        $width = absint($slot['max_width'] ?? 0);
        $height = absint($slot['max_height'] ?? 0);
        $strategies = [];

        foreach (self::slot_slug_candidates($slot_key) as $slug) {
            $strategies[] = ['mode' => 'slug', 'slug' => $slug];
        }

        if ($slot_key === 'content-bottom') {
            $strategies[] = ['mode' => 'slot_terms_wide', 'terms' => ['content', 'bottom']];
            $strategies[] = ['mode' => 'wide'];
        }

        if ($slot_key === 'sidebar-community') {
            $strategies[] = ['mode' => 'slot_terms_square', 'terms' => ['sidebar', 'community']];
            $strategies[] = ['mode' => 'slot_terms_square', 'terms' => ['sidebar', 'html']];
            $strategies[] = ['mode' => 'square'];
        }

        if ($slot_key === 'sidebar-square') {
            $strategies[] = ['mode' => 'slot_terms_square', 'terms' => ['sidebar', 'mega']];
            $strategies[] = ['mode' => 'square'];
        }

        if ($width && $height) {
            $strategies[] = ['mode' => 'size', 'width' => $width, 'height' => $height];
        }

        return $strategies;
    }

    private static function query_creative(int $campaign_id, string $language, string $device, array $strategy): ?array
    {
        global $wpdb;
        $table = M360_Ads_DB::table('ad_creatives');
        $base = "SELECT * FROM {$table} WHERE campaign_id = %d AND status = 'active' AND (language = %s OR language = 'all') AND (device = %s OR device = 'all')";
        $order = " ORDER BY CASE WHEN language = %s THEN 0 ELSE 1 END ASC, CASE WHEN device = %s THEN 0 ELSE 1 END ASC, id DESC LIMIT 1";
        $mode = (string) ($strategy['mode'] ?? 'campaign');

        if ($mode === 'slug') {
            $sql = $wpdb->prepare($base . " AND slug = %s" . $order, $campaign_id, $language, $device, (string) $strategy['slug'], $language, $device);
            $row = $wpdb->get_row($sql, ARRAY_A);
            return is_array($row) ? $row : null;
        }

        if ($mode === 'size') {
            $sql = $wpdb->prepare($base . " AND width = %d AND height = %d" . $order, $campaign_id, $language, $device, absint($strategy['width']), absint($strategy['height']), $language, $device);
            $row = $wpdb->get_row($sql, ARRAY_A);
            return is_array($row) ? $row : null;
        }

        if ($mode === 'wide') {
            $sql = $wpdb->prepare($base . " AND width > height AND width >= 700" . $order, $campaign_id, $language, $device, $language, $device);
            $row = $wpdb->get_row($sql, ARRAY_A);
            return is_array($row) ? $row : null;
        }

        if ($mode === 'square') {
            $sql = $wpdb->prepare($base . " AND width = height" . $order, $campaign_id, $language, $device, $language, $device);
            $row = $wpdb->get_row($sql, ARRAY_A);
            return is_array($row) ? $row : null;
        }

        if ($mode === 'slot_terms_wide' || $mode === 'slot_terms_square') {
            $terms = array_values(array_filter((array) ($strategy['terms'] ?? [])));
            if (!$terms) { return null; }
            $conditions = [];
            $params = [$campaign_id, $language, $device];
            foreach ($terms as $term) {
                $conditions[] = 'slug LIKE %s';
                $params[] = '%' . $wpdb->esc_like((string) $term) . '%';
            }
            $shape = $mode === 'slot_terms_wide' ? ' AND width > height AND width >= 700' : ' AND width = height';
            $params[] = $language;
            $params[] = $device;
            $sql = $wpdb->prepare($base . ' AND (' . implode(' OR ', $conditions) . ')' . $shape . $order, ...$params);
            $row = $wpdb->get_row($sql, ARRAY_A);
            return is_array($row) ? $row : null;
        }

        $sql = $wpdb->prepare($base . $order, $campaign_id, $language, $device, $language, $device);
        $row = $wpdb->get_row($sql, ARRAY_A);
        return is_array($row) ? $row : null;
    }

    private static function slot_slug_candidates(string $slot_key): array
    {
        $map = [
            'header-top' => ['m360-pilot-header-mega-bolao'],
            'content-bottom' => ['m360-pilot-content-whatsapp', 'bottom-html-comunidade-en-us', 'bottom-html-comunidade-pt-br'],
            'sidebar-community' => ['m360-pilot-sidebar-whatsapp', 'sidebar-html-11-comunidade-en-us', 'sidebar-html-11-comunidade-pt-br'],
            'sidebar-square' => ['m360-pilot-sidebar-mega-bolao'],
        ];
        $base = ['m360-pilot-' . $slot_key, 'm360-pilot-' . str_replace('-', '_', $slot_key)];
        return array_values(array_unique(array_merge($map[$slot_key] ?? [], $base)));
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
            return '<div class="m360-ad__script m360-ad__script--' . esc_attr($type) . '">' . self::trusted_ad_markup((string) ($payload['script_code'] ?? '')) . '</div>';
        }
        return '';
    }

    private static function render_html_payload(array $payload): string
    {
        $html = (string) ($payload['html_code'] ?? '');
        if ($html === '') { return ''; }
        return '<div class="m360-ad__html">' . self::trusted_ad_markup($html) . '</div>';
    }

    private static function trusted_ad_markup(string $markup): string
    {
        return do_shortcode($markup);
    }

    private static function placeholder(string $slot_key, array $args = []): string
    {
        $fallback = trim((string) ($args['fallback'] ?? ''));
        if ($fallback !== '') { return '<div class="m360-ad-slot__placeholder m360-ad-slot__placeholder--custom">' . wp_kses_post($fallback) . '</div>'; }
        $message = self::language() === 'en-us' ? 'Advertising space available.' : 'Espaço publicitário disponível.';
        return '<div class="m360-ad-slot__placeholder" aria-hidden="true"><span>' . esc_html($message) . '</span></div>';
    }

    private static function label(string $language): string
    {
        return $language === 'en-us' ? 'ADVERTISEMENT' : 'PUBLICIDADE';
    }

    private static function provider_from_payload(array $payload): string
    {
        $type = sanitize_key((string) ($payload['creative_type'] ?? $payload['campaign_type'] ?? 'internal'));
        return self::normalize_provider($type);
    }

    private static function normalize_provider(string $provider): string
    {
        $provider = sanitize_key($provider ?: 'internal');
        if ($provider === 'gam') { return 'google-ad-manager'; }
        if ($provider === 'image' || $provider === 'html' || $provider === 'script') { return 'internal'; }
        if (in_array($provider, ['internal','adsense','google-ad-manager','house','affiliate','sponsor'], true)) { return $provider; }
        return 'internal';
    }

    private static function format_from_slot(string $slot_key, ?array $slot = null): string
    {
        $width = absint($slot['max_width'] ?? 0);
        $height = absint($slot['max_height'] ?? 0);
        if ($width >= 700 && $height > 0 && $width > $height) { return 'leaderboard'; }
        if ($width > 0 && $height > 0 && $width === $height) { return 'square'; }
        if (str_contains($slot_key, 'sidebar')) { return 'sidebar'; }
        if (str_contains($slot_key, 'header')) { return 'leaderboard'; }
        if (str_contains($slot_key, 'bottom')) { return 'wide'; }
        return 'responsive';
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
