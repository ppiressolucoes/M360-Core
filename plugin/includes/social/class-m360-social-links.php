<?php
if (!defined('ABSPATH')) { exit; }

/** Reusable social-network navigation for M360 portal headers, footers and sidebars. */
final class M360_Social_Links
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_social_links', [self::class, 'shortcode']);
        add_shortcode('m360_social_networks', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'facebook' => '',
            'instagram' => '',
            'youtube' => '',
            'whatsapp' => '',
            'x' => '',
            'tiktok' => '',
            'linkedin' => '',
            'variant' => 'boxed',
            'target' => '_blank',
            'primary' => '#d71920',
            'secondary' => '#b81218',
            'label' => '',
        ], $atts, 'm360_social_links');

        $configured = get_option('m360_social_links', []);
        $configured = is_array($configured) ? $configured : [];
        $links = [
            'facebook' => (string) ($atts['facebook'] ?: ($configured['facebook'] ?? '')),
            'instagram' => (string) ($atts['instagram'] ?: ($configured['instagram'] ?? '')),
            'youtube' => (string) ($atts['youtube'] ?: ($configured['youtube'] ?? '')),
            'whatsapp' => (string) ($atts['whatsapp'] ?: ($configured['whatsapp'] ?? '')),
            'x' => (string) ($atts['x'] ?: ($configured['x'] ?? '')),
            'tiktok' => (string) ($atts['tiktok'] ?: ($configured['tiktok'] ?? '')),
            'linkedin' => (string) ($atts['linkedin'] ?: ($configured['linkedin'] ?? '')),
        ];
        $links = (array) apply_filters('m360_social_links', $links, $atts);
        $links = array_filter($links, static fn($url): bool => is_string($url) && self::valid_url($url));
        if (!$links) { return ''; }

        self::enqueue_assets();
        $variant = in_array((string) $atts['variant'], ['boxed', 'plain', 'pill'], true) ? (string) $atts['variant'] : 'boxed';
        $target = (string) $atts['target'] === '_self' ? '_self' : '_blank';
        $primary = self::color((string) $atts['primary'], '#d71920');
        $secondary = self::color((string) $atts['secondary'], '#b81218');
        $label = sanitize_text_field((string) $atts['label']);
        $html = '<nav class="m360-social-links m360-social-links--' . esc_attr($variant) . '" style="--m360-social-primary:' . esc_attr($primary) . ';--m360-social-secondary:' . esc_attr($secondary) . '" aria-label="' . esc_attr($label ?: (self::is_en() ? 'Social networks' : 'Redes sociais')) . '">';
        if ($label !== '') { $html .= '<span class="m360-social-links__label">' . esc_html($label) . '</span>'; }
        foreach ($links as $network => $url) {
            $network = sanitize_key((string) $network);
            if (!isset(self::icons()[$network])) { continue; }
            $name = self::names()[$network] ?? ucfirst($network);
            $html .= '<a class="m360-social-links__link m360-social-links__link--' . esc_attr($network) . '" href="' . esc_url($url) . '" target="' . esc_attr($target) . '"' . ($target === '_blank' ? ' rel="noopener noreferrer"' : '') . ' aria-label="' . esc_attr($name) . '" title="' . esc_attr($name) . '">' . self::icons()[$network] . '<span class="screen-reader-text">' . esc_html($name) . '</span></a>';
        }
        return $html . '</nav>';
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-social-links', 'registered')) { wp_enqueue_style('m360-core-social-links'); }
    }

    private static function valid_url(string $url): bool
    {
        return (bool) wp_http_validate_url($url) || (bool) preg_match('/^sms:|^tel:/i', $url);
    }

    private static function color(string $value, string $fallback): string
    {
        $value = sanitize_hex_color($value);
        return $value ?: $fallback;
    }

    private static function is_en(): bool
    {
        $slug = function_exists('pll_current_language') ? (string) pll_current_language('slug') : (string) get_locale();
        return str_starts_with(strtolower($slug), 'en');
    }

    private static function names(): array
    {
        return ['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'whatsapp' => 'WhatsApp', 'x' => 'X', 'tiktok' => 'TikTok', 'linkedin' => 'LinkedIn'];
    }

    private static function icons(): array
    {
        return [
            'facebook' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3.3 0-5 1.9-5 5v3H6v4h3v4h4v-4h3l1-4h-4V9c0-.7.3-1 1-1Z"/></svg>',
            'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" class="m360-social-icon-dot"/></svg>',
            'youtube' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.6 7.2a2.8 2.8 0 0 0-2-2C17.8 4.7 12 4.7 12 4.7s-5.8 0-7.6.5a2.8 2.8 0 0 0-2 2A29 29 0 0 0 2 12a29 29 0 0 0 .4 4.8 2.8 2.8 0 0 0 2 2c1.8.5 7.6.5 7.6.5s5.8 0 7.6-.5a2.8 2.8 0 0 0 2-2A29 29 0 0 0 22 12a29 29 0 0 0-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z"/></svg>',
            'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4a9.8 9.8 0 0 0-15 12.4L4 20l3.8-1a9.8 9.8 0 0 0 12.2-15Zm-8 16a8 8 0 0 1-4-1.1l-.3-.2-2.3.6.6-2.2-.2-.3A8 8 0 1 1 12 20Zm4.4-5.9c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.3 0-.4.1-.5l.4-.5c.1-.1.1-.3.2-.4 0-.1 0-.3-.1-.4l-.8-1.9c-.1-.3-.3-.3-.5-.3h-.4c-.2 0-.4.1-.5.2a2 2 0 0 0-.7 1.5c0 .9.6 1.8.7 1.9.1.1 1.1 1.8 2.8 2.8 1.7 1 1.7.7 2 .7.3 0 1.1-.4 1.3-.8.2-.4.2-.7.1-.8Z"/></svg>',
            'x' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 3 6.2 8.3L4.4 21H7l4.4-7 5.2 7H21l-6.6-8.8L20 3h-2.6l-4 6.3L8.7 3H4Zm3.8 2h.8l8.5 14h-.8L7.8 5Z"/></svg>',
            'tiktok' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 3h3c.2 1.7 1.1 3 3 3v3c-1.1 0-2.1-.3-3-.8V14a6 6 0 1 1-6-6v3a3 3 0 1 0 3 3V3Z"/></svg>',
            'linkedin' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3.5A2.5 2.5 0 1 1 5 8a2.5 2.5 0 0 1 0-4.5ZM3 9h4v12H3V9Zm6 0h3.8v1.6h.1A4.2 4.2 0 0 1 17 8.5c4 0 4.7 2.6 4.7 6v6.5h-4v-5.8c0-1.4 0-3.2-2-3.2s-2.3 1.5-2.3 3.1V21H9V9Z"/></svg>',
        ];
    }
}
