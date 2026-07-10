<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Universal public rendering facade for every M360 advertising surface.
 *
 * The renderer normalizes input, exposes stable extension hooks and delegates
 * the proven campaign/creative selection pipeline to M360_Ad_Slot_Component.
 */
final class M360_Slot_Renderer
{
    private const PROVIDERS = [
        'internal',
        'house',
        'adsense',
        'google-ad-manager',
        'affiliate',
        'sponsor',
    ];

    public static function render(string $slot_key, array $args = []): string
    {
        $slot_key = sanitize_key($slot_key);
        if ($slot_key === '') { return ''; }

        $args = self::normalize_args($args);
        $context = self::context($args);
        $provider = self::provider($args);

        $request = [
            'slot' => $slot_key,
            'context' => $context,
            'provider' => $provider,
            'language' => self::language(),
            'device' => wp_is_mobile() ? 'mobile' : 'desktop',
            'args' => $args,
        ];

        $request = apply_filters('m360_slot_before_render', $request, $slot_key, $args);
        if (!is_array($request)) { return ''; }

        $resolved_slot = sanitize_key((string) ($request['slot'] ?? $slot_key));
        if ($resolved_slot === '') { return ''; }

        $component_args = is_array($request['args'] ?? null) ? $request['args'] : $args;
        $component_args['context'] = sanitize_key((string) ($request['context'] ?? $context));
        $component_args['provider'] = self::normalize_provider((string) ($request['provider'] ?? $provider));

        $html = M360_Ad_Slot_Component::render($resolved_slot, $component_args);
        $html = apply_filters('m360_slot_after_render', $html, $request, $resolved_slot, $component_args);
        return is_string($html) ? $html : '';
    }

    public static function render_shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'id' => '',
            'slot' => '',
            'class' => '',
            'fallback' => '',
            'context' => '',
            'provider' => '',
        ], $atts, 'm360_ad_slot');

        $slot = (string) ($atts['slot'] !== '' ? $atts['slot'] : $atts['id']);
        return self::render($slot, [
            'class' => (string) $atts['class'],
            'fallback' => (string) $atts['fallback'],
            'context' => (string) $atts['context'],
            'provider' => (string) $atts['provider'],
            'source' => 'shortcode',
        ]);
    }

    public static function register_shortcodes(): void
    {
        add_shortcode('m360_ad_slot', [self::class, 'render_shortcode']);
        add_shortcode('m360_ads_slot', [self::class, 'render_shortcode']);
    }

    private static function normalize_args(array $args): array
    {
        $normalized = [
            'class' => sanitize_html_class((string) ($args['class'] ?? '')),
            'fallback' => (string) ($args['fallback'] ?? ''),
            'context' => sanitize_key((string) ($args['context'] ?? '')),
            'provider' => self::normalize_provider((string) ($args['provider'] ?? '')),
            'source' => sanitize_key((string) ($args['source'] ?? 'php')),
            'cache' => (bool) ($args['cache'] ?? false),
        ];

        $filtered = apply_filters('m360_slot_render_args', $normalized, $args);
        return is_array($filtered) ? $filtered : $normalized;
    }

    private static function context(array $args): string
    {
        $context = sanitize_key((string) ($args['context'] ?? ''));
        if ($context !== '') { return $context; }
        if (class_exists('M360_Ads_Context_Renderer')) { return M360_Ads_Context_Renderer::detect_context(); }
        return 'global';
    }

    private static function provider(array $args): string
    {
        $provider = self::normalize_provider((string) ($args['provider'] ?? 'internal'));
        $provider = apply_filters('m360_slot_provider', $provider, $args);
        return self::normalize_provider(is_string($provider) ? $provider : 'internal');
    }

    private static function normalize_provider(string $provider): string
    {
        $provider = sanitize_key($provider ?: 'internal');
        if ($provider === 'gam') { $provider = 'google-ad-manager'; }
        return in_array($provider, self::PROVIDERS, true) ? $provider : 'internal';
    }

    private static function language(): string
    {
        if (function_exists('pll_current_language')) {
            $lang = pll_current_language('slug');
            if ($lang === 'en') { return 'en-us'; }
            if ($lang === 'pt') { return 'pt-br'; }
        }
        return str_starts_with((string) get_locale(), 'en') ? 'en-us' : 'pt-br';
    }
}

if (!function_exists('m360_render_ad_slot')) {
    function m360_render_ad_slot(string $slot_key, array $args = []): string
    {
        return M360_Slot_Renderer::render($slot_key, $args);
    }
}

if (!function_exists('m360_ad_slot')) {
    function m360_ad_slot(string $slot_key, array $args = []): string
    {
        return M360_Slot_Renderer::render($slot_key, $args);
    }
}

if (!function_exists('m360_ads_render_slot')) {
    function m360_ads_render_slot(string $slot_key, array $args = []): string
    {
        return M360_Slot_Renderer::render($slot_key, $args);
    }
}
