<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Asynchronous ownership layer for Content Discovery.
 *
 * Publication hooks only enqueue work. Generation runs in WP-Cron or in the
 * explicit backfill and writes exclusively to the Core discovery tables.
 */
final class M360_Discovery_Scheduler
{
    public const PROCESS_HOOK = 'm360_discovery_process_post';
    public const BACKFILL_HOOK = 'm360_discovery_backfill_batch';

    private const QUEUE_OPTION = 'm360_discovery_writer_queue';
    private const BACKFILL_OPTION = 'm360_discovery_backfill_state';
    private const RETRY_LIMIT = 3;
    private const BACKFILL_BATCH = 10;

    public static function register(): void
    {
        add_action('transition_post_status', [self::class, 'on_transition'], 90, 3);
        add_action('save_post', [self::class, 'on_save'], 90, 3);
        add_action('set_object_terms', [self::class, 'on_terms'], 90, 6);
        add_action(self::PROCESS_HOOK, [self::class, 'process'], 10, 2);
        add_action(self::BACKFILL_HOOK, [self::class, 'backfill_batch']);
        if (did_action('init')) { self::ensure_backfill_scheduled(); }
        else { add_action('init', [self::class, 'ensure_backfill_scheduled'], 99); }
    }

    public static function ensure_backfill_scheduled(): void
    {
        $state = self::backfill_state();
        if (($state['status'] ?? '') === 'running' && self::writer_enabled() && !wp_next_scheduled(self::BACKFILL_HOOK)) {
            wp_schedule_single_event(time() + 20, self::BACKFILL_HOOK);
        }
    }

    public static function on_transition(string $new_status, string $old_status, WP_Post $post): void
    {
        if ($old_status === 'publish' && $new_status !== 'publish') {
            M360_Discovery_DB::retire_source((int) $post->ID);
            self::cancel((int) $post->ID);
            self::forget((int) $post->ID);
            return;
        }
        if ($new_status === 'publish') {
            self::schedule((int) $post->ID, $old_status === 'publish' ? 'post_updated' : 'post_published', 30);
        }
    }

    public static function on_save(int $post_id, WP_Post $post, bool $update): void
    {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id) || $post->post_status !== 'publish') { return; }
        self::schedule($post_id, $update ? 'save_updated' : 'save_published', 45);
    }

    public static function on_terms(int $object_id, $terms, $tt_ids, string $taxonomy, bool $append, $old_tt_ids): void
    {
        $settings = M360_Content_Discovery_Module::settings();
        if (!in_array($taxonomy, (array) $settings['taxonomies'], true)) { return; }
        $post = get_post($object_id);
        if ($post instanceof WP_Post && $post->post_status === 'publish') {
            self::schedule($object_id, 'terms_updated', 45);
        }
    }

    public static function schedule(int $post_id, string $trigger = 'publication', int $delay = 45, int $attempt = 0, bool $force = false): bool
    {
        if (!self::writer_enabled() || !self::eligible_post($post_id)) { return false; }
        $queue = self::queue();
        $key = (string) $post_id;
        $current = (array) ($queue[$key] ?? []);
        if (!$force && (string) ($current['status'] ?? '') === 'queued') {
            $current_attempt = max(0, (int) ($current['attempt'] ?? 0));
            if (wp_next_scheduled(self::PROCESS_HOOK, [$post_id, $current_attempt])) { return true; }
        }
        if (!$force && (string) ($current['status'] ?? '') === 'running' && (int) ($current['updated_at'] ?? 0) > time() - 900) {
            return true;
        }

        $trigger = substr(sanitize_key($trigger), 0, 40);
        $attempt = max(0, min(self::RETRY_LIMIT, $attempt));
        $args = [$post_id, $attempt];
        $scheduled = wp_next_scheduled(self::PROCESS_HOOK, $args);
        if (!$scheduled) { $scheduled = wp_schedule_single_event(time() + max(5, $delay), self::PROCESS_HOOK, $args); }
        if (!$scheduled) { return false; }

        $queue[$key] = [
            'post_id'=>$post_id, 'status'=>'queued', 'trigger'=>$trigger, 'attempt'=>$attempt,
            'scheduled_at'=>is_int($scheduled) ? $scheduled : time() + max(5, $delay),
            'updated_at'=>time(), 'last_code'=>'',
        ];
        self::save_queue($queue);
        return true;
    }

    public static function process(int $post_id, int $attempt = 0): void
    {
        if (!self::writer_enabled() || !self::eligible_post($post_id)) {
            self::mark($post_id, 'ignored', 'writer_disabled_or_invalid');
            return;
        }
        if (!self::lock($post_id)) {
            self::schedule($post_id, 'lock_retry', 60, $attempt, true);
            return;
        }

        $queue = self::queue();
        $trigger = sanitize_key((string) ($queue[(string) $post_id]['trigger'] ?? 'publication'));
        self::mark($post_id, 'running', '', $attempt);
        try {
            $result = (new M360_Shadow_Generator())->generate($post_id, 'writer_' . $trigger);
            $code = sanitize_key((string) ($result['code'] ?? 'generation_error'));
            if (!empty($result['ok'])) {
                self::mark($post_id, 'active', $code, $attempt);
                return;
            }
            if (self::retryable($code) && $attempt < self::RETRY_LIMIT) {
                $delays = [60, 300, 900];
                self::schedule($post_id, 'retry_' . $code, $delays[$attempt] ?? 900, $attempt + 1, true);
                return;
            }
            self::mark($post_id, 'failed', $code, $attempt);
        } finally {
            self::unlock($post_id);
        }
    }

    public static function start_backfill(): bool
    {
        if (!self::writer_enabled()) { return false; }
        $state = [
            'status'=>'running', 'cursor'=>0, 'processed'=>0, 'generated'=>0, 'unchanged'=>0, 'failed'=>0,
            'started_at'=>current_time('mysql'), 'updated_at'=>current_time('mysql'), 'finished_at'=>'',
        ];
        update_option(self::BACKFILL_OPTION, $state, false);
        if (!wp_next_scheduled(self::BACKFILL_HOOK)) {
            $scheduled = (bool) wp_schedule_single_event(time() + 10, self::BACKFILL_HOOK);
            if (!$scheduled) {
                $state['status'] = 'failed';
                $state['updated_at'] = current_time('mysql');
                update_option(self::BACKFILL_OPTION, $state, false);
            }
            return $scheduled;
        }
        return true;
    }

    public static function stop_backfill(): void
    {
        wp_clear_scheduled_hook(self::BACKFILL_HOOK);
        $state = self::backfill_state();
        $state['status'] = 'stopped';
        $state['updated_at'] = current_time('mysql');
        update_option(self::BACKFILL_OPTION, $state, false);
    }

    public static function backfill_batch(): void
    {
        $state = self::backfill_state();
        if (($state['status'] ?? '') !== 'running' || !self::writer_enabled()) { return; }
        $ids = self::next_backfill_ids(max(0, (int) ($state['cursor'] ?? 0)), self::BACKFILL_BATCH);
        if (!$ids) {
            $state['status'] = 'completed';
            $state['finished_at'] = current_time('mysql');
            $state['updated_at'] = current_time('mysql');
            update_option(self::BACKFILL_OPTION, $state, false);
            return;
        }

        $generator = new M360_Shadow_Generator();
        foreach ($ids as $post_id) {
            if (!self::lock($post_id)) {
                $result = ['ok'=>false, 'code'=>'lock_busy'];
            } else {
                try {
                    $result = $generator->generate($post_id, 'backfill');
                } finally {
                    self::unlock($post_id);
                }
            }
            $state['processed'] = max(0, (int) ($state['processed'] ?? 0)) + 1;
            if (!empty($result['ok']) && ($result['code'] ?? '') === 'unchanged') {
                $state['unchanged'] = max(0, (int) ($state['unchanged'] ?? 0)) + 1;
            } elseif (!empty($result['ok'])) {
                $state['generated'] = max(0, (int) ($state['generated'] ?? 0)) + 1;
            } else {
                $state['failed'] = max(0, (int) ($state['failed'] ?? 0)) + 1;
            }
            $state['cursor'] = $post_id;
        }
        $state['updated_at'] = current_time('mysql');
        update_option(self::BACKFILL_OPTION, $state, false);
        if (!wp_next_scheduled(self::BACKFILL_HOOK)) {
            wp_schedule_single_event(time() + 20, self::BACKFILL_HOOK);
        }
    }

    public static function summary(): array
    {
        $counts = ['queued'=>0, 'running'=>0, 'active'=>0, 'failed'=>0, 'ignored'=>0];
        foreach (self::queue() as $entry) {
            $status = sanitize_key((string) ($entry['status'] ?? ''));
            if (array_key_exists($status, $counts)) { $counts[$status]++; }
        }
        $settings = M360_Content_Discovery_Module::settings();
        return [
            'writer_mode'=>(string) ($settings['writer_mode'] ?? 'manual'),
            'queue'=>$counts,
            'backfill'=>self::backfill_state(),
            'coverage'=>M360_Discovery_DB::coverage_summary((array) $settings['post_types']),
        ];
    }

    private static function writer_enabled(): bool
    {
        $settings = M360_Content_Discovery_Module::settings();
        return ($settings['mode'] ?? 'off') === 'shadow' && ($settings['writer_mode'] ?? 'manual') === 'automatic';
    }

    private static function eligible_post(int $post_id): bool
    {
        $post = get_post($post_id);
        $settings = M360_Content_Discovery_Module::settings();
        return $post instanceof WP_Post && $post->post_status === 'publish'
            && in_array($post->post_type, (array) $settings['post_types'], true);
    }

    private static function retryable(string $code): bool
    {
        return in_array($code, ['unsupported_locale', 'run_insert_failed', 'promotion_failed', 'generation_error'], true);
    }

    private static function lock(int $post_id): bool
    {
        $key = 'm360_discovery_lock_' . $post_id;
        if (add_option($key, time(), '', false)) { return true; }
        $created_at = (int) get_option($key, 0);
        if ($created_at > 0 && $created_at < time() - 900) {
            delete_option($key);
            return add_option($key, time(), '', false);
        }
        return false;
    }

    private static function unlock(int $post_id): void { delete_option('m360_discovery_lock_' . $post_id); }

    private static function queue(): array
    {
        $queue = get_option(self::QUEUE_OPTION, []);
        return is_array($queue) ? $queue : [];
    }

    private static function save_queue(array $queue): void
    {
        uasort($queue, static fn(array $a, array $b): int => ((int) ($b['updated_at'] ?? 0)) <=> ((int) ($a['updated_at'] ?? 0)));
        update_option(self::QUEUE_OPTION, array_slice($queue, 0, 200, true), false);
    }

    private static function mark(int $post_id, string $status, string $code = '', int $attempt = 0): void
    {
        $queue = self::queue();
        $key = (string) $post_id;
        $queue[$key] = array_merge((array) ($queue[$key] ?? []), [
            'post_id'=>$post_id, 'status'=>sanitize_key($status), 'attempt'=>max(0, $attempt),
            'last_code'=>sanitize_key($code), 'updated_at'=>time(),
        ]);
        self::save_queue($queue);
    }

    private static function forget(int $post_id): void
    {
        $queue = self::queue();
        unset($queue[(string) $post_id]);
        self::save_queue($queue);
    }

    private static function cancel(int $post_id): void
    {
        for ($attempt = 0; $attempt <= self::RETRY_LIMIT; $attempt++) {
            wp_clear_scheduled_hook(self::PROCESS_HOOK, [$post_id, $attempt]);
        }
    }

    private static function next_backfill_ids(int $cursor, int $limit): array
    {
        global $wpdb;
        $settings = M360_Content_Discovery_Module::settings();
        $types = array_values(array_filter(array_map('sanitize_key', (array) $settings['post_types'])));
        if (!$types) { return []; }
        $placeholders = implode(',', array_fill(0, count($types), '%s'));
        $sql = "SELECT ID FROM {$wpdb->posts} WHERE post_status='publish' AND post_type IN ({$placeholders}) AND ID>%d ORDER BY ID ASC LIMIT %d";
        $ids = $wpdb->get_col($wpdb->prepare($sql, array_merge($types, [$cursor, max(1, min(50, $limit))])));
        return array_values(array_filter(array_map('intval', is_array($ids) ? $ids : [])));
    }

    public static function backfill_state(): array
    {
        $state = get_option(self::BACKFILL_OPTION, []);
        return is_array($state) ? array_merge([
            'status'=>'idle', 'cursor'=>0, 'processed'=>0, 'generated'=>0,
            'unchanged'=>0, 'failed'=>0, 'started_at'=>'', 'updated_at'=>'', 'finished_at'=>'',
        ], $state) : [];
    }
}
