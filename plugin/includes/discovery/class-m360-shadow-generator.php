<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Shadow_Generator
{
    public const ALGORITHM_VERSION = 'portable-v2-internal-terms';
    public const CRON_HOOK = 'm360_discovery_generate_shadow';

    private M360_WordPress_Catalog_Provider $provider;
    private M360_Discovery_Locale_Resolver $locale_resolver;

    public function __construct()
    {
        $this->provider = new M360_WordPress_Catalog_Provider();
        $this->locale_resolver = new M360_Discovery_Locale_Resolver();
    }

    public static function register(): void
    {
        add_action(self::CRON_HOOK, [self::class, 'run_scheduled'], 10, 2);
    }

    public static function run_scheduled(int $post_id, string $trigger = 'async'): void
    {
        (new self())->generate($post_id, $trigger);
    }

    public function enqueue(int $post_id, string $trigger = 'manual_async'): bool
    {
        $args = [$post_id, sanitize_key($trigger)];
        if (wp_next_scheduled(self::CRON_HOOK, $args)) { return true; }
        return wp_schedule_single_event(time() + 15, self::CRON_HOOK, $args);
    }

    public function generate(int $post_id, string $trigger = 'manual'): array
    {
        $started = microtime(true);
        $post = get_post($post_id);
        $settings = M360_Content_Discovery_Module::settings();
        if (($settings['mode'] ?? 'off') !== 'shadow') {
            return ['ok'=>false,'code'=>'shadow_disabled','message'=>'Geração bloqueada porque o modo shadow não está ativo.'];
        }
        if (!$post instanceof WP_Post || $post->post_status !== 'publish' || !in_array($post->post_type, (array) $settings['post_types'], true)) {
            return ['ok'=>false,'code'=>'invalid_post','message'=>'Post inexistente, não publicado ou fora dos tipos configurados.'];
        }
        $locale = $this->locale_resolver->resolve($post_id);
        if ($locale === '' || !in_array($locale, (array) $settings['supported_locales'], true)) {
            return ['ok'=>false,'code'=>'unsupported_locale','message'=>'Locale ausente ou não suportado; geração encerrada sem fallback cruzado.'];
        }
        $hash = $this->source_hash($post, $locale, (array) $settings['taxonomies']);
        if (hash_equals($hash, M360_Discovery_DB::active_source_hash($post_id, $locale, self::ALGORITHM_VERSION))) {
            return ['ok'=>true,'code'=>'unchanged','message'=>'Snapshot ativo já corresponde à origem atual; nenhuma escrita necessária.','locale'=>$locale];
        }
        $run_id = M360_Discovery_DB::create_run($post_id, $locale, $trigger, $hash, $this->provider->id(), self::ALGORITHM_VERSION);
        if ($run_id < 1) { return ['ok'=>false,'code'=>'run_insert_failed','message'=>'Não foi possível criar o run shadow.']; }
        try {
            $relations = $this->relations($post_id, $locale, (array) $settings['taxonomies']);
            $count = 0; $ranks = [];
            foreach ($relations as $relation) {
                $kind = sanitize_key((string) $relation['relation_kind']);
                $ranks[$kind] = ($ranks[$kind] ?? 0) + 1;
                if (!M360_Discovery_DB::insert_relation($run_id, $post_id, $locale, $kind, $relation, $ranks[$kind])) {
                    throw new RuntimeException('relation_insert_failed');
                }
                $count++;
            }
            $duration = max(0, (int) round((microtime(true) - $started) * 1000));
            if ($count < 1) {
                M360_Discovery_DB::fail($run_id, 'no_candidates', 'Nenhuma relação portátil encontrada; snapshot anterior preservado.', $duration);
                return ['ok'=>false,'code'=>'no_candidates','message'=>'Nenhuma relação encontrada; snapshot anterior preservado.','run_id'=>$run_id,'locale'=>$locale];
            }
            if (!M360_Discovery_DB::promote($run_id, $post_id, $locale, $count, $duration)) {
                return ['ok'=>false,'code'=>'promotion_failed','message'=>'Falha na promoção atômica; snapshot anterior preservado.','run_id'=>$run_id,'locale'=>$locale];
            }
            return ['ok'=>true,'code'=>'success','message'=>'Snapshot shadow gerado e promovido no storage próprio.','run_id'=>$run_id,'locale'=>$locale,'active_count'=>$count,'duration_ms'=>$duration];
        } catch (Throwable $error) {
            $duration = max(0, (int) round((microtime(true) - $started) * 1000));
            M360_Discovery_DB::fail($run_id, 'generation_error', $error->getMessage(), $duration);
            return ['ok'=>false,'code'=>'generation_error','message'=>'Geração interrompida; snapshot anterior preservado.','run_id'=>$run_id,'locale'=>$locale];
        }
    }

    private function relations(int $post_id, string $locale, array $taxonomies): array
    {
        $relations = [];
        $terms = array_values(array_filter($taxonomies, 'taxonomy_exists'));
        $terms = $terms ? wp_get_post_terms($post_id, $terms) : [];
        if (!is_wp_error($terms)) {
            foreach (array_slice($terms, 0, 8) as $term) {
                if (!$term instanceof WP_Term) { continue; }
                $relations[] = ['relation_kind'=>'topic','target_type'=>'term','target_id'=>(int)$term->term_id,'score'=>1,'reason_codes'=>['assigned_term'],'score_breakdown'=>['assigned'=>1]];
            }
        }
        $candidates = $this->provider->candidates($post_id, $locale, ['post']);
        foreach (array_slice($candidates, 0, 6) as $candidate) {
            $candidate['relation_kind'] = 'related_post';
            $relations[] = $candidate;
        }
        foreach ($this->internal_link_terms($terms) as $term) {
            $relations[] = [
                'relation_kind' => 'internal_link',
                'target_type' => 'term',
                'target_id' => (int) $term->term_id,
                'score' => 1,
                'reason_codes' => ['assigned_term', 'portable_internal_term'],
                'score_breakdown' => ['assigned_term' => 1, 'contract_version' => 2],
            ];
        }
        return $relations;
    }

    /**
     * Internal links are portable taxonomy destinations. They intentionally do
     * not point to posts: a renderer can resolve a valid term archive later,
     * without inheriting legacy catalogue or contextual HTML behaviour.
     *
     * @param WP_Term[] $terms
     * @return WP_Term[]
     */
    private function internal_link_terms(array $terms): array
    {
        $targets = array_values(array_filter($terms, static function ($term): bool {
            return $term instanceof WP_Term && $term->term_id > 0 && taxonomy_exists($term->taxonomy);
        }));
        usort($targets, static function (WP_Term $a, WP_Term $b): int {
            $taxonomy = strcmp($a->taxonomy, $b->taxonomy);
            return $taxonomy !== 0 ? $taxonomy : ($a->term_id <=> $b->term_id);
        });
        return array_slice($targets, 0, 4);
    }

    private function source_hash(WP_Post $post, string $locale, array $taxonomies): string
    {
        $taxonomies = array_values(array_filter($taxonomies, 'taxonomy_exists'));
        $terms = $taxonomies ? wp_get_post_terms((int) $post->ID, $taxonomies, ['fields'=>'ids']) : [];
        $terms = is_wp_error($terms) ? [] : array_map('intval', $terms);
        sort($terms);
        return hash('sha256', implode('|', [$post->post_title,$post->post_excerpt,wp_strip_all_tags($post->post_content),implode(',',$terms),$locale,self::ALGORITHM_VERSION]));
    }
}
