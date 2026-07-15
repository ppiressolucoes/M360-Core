<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Navigation_Shortcodes
{
    private static bool $breadcrumb_schema_rendered = false;

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
        return '<div class="m360-navigation-shell m360-ui-nav">'
            . '<input class="m360-mobile-nav-toggle" id="' . esc_attr($menu_id) . '" type="checkbox" aria-hidden="true">'
            . '<label class="m360-mobile-nav-button" for="' . esc_attr($menu_id) . '" aria-label="Menu"><span></span><span></span><span></span></label>'
            . $html
            . '</div>';
    }

    public static function breadcrumb(array $atts = []): string
    {
        self::enqueue_assets();
        $atts = shortcode_atts([
            'schema' => 'true',
        ], $atts, 'm360_breadcrumb');

        $is_en = self::is_en();
        $labels = self::breadcrumb_labels($is_en);
        $items = [[
            'label' => $labels['home'],
            'url' => self::language_home_url(),
            'current' => is_front_page(),
            'home' => true,
        ]];

        if (is_author()) {
            $author = get_queried_object();
            $items[] = self::breadcrumb_item($labels['author'], '', false, false);
            if ($author instanceof WP_User) {
                $items[] = self::breadcrumb_item($author->display_name, get_author_posts_url($author->ID), true);
            }
        } elseif (is_category()) {
            $term = get_queried_object();
            $items[] = self::breadcrumb_item($labels['category'], '', false, false);
            if ($term instanceof WP_Term) {
                $items = array_merge($items, self::term_ancestor_items($term));
                $items[] = self::breadcrumb_item($term->name, self::term_url($term), true);
            }
        } elseif (is_tag()) {
            $term = get_queried_object();
            $items[] = self::breadcrumb_item($labels['tag'], '', false, false);
            if ($term instanceof WP_Term) {
                $items[] = self::breadcrumb_item($term->name, self::term_url($term), true);
            }
        } elseif (is_date()) {
            $year = (int) get_query_var('year');
            $month = (int) get_query_var('monthnum');
            $day = (int) get_query_var('day');
            $items[] = self::breadcrumb_item($labels['date_archive'], '', false, false);
            if ($month > 0 || $day > 0) {
                $items[] = self::breadcrumb_item((string) $year, get_year_link($year));
            }
            if ($day > 0 && $month > 0) {
                $items[] = self::breadcrumb_item(self::month_label($month, $is_en), get_month_link($year, $month));
            }
            $date_title = class_exists('M360_Date_Archive_Controller')
                ? M360_Date_Archive_Controller::archive_title($is_en)
                : get_the_archive_title();
            $date_url = $day > 0 && $month > 0
                ? get_day_link($year, $month, $day)
                : ($month > 0 ? get_month_link($year, $month) : get_year_link($year));
            $items[] = self::breadcrumb_item(wp_strip_all_tags((string) $date_title), $date_url, true);
        } elseif (is_search()) {
            $query = trim((string) get_search_query());
            $items[] = self::breadcrumb_item($labels['search'] . ($query !== '' ? ': ' . $query : ''), get_search_link($query), true);
        } elseif (is_404()) {
            $items[] = self::breadcrumb_item($labels['not_found'], '', true);
        } elseif (is_singular()) {
            $post_id = (int) get_queried_object_id();
            if (is_page()) {
                $items = array_merge($items, self::page_ancestor_items($post_id));
            } elseif (get_post_type($post_id) === 'post') {
                $category = self::primary_category($post_id);
                if ($category instanceof WP_Term) {
                    $items = array_merge($items, self::term_ancestor_items($category));
                    $items[] = self::breadcrumb_item($category->name, self::term_url($category));
                }
            } else {
                $post_type = get_post_type_object((string) get_post_type($post_id));
                if ($post_type && !empty($post_type->has_archive)) {
                    $items[] = self::breadcrumb_item($post_type->labels->name, (string) get_post_type_archive_link($post_type->name));
                }
            }
            $items[] = self::breadcrumb_item(get_the_title($post_id), get_permalink($post_id), true);
        } elseif (is_post_type_archive()) {
            $post_type_name = get_query_var('post_type');
            if (is_array($post_type_name)) { $post_type_name = reset($post_type_name); }
            $post_type = get_post_type_object((string) $post_type_name);
            if ($post_type) {
                $items[] = self::breadcrumb_item($post_type->labels->name, (string) get_post_type_archive_link($post_type->name), true);
            }
        } elseif (is_archive()) {
            $items[] = self::breadcrumb_item(wp_strip_all_tags((string) get_the_archive_title()), '', true);
        }

        $items = self::append_paged_item($items, $labels);
        $html = self::render_breadcrumb_items($items, $labels);
        $schema_enabled = filter_var($atts['schema'], FILTER_VALIDATE_BOOLEAN)
            && (bool) apply_filters('m360_breadcrumb_schema_enabled', true, $items);

        if ($schema_enabled && !self::$breadcrumb_schema_rendered) {
            self::$breadcrumb_schema_rendered = true;
            $html .= self::breadcrumb_schema($items);
        }

        return $html;
    }

    private static function breadcrumb_item(string $label, string $url = '', bool $current = false, bool $schema = true): array
    {
        return [
            'label' => trim(wp_strip_all_tags($label)),
            'url' => $url,
            'current' => $current,
            'schema' => $schema,
            'home' => false,
        ];
    }

    private static function render_breadcrumb_items(array $items, array $labels): string
    {
        $rendered = [];
        foreach ($items as $item) {
            $label = (string) ($item['label'] ?? '');
            if ($label === '') { continue; }
            $current = !empty($item['current']);
            $url = (string) ($item['url'] ?? '');
            $content = esc_html($label);

            if (!empty($item['home'])) {
                $content = '<span class="m360-breadcrumb__home-icon" aria-hidden="true"></span>'
                    . '<span class="m360-breadcrumb__home-label">' . esc_html($label) . '</span>';
            }

            if (!$current && $url !== '') {
                $content = '<a href="' . esc_url($url) . '">' . $content . '</a>';
            } else {
                $attributes = $current
                    ? ' class="m360-breadcrumb__current" aria-current="page" title="' . esc_attr($label) . '"'
                    : ' class="m360-breadcrumb__context"';
                $content = '<span' . $attributes . '>' . $content . '</span>';
            }
            $rendered[] = '<li>' . $content . '</li>';
        }

        if (empty($rendered)) { return ''; }

        return '<nav class="m360-breadcrumb m360-ui-breadcrumb-nav" aria-label="' . esc_attr($labels['aria']) . '">'
            . '<ol>' . implode('', $rendered) . '</ol></nav>';
    }

    private static function breadcrumb_schema(array $items): string
    {
        $elements = [];
        $position = 1;
        foreach ($items as $item) {
            if (isset($item['schema']) && !$item['schema']) { continue; }
            $label = (string) ($item['label'] ?? '');
            $url = (string) ($item['url'] ?? '');
            if ($label === '' || $url === '') { continue; }
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $label,
                'item' => $url,
            ];
        }

        if (count($elements) < 2) { return ''; }
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];

        return '<script type="application/ld+json" class="m360-breadcrumb__schema">'
            . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . '</script>';
    }

    private static function page_ancestor_items(int $post_id): array
    {
        $items = [];
        foreach (array_reverse(get_post_ancestors($post_id)) as $ancestor_id) {
            $items[] = self::breadcrumb_item(get_the_title($ancestor_id), get_permalink($ancestor_id));
        }
        return $items;
    }

    private static function term_ancestor_items(WP_Term $term): array
    {
        $items = [];
        foreach (array_reverse(get_ancestors($term->term_id, $term->taxonomy, 'taxonomy')) as $ancestor_id) {
            $ancestor = get_term($ancestor_id, $term->taxonomy);
            if ($ancestor instanceof WP_Term) {
                $items[] = self::breadcrumb_item($ancestor->name, self::term_url($ancestor));
            }
        }
        return $items;
    }

    private static function primary_category(int $post_id): ?WP_Term
    {
        $category_id = 0;
        if (class_exists('WPSEO_Primary_Term')) {
            $primary = new WPSEO_Primary_Term('category', $post_id);
            $category_id = (int) $primary->get_primary_term();
        }
        $category_id = (int) apply_filters('m360_breadcrumb_primary_category_id', $category_id, $post_id);
        if ($category_id > 0) {
            $term = get_term($category_id, 'category');
            if ($term instanceof WP_Term) { return $term; }
        }
        $categories = get_the_category($post_id);
        return !empty($categories) && $categories[0] instanceof WP_Term ? $categories[0] : null;
    }

    private static function term_url(WP_Term $term): string
    {
        $url = get_term_link($term);
        return is_wp_error($url) ? '' : (string) $url;
    }

    private static function append_paged_item(array $items, array $labels): array
    {
        $page = max((int) get_query_var('paged'), (int) get_query_var('page'));
        if ($page < 2) { return $items; }
        $last = array_key_last($items);
        if ($last !== null) { $items[$last]['current'] = false; }
        $items[] = self::breadcrumb_item(sprintf($labels['page'], $page), self::current_url(), true);
        return $items;
    }

    private static function current_url(): string
    {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash((string) $_SERVER['REQUEST_URI']) : '/';
        return home_url($request_uri);
    }

    private static function language_home_url(): string
    {
        if (function_exists('pll_home_url')) {
            $language = function_exists('pll_current_language') ? (string) pll_current_language('slug') : '';
            $url = $language !== '' ? pll_home_url($language) : pll_home_url();
            if (is_string($url) && $url !== '') { return $url; }
        }
        return home_url(self::is_en() ? '/en/' : '/');
    }

    private static function breadcrumb_labels(bool $is_en): array
    {
        return $is_en ? [
            'home' => 'Home',
            'author' => 'Author',
            'category' => 'Category',
            'tag' => 'Tag',
            'date_archive' => 'Date archive',
            'search' => 'Search',
            'not_found' => 'Page not found',
            'page' => 'Page %d',
            'aria' => 'Breadcrumb',
        ] : [
            'home' => 'Início',
            'author' => 'Autor',
            'category' => 'Categoria',
            'tag' => 'Tag',
            'date_archive' => 'Arquivo por data',
            'search' => 'Busca',
            'not_found' => 'Página não encontrada',
            'page' => 'Página %d',
            'aria' => 'Trilha de navegação',
        ];
    }

    public static function section_navigation(array $atts = []): string
    {
        self::enqueue_assets();
        $atts = shortcode_atts(['menu'=>'','menu_pt'=>'','menu_en'=>''], $atts, 'm360_section_navigation');
        $items = self::institutional_items();
        if (!empty($items)) { return '<nav class="m360-section-navigation m360-section-navigation--index"><ul><li>' . implode('</li><li>', $items) . '</li></ul></nav>'; }
        $menu_name = self::section_menu_name($atts);
        if ($menu_name !== '') {
            $html = wp_nav_menu(['menu'=>$menu_name,'container'=>false,'menu_class'=>'m360-section-navigation__menu','echo'=>false,'fallback_cb'=>false,'depth'=>2]);
            if (is_string($html) && $html !== '') { return '<nav class="m360-section-navigation m360-section-navigation--index">' . $html . '</nav>'; }
        }
        if (is_singular()) { foreach (array_slice(get_the_category(), 0, 5) as $cat) { $items[] = '<a href="' . esc_url(get_category_link($cat)) . '">' . esc_html($cat->name) . '</a>'; } }
        return empty($items) ? '' : '<nav class="m360-section-navigation m360-section-navigation--index"><ul><li>' . implode('</li><li>', $items) . '</li></ul></nav>';
    }

    private static function institutional_items(): array
    {
        if (!is_page()) { return []; }
        $current = trailingslashit(parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '');
        $map = self::is_en() ? [
            'About Us'=>'/en/about-us/', 'Masthead'=>'/en/masthead/', 'Advertising'=>'/en/advertising/', 'Editorial Policy'=>'/en/editorial-policy/', 'Privacy Policy'=>'/en/privacy-policy/', 'Terms of Use'=>'/en/terms-of-use/', 'Contact'=>'/en/contact/'
        ] : [
            'Quem Somos'=>'/quem-somos/', 'Expediente'=>'/expediente/', 'Publicidades'=>'/publicidades/', 'Política Editorial'=>'/politica-editorial/', 'Política de Privacidade'=>'/politica-de-privacidade/', 'Termos de Uso'=>'/termos-de-uso/', 'Contato'=>'/contato/'
        ];
        $items = [];
        foreach ($map as $label=>$path) { $active = trailingslashit($path) === $current; $items[] = '<a href="' . esc_url(home_url($path)) . '"' . ($active ? ' aria-current="page"' : '') . '>' . esc_html($label) . '</a>'; }
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

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-foundation','registered')) { wp_enqueue_style('m360-core-foundation'); }
        if (wp_style_is('m360-core-navigation-components','registered')) { wp_enqueue_style('m360-core-navigation-components'); }
    }
    private static function month_label(int $month, bool $is_en): string
    {
        $months_en = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
        $months_pt = [1=>'janeiro',2=>'fevereiro',3=>'março',4=>'abril',5=>'maio',6=>'junho',7=>'julho',8=>'agosto',9=>'setembro',10=>'outubro',11=>'novembro',12=>'dezembro'];
        return $is_en ? ($months_en[$month] ?? '') : ($months_pt[$month] ?? '');
    }
    private static function is_en(): bool
    {
        if (function_exists('pll_current_language')) {
            $language = (string) pll_current_language('slug');
            if ($language !== '') { return str_starts_with(strtolower($language), 'en'); }
        }
        return str_starts_with(strtolower((string) get_locale()), 'en');
    }
}
