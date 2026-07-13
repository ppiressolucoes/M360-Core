<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ads_Archive_Engine
{
    private const DEFAULT_POSITIONS = [
        'search' => 3,
        'category' => 3,
        'tag' => 3,
        'author' => 3,
        'latest-news' => 4,
        'archive' => 3,
    ];

    private const CONTEXT_SLOTS = [
        'search' => 'search-inline',
        'category' => 'category-inline',
        'tag' => 'tag-inline',
        'author' => 'author-inline',
        'latest-news' => 'latest-inline',
        'archive' => 'archive-inline',
    ];

    public static function position(string $context): int
    {
        $context = sanitize_key($context);
        $default = self::DEFAULT_POSITIONS[$context] ?? 3;
        return max(1, absint(apply_filters('m360_ads_archive_position', $default, $context)));
    }

    public static function slot(string $context): string
    {
        $context = sanitize_key($context);
        $default = self::CONTEXT_SLOTS[$context] ?? '';
        return sanitize_key((string) apply_filters('m360_ads_archive_slot', $default, $context));
    }

    public static function after_item(string $context, int $index, array $args = []): string
    {
        $context = sanitize_key($context);
        if (!self::is_enabled($context)) { return ''; }
        if ($index !== self::position($context)) { return ''; }

        $slot = self::slot($context);
        if ($slot === '') { return ''; }

        $class = sanitize_html_class((string) ($args['class'] ?? ''));
        $rendered = M360_Slot_Renderer::render($slot, [
            'class' => trim('m360-archive-ad__slot ' . $class),
            'context' => $context,
            'source' => 'archive-engine',
        ]);
        if (trim($rendered) === '') { return ''; }

        return '<div class="m360-archive-ad m360-archive-ad--' . esc_attr($context) . '" data-m360-archive-context="' . esc_attr($context) . '" data-m360-archive-position="' . esc_attr((string) $index) . '">' . $rendered . '</div>';
    }

    public static function empty_state(string $context): string
    {
        $context = sanitize_key($context);
        if ($context !== 'search' || !self::is_enabled('search')) { return ''; }
        return M360_Slot_Renderer::render('search-empty', [
            'class' => 'm360-archive-ad__slot m360-archive-ad__slot--empty',
            'context' => 'search',
            'source' => 'archive-engine',
        ]);
    }

    public static function render(string $context, array $args = []): string
    {
        $context = sanitize_key($context);
        $index = absint($args['index'] ?? self::position($context));
        return self::after_item($context, $index, $args);
    }

    private static function is_enabled(string $context): bool
    {
        if (is_admin() || is_feed() || wp_doing_ajax()) { return false; }
        if (defined('REST_REQUEST') && REST_REQUEST) { return false; }
        return (bool) apply_filters('m360_ads_archive_enabled', true, $context);
    }
}

if (!function_exists('m360_ads_render_archive')) {
    function m360_ads_render_archive(string $context, array $args = []): string
    {
        return M360_Ads_Archive_Engine::render($context, $args);
    }
}

if (!function_exists('m360_ads_archive_position')) {
    function m360_ads_archive_position(string $context): int
    {
        return M360_Ads_Archive_Engine::position($context);
    }
}
