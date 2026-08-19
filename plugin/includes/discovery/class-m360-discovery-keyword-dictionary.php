<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Optional, read-only adapter for an environment-owned internal-link table.
 * Absence of the table is a supported state and never blocks portable mode.
 */
final class M360_Discovery_Keyword_Dictionary
{
    /** @return array<string,mixed> */
    public static function diagnostics(array $locales, array $post_ids = []): array
    {
        $external = M360_Discovery_External_Dictionary_Provider::configured();
        $table = $external ? M360_Discovery_External_Dictionary_Provider::table() : self::table_name();
        $available = $external ? self::external_available() : $table !== '';
        $counts = [];
        foreach ($locales as $locale) {
            $normalized = str_replace('_', '-', sanitize_text_field((string) $locale));
            $counts[$normalized] = count(self::rows($normalized));
        }
        $matches = [];
        $settings = M360_Content_Discovery_Module::settings();
        $resolver = new M360_Discovery_Locale_Resolver();
        foreach (array_slice(array_values(array_unique(array_map('intval', $post_ids))), 0, 20) as $post_id) {
            $post = get_post($post_id);
            if (!$post instanceof WP_Post || $post->post_status !== 'publish') { continue; }
            $locale = $resolver->resolve($post_id);
            $terms = self::matching_terms((string) $post->post_content, $locale, (array) $settings['taxonomies'], 20);
            $matches[$post_id] = array_map(static fn(WP_Term $term): string => $term->taxonomy . ':' . $term->term_id . ':' . $term->name, $terms);
        }
        $status = $available ? 'connected' : 'not_configured';
        if ($external && !$available) {
            $status = M360_Discovery_External_Dictionary_Provider::connection() instanceof PDO
                ? 'table_unavailable'
                : M360_Discovery_External_Dictionary_Provider::status();
        }
        return [
            'available' => $available,
            'provider' => $external ? 'external-pdo' : ($available ? 'wordpress-db' : 'none'),
            'status' => $status,
            'table' => $table,
            'rows' => $counts,
            'matches' => $matches,
        ];
    }

    /** @return WP_Term[] */
    public static function matching_terms(string $content, string $locale, array $taxonomies, int $limit = 8): array
    {
        $visible = wp_strip_all_tags(strip_shortcodes($content));
        if (trim($visible) === '') { return []; }
        $allowed = array_values(array_filter(array_map('sanitize_key', $taxonomies), 'taxonomy_exists'));
        $matches = [];
        foreach (self::rows($locale) as $row) {
            $phrase = trim(html_entity_decode((string) ($row['txt_keyword'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if (mb_strlen($phrase, 'UTF-8') < 3 || !self::contains_phrase($visible, $phrase)) { continue; }
            $term = get_term(max(0, (int) ($row['id_wp_origem'] ?? 0)));
            if (!$term instanceof WP_Term || is_wp_error($term) || !in_array($term->taxonomy, $allowed, true)) { continue; }
            if (!self::term_matches_locale($term, $locale)) { continue; }
            $matches[$term->term_id] = $term;
            if (count($matches) >= max(1, min(20, $limit))) { break; }
        }
        return array_values($matches);
    }

    /** @return string[] */
    public static function aliases_for_term(WP_Term $term, string $locale): array
    {
        $aliases = [];
        foreach (self::rows($locale) as $row) {
            if ((int) ($row['id_wp_origem'] ?? 0) !== (int) $term->term_id) { continue; }
            $value = trim(html_entity_decode((string) ($row['txt_keyword'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if (mb_strlen($value, 'UTF-8') >= 3) { $aliases[] = $value; }
        }
        return array_values(array_unique($aliases));
    }

    public static function fingerprint(string $locale): string
    {
        $parts = [];
        foreach (self::rows($locale) as $row) {
            $parts[] = implode(':', [
                (int) ($row['id_wp_origem'] ?? 0),
                (string) ($row['txt_keyword'] ?? ''),
                (int) ($row['ind_prioridade'] ?? 1),
            ]);
        }
        return $parts ? hash('sha256', implode('|', $parts)) : 'none';
    }

    /** @return array<int,array<string,mixed>> */
    private static function rows(string $locale): array
    {
        static $cache = [];
        $locale = str_replace('_', '-', sanitize_text_field($locale));
        if (isset($cache[$locale])) { return $cache[$locale]; }
        $language = strtolower((string) strtok($locale, '-'));
        if (M360_Discovery_External_Dictionary_Provider::configured()) {
            $pdo = M360_Discovery_External_Dictionary_Provider::connection();
            if (!$pdo instanceof PDO) { return $cache[$locale] = []; }
            try {
                $statement = $pdo->prepare(
                    'SELECT id_wp_origem, txt_keyword, tpo_link, ind_prioridade'
                    . ' FROM ' . M360_Discovery_External_Dictionary_Provider::quote_identifier(M360_Discovery_External_Dictionary_Provider::table())
                    . " WHERE ind_ativo = 1 AND ind_idioma IN (?, ?)"
                    . " AND tpo_link IN ('categoria', 'tag')"
                    . ' AND id_wp_origem IS NOT NULL AND txt_keyword IS NOT NULL'
                    . ' ORDER BY ind_prioridade ASC, id ASC LIMIT 500'
                );
                $statement->execute([$language, $locale]);
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                return $cache[$locale] = is_array($rows) ? $rows : [];
            } catch (Throwable $exception) {
                return $cache[$locale] = [];
            }
        }
        global $wpdb;
        if (!$wpdb instanceof wpdb) { return $cache[$locale] = []; }
        $table = self::table_name();
        if ($table === '') { return $cache[$locale] = []; }
        $table_sql = '`' . str_replace('`', '``', $table) . '`';
        $sql = $wpdb->prepare(
            "SELECT id_wp_origem, txt_keyword, tpo_link, ind_prioridade
             FROM {$table_sql}
             WHERE ind_ativo = 1
               AND ind_idioma IN (%s, %s)
               AND tpo_link IN ('categoria', 'tag')
               AND id_wp_origem IS NOT NULL
               AND txt_keyword IS NOT NULL
             ORDER BY ind_prioridade ASC, id ASC
             LIMIT 500",
            $language,
            $locale
        );
        $rows = $wpdb->get_results($sql, ARRAY_A);
        return $cache[$locale] = is_array($rows) ? $rows : [];
    }

    private static function external_available(): bool
    {
        static $available = null;
        if (is_bool($available)) { return $available; }
        $pdo = M360_Discovery_External_Dictionary_Provider::connection();
        if (!$pdo instanceof PDO) { return $available = false; }
        try {
            $pdo->query(
                'SELECT 1 FROM '
                . M360_Discovery_External_Dictionary_Provider::quote_identifier(M360_Discovery_External_Dictionary_Provider::table())
                . ' LIMIT 1'
            );
            return $available = true;
        } catch (Throwable $exception) {
            return $available = false;
        }
    }

    private static function table_name(): string
    {
        static $resolved = null;
        if (is_string($resolved)) { return $resolved; }
        global $wpdb;
        if (!$wpdb instanceof wpdb) { return $resolved = ''; }
        $candidates = array_values(array_unique([
            $wpdb->prefix . 'links_internos',
            strtolower($wpdb->prefix) . 'links_internos',
            strtoupper($wpdb->prefix) . 'links_internos',
            'WP_links_internos',
            'wp_links_internos',
        ]));
        $tables = $wpdb->get_col('SHOW TABLES');
        if (!is_array($tables)) { return $resolved = ''; }
        foreach ($candidates as $candidate) {
            foreach ($tables as $table) {
                if (strcasecmp((string) $table, $candidate) === 0) { return $resolved = (string) $table; }
            }
        }
        return $resolved = '';
    }

    private static function contains_phrase(string $content, string $phrase): bool
    {
        return (bool) preg_match('~(?<![\p{L}\p{N}_])' . preg_quote($phrase, '~') . '(?![\p{L}\p{N}_])~iu', $content);
    }

    private static function term_matches_locale(WP_Term $term, string $locale): bool
    {
        if (!function_exists('pll_get_term_language')) { return true; }
        $term_locale = (string) pll_get_term_language($term->term_id, 'locale');
        if ($term_locale === '') { $term_locale = (string) pll_get_term_language($term->term_id, 'slug'); }
        if ($term_locale === '') { return true; }
        $resolver = new M360_Discovery_Locale_Resolver();
        return strcasecmp($resolver->normalize_supported($term_locale), $locale) === 0;
    }
}
