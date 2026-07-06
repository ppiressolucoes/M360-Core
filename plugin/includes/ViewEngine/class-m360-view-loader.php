<?php
/**
 * Template loader for M360 Core views.
 */

if (!defined('ABSPATH')) {
    exit;
}

final class M360_View_Loader
{
    public function resolve(string $template, string $language = ''): ?string
    {
        $template = sanitize_file_name($template);
        $language = $language !== '' ? sanitize_file_name($language) : $this->detect_language();

        $candidates = [
            M360_CORE_PATH . 'views/' . $language . '/' . $template . '.php',
            M360_CORE_PATH . 'views/default/' . $template . '.php',
            M360_CORE_PATH . 'views/' . $template . '.php',
        ];

        foreach ($candidates as $candidate) {
            if (is_readable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    public function detect_language(): string
    {
        $locale = get_locale();

        return $locale !== '' ? str_replace('-', '_', $locale) : 'pt_BR';
    }
}
