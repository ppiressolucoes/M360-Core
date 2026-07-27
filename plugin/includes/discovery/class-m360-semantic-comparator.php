<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Semantic_Comparator
{
    private const KINDS = ['topic', 'internal_link', 'related_post'];
    private M360_Legacy_Semantic_Adapter $legacy;
    private M360_Discovery_DB $core;
    private M360_Discovery_Locale_Resolver $locales;

    public function __construct()
    {
        $this->legacy = M360_Content_Discovery_Module::adapter();
        $this->core = new M360_Discovery_DB();
        $this->locales = new M360_Discovery_Locale_Resolver();
    }

    /** @return array<string,mixed> */
    public function compare(int $post_id, string $locale = ''): array
    {
        $post_id = max(0, $post_id);
        $post = get_post($post_id);
        if (!$post instanceof WP_Post || $post->post_status !== 'publish') {
            return $this->blocked($post_id, '', 'Post inexistente ou não publicado.');
        }
        $locale = $locale !== '' ? $this->locales->normalize_supported($locale) : $this->locales->resolve($post_id);
        if ($locale === '') { return $this->blocked($post_id, '', 'Locale ausente ou fora do Site Profile.'); }
        if (!$this->legacy->precursor_active()) { return $this->blocked($post_id, $locale, 'Precursor Semantic Relations não está ativo.'); }
        if (!M360_Discovery_DB::schema_health()['healthy']) { return $this->blocked($post_id, $locale, 'Storage próprio do Core não está saudável.'); }

        $kinds = [];
        $legacy_total = 0; $core_total = 0; $shared_total = 0; $invalid_total = 0; $cross_locale_total = 0; $rank_differences = 0; $kind_without_overlap = 0;
        foreach (self::KINDS as $kind) {
            $legacy = $this->index($this->legacy->active($post_id, $locale, $kind, 20));
            $core = $this->index($this->core->active($post_id, $locale, $kind, 20));
            $legacy_keys = array_keys($legacy); $core_keys = array_keys($core);
            $shared = array_values(array_intersect($legacy_keys, $core_keys));
            $legacy_only = array_values(array_diff($legacy_keys, $core_keys));
            $core_only = array_values(array_diff($core_keys, $legacy_keys));
            if ($legacy && $core && !$shared) { $kind_without_overlap++; }
            $rank_delta = 0;
            foreach ($shared as $key) {
                if ((int) $legacy[$key]['rank'] !== (int) $core[$key]['rank']) { $rank_delta++; }
            }
            $validity = $this->validity(array_merge(array_values($legacy), array_values($core)), $locale);
            $legacy_total += count($legacy); $core_total += count($core); $shared_total += count($shared);
            $invalid_total += $validity['invalid']; $cross_locale_total += $validity['cross_locale']; $rank_differences += $rank_delta;
            $kinds[$kind] = [
                'legacy_count' => count($legacy), 'core_count' => count($core), 'shared_count' => count($shared),
                'legacy_only' => array_slice($legacy_only, 0, 10), 'core_only' => array_slice($core_only, 0, 10),
                'rank_differences' => $rank_delta, 'invalid_targets' => $validity['invalid'], 'cross_locale_targets' => $validity['cross_locale'],
            ];
        }
        $coverage = $legacy_total > 0 ? round($shared_total / $legacy_total, 4) : null;
        $legacy_metrics = $this->legacy->run_metrics($post_id, $locale);
        $core_metrics = M360_Discovery_DB::run_metrics($post_id, $locale);
        $status = 'eligible';
        $message = 'Comparação limpa para os bloqueios técnicos. Renderer canário continua exigindo autorização separada.';
        if ($core_total < 1) { $status = 'blocked'; $message = 'Nenhuma relação ativa do Core para este post e locale.'; }
        elseif ($invalid_total > 0 || $cross_locale_total > 0) { $status = 'blocked'; $message = 'Destinos inválidos ou locale cruzado detectados; promoção pública bloqueada.'; }
        elseif ($legacy_total < 1) { $status = 'review'; $message = 'Precursor sem relações ativas para comparação neste post e locale.'; }
        elseif ($kind_without_overlap > 0) { $status = 'review'; $message = 'Há tipo de relação sem destinos compartilhados; revisão de contrato necessária antes de qualquer canário.'; }
        elseif ($coverage !== null && $coverage < 0.5) { $status = 'review'; $message = 'Cobertura compartilhada abaixo de 50%; revisão editorial necessária antes de qualquer canário.'; }

        return [
            'status' => $status, 'message' => $message, 'post_id' => $post_id, 'locale' => $locale,
            'summary' => [
                'legacy_total' => $legacy_total, 'core_total' => $core_total, 'shared_total' => $shared_total,
                'coverage_ratio' => $coverage, 'rank_differences' => $rank_differences,
                'invalid_targets' => $invalid_total, 'cross_locale_targets' => $cross_locale_total, 'kind_without_overlap' => $kind_without_overlap,
                'legacy_duration_ms' => $legacy_metrics['duration_ms'], 'core_duration_ms' => $core_metrics['duration_ms'],
                'legacy_failed_runs' => $legacy_metrics['failed_runs'], 'core_failed_runs' => $core_metrics['failed_runs'],
            ],
            'kinds' => $kinds,
        ];
    }

    /** @return array<string,array<string,mixed>> */
    private function index(array $rows): array
    {
        $indexed = [];
        foreach ($rows as $row) {
            $type = $this->canonical_type((string) ($row['target_type'] ?? ''));
            $id = max(0, (int) ($row['target_id'] ?? 0));
            if ($type === '' || $id < 1) { continue; }
            $key = $type . ':' . $id;
            $indexed[$key] = ['target_type'=>$type, 'target_id'=>$id, 'rank'=>max(0, (int) ($row['rank'] ?? 0))];
        }
        return $indexed;
    }

    private function canonical_type(string $type): string
    {
        $type = sanitize_key($type);
        return in_array($type, ['category', 'post_tag'], true) ? 'term' : $type;
    }

    /** @return array{invalid:int,cross_locale:int} */
    private function validity(array $targets, string $locale): array
    {
        $invalid = 0; $cross_locale = 0; $checked = [];
        foreach ($targets as $target) {
            $key = (string) $target['target_type'] . ':' . (int) $target['target_id'];
            if (isset($checked[$key])) { continue; }
            $checked[$key] = true;
            if ($target['target_type'] === 'post') {
                $post = get_post((int) $target['target_id']);
                if (!$post instanceof WP_Post || $post->post_status !== 'publish') { $invalid++; continue; }
                $target_locale = $this->locales->resolve((int) $post->ID);
                if ($target_locale === '' || $target_locale !== $locale) { $cross_locale++; }
            } elseif ($target['target_type'] === 'term') {
                if (!term_exists((int) $target['target_id'])) { $invalid++; }
            } else {
                $invalid++;
            }
        }
        return ['invalid'=>$invalid, 'cross_locale'=>$cross_locale];
    }

    /** @return array<string,mixed> */
    private function blocked(int $post_id, string $locale, string $message): array
    {
        return ['status'=>'blocked','message'=>$message,'post_id'=>$post_id,'locale'=>$locale,'summary'=>['legacy_total'=>0,'core_total'=>0,'shared_total'=>0,'coverage_ratio'=>null,'rank_differences'=>0,'invalid_targets'=>0,'cross_locale_targets'=>0,'kind_without_overlap'=>0,'legacy_duration_ms'=>0,'core_duration_ms'=>0,'legacy_failed_runs'=>0,'core_failed_runs'=>0],'kinds'=>[]];
    }
}
