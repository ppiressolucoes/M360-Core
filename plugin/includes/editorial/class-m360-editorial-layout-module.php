<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Editorial_Layout_Module implements M360_Module_Interface
{
    private const SETTINGS = 'm360_editorial_settings';
    private const CACHE_VERSION = 'm360_editorial_cache_version';
    private static array $rendered = [];

    public function id(): string { return 'editorial-layout-home'; }
    public function label(): string { return 'Editorial Layout & Home'; }
    public function version(): string { return M360_CORE_VERSION; }
    public function schema_version(): string { return '2'; }
    public function dependencies(): array { return ['publisher-foundation']; }
    public function capabilities(): array { return ['manage_options', 'edit_posts']; }
    public function settings_schema(): array
    {
        return [
            'mode' => ['type' => 'enum:off,shadow,hybrid,public', 'portable' => true],
            'legacy_shortcodes' => ['type' => 'enum:precursor,core', 'portable' => false],
            'post_type' => ['type' => 'string', 'portable' => true],
            'category_taxonomy' => ['type' => 'string', 'portable' => true],
            'tag_taxonomy' => ['type' => 'string', 'portable' => true],
            'heading_level' => ['type' => 'integer', 'portable' => true],
            'cache_ttl' => ['type' => 'integer', 'portable' => true],
        ];
    }
    public function asset_handles(): array { return ['styles' => ['m360-core-editorial','m360-core-editorial-carousel','m360-core-editorial-layout','m360-core-editorial-polish','m360-core-editorial-sections','m360-core-editorial-widgets','m360-core-editorial-ticker'], 'scripts' => ['m360-core-editorial']]; }
    public function is_required(): bool { return false; }
    public function default_enabled(): bool { return false; }

    public function activate(): void
    {
        M360_Editorial_Widgets::activate();
        if (get_option(self::SETTINGS, null) === null) {
            add_option(self::SETTINGS, self::defaults(), '', false);
        } else {
            $settings = get_option(self::SETTINGS, []);
            if (is_array($settings) && ($settings['mode'] ?? '') === 'shadow') {
                $settings['mode'] = 'hybrid';
                update_option(self::SETTINGS, $settings, false);
            }
        }
        if (get_option(self::CACHE_VERSION, null) === null) { add_option(self::CACHE_VERSION, '1', '', false); }
    }
    public function deactivate(): void {}

    public function boot(): void
    {
        add_action('save_post', [self::class, 'invalidate_post'], 10, 2);
        add_action('created_term', [self::class, 'invalidate_term'], 10, 3);
        add_action('edited_term', [self::class, 'invalidate_term'], 10, 3);
        $settings = self::settings();
        if ($settings['mode'] === 'shadow') { add_action('wp', [self::class, 'shadow_sample'], 99); }
        if ($settings['mode'] === 'off' || $settings['mode'] === 'shadow') { return; }
        add_shortcode('m360_editorial_ticker', [self::class, 'ticker']);
        add_shortcode('m360_editorial_hero', [self::class, 'hero']);
        add_shortcode('m360_editorial_section', [self::class, 'section']);
        add_shortcode('m360_editorial_newsroom', [self::class, 'newsroom']);
        add_shortcode('m360_newsroom', [self::class, 'newsroom']);
        M360_Editorial_Widgets::register();
        add_action('init', [self::class, 'register_legacy_fallbacks'], 100);
    }

    public static function register_legacy_fallbacks(): void
    {
        $settings = self::settings();
        if (in_array($settings['mode'], ['off','shadow'], true)) { return; }
        $fallbacks = ['m360_news_ticker'=>'ticker','m360_news_hero'=>'hero','m360_news_section'=>'section'];
        foreach ($fallbacks as $shortcode => $method) {
            if (!shortcode_exists($shortcode)) { add_shortcode($shortcode, [self::class, $method]); }
        }
    }

    public function health(): array
    {
        $settings = self::settings();
        if ($settings['mode'] === 'public' && $settings['legacy_shortcodes'] === 'core' && self::precursor_active()) {
            return ['status' => 'warning', 'message' => 'Precursor ativo; ownership dos shortcodes permanece protegido.'];
        }
        $message = $settings['mode'] === 'shadow' ? 'Shadow mode ativo, sem HTML publico.' : ($settings['mode'] === 'hybrid' ? (self::precursor_active() ? 'Shortcodes publicos do Core ativos; contratos legados preservados no precursor.' : 'Shortcodes publicos ativos; fallbacks legados assumidos pelo Core.') : ($settings['mode'] === 'public' ? 'Renderizacao publica e cutover legado ativos.' : 'Modulo preparado e sem saida publica.'));
        $widget_count = count(M360_Editorial_Widgets::all());
        if ($widget_count > 0) { $message .= ' Widgets editoriais configurados: ' . $widget_count . '.'; }
        return ['status' => 'healthy', 'message' => $message];
    }

    public static function defaults(): array
    {
        return ['mode' => 'hybrid', 'legacy_shortcodes' => 'precursor', 'post_type' => 'post', 'category_taxonomy' => 'category', 'tag_taxonomy' => 'post_tag', 'heading_level' => 2, 'cache_ttl' => 600];
    }
    public static function settings(): array
    {
        $stored = get_option(self::SETTINGS, []);
        $input = is_array($stored) ? array_merge(self::defaults(), $stored) : self::defaults();
        return [
            'mode' => in_array($input['mode'], ['off','shadow','hybrid','public'], true) ? $input['mode'] : 'off',
            'legacy_shortcodes' => in_array($input['legacy_shortcodes'], ['precursor','core'], true) ? $input['legacy_shortcodes'] : 'precursor',
            'post_type' => sanitize_key((string) $input['post_type']) ?: 'post',
            'category_taxonomy' => sanitize_key((string) $input['category_taxonomy']) ?: 'category',
            'tag_taxonomy' => sanitize_key((string) $input['tag_taxonomy']) ?: 'post_tag',
            'heading_level' => max(2, min(6, (int) $input['heading_level'])),
            'cache_ttl' => max(0, min(DAY_IN_SECONDS, (int) $input['cache_ttl'])),
        ];
    }

    public static function ticker(array $atts = []): string
    {
        $atts = shortcode_atts(['lang'=>'','label'=>'','limit'=>8,'category'=>'','interval'=>4500,'autoplay'=>'true','reduced_motion'=>'allow'], $atts, 'm360_editorial_ticker');
        $posts = self::posts($atts, false); if (!$posts) { return ''; } self::assets();
        wp_enqueue_style('m360-core-editorial-ticker');
        $lang = strtolower(substr((string) $atts['lang'], 0, 2));
        $label = sanitize_text_field((string) $atts['label']);
        if ($label === '') { $label = $lang === 'pt' ? 'Últimas Notícias' : 'Latest News'; }
        $autoplay = filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN);
        $reduced_motion = strtolower(sanitize_key((string) $atts['reduced_motion'])) === 'respect' ? 'respect' : 'allow';
        $html = '<section class="m360-editorial m360-editorial-ticker" data-m360-editorial-ticker data-interval="' . esc_attr((string) max(2500, (int) $atts['interval'])) . '" data-autoplay="' . ($autoplay ? 'true' : 'false') . '" data-reduced-motion="' . esc_attr($reduced_motion) . '" aria-label="' . esc_attr($label) . '">';
        $html .= '<strong class="m360-editorial-ticker__label">' . esc_html($label) . '</strong><div class="m360-editorial-ticker__viewport" aria-live="polite">';
        foreach ($posts as $index => $post) {
            $html .= '<article class="m360-editorial-ticker__item" data-m360-ticker-slide' . ($index ? ' hidden' : '') . ' aria-hidden="' . ($index ? 'true' : 'false') . '">';
            $terms = get_the_terms($post, self::settings()['category_taxonomy']);
            if (is_array($terms) && $terms && $terms[0] instanceof WP_Term) { $html .= '<span class="m360-editorial-ticker__category">' . esc_html($terms[0]->name) . '</span>'; }
            $html .= '<a href="' . esc_url(get_permalink($post)) . '">' . esc_html(get_the_title($post)) . '</a></article>';
        }
        $html .= '</div>';
        if (count($posts) > 1) {
            $previous = $lang === 'pt' ? 'Notícia anterior' : __('Previous story', 'm360-core');
            $next = $lang === 'pt' ? 'Próxima notícia' : __('Next story', 'm360-core');
            $pause = $lang === 'pt' ? 'Pausar noticias' : __('Pause stories', 'm360-core');
            $play = $lang === 'pt' ? 'Retomar noticias' : __('Resume stories', 'm360-core');
            $left_icon = '<svg aria-hidden="true" viewBox="0 0 24 24" focusable="false"><path d="M15 18l-6-6 6-6"/></svg>';
            $right_icon = '<svg aria-hidden="true" viewBox="0 0 24 24" focusable="false"><path d="M9 6l6 6-6 6"/></svg>';
            $pause_icon = '<svg data-m360-ticker-pause-icon aria-hidden="true" viewBox="0 0 24 24" focusable="false"><path d="M9 7v10M15 7v10"/></svg>';
            $play_icon = '<svg data-m360-ticker-play-icon aria-hidden="true" viewBox="0 0 24 24" focusable="false" hidden><path d="M9 6l8 6-8 6z"/></svg>';
            $toggle = $autoplay ? '<button type="button" data-m360-ticker-toggle data-pause-label="' . esc_attr($pause) . '" data-play-label="' . esc_attr($play) . '" aria-label="' . esc_attr($pause) . '" aria-pressed="false">' . $pause_icon . $play_icon . '</button>' : '';
            $html .= '<div class="m360-editorial-ticker__controls"><button type="button" data-m360-ticker-prev aria-label="' . esc_attr($previous) . '">' . $left_icon . '</button>' . $toggle . '<button type="button" data-m360-ticker-next aria-label="' . esc_attr($next) . '">' . $right_icon . '</button></div>';
        }
        return $html . '</section>';
    }
    public static function hero(array $atts = []): string
    {
        $atts = shortcode_atts(['lang'=>'','limit'=>5,'tag'=>'','category'=>'','heading_level'=>''], $atts, 'm360_editorial_hero');
        $posts = self::posts($atts); if (!$posts) { return ''; } self::assets();
        $level = self::heading($atts); $html = '<section class="m360-editorial m360-editorial-hero">';
        foreach ($posts as $post) {
            $image = get_the_post_thumbnail_url($post, 'large'); $html .= '<article class="m360-editorial-hero__item">';
            if ($image) { $html .= '<img src="' . esc_url($image) . '" alt="" loading="lazy">'; }
            $html .= '<h' . $level . '><a href="' . esc_url(get_permalink($post)) . '">' . esc_html(get_the_title($post)) . '</a></h' . $level . '></article>';
        }
        return $html . '</section>';
    }
    public static function section(array $atts = []): string
    {
        $atts = shortcode_atts(['title'=>'','lang'=>'','category'=>'','tag'=>'','layout'=>'grid','limit'=>4,'more_url'=>'','heading_level'=>''], $atts, 'm360_editorial_section');
        $posts = self::posts($atts); if (!$posts) { return ''; } self::assets();
        $layout = in_array($atts['layout'], ['grid','featured-list','compact'], true) ? $atts['layout'] : 'grid'; $level = self::heading($atts);
        $html = '<section class="m360-editorial m360-editorial-section m360-editorial-section--' . esc_attr($layout) . '">';
        if ($atts['title']) { $html .= '<h' . $level . '>' . esc_html($atts['title']) . '</h' . $level . '>'; }
        $html .= '<div class="m360-editorial-section__items">';
        foreach ($posts as $index => $post) { $html .= self::section_item($post, $layout, $index, min(6,$level+1)); }
        $html .= '</div>';
        if ($atts['more_url']) { $html .= '<a href="' . esc_url($atts['more_url']) . '">' . esc_html__('More news', 'm360-core') . '</a>'; }
        return $html . '</section>';
    }

    private static function section_item(WP_Post $post, string $layout, int $index, int $level): string
    {
        $image = get_the_post_thumbnail_url($post, $layout === 'compact' ? 'medium' : 'medium_large');
        $class = 'm360-editorial-section-card m360-editorial-section-card--' . $layout;
        if ($layout === 'featured-list' && $index === 0) { $class .= ' m360-editorial-section-card--lead'; }
        $html = '<article class="' . esc_attr($class) . '">';
        if ($image) { $html .= '<a class="m360-editorial-section-card__media" href="' . esc_url(get_permalink($post)) . '"><img src="' . esc_url($image) . '" alt="" loading="lazy"></a>'; }
        $html .= '<div class="m360-editorial-section-card__content">' . self::category_badge($post);
        $html .= '<h' . $level . '><a href="' . esc_url(get_permalink($post)) . '">' . esc_html(get_the_title($post)) . '</a></h' . $level . '>';
        if ($layout === 'featured-list' && $index === 0) {
            $excerpt = wp_trim_words((string)get_the_excerpt($post), 20);
            if ($excerpt !== '') { $html .= '<p>' . esc_html($excerpt) . '</p>'; }
        }
        return $html . '</div></article>';
    }

    public static function newsroom(array $atts = []): string
    {
        $atts = shortcode_atts([
            'title' => '',
            'show_title' => 'false',
            'lang' => '',
            'featured_category' => 'destaque',
            'featured_tag' => '',
            'featured_limit' => 5,
            'card_categories' => '',
            'international_category' => 'internacional',
            'include_international' => 'true',
            'cards' => 4,
            'interval' => 6500,
            'autoplay' => 'true',
            'heading_level' => '',
        ], $atts, 'm360_editorial_newsroom');

        $featured_limit = max(1,min(10,(int)$atts['featured_limit']));
        $featured_query = ['lang'=>$atts['lang'],'category'=>$atts['featured_category'],'tag'=>$atts['featured_tag'],'limit'=>$featured_limit];
        if ($atts['featured_tag'] !== '') { $featured_query['category'] = ''; }
        $featured = self::posts($featured_query);
        if (count($featured) < $featured_limit) {
            $featured = array_merge($featured, self::posts(['lang'=>$atts['lang'],'limit'=>$featured_limit-count($featured)]));
        }
        $card_categories = trim((string) $atts['card_categories']);
        if (filter_var($atts['include_international'], FILTER_VALIDATE_BOOLEAN) && $atts['international_category'] !== '') {
            $card_categories = trim($card_categories . ',' . sanitize_title((string) $atts['international_category']), ',');
        }
        $cards = self::posts(['lang'=>$atts['lang'],'category'=>$card_categories,'limit'=>max(1,min(8,(int)$atts['cards']))]);
        if (!$cards) { $cards = self::posts(['lang'=>$atts['lang'],'limit'=>max(1,min(8,(int)$atts['cards']))]); }
        if (!$featured && !$cards) { return ''; }

        self::assets();
        $level = self::heading($atts);
        $html = '<section class="m360-editorial m360-editorial-newsroom">';
        if (filter_var($atts['show_title'], FILTER_VALIDATE_BOOLEAN) && $atts['title'] !== '') { $html .= '<h' . $level . ' class="m360-editorial-newsroom__heading">' . esc_html($atts['title']) . '</h' . $level . '>'; }
        $autoplay = filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN);
        $html .= '<div class="m360-editorial-newsroom__layout"><div class="m360-editorial-newsroom__carousel" data-m360-editorial-carousel data-interval="' . esc_attr((string)max(2500,(int)$atts['interval'])) . '" data-autoplay="' . ($autoplay ? 'true' : 'false') . '">';
        foreach ($featured as $index => $post) { $html .= self::newsroom_featured($post, min(6,$level+1), $index); }
        if (count($featured) > 1) {
            $html .= '<div class="m360-editorial-newsroom__controls"><button type="button" data-m360-editorial-prev aria-label="' . esc_attr__('Previous story','m360-core') . '">&#8249;</button><button type="button" data-m360-editorial-next aria-label="' . esc_attr__('Next story','m360-core') . '">&#8250;</button></div>';
        }
        $html .= '</div>';
        $html .= '<div class="m360-editorial-newsroom__cards">';
        foreach ($cards as $post) { $html .= self::newsroom_card($post, min(6,$level+2)); }
        return $html . '</div></div></section>';
    }

    private static function newsroom_featured(WP_Post $post, int $level, int $index): string
    {
        $image = get_the_post_thumbnail_url($post, 'large');
        $html = '<article class="m360-editorial-newsroom__featured" data-m360-editorial-slide' . ($index > 0 ? ' hidden' : '') . ' aria-hidden="' . ($index > 0 ? 'true' : 'false') . '"><a class="m360-editorial-newsroom__media" href="' . esc_url(get_permalink($post)) . '">';
        if ($image) { $html .= '<img src="' . esc_url($image) . '" alt="" loading="eager">'; }
        $html .= '</a><div class="m360-editorial-newsroom__featured-content">' . self::category_badge($post);
        $html .= '<h' . $level . '><a href="' . esc_url(get_permalink($post)) . '">' . esc_html(get_the_title($post)) . '</a></h' . $level . '>';
        $excerpt = wp_trim_words((string) get_the_excerpt($post), 24);
        if ($excerpt !== '') { $html .= '<p>' . esc_html($excerpt) . '</p>'; }
        return $html . '</div></article>';
    }

    private static function newsroom_card(WP_Post $post, int $level): string
    {
        $image = get_the_post_thumbnail_url($post, 'medium_large');
        $html = '<article class="m360-editorial-newsroom__card">';
        if ($image) { $html .= '<a href="' . esc_url(get_permalink($post)) . '"><img src="' . esc_url($image) . '" alt="" loading="lazy"></a>'; }
        $html .= '<div class="m360-editorial-newsroom__card-content">' . self::category_badge($post);
        $html .= '<h' . $level . '><a href="' . esc_url(get_permalink($post)) . '">' . esc_html(get_the_title($post)) . '</a></h' . $level . '>';
        return $html . '</div></article>';
    }

    private static function category_badge(WP_Post $post): string
    {
        $terms = get_the_terms($post, self::settings()['category_taxonomy']);
        if (!is_array($terms) || !$terms) { return ''; }
        $term = reset($terms);
        return $term instanceof WP_Term ? '<span class="m360-editorial-newsroom__category">' . esc_html($term->name) . '</span>' : '';
    }

    private static function posts(array $atts, bool $track_rendered = true): array
    {
        $settings = self::settings(); $args = ['post_type'=>$settings['post_type'],'post_status'=>'publish','posts_per_page'=>max(1,min(20,(int)($atts['limit']??5))),'ignore_sticky_posts'=>true,'no_found_rows'=>true,'post__not_in'=>$track_rendered ? self::$rendered : []];
        $tax = [];
        foreach (['category'=>$settings['category_taxonomy'],'tag'=>$settings['tag_taxonomy']] as $key=>$taxonomy) {
            $slugs = array_values(array_filter(array_map('sanitize_title', preg_split('/\s*,\s*/', (string)($atts[$key]??'')) ?: [])));
            if ($slugs) { $tax[] = ['taxonomy'=>$taxonomy,'field'=>'slug','terms'=>$slugs]; }
        }
        if ($tax) { $args['tax_query'] = count($tax)>1 ? array_merge(['relation'=>'AND'],$tax) : $tax; }
        $lang = sanitize_key(substr((string)($atts['lang']??''),0,2)); if ($lang) { $args['lang'] = apply_filters('m360_editorial_query_language',$lang,$atts); }
        $args = apply_filters('m360_editorial_query_args',$args,$atts,$settings); $key = 'm360_editorial_' . md5(wp_json_encode([$args,get_option(self::CACHE_VERSION,'1')]));
        $ids = $settings['cache_ttl'] ? get_transient($key) : false;
        if (!is_array($ids)) { $query = new WP_Query($args); $ids = wp_list_pluck($query->posts,'ID'); if ($settings['cache_ttl']) { set_transient($key,$ids,$settings['cache_ttl']); } }
        $posts = array_values(array_filter(array_map('get_post',array_map('absint',$ids)))); if ($track_rendered) { self::$rendered = array_merge(self::$rendered,wp_list_pluck($posts,'ID')); } return $posts;
    }
    public static function shadow_sample(): void
    {
        if (is_admin() || !is_front_page()) { return; } $posts = self::posts(['limit'=>5]);
        do_action('m360_editorial_shadow_sample',['generated_at'=>current_time('mysql'),'post_ids'=>array_map('absint',wp_list_pluck($posts,'ID')),'precursor_active'=>self::precursor_active()]);
    }
    public static function invalidate_post(int $post_id, $post): void { if ($post instanceof WP_Post && $post->post_type === self::settings()['post_type']) { self::bump(); } }
    public static function invalidate_term(int $term_id, int $tt_id, string $taxonomy): void { $s=self::settings(); if (in_array($taxonomy,[$s['category_taxonomy'],$s['tag_taxonomy']],true)) { self::bump(); } }
    private static function bump(): void { update_option(self::CACHE_VERSION,(string)microtime(true),false); }
    private static function assets(): void { wp_enqueue_style('m360-core-editorial'); wp_enqueue_style('m360-core-editorial-carousel'); wp_enqueue_style('m360-core-editorial-layout'); wp_enqueue_style('m360-core-editorial-polish'); wp_enqueue_style('m360-core-editorial-sections'); wp_enqueue_script('m360-core-editorial'); }
    private static function heading(array $atts): int { return max(2,min(6,(int)($atts['heading_level'] ?: self::settings()['heading_level']))); }
    private static function precursor_active(): bool { return class_exists('M360_Home_Editorial'); }
}
