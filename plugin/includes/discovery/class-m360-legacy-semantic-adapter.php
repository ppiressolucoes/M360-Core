<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Legacy_Semantic_Adapter implements M360_Relation_Repository_Interface
{
    private const RUNS_COLUMNS = ['id', 'source_post_id', 'language', 'trigger_source', 'status', 'duration_ms'];
    private const RELATIONS_COLUMNS = ['id', 'source_post_id', 'target_type', 'target_id', 'language', 'relation_kind', 'score', 'rank', 'run_id', 'status'];
    private const KINDS = ['topic', 'internal_link', 'related_post'];
    private const OPTION_NAMES = [
        'm360_sr_db_version',
        'm360_sr_enabled',
        'm360_sr_shadow_mode',
        'm360_sr_sync_generation',
        'm360_sr_auto_heal_on_view',
        'm360_sr_freeze_promotions',
        'm360_sr_pending_window_days',
        'm360_sr_unfiltered_reprocess',
        'm360_sr_enable_related_posts',
        'm360_sr_enable_topics',
        'm360_sr_enable_internal_links',
        'm360_sr_auto_append_ptbr',
        'm360_sr_auto_append_topics_ptbr',
        'm360_sr_auto_append_related_ptbr',
        'm360_sr_auto_inline_related_ptbr',
        'm360_sr_auto_contextual_terms_ptbr',
        'm360_sr_auto_contextual_posts_ptbr',
        'm360_sr_related_layout_ptbr',
        'm360_sr_auto_append_topics_enus',
        'm360_sr_auto_append_related_enus',
        'm360_sr_auto_inline_related_enus',
        'm360_sr_auto_contextual_terms_enus',
        'm360_sr_auto_contextual_posts_enus',
        'm360_sr_related_layout_enus',
    ];

    public function precursor_active(): bool
    {
        return class_exists('M360_Semantic_Relations_Plugin');
    }

    public function precursor_version(): string
    {
        return defined('M360_SR_VERSION') ? sanitize_text_field((string) M360_SR_VERSION) : '';
    }

    public function runs_table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'm360_semantic_runs';
    }

    public function relations_table(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'm360_semantic_relations';
    }

    public function active(int $source_post_id, string $locale, string $kind, int $limit = 3): array
    {
        global $wpdb;
        $source_post_id = max(0, $source_post_id);
        $locale = sanitize_text_field($locale);
        $kind = sanitize_key($kind);
        $limit = max(1, min(20, $limit));
        if ($source_post_id < 1 || $locale === '' || !in_array($kind, self::KINDS, true)) { return []; }
        if (!$this->relations_schema()['compatible']) { return []; }

        $table = $this->relations_table();
        $sql = "SELECT target_type, target_id, language, relation_kind, score, rank, run_id, status
                FROM {$table}
                WHERE source_post_id = %d
                  AND language = %s
                  AND relation_kind = %s
                  AND status IN ('active','pinned')
                ORDER BY rank ASC, score DESC
                LIMIT %d";
        $rows = $wpdb->get_results($wpdb->prepare($sql, $source_post_id, $locale, $kind, $limit), ARRAY_A);
        return is_array($rows) ? $rows : [];
    }

    /** @return array{duration_ms:int,status:string,failed_runs:int} */
    public function run_metrics(int $source_post_id, string $locale): array
    {
        global $wpdb;
        if (!$this->runs_schema()['compatible'] || !$this->relations_schema()['compatible']) { return ['duration_ms'=>0,'status'=>'','failed_runs'=>0]; }
        $runs = $this->runs_table(); $relations = $this->relations_table();
        $row = $wpdb->get_row($wpdb->prepare("SELECT r.status,r.duration_ms FROM {$runs} r INNER JOIN {$relations} rel ON rel.run_id=r.id WHERE rel.source_post_id=%d AND rel.language=%s AND rel.status IN ('active','pinned') GROUP BY r.id,r.status,r.duration_ms ORDER BY r.id DESC LIMIT 1", $source_post_id, $locale), ARRAY_A);
        $failed = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$runs} WHERE source_post_id=%d AND language=%s AND status='failed'", $source_post_id, $locale));
        return ['duration_ms'=>max(0, (int) ($row['duration_ms'] ?? 0)), 'status'=>sanitize_key((string) ($row['status'] ?? '')), 'failed_runs'=>max(0, $failed)];
    }

    /** @return array<string,mixed> */
    public function summary(): array
    {
        $runs = $this->runs_schema();
        $relations = $this->relations_schema();
        return [
            'mode' => 'legacy-read',
            'read_only' => true,
            'precursor' => [
                'active' => $this->precursor_active(),
                'version' => $this->precursor_version(),
            ],
            'tables' => [
                'runs' => $runs,
                'relations' => $relations,
            ],
            'options' => $this->option_snapshot(),
            'cron' => $this->cron_counts(),
            'run_counts' => $runs['compatible'] ? $this->run_counts() : [],
            'relation_counts' => $relations['compatible'] ? $this->relation_counts() : [],
            'semantic_states' => $this->semantic_state_counts(),
            'warnings' => $this->warnings($runs, $relations),
        ];
    }

    /** @return array{status:string,message:string} */
    public function health(): array
    {
        $runs = $this->runs_schema();
        $relations = $this->relations_schema();
        if (!$this->precursor_active()) {
            return ['status' => 'warning', 'message' => 'Precursor Semantic Relations nao detectado; adapter legado sem origem ativa.'];
        }
        if (!$runs['exists'] || !$relations['exists']) {
            return ['status' => 'error', 'message' => 'Precursor ativo, mas uma ou mais tabelas semanticas nao foram encontradas.'];
        }
        if (!$runs['compatible'] || !$relations['compatible']) {
            return ['status' => 'error', 'message' => 'Schema legado incompativel; nenhuma leitura operacional deve prosseguir.'];
        }
        if (!$runs['transactional'] || !$relations['transactional']) {
            return ['status' => 'warning', 'message' => 'Adapter somente leitura ativo; engine legada nao transacional detectada.'];
        }
        return ['status' => 'healthy', 'message' => 'Adapter legado somente leitura ativo; precursor 0.9.0 permanece writer e renderer exclusivo.'];
    }

    /** @return array<string,mixed> */
    private function runs_schema(): array
    {
        return $this->table_schema($this->runs_table(), self::RUNS_COLUMNS);
    }

    /** @return array<string,mixed> */
    private function relations_schema(): array
    {
        return $this->table_schema($this->relations_table(), self::RELATIONS_COLUMNS);
    }

    /** @return array<string,mixed> */
    private function table_schema(string $table, array $required): array
    {
        global $wpdb;
        $like = $wpdb->esc_like($table);
        $exists = (string) $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $like)) === $table;
        if (!$exists) {
            return ['exists' => false, 'compatible' => false, 'engine' => '', 'collation' => '', 'rows' => 0, 'missing_columns' => $required, 'transactional' => false];
        }

        $status = $wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS LIKE %s', $like), ARRAY_A);
        $columns = $wpdb->get_col("SHOW COLUMNS FROM {$table}", 0);
        $columns = is_array($columns) ? array_map('sanitize_key', $columns) : [];
        $missing = array_values(array_diff($required, $columns));
        $engine = sanitize_text_field((string) ($status['Engine'] ?? ''));
        return [
            'exists' => true,
            'compatible' => $missing === [],
            'engine' => $engine,
            'collation' => sanitize_text_field((string) ($status['Collation'] ?? '')),
            'rows' => max(0, (int) ($status['Rows'] ?? 0)),
            'missing_columns' => $missing,
            'transactional' => strtolower($engine) === 'innodb',
        ];
    }

    /** @return array<string,string> */
    private function option_snapshot(): array
    {
        $snapshot = [];
        foreach (self::OPTION_NAMES as $name) {
            $value = get_option($name, null);
            if ($value === null || is_array($value) || is_object($value)) { continue; }
            $snapshot[$name] = sanitize_text_field((string) $value);
        }
        return $snapshot;
    }

    /** @return array<int,array<string,mixed>> */
    private function run_counts(): array
    {
        global $wpdb;
        $table = $this->runs_table();
        $rows = $wpdb->get_results("SELECT language, status, COUNT(*) AS total FROM {$table} GROUP BY language, status ORDER BY language, status", ARRAY_A);
        return $this->sanitize_count_rows($rows, ['language', 'status']);
    }

    /** @return array<int,array<string,mixed>> */
    private function relation_counts(): array
    {
        global $wpdb;
        $table = $this->relations_table();
        $rows = $wpdb->get_results("SELECT language, relation_kind, status, COUNT(*) AS total FROM {$table} GROUP BY language, relation_kind, status ORDER BY language, relation_kind, status", ARRAY_A);
        return $this->sanitize_count_rows($rows, ['language', 'relation_kind', 'status']);
    }

    /** @return array<int,array<string,mixed>> */
    private function semantic_state_counts(): array
    {
        global $wpdb;
        $table = $wpdb->postmeta;
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT meta_value AS semantic_state, COUNT(*) AS total FROM {$table} WHERE meta_key = %s GROUP BY meta_value ORDER BY total DESC",
            '_m360_semantic_state'
        ), ARRAY_A);
        return $this->sanitize_count_rows($rows, ['semantic_state']);
    }

    /** @return array<string,int> */
    private function cron_counts(): array
    {
        $counts = [];
        if (!function_exists('_get_cron_array')) { return $counts; }
        $cron = _get_cron_array();
        if (!is_array($cron)) { return $counts; }
        foreach ($cron as $hooks) {
            if (!is_array($hooks)) { continue; }
            foreach ($hooks as $hook => $events) {
                if (strpos((string) $hook, 'm360_sr_') !== 0 || !is_array($events)) { continue; }
                $safe_hook = sanitize_key((string) $hook);
                $counts[$safe_hook] = ($counts[$safe_hook] ?? 0) + count($events);
            }
        }
        ksort($counts);
        return $counts;
    }

    /** @return array<int,array<string,mixed>> */
    private function sanitize_count_rows($rows, array $text_fields): array
    {
        $clean = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            $item = [];
            foreach ($text_fields as $field) {
                $item[$field] = sanitize_text_field((string) ($row[$field] ?? ''));
            }
            $item['total'] = max(0, (int) ($row['total'] ?? 0));
            $clean[] = $item;
        }
        return $clean;
    }

    /** @return string[] */
    private function warnings(array $runs, array $relations): array
    {
        $warnings = [];
        if (get_option('m360_sr_shadow_mode', '0') === '1') {
            $warnings[] = 'A flag legada shadow_mode nao impede geracao ou renderizacao no fonte 0.9.0.';
        }
        if (get_option('m360_sr_sync_generation', '1') === '1') {
            $warnings[] = 'Geracao sincrona legada esta ativa.';
        }
        if (get_option('m360_sr_auto_heal_on_view', '1') === '1') {
            $warnings[] = 'Auto-heal legado pode gerar escrita na primeira visualizacao publica.';
        }
        if (!$runs['transactional'] || !$relations['transactional']) {
            $warnings[] = 'Uma ou mais tabelas legadas nao usam InnoDB.';
        }
        return $warnings;
    }
}
