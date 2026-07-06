<?php
/**
 * Renderer for M360 Core views.
 */

if (!defined('ABSPATH')) {
    exit;
}

final class M360_View_Renderer
{
    private M360_View_Loader $loader;

    public function __construct(M360_View_Loader $loader)
    {
        $this->loader = $loader;
    }

    public function render(string $template, array $context = [], string $language = ''): string
    {
        $file = $this->loader->resolve($template, $language);

        if ($file === null) {
            return $this->fallback($template);
        }

        ob_start();
        $m360_context = $context;
        require $file;
        return (string) ob_get_clean();
    }

    private function fallback(string $template): string
    {
        if (!current_user_can('manage_options')) {
            return '';
        }

        return '<div class="m360-core-placeholder">M360 View not found: <code>' . esc_html($template) . '</code></div>';
    }
}
