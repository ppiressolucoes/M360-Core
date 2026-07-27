<?php
if (!defined('ABSPATH')) { exit; }

final class M360_WordPress_Catalog_Provider implements M360_Catalog_Provider_Interface
{
    public function id(): string { return 'wordpress'; }

    public function candidates(int $source_post_id, string $locale, array $target_types = []): array
    {
        $source = get_post($source_post_id);
        if (!$source instanceof WP_Post || $source->post_status !== 'publish') { return []; }
        $settings = M360_Content_Discovery_Module::settings();
        $post_types = array_values(array_intersect((array) $settings['post_types'], get_post_types(['public' => true], 'names')));
        if (!$post_types || !in_array($source->post_type, $post_types, true)) { return []; }
        $taxonomies = array_values(array_filter((array) $settings['taxonomies'], 'taxonomy_exists'));
        $source_terms = $taxonomies ? wp_get_post_terms($source_post_id, $taxonomies, ['fields' => 'ids']) : [];
        $source_terms = is_wp_error($source_terms) ? [] : array_values(array_unique(array_map('intval', $source_terms)));
        $args = [
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => 80,
            'post__not_in' => [$source_post_id],
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        if (function_exists('pll_get_post_language')) {
            $language_slug = (string) pll_get_post_language($source_post_id, 'slug');
            if ($language_slug !== '') { $args['lang'] = $language_slug; }
        }
        $query = new WP_Query($args);
        $resolver = new M360_Discovery_Locale_Resolver();
        $source_tokens = $this->tokens($source->post_title . ' ' . $source->post_excerpt);
        $rows = [];
        foreach ($query->posts as $candidate) {
            if (!$candidate instanceof WP_Post || $resolver->resolve((int) $candidate->ID) !== $locale) { continue; }
            $terms = $taxonomies ? wp_get_post_terms((int) $candidate->ID, $taxonomies, ['fields' => 'ids']) : [];
            $terms = is_wp_error($terms) ? [] : array_values(array_unique(array_map('intval', $terms)));
            $shared_terms = array_values(array_intersect($source_terms, $terms));
            $candidate_tokens = $this->tokens($candidate->post_title . ' ' . $candidate->post_excerpt);
            $shared_tokens = array_values(array_intersect($source_tokens, $candidate_tokens));
            $score = min(1, (count($shared_terms) * 0.22) + (count($shared_tokens) * 0.035));
            if ($score <= 0) { continue; }
            $rows[] = [
                'target_type' => 'post',
                'target_id' => (int) $candidate->ID,
                'score' => round($score, 5),
                'reason_codes' => array_values(array_filter([$shared_terms ? 'shared_taxonomy' : '', $shared_tokens ? 'shared_title_tokens' : ''])),
                'score_breakdown' => ['shared_terms' => count($shared_terms), 'shared_tokens' => count($shared_tokens)],
            ];
        }
        usort($rows, static function (array $a, array $b): int {
            $score = $b['score'] <=> $a['score'];
            return $score !== 0 ? $score : ($a['target_id'] <=> $b['target_id']);
        });
        return array_slice($rows, 0, 20);
    }

    private function tokens(string $text): array
    {
        $text = remove_accents(strtolower(wp_strip_all_tags($text)));
        $tokens = preg_split('/[^a-z0-9]+/', $text) ?: [];
        $stop = ['para','com','uma','das','dos','que','the','and','for','with','from','this','that'];
        return array_values(array_unique(array_filter($tokens, static fn(string $token): bool => strlen($token) >= 4 && !in_array($token, $stop, true))));
    }
}
