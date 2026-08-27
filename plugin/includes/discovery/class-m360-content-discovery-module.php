<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Content_Discovery_Module implements M360_Module_Interface
{
    private const SETTINGS = 'm360_discovery_settings';
    private const CANARY_POSTS = 'm360_discovery_canary_posts';

    public function id(): string { return 'content-discovery-seo'; }
    public function label(): string { return 'Content Discovery & SEO'; }
    public function version(): string { return M360_CORE_VERSION; }
    public function schema_version(): string { return '8'; }
    public function dependencies(): array { return ['publisher-foundation']; }
    public function capabilities(): array { return ['manage_options']; }
    public function asset_handles(): array { return ['styles' => [], 'scripts' => []]; }
    public function is_required(): bool { return false; }
    public function default_enabled(): bool { return false; }

    public function settings_schema(): array
    {
        return [
            'mode' => ['type' => 'enum:off,legacy-read,shadow', 'portable' => false],
            'legacy_read_fallback' => ['type' => 'boolean', 'portable' => false],
            'post_types' => ['type' => 'string[]', 'portable' => true],
            'taxonomies' => ['type' => 'string[]', 'portable' => true],
            'supported_locales' => ['type' => 'locale[]', 'portable' => true],
            'generation_strategy' => ['type' => 'enum:manual,async', 'portable' => true],
            'renderer_canary_posts' => ['type' => 'int[]', 'portable' => false],
            'public_render_mode' => ['type' => 'enum:shortcode,canary,prospective,automatic', 'portable' => false],
            'prospective_started_at' => ['type' => 'datetime', 'portable' => false],
            'contextual_links_max' => ['type' => 'integer:0,3', 'portable' => false],
            'writer_mode' => ['type' => 'enum:manual,prospective,automatic', 'portable' => false],
        ];
    }

    public function activate(): void
    {
        $stored = get_option(self::SETTINGS, null);
        if ($stored === null) { add_option(self::SETTINGS, self::defaults(), '', false); }
        elseif (is_array($stored) && ($stored['mode'] ?? 'legacy-read') === 'legacy-read') {
            $stored['mode'] = 'shadow';
            update_option(self::SETTINGS, $stored, false);
        }
        if (get_option(self::CANARY_POSTS, null) === null) {
            $legacy_canary = is_array($stored) ? (array) ($stored['renderer_canary_posts'] ?? []) : [];
            add_option(self::CANARY_POSTS, self::sanitize_post_ids($legacy_canary), '', false);
        }
        M360_Discovery_DB::activate();
    }

    public function deactivate(): void {}

    public function boot(): void
    {
        M360_Shadow_Generator::register();
        M360_Discovery_Scheduler::register();
        M360_Canary_Renderer::register();
        M360_Discovery_Content_Injector::register();
    }

    public function health(): array
    {
        $settings = self::settings();
        if ($settings['mode'] === 'off') { return ['status' => 'warning', 'message' => 'Módulo ativo, mas geração shadow está desligada.']; }
        $legacy = self::adapter()->health();
        $storage = M360_Discovery_DB::schema_health();
        if (!$storage['healthy']) { return ['status'=>'error','message'=>'Storage próprio ausente ou não transacional.']; }
        if ($legacy['status'] === 'error') { return $legacy; }
        $renderer = match ($settings['public_render_mode']) {
            'automatic' => 'injeção automática do Core ativa',
            'prospective' => 'injeção pública restrita aos canários e novas publicações',
            'canary' => 'injeção automática restrita aos posts canários',
            default => 'renderer disponível por shortcode',
        };
        $writer = match ($settings['writer_mode']) {
            'automatic' => 'writer assíncrono e backfill disponíveis',
            'prospective' => 'writer prospectivo ativo somente para novas publicações',
            default => 'writer automático desativado',
        };
        return ['status'=>'healthy','message'=>'Storage próprio saudável; ' . $renderer . '; ' . $writer . '.'];
    }

    public static function adapter(): M360_Legacy_Semantic_Adapter
    {
        static $adapter = null;
        if (!$adapter instanceof M360_Legacy_Semantic_Adapter) {
            $adapter = new M360_Legacy_Semantic_Adapter();
        }
        return $adapter;
    }

    public static function comparator(): M360_Semantic_Comparator
    {
        static $comparator = null;
        if (!$comparator instanceof M360_Semantic_Comparator) { $comparator = new M360_Semantic_Comparator(); }
        return $comparator;
    }

    /** @return array<string,mixed> */
    public static function defaults(): array
    {
        $profile = M360_Site_Profile::get();
        return [
            'mode' => 'shadow',
            'legacy_read_fallback' => true,
            'post_types' => ['post'],
            'taxonomies' => ['category', 'post_tag'],
            'supported_locales' => array_values((array) ($profile['supported_locales'] ?? ['pt-BR', 'en-US'])),
            'generation_strategy' => 'manual',
            'renderer_canary_posts' => [],
            'public_render_mode' => 'shortcode',
            'prospective_started_at' => '',
            'contextual_links_max' => 3,
            'writer_mode' => 'manual',
        ];
    }

    /** @return array<string,mixed> */
    public static function settings(): array
    {
        $stored = get_option(self::SETTINGS, []);
        $input = is_array($stored) ? array_merge(self::defaults(), $stored) : self::defaults();
        return [
            'mode' => in_array((string) $input['mode'], ['off', 'legacy-read', 'shadow'], true) ? (string) $input['mode'] : 'off',
            'legacy_read_fallback' => (bool) $input['legacy_read_fallback'],
            'post_types' => self::sanitize_keys((array) $input['post_types'], ['post']),
            'taxonomies' => self::sanitize_keys((array) $input['taxonomies'], ['category', 'post_tag']),
            'supported_locales' => self::sanitize_locales((array) $input['supported_locales']),
            'generation_strategy' => in_array((string) $input['generation_strategy'], ['manual', 'async'], true) ? (string) $input['generation_strategy'] : 'manual',
            'renderer_canary_posts' => self::sanitize_post_ids((array) get_option(self::CANARY_POSTS, $input['renderer_canary_posts'])),
            'public_render_mode' => in_array((string) $input['public_render_mode'], ['shortcode', 'canary', 'prospective', 'automatic'], true) ? (string) $input['public_render_mode'] : 'shortcode',
            'prospective_started_at' => preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', (string) $input['prospective_started_at']) ? (string) $input['prospective_started_at'] : '',
            'contextual_links_max' => max(0, min(3, (int) $input['contextual_links_max'])),
            'writer_mode' => in_array((string) $input['writer_mode'], ['manual', 'prospective', 'automatic'], true) ? (string) $input['writer_mode'] : 'manual',
        ];
    }

    public static function update_renderer_settings(string $mode, int $max_links): bool
    {
        $stored = get_option(self::SETTINGS, []);
        $stored = is_array($stored) ? $stored : [];
        $stored['public_render_mode'] = in_array($mode, ['shortcode', 'canary', 'prospective', 'automatic'], true) ? $mode : 'shortcode';
        if ($stored['public_render_mode'] === 'prospective' && empty($stored['prospective_started_at'])) {
            $stored['prospective_started_at'] = gmdate('Y-m-d H:i:s');
        }
        $stored['contextual_links_max'] = max(0, min(3, $max_links));
        $updated = update_option(self::SETTINGS, $stored, false);
        wp_cache_delete(self::SETTINGS, 'options');
        $settings = self::settings();
        return ($updated || ((string) $settings['public_render_mode'] === $stored['public_render_mode']))
            && (int) $settings['contextual_links_max'] === (int) $stored['contextual_links_max'];
    }

    public static function update_writer_mode(string $mode): bool
    {
        $stored = get_option(self::SETTINGS, []);
        $stored = is_array($stored) ? $stored : [];
        $stored['writer_mode'] = in_array($mode, ['manual', 'prospective', 'automatic'], true) ? $mode : 'manual';
        $updated = update_option(self::SETTINGS, $stored, false);
        wp_cache_delete(self::SETTINGS, 'options');
        return $updated || self::settings()['writer_mode'] === $stored['writer_mode'];
    }

    /** @param array<string,mixed>|null $settings */
    public static function prospective_post_allowed(int $post_id, ?array $settings = null): bool
    {
        $settings = $settings ?? self::settings();
        if (in_array($post_id, (array) ($settings['renderer_canary_posts'] ?? []), true)) { return true; }
        $started_at = (string) ($settings['prospective_started_at'] ?? '');
        if ($started_at === '') { return false; }
        $post = get_post($post_id);
        if (!$post instanceof WP_Post || $post->post_status !== 'publish') { return false; }
        $published_gmt = (string) $post->post_date_gmt;
        if ($published_gmt === '' || $published_gmt === '0000-00-00 00:00:00') {
            $published_gmt = get_gmt_from_date((string) $post->post_date);
        }
        return $published_gmt >= $started_at;
    }

    /** @return string[] */
    private static function sanitize_keys(array $values, array $fallback): array
    {
        $clean = array_values(array_unique(array_filter(array_map('sanitize_key', $values))));
        return $clean ?: $fallback;
    }

    /** @return string[] */
    private static function sanitize_locales(array $values): array
    {
        $clean = [];
        foreach ($values as $value) {
            $locale = str_replace('_', '-', sanitize_text_field((string) $value));
            if (preg_match('/^[a-z]{2,3}(?:-[A-Z]{2})?$/', $locale)) { $clean[] = $locale; }
        }
        return array_values(array_unique($clean)) ?: ['pt-BR', 'en-US'];
    }

    /** @return int[] */
    private static function sanitize_post_ids(array $values): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $values), static fn(int $id): bool => $id > 0)));
        return array_slice($ids, 0, 20);
    }
}
