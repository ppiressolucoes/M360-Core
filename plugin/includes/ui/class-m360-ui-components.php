<?php
if (!defined('ABSPATH')) { exit; }

final class M360_UI_Components
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_ui_chip', [self::class, 'chip_shortcode']);
        add_shortcode('m360_ui_badge', [self::class, 'badge_shortcode']);
        add_shortcode('m360_ui_card', [self::class, 'card_shortcode']);
        add_shortcode('m360_ui_container', [self::class, 'container_shortcode']);
    }

    public static function container_shortcode(array $atts = [], ?string $content = null): string
    {
        self::enqueue_assets();
        $atts = shortcode_atts(['size' => 'default', 'class' => ''], $atts, 'm360_ui_container');
        $size = sanitize_html_class((string) $atts['size']);
        $extra = sanitize_html_class((string) $atts['class']);
        $classes = trim('m360-ui m360-ui-container m360-ui-container--' . $size . ' ' . $extra);
        return '<div class="' . esc_attr($classes) . '">' . do_shortcode((string) $content) . '</div>';
    }

    public static function chip_shortcode(array $atts = []): string
    {
        self::enqueue_assets();
        $atts = shortcode_atts([
            'label' => '',
            'url' => '',
            'active' => 'false',
            'variant' => 'default',
        ], $atts, 'm360_ui_chip');

        $label = trim((string) $atts['label']);
        if ($label === '') { return ''; }
        $variant = sanitize_html_class((string) $atts['variant']);
        $is_active = filter_var($atts['active'], FILTER_VALIDATE_BOOLEAN);
        $classes = 'm360-ui m360-ui-chip m360-ui-chip--' . $variant . ($is_active ? ' is-active' : '');
        $url = trim((string) $atts['url']);

        if ($url !== '') {
            return '<a class="' . esc_attr($classes) . '" href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
        }

        return '<span class="' . esc_attr($classes) . '">' . esc_html($label) . '</span>';
    }

    public static function badge_shortcode(array $atts = []): string
    {
        self::enqueue_assets();
        $atts = shortcode_atts([
            'label' => '',
            'value' => '',
            'variant' => 'soft',
        ], $atts, 'm360_ui_badge');

        $label = trim((string) $atts['label']);
        $value = trim((string) $atts['value']);
        if ($label === '' && $value === '') { return ''; }
        $variant = sanitize_html_class((string) $atts['variant']);
        $html = '<span class="m360-ui m360-ui-badge m360-ui-badge--' . esc_attr($variant) . '">';
        if ($value !== '') { $html .= '<strong>' . esc_html($value) . '</strong>'; }
        if ($label !== '') { $html .= '<span>' . esc_html($label) . '</span>'; }
        $html .= '</span>';
        return $html;
    }

    public static function card_shortcode(array $atts = [], ?string $content = null): string
    {
        self::enqueue_assets();
        $atts = shortcode_atts([
            'title' => '',
            'eyebrow' => '',
            'url' => '',
            'image' => '',
            'variant' => 'article',
        ], $atts, 'm360_ui_card');

        $variant = sanitize_html_class((string) $atts['variant']);
        $title = trim((string) $atts['title']);
        $url = trim((string) $atts['url']);
        $image = trim((string) $atts['image']);
        $eyebrow = trim((string) $atts['eyebrow']);

        $html = '<article class="m360-ui m360-ui-card m360-ui-card--' . esc_attr($variant) . '">';
        if ($image !== '') {
            $html .= '<div class="m360-ui-card__media">';
            if ($url !== '') { $html .= '<a href="' . esc_url($url) . '">'; }
            $html .= '<img src="' . esc_url($image) . '" alt="' . esc_attr($title) . '" loading="lazy">';
            if ($url !== '') { $html .= '</a>'; }
            $html .= '</div>';
        }
        $html .= '<div class="m360-ui-card__body">';
        if ($eyebrow !== '') { $html .= '<span class="m360-ui-card__eyebrow">' . esc_html($eyebrow) . '</span>'; }
        if ($title !== '') {
            $html .= '<h3 class="m360-ui-card__title">';
            $html .= $url !== '' ? '<a href="' . esc_url($url) . '">' . esc_html($title) . '</a>' : esc_html($title);
            $html .= '</h3>';
        }
        $body = trim((string) $content);
        if ($body !== '') { $html .= '<div class="m360-ui-card__content">' . do_shortcode($body) . '</div>'; }
        $html .= '</div></article>';
        return $html;
    }

    public static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-ui-components', 'registered')) { wp_enqueue_style('m360-core-ui-components'); }
    }
}
