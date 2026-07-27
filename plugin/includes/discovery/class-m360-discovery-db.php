<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Discovery_DB implements M360_Relation_Repository_Interface
{
    public const SCHEMA_VERSION = '1';
    private const SCHEMA_OPTION = 'm360_discovery_db_version';

    public static function runs_table(): string { global $wpdb; return $wpdb->prefix . 'm360_discovery_runs'; }
    public static function relations_table(): string { global $wpdb; return $wpdb->prefix . 'm360_discovery_relations'; }

    public static function activate(): void
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset = $wpdb->get_charset_collate();
        $runs = self::runs_table();
        $relations = self::relations_table();
        dbDelta("CREATE TABLE {$runs} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            source_post_id bigint(20) unsigned NOT NULL,
            locale varchar(12) NOT NULL,
            trigger_source varchar(40) NOT NULL DEFAULT 'manual',
            source_hash char(64) NOT NULL,
            provider_id varchar(40) NOT NULL,
            algorithm_version varchar(32) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'running',
            candidate_count int(10) unsigned NOT NULL DEFAULT 0,
            active_count int(10) unsigned NOT NULL DEFAULT 0,
            duration_ms int(10) unsigned NULL,
            previous_run_id bigint(20) unsigned NULL,
            error_code varchar(80) NULL,
            error_message text NULL,
            metadata longtext NULL,
            started_at datetime NOT NULL,
            finished_at datetime NULL,
            promoted_at datetime NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY source_locale_status (source_post_id,locale,status),
            KEY hash_algorithm (source_hash,algorithm_version),
            KEY promoted (source_post_id,locale,promoted_at)
        ) ENGINE=InnoDB {$charset};");
        dbDelta("CREATE TABLE {$relations} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            run_id bigint(20) unsigned NOT NULL,
            source_post_id bigint(20) unsigned NOT NULL,
            locale varchar(12) NOT NULL,
            relation_kind varchar(24) NOT NULL,
            target_type varchar(20) NOT NULL,
            target_id bigint(20) unsigned NOT NULL,
            score decimal(8,5) NOT NULL DEFAULT 0.00000,
            score_breakdown longtext NULL,
            reason_codes longtext NULL,
            rank smallint(5) unsigned NOT NULL DEFAULT 0,
            status varchar(20) NOT NULL DEFAULT 'candidate',
            created_at datetime NOT NULL,
            activated_at datetime NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY run_kind_rank (run_id,relation_kind,rank),
            KEY active_lookup (source_post_id,locale,relation_kind,status,rank),
            KEY reverse_lookup (target_type,target_id,status),
            KEY run_id (run_id)
        ) ENGINE=InnoDB {$charset};");
        update_option(self::SCHEMA_OPTION, self::SCHEMA_VERSION, false);
    }

    public static function schema_health(): array
    {
        global $wpdb;
        $tables = [];
        foreach (['runs' => self::runs_table(), 'relations' => self::relations_table()] as $key => $table) {
            $exists = (string) $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table))) === $table;
            $status = $exists ? $wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS LIKE %s', $wpdb->esc_like($table)), ARRAY_A) : [];
            $tables[$key] = ['exists' => $exists, 'engine' => sanitize_text_field((string) ($status['Engine'] ?? '')), 'rows' => max(0, (int) ($status['Rows'] ?? 0))];
        }
        $schema_version = (string) get_option(self::SCHEMA_OPTION, '');
        $healthy = $schema_version === self::SCHEMA_VERSION && $tables['runs']['exists'] && $tables['relations']['exists'] && strtolower($tables['runs']['engine']) === 'innodb' && strtolower($tables['relations']['engine']) === 'innodb';
        return ['healthy' => $healthy, 'schema_version' => $schema_version, 'tables' => $tables];
    }

    public static function create_run(int $post_id, string $locale, string $trigger, string $hash, string $provider, string $algorithm): int
    {
        global $wpdb;
        $previous = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM " . self::runs_table() . " WHERE source_post_id=%d AND locale=%s AND status='active' ORDER BY id DESC LIMIT 1", $post_id, $locale));
        $now = current_time('mysql');
        $ok = $wpdb->insert(self::runs_table(), [
            'source_post_id' => $post_id, 'locale' => $locale, 'trigger_source' => substr(sanitize_key($trigger), 0, 40),
            'source_hash' => $hash, 'provider_id' => sanitize_key($provider), 'algorithm_version' => sanitize_text_field($algorithm),
            'status' => 'running', 'previous_run_id' => $previous ?: null, 'started_at' => $now, 'created_at' => $now,
        ], ['%d','%s','%s','%s','%s','%s','%s','%d','%s','%s']);
        return $ok ? (int) $wpdb->insert_id : 0;
    }

    public static function insert_relation(int $run_id, int $post_id, string $locale, string $kind, array $relation, int $rank): bool
    {
        global $wpdb;
        return $wpdb->insert(self::relations_table(), [
            'run_id' => $run_id, 'source_post_id' => $post_id, 'locale' => $locale, 'relation_kind' => sanitize_key($kind),
            'target_type' => sanitize_key((string) ($relation['target_type'] ?? '')), 'target_id' => max(0, (int) ($relation['target_id'] ?? 0)),
            'score' => max(0, min(1, (float) ($relation['score'] ?? 0))),
            'score_breakdown' => wp_json_encode((array) ($relation['score_breakdown'] ?? [])),
            'reason_codes' => wp_json_encode(array_values((array) ($relation['reason_codes'] ?? []))),
            'rank' => $rank, 'status' => 'candidate', 'created_at' => current_time('mysql'),
        ], ['%d','%d','%s','%s','%s','%d','%f','%s','%s','%d','%s','%s']) !== false;
    }

    public static function promote(int $run_id, int $post_id, string $locale, int $count, int $duration_ms): bool
    {
        global $wpdb;
        if ($count < 1) { return false; }
        $runs = self::runs_table(); $relations = self::relations_table(); $now = current_time('mysql');
        $wpdb->query('START TRANSACTION');
        try {
            $wpdb->query($wpdb->prepare("UPDATE {$relations} SET status='superseded' WHERE source_post_id=%d AND locale=%s AND status='active'", $post_id, $locale));
            $wpdb->query($wpdb->prepare("UPDATE {$runs} SET status='superseded' WHERE source_post_id=%d AND locale=%s AND status='active'", $post_id, $locale));
            $activated = $wpdb->query($wpdb->prepare("UPDATE {$relations} SET status='active', activated_at=%s WHERE run_id=%d AND status='candidate'", $now, $run_id));
            if ((int) $activated !== $count) { throw new RuntimeException('relation_activation_count_mismatch'); }
            $updated = $wpdb->update($runs, ['status'=>'active','candidate_count'=>$count,'active_count'=>$count,'duration_ms'=>$duration_ms,'finished_at'=>$now,'promoted_at'=>$now], ['id'=>$run_id], ['%s','%d','%d','%d','%s','%s'], ['%d']);
            if ($updated === false) { throw new RuntimeException('run_promotion_failed'); }
            $wpdb->query('COMMIT');
            return true;
        } catch (Throwable $error) {
            $wpdb->query('ROLLBACK');
            self::fail($run_id, 'promotion_failed', $error->getMessage(), $duration_ms);
            return false;
        }
    }

    public static function fail(int $run_id, string $code, string $message, int $duration_ms = 0): void
    {
        global $wpdb;
        $wpdb->update(self::runs_table(), ['status'=>'failed','error_code'=>sanitize_key($code),'error_message'=>sanitize_text_field($message),'duration_ms'=>max(0,$duration_ms),'finished_at'=>current_time('mysql')], ['id'=>$run_id], ['%s','%s','%s','%d','%s'], ['%d']);
    }

    public static function active_source_hash(int $post_id, string $locale, string $algorithm): string
    {
        global $wpdb;
        return (string) $wpdb->get_var($wpdb->prepare(
            "SELECT source_hash FROM " . self::runs_table() . " WHERE source_post_id=%d AND locale=%s AND algorithm_version=%s AND status='active' ORDER BY id DESC LIMIT 1",
            $post_id,
            $locale,
            $algorithm
        ));
    }

    public static function retire_source(int $post_id): void
    {
        global $wpdb;
        $wpdb->update(self::relations_table(), ['status'=>'superseded'], ['source_post_id'=>$post_id,'status'=>'active'], ['%s'], ['%d','%s']);
        $wpdb->update(self::runs_table(), ['status'=>'superseded','finished_at'=>current_time('mysql')], ['source_post_id'=>$post_id,'status'=>'active'], ['%s','%s'], ['%d','%s']);
    }

    public static function coverage_summary(array $post_types): array
    {
        global $wpdb;
        $types = array_values(array_filter(array_map('sanitize_key', $post_types)));
        $health = self::schema_health();
        if (!$types || empty($health['tables']['runs']['exists'])) {
            return ['published'=>0,'covered'=>0,'missing'=>0,'ratio'=>0];
        }
        $placeholders = implode(',', array_fill(0, count($types), '%s'));
        $published = max(0, (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status='publish' AND post_type IN ({$placeholders})",
            $types
        )));
        $covered = max(0, (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT r.source_post_id) FROM " . self::runs_table() . " r INNER JOIN {$wpdb->posts} p ON p.ID=r.source_post_id WHERE r.status='active' AND p.post_status='publish' AND p.post_type IN ({$placeholders})",
            $types
        )));
        return [
            'published'=>$published,
            'covered'=>$covered,
            'missing'=>max(0, $published - $covered),
            'ratio'=>$published > 0 ? round($covered / $published, 4) : 0,
        ];
    }

    public function active(int $source_post_id, string $locale, string $kind, int $limit = 3): array
    {
        global $wpdb;
        $limit = max(1, min(20, $limit));
        $rows = $wpdb->get_results($wpdb->prepare("SELECT target_type,target_id,locale,relation_kind,score,rank,run_id,status FROM " . self::relations_table() . " WHERE source_post_id=%d AND locale=%s AND relation_kind=%s AND status='active' ORDER BY rank ASC LIMIT %d", $source_post_id, $locale, sanitize_key($kind), $limit), ARRAY_A);
        return is_array($rows) ? $rows : [];
    }

    /** @return array{duration_ms:int,status:string,failed_runs:int} */
    public static function run_metrics(int $source_post_id, string $locale): array
    {
        global $wpdb;
        $runs = self::runs_table();
        $row = $wpdb->get_row($wpdb->prepare("SELECT status,duration_ms FROM {$runs} WHERE source_post_id=%d AND locale=%s AND status='active' ORDER BY id DESC LIMIT 1", $source_post_id, $locale), ARRAY_A);
        $failed = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$runs} WHERE source_post_id=%d AND locale=%s AND status='failed'", $source_post_id, $locale));
        return ['duration_ms'=>max(0, (int) ($row['duration_ms'] ?? 0)), 'status'=>sanitize_key((string) ($row['status'] ?? '')), 'failed_runs'=>max(0, $failed)];
    }

    public static function summary(): array
    {
        global $wpdb;
        $health = self::schema_health();
        $runs = $health['tables']['runs']['exists'] ? $wpdb->get_results("SELECT locale,status,COUNT(*) total FROM " . self::runs_table() . " GROUP BY locale,status ORDER BY locale,status", ARRAY_A) : [];
        $relations = $health['tables']['relations']['exists'] ? $wpdb->get_results("SELECT locale,relation_kind,status,COUNT(*) total FROM " . self::relations_table() . " GROUP BY locale,relation_kind,status ORDER BY locale,relation_kind,status", ARRAY_A) : [];
        return ['storage' => $health, 'runs' => is_array($runs) ? $runs : [], 'relations' => is_array($relations) ? $relations : []];
    }
}
