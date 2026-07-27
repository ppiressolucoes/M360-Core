<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Discovery_Locale_Resolver
{
    public function resolve(int $post_id): string
    {
        $locale = '';
        if (function_exists('pll_get_post_language')) {
            $locale = (string) pll_get_post_language($post_id, 'locale');
            if ($locale === '') { $locale = (string) pll_get_post_language($post_id, 'slug'); }
        }
        if ($locale === '') {
            $terms = wp_get_post_terms($post_id, 'language', ['fields' => 'slugs']);
            if (!is_wp_error($terms) && !empty($terms[0])) { $locale = (string) $terms[0]; }
        }
        if ($locale === '' && !function_exists('pll_get_post_language')) {
            $profile = M360_Site_Profile::get();
            $locale = (string) ($profile['default_locale'] ?? get_locale());
        }
        return $this->normalize_supported($locale);
    }

    public function normalize_supported(string $locale): string
    {
        $locale = str_replace('_', '-', trim($locale));
        $profile = M360_Site_Profile::get();
        foreach ((array) ($profile['supported_locales'] ?? []) as $candidate) {
            $candidate = str_replace('_', '-', (string) $candidate);
            if (strcasecmp($candidate, $locale) === 0 || strcasecmp(strtok($candidate, '-'), $locale) === 0) { return $candidate; }
        }
        return '';
    }
}
