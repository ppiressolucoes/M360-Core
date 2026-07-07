<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Navigation_Shortcodes
{
    public static function register(): void
    {
        add_shortcode('m360_main_navigation', [self::class, 'main_navigation']);
        add_shortcode('m360_breadcrumb', [self::class, 'breadcrumb']);
        add_shortcode('m360_section_navigation', [self::class, 'section_navigation']);
    }

    public static function main_navigation(array $atts = []): string
    {
        self::enqueue_assets();
        $atts = shortcode_atts(['menu_pt'=>'','menu_en'=>'','menu'=>''], $atts, 'm360_main_navigation');
        $menu = self::resolve_main_menu($atts, self::is_en());
        if (!$menu) { return ''; }

        $menu_id = 'm360-mobile-nav-' . wp_rand(1000, 999999);
        $html = wp_nav_menu(['menu'=>$menu->term_id,'container'=>'nav','container_class'=>'m360-main-navigation','menu_class'=>'m360-main-navigation__menu','echo'=>false,'fallback_cb'=>false,'depth'=>3]);
        if (!is_string($html) || $html === '') { return ''; }

        return '<div class="m360-navigation-shell">'
            . '<input class="m360-mobile-nav-toggle" id="' . esc_attr($menu_id) . '" type="checkbox" aria-hidden="true">'
            . '<label class="m360-mobile-nav-button" for="' . esc_attr($menu_id) . '" aria-label="Menu"><span></span><span></span><span></span></label>'
            . $html
            . '</div>';
    }

    public static function breadcrumb(array $atts = []): string
    {
        self::enqueue_assets();
        $items = [];
        $items[] = '<a href="' . esc_url(home_url('/')) . '">' . esc_html(self::is_en() ? 'Home' : 'Início') . '</a>';
        if (is_singular()) {
            $cats = get_the_category();
            if (!empty($cats)) { $items[] = '<a href="' . esc_url(get_category_link($cats[0])) . '">' . esc_html($cats[0]->name) . '</a>'; }
            $items[] = '<span aria-current="page">' . esc_html(get_the_title()) . '</span>';
        } elseif (is_search()) {
            $items[] = '<span aria-current="page">' . esc_html((self::is_en() ? 'Search: ' : 'Busca: ') . get_search_query()) . '</span>';
        } elseif (is_page()) {
            $items[] = '<span aria-current="page">' . esc_html(get_the_title()) . '</span>';
        }
        return '<nav class="m360-breadcrumb" aria-label="Breadcrumb"><ol><li>' . implode('</li><li>', $items) . '</li></ol></nav>';
    }

    public static function section_navigation(array $atts = []): string
    {
        self::enqueue_assets();
        $atts = shortcode_atts(['menu'=>'','menu_pt'=>'','menu_en'=>''], $atts, 'm360_section_navigation');

        $items = self::institutional_items();
        if (!empty($items)) {
            return '<nav class="m360-section-navigation m360-section-navigation--index"><ul><li>' . implode('</li><li>', $items) . '</li></ul></nav>';
        }

        $menu_name = self::section_menu_name($atts);
        if ($menu_name !== '') {
            $html = wp_nav_menu(['menu'=>$menu_name,'container'=>false,'menu_class'=>'m360-section-navigation__menu','echo'=>false,'fallback_cb'=>false,'depth'=>2]);
            if (is_string($html) && $html !== '') { return '<nav class="m360-section-navigation m360-section-navigation--index">' . $html . '</nav>'; }
        }

        if (is_singular()) {
            foreach (array_slice(get_the_category(), 0, 5) as $cat) { $items[] = '<a href="' . esc_url(get_category_link($cat)) . '">' . esc_html($cat->name) . '</a>'; }
        }
        return empty($items) ? '' : '<nav class="m360-section-navigation m360-section-navigation--index"><ul><li>' . implode('</li><li>', $items) . '</li></ul></nav>';
    }

    private static function institutional_items(): array
    {
        if (!is_page()) { return []; }
        $current = trailingslashit(parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '');
        $map = self::is_en() ? [
            'About Us'=>'/en/about-us/',
            'Masthead'=>'/en/masthead/',
            'Advertising'=>'/en/advertising/',
            'Editorial Policy'=>'/en/editorial-policy/',
            'Privacy Policy'=>'/en/privacy-policy/',
            'Terms of Use'=>'/en/terms-of-use/',
            'Contact'=>'/en/contact/'
        ] : [
            'Quem Somos'=>'/quem-somos/',
            'Expediente'=>'/expediente/',
            'Publicidades'=>'/publicidades/',
            'Política Editorial'=>'/politica-editorial/',
            'Política de Privacidade'=>'/politica-de-privacidade/',
            'Termos de Uso'=>'/termos-de-uso/',
            'Contato'=>'/contato/'
        ];
        $items = [];
        foreach ($map as $label=>$path) {
            $active = trailingslashit($path) === $current;
            $items[] = '<a href="' . esc_url(home_url($path)) . '"' . ($active ? ' aria-current="page"' : '') . '>' . esc_html($label) . '</a>';
        }
        return $items;
    }

    private static function section_menu_name(array $atts): string
    {
        foreach ([$atts['menu'], self::is_en() ? $atts['menu_en'] : $atts['menu_pt'], self::is_en() ? 'Support EN' : 'Apoio | Suporte', self::is_en() ? 'Institutional EN' : 'Institucional'] as $candidate) {
            if ((string)$candidate !== '' && wp_get_nav_menu_object($candidate) instanceof WP_Term) { return (string)$candidate; }
        }
        return '';
    }

    private static function resolve_main_menu(array $atts, bool $is_en): ?WP_Term
    {
        $candidates = $is_en ? ['primary menu English','Primary Menu English','primary-menu-english','main-menu-en',$atts['menu_en']] : [$atts['menu_pt'],'main-menu-pt','Primary Menu','primary-menu'];
        if ((string)$atts['menu'] !== '') { array_unshift($candidates, $atts['menu']); }
        foreach (array_unique(array_filter($candidates)) as $candidate) { $menu = wp_get_nav_menu_object($candidate); if ($menu instanceof WP_Term) { return $menu; } }
        return null;
    }

    private static function enqueue_assets(): void { if (wp_style_is('m360-core-foundation','registered')) { wp_enqueue_style('m360-core-foundation'); } }
    private static function is_en(): bool { return str_starts_with((string)get_locale(), 'en'); }
}
