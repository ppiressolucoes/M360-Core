<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Header_Orchestrator
{
    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'admin_menu'], 25);
    }

    public static function register_shortcodes(): void
    {
        add_shortcode('m360_header_orchestrator', [self::class, 'shortcode']);
        add_shortcode('m360_header_delivery', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'slot' => 'header-top',
            'adsense_slot' => 'header-adsense',
            'fallback' => 'search',
            'search_variant' => 'hero',
            'class' => '',
        ], $atts, 'm360_header_orchestrator');

        $result = self::resolve($atts, true);
        if ($result['html'] === '') { return ''; }

        self::enqueue_assets();
        $classes = ['m360-header-orchestrator', 'm360-header-orchestrator--' . $result['mode']];
        $custom_class = sanitize_html_class((string) $atts['class']);
        if ($custom_class !== '') { $classes[] = $custom_class; }

        return '<div class="' . esc_attr(implode(' ', $classes)) . '" data-m360-header-delivery="' . esc_attr($result['mode']) . '"><!-- M360 HEADER DELIVERY: ' . esc_html($result['mode']) . ' -->' . $result['html'] . '</div>';
    }

    public static function admin_menu(): void
    {
        add_submenu_page(
            'm360-ads-manager',
            'Header Orchestration',
            'Header Delivery',
            'manage_options',
            'm360-ads-header-delivery',
            [self::class, 'render_admin']
        );
    }

    public static function render_admin(): void
    {
        if (!current_user_can('manage_options')) { wp_die(esc_html__('Acesso negado.', 'm360-core')); }
        $result = self::resolve([], false);
        $labels = [
            'campaign' => 'Campanha elegível',
            'adsense' => 'AdSense elegível',
            'search' => 'Busca M360',
            'collapsed' => 'Recolhido',
        ];
        $label = $labels[$result['mode']] ?? $result['mode'];

        echo '<div class="wrap m360-ads-admin m360-header-delivery-admin"><h1>M360 Header Delivery</h1>';
        echo '<p>Diagnóstico da prioridade aplicada ao espaço horizontal do cabeçalho.</p>';
        echo '<div class="m360-ads-admin__cards">';
        self::metric('Entrega atual', $label);
        self::metric('Slot comercial', 'header-top');
        self::metric('Fallback Google', 'header-adsense');
        self::metric('Fallback útil', 'Busca M360');
        echo '</div>';
        echo '<div class="notice notice-info inline"><p><strong>Ordem:</strong> campanha elegível → AdSense elegível → busca → recolher.</p></div>';
        echo '<h2>Instalação no Elementor</h2><p>Substitua o shortcode isolado do banner ou da busca pelo componente único:</p>';
        echo '<p><code>[m360_header_orchestrator]</code></p>';
        echo '<p>Para recolher o espaço quando não houver anúncio: <code>[m360_header_orchestrator fallback="collapse"]</code></p>';
        echo '</div>';
    }

    private static function resolve(array $atts = [], bool $include_search = true): array
    {
        $slot = sanitize_key((string) ($atts['slot'] ?? 'header-top')) ?: 'header-top';
        $adsense_slot = sanitize_key((string) ($atts['adsense_slot'] ?? 'header-adsense')) ?: 'header-adsense';
        $fallback = sanitize_key((string) ($atts['fallback'] ?? 'search'));

        $campaign = M360_Slot_Renderer::render($slot, [
            'class' => 'm360-header-orchestrator__ad',
            'source' => 'header-orchestrator',
            'show_placeholder' => false,
        ]);
        if (trim($campaign) !== '') { return ['mode' => 'campaign', 'html' => $campaign]; }

        $adsense = M360_Slot_Renderer::render($adsense_slot, [
            'class' => 'm360-header-orchestrator__ad',
            'source' => 'header-orchestrator',
            'provider' => 'adsense',
            'provider_strict' => true,
            'show_placeholder' => false,
        ]);
        if (trim($adsense) !== '') { return ['mode' => 'adsense', 'html' => $adsense]; }

        if ($fallback === 'search') {
            if (!$include_search) { return ['mode' => 'search', 'html' => '']; }
            $variant = sanitize_key((string) ($atts['search_variant'] ?? 'hero'));
            $search = M360_Search_Form_Component::shortcode(['variant' => $variant, 'show_intro' => 'false']);
            return ['mode' => 'search', 'html' => $search];
        }

        return ['mode' => 'collapsed', 'html' => ''];
    }

    private static function metric(string $label, string $value): void
    {
        echo '<div class="m360-ads-admin__card"><strong>' . esc_html($value) . '</strong><span>' . esc_html($label) . '</span></div>';
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-header-orchestrator', 'registered')) { wp_enqueue_style('m360-core-header-orchestrator'); }
    }
}
