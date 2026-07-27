<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Editorial_Widgets
{
    private const OPTION = 'm360_editorial_widgets';
    private const MAX_INSTANCES = 24;

    public static function activate(): void
    {
        if (get_option(self::OPTION, null) === null) { add_option(self::OPTION, [], '', false); }
    }

    public static function register(): void
    {
        add_shortcode('m360_editorial_widget', [self::class, 'shortcode']);
        add_action('elementor/preview/enqueue_styles', [self::class, 'enqueue_preview_assets']);
        add_action('elementor/preview/enqueue_scripts', [self::class, 'enqueue_preview_assets']);
        add_action('elementor/frontend/after_enqueue_scripts', [self::class, 'enqueue_preview_assets']);
    }

    public static function enqueue_preview_assets(): void
    {
        wp_enqueue_style('m360-core-editorial-widgets');
        wp_enqueue_style('m360-core-editorial-ticker');
        wp_enqueue_script('m360-core-editorial');
    }

    public static function presets(): array
    {
        return [
            'newsroom' => ['label' => 'Top Header Section · Newsroom', 'count' => 5],
            '1' => ['label' => 'Destaque 100% + 3 cards', 'count' => 4],
            '2' => ['label' => 'Destaque 50% + 6 notícias', 'count' => 7],
            '3' => ['label' => '2 destaques + 4 notícias', 'count' => 6],
            '4' => ['label' => '3 destaques + 6 notícias', 'count' => 9],
            '5' => ['label' => 'Latest News · carrossel retrato', 'count' => 12],
        ];
    }

    public static function all(): array
    {
        $stored = get_option(self::OPTION, []);
        if (!is_array($stored)) { return []; }
        $widgets = [];
        foreach ($stored as $id => $widget) {
            if (!is_array($widget)) { continue; }
            $clean = self::sanitize($widget, (string) $id);
            if ($clean['id'] !== '') { $widgets[$clean['id']] = $clean; }
        }
        return $widgets;
    }

    public static function save(array $input, string $original_id = '')
    {
        $widgets = self::all();
        $original_id = sanitize_key($original_id);
        $widget = self::sanitize($input, $original_id);
        if ($widget['id'] === '') { return new WP_Error('m360_widget_id', 'Informe um ID válido para o widget.'); }
        if ($original_id === '' && !isset($widgets[$widget['id']]) && count($widgets) >= self::MAX_INSTANCES) {
            return new WP_Error('m360_widget_limit', 'Limite de widgets editoriais atingido.');
        }
        if ($original_id !== '' && $original_id !== $widget['id']) { unset($widgets[$original_id]); }
        $widgets[$widget['id']] = $widget;
        update_option(self::OPTION, $widgets, false);
        return $widget;
    }

    public static function delete(string $id): bool
    {
        $widgets = self::all();
        $id = sanitize_key($id);
        if (!isset($widgets[$id])) { return false; }
        unset($widgets[$id]);
        return update_option(self::OPTION, $widgets, false);
    }

    public static function shortcode(array $atts = []): string
    {
        $raw = is_array($atts) ? $atts : [];
        $id = sanitize_key((string) ($raw['id'] ?? ''));
        $saved = $id !== '' ? (self::all()[$id] ?? []) : [];
        if ($id !== '' && !$saved) { return ''; }
        $config = self::sanitize(array_merge($saved, $raw), $id);
        if ($config['id'] === '') { $config['id'] = 'inline-' . substr(md5(wp_json_encode($config)), 0, 8); }
        if ($config['layout'] === 'newsroom') {
            return M360_Editorial_Layout_Module::newsroom([
                'title'=>$config['title'],
                'show_title'=>$config['title'] !== '' ? 'true' : 'false',
                'lang'=>$config['lang'],
                'featured_tag'=>$config['featured_tag'],
                'featured_limit'=>$config['limit'],
                'card_categories'=>implode(',', $config['categories']),
                'cards'=>$config['card_count'],
                'interval'=>$config['interval'],
                'autoplay'=>$config['autoplay'] ? 'true' : 'false',
            ]);
        }
        $posts = self::query($config);
        if (!$posts) { return ''; }

        wp_enqueue_style('m360-core-editorial-widgets');
        wp_enqueue_script('m360-core-editorial');
        $layout = $config['layout'];
        $html = '<section class="m360-editorial-widget m360-editorial-widget--layout-' . esc_attr($layout) . '" data-m360-widget="' . esc_attr($config['id']) . '">';
        if ($config['title'] !== '') {
            $html .= '<header class="m360-editorial-widget__header"><h2 class="m360-editorial-widget__heading">' . esc_html($config['title']) . '</h2>';
            $view_all = self::view_all_url($config);
            if ($view_all !== '') { $html .= '<a class="m360-editorial-widget__view-all" href="' . esc_url($view_all) . '">' . esc_html__('View all', 'm360-core') . ' <span aria-hidden="true">→</span></a>'; }
            $html .= '</header>';
        }
        if ($layout === '1') { $html .= self::layout_one($posts, $config); }
        elseif ($layout === '2') { $html .= self::layout_two($posts, $config); }
        elseif ($layout === '3') { $html .= self::layout_three($posts, $config); }
        elseif ($layout === '4') { $html .= self::layout_four($posts, $config); }
        else { $html .= self::layout_five($posts, $config); }
        return $html . '</section>';
    }

    public static function render_admin(): void
    {
        $widgets = self::all();
        $terms = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
        $terms = is_array($terms) ? $terms : [];
        $edit_id = sanitize_key((string) ($_GET['edit_widget'] ?? ''));
        $editing = $edit_id !== '' && isset($widgets[$edit_id]) ? $widgets[$edit_id] : null;
        $defaults = ['id'=>'','title'=>'','layout'=>'1','categories'=>[],'lang'=>'en','limit'=>4,'excerpt_words'=>22,'show_view_all'=>false,'view_all_url'=>'','featured_tag'=>'','card_count'=>4,'interval'=>6500,'autoplay'=>true];
        $form_widget = $editing ?: $defaults;
        echo '<p>Crie instâncias por editoria e publique pelo shortcode estável da lista. Nenhuma página é alterada automaticamente.</p>';
        echo '<details class="m360-editorial-admin__form"' . ($editing ? ' open' : '') . '><summary>' . esc_html($editing ? 'Editar widget: ' . $editing['title'] : 'Cadastrar nova instância') . '</summary>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="m360_platform_save_editorial_widget"><input type="hidden" name="original_id" value="' . esc_attr($editing['id'] ?? '') . '">';
        wp_nonce_field('m360_platform_save_editorial_widget');
        self::admin_fields($form_widget, $terms, (bool) $editing);
        submit_button($editing ? 'Atualizar widget' : 'Criar widget');
        echo '</form></details>';

        echo '<h2>Widgets cadastrados</h2><table class="widefat striped m360-editorial-admin__list"><thead><tr><th>ID</th><th>Título</th><th>Modelo</th><th>Editorias</th><th>Shortcode</th><th>Ações</th></tr></thead><tbody>';
        if (!$widgets) { echo '<tr><td colspan="6">Nenhuma instância cadastrada.</td></tr>'; }
        foreach ($widgets as $widget) {
            $preset = self::presets()[$widget['layout']];
            $model_key = $widget['layout'] === 'newsroom' ? 'Newsroom' : '#' . $widget['layout'];
            $edit_url = add_query_arg(['page'=>'m360-editorial-widgets','edit_widget'=>$widget['id']], admin_url('admin.php'));
            echo '<tr><td><strong>' . esc_html($widget['id']) . '</strong></td><td>' . esc_html($widget['title'] ?: '—') . '</td><td>' . esc_html($model_key . ' — ' . $preset['label']) . '</td><td>' . esc_html($widget['categories'] ? implode(', ', $widget['categories']) : 'Todas') . '</td><td><code>[m360_editorial_widget id=&quot;' . esc_html($widget['id']) . '&quot;]</code></td><td><a class="button button-small" href="' . esc_url($edit_url) . '">Editar</a> ';
            echo '<form class="m360-editorial-admin__delete" method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="m360_platform_delete_editorial_widget"><input type="hidden" name="widget_id" value="' . esc_attr($widget['id']) . '">';
            wp_nonce_field('m360_platform_delete_editorial_widget_' . $widget['id']);
            submit_button('Excluir', 'delete small', 'submit', false, ['onclick'=>"return confirm('Excluir esta configuração?')"]);
            echo '</form></td></tr>';
        }
        echo '</tbody></table>';
    }

    private static function admin_fields(array $widget, array $terms, bool $locked): void
    {
        echo '<table class="form-table"><tbody><tr><th><label>ID técnico</label></th><td><input class="regular-text" name="widget[id]" value="' . esc_attr($widget['id']) . '" ' . ($locked ? 'readonly' : 'required') . '><p class="description">Ex.: brasileirao-home ou transfers-en.</p></td></tr>';
        echo '<tr><th><label>Título público</label></th><td><input class="regular-text" name="widget[title]" value="' . esc_attr($widget['title']) . '"></td></tr>';
        echo '<tr><th><label>Modelo</label></th><td><select name="widget[layout]">';
        foreach (self::presets() as $key => $preset) { $model_key = $key === 'newsroom' ? 'Newsroom' : '#' . $key; echo '<option value="' . esc_attr($key) . '" ' . selected($widget['layout'], $key, false) . '>' . esc_html($model_key . ' — ' . $preset['label']) . '</option>'; }
        echo '</select></td></tr><tr><th><label>Idioma</label></th><td><input class="small-text" name="widget[lang]" value="' . esc_attr($widget['lang']) . '" placeholder="en"><p class="description">Código de consulta, como en ou pt.</p></td></tr>';
        echo '<tr><th><label>Editorias</label></th><td><details class="m360-editorial-admin__dropdown"><summary>' . esc_html($widget['categories'] ? count($widget['categories']) . ' selecionada(s)' : 'Todas as editorias') . '</summary><div class="m360-editorial-admin__options">';
        foreach ($terms as $term) { if (!$term instanceof WP_Term) { continue; } echo '<label><input type="checkbox" name="widget[categories][]" value="' . esc_attr($term->slug) . '" ' . checked(in_array($term->slug, $widget['categories'], true), true, false) . '> ' . esc_html($term->name . ' (' . $term->slug . ')') . '</label>'; }
        echo '</div></details><p class="description">Selecione uma ou várias. Sem seleção, consulta as últimas notícias.</p></td></tr>';
        echo '<tr><th><label>Quantidade e resumo</label></th><td><label>Notícias <input class="small-text" type="number" min="1" max="24" name="widget[limit]" value="' . esc_attr((string) $widget['limit']) . '"></label> &nbsp; <label>Palavras no resumo <input class="small-text" type="number" min="0" max="80" name="widget[excerpt_words]" value="' . esc_attr((string) $widget['excerpt_words']) . '"></label><p class="description">No modelo #5, o máximo é 12. Zero oculta o resumo dos destaques.</p></td></tr>';
        echo '<tr><th><label>View All</label></th><td><label><input type="checkbox" name="widget[show_view_all]" value="1" ' . checked($widget['show_view_all'], true, false) . '> exibir link</label> &nbsp; <input class="regular-text" type="url" name="widget[view_all_url]" value="' . esc_attr($widget['view_all_url']) . '" placeholder="https://..."><p class="description">Com uma única editoria, o Core usa automaticamente o arquivo da categoria. Para várias editorias, informe a URL.</p></td></tr>';
        echo '<tr><th><label>Top Header Section</label></th><td><label>Tag dos destaques <input class="regular-text" name="widget[featured_tag]" value="' . esc_attr($widget['featured_tag']) . '" placeholder="featured-en"></label> &nbsp; <label>Cards <input class="small-text" type="number" min="1" max="8" name="widget[card_count]" value="' . esc_attr((string) $widget['card_count']) . '"></label><p class="description">Aplicável ao preset Newsroom. As editorias selecionadas alimentam os cards laterais.</p></td></tr>';
        echo '<tr><th><label>Carrossel</label></th><td><label><input type="checkbox" name="widget[autoplay]" value="1" ' . checked($widget['autoplay'], true, false) . '> avanço automático</label> &nbsp; <label>Intervalo <input class="small-text" type="number" min="2500" step="500" name="widget[interval]" value="' . esc_attr((string) $widget['interval']) . '"> ms</label><p class="description">Aplicável ao modelo #5.</p></td></tr></tbody></table>';
    }

    private static function sanitize(array $input, string $fallback_id = ''): array
    {
        $categories = $input['categories'] ?? ($input['category'] ?? []);
        if (is_string($categories)) { $categories = preg_split('/\s*,\s*/', $categories) ?: []; }
        $categories = array_slice(array_values(array_unique(array_filter(array_map('sanitize_title', is_array($categories) ? $categories : [])))), 0, 12);
        $layout = (string) ($input['layout'] ?? '1');
        if (!isset(self::presets()[$layout])) { $layout = '1'; }
        $default_count = (int) self::presets()[$layout]['count'];
        $limit = max(1, min(24, (int) ($input['limit'] ?? $default_count)));
        if ($layout === '5') { $limit = min(12, $limit); }
        return [
            'id' => sanitize_key((string) ($input['id'] ?? $fallback_id)),
            'title' => sanitize_text_field((string) ($input['title'] ?? '')),
            'layout' => $layout,
            'categories' => $categories,
            'lang' => sanitize_key(substr((string) ($input['lang'] ?? ''), 0, 8)),
            'limit' => $limit,
            'excerpt_words' => max(0, min(80, (int) ($input['excerpt_words'] ?? 22))),
            'show_view_all' => filter_var($input['show_view_all'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'view_all_url' => esc_url_raw((string) ($input['view_all_url'] ?? '')),
            'featured_tag' => sanitize_title((string) ($input['featured_tag'] ?? '')),
            'card_count' => max(1, min(8, (int) ($input['card_count'] ?? 4))),
            'interval' => max(2500, min(30000, (int) ($input['interval'] ?? 6500))),
            'autoplay' => filter_var($input['autoplay'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }

    private static function query(array $config): array
    {
        $count = (int) $config['limit'];
        $args = ['post_type'=>'post','post_status'=>'publish','posts_per_page'=>$count,'ignore_sticky_posts'=>true,'no_found_rows'=>true];
        if ($config['categories']) { $args['tax_query'] = [['taxonomy'=>'category','field'=>'slug','terms'=>$config['categories'],'operator'=>'IN']]; }
        if ($config['lang'] !== '') { $args['lang'] = apply_filters('m360_editorial_query_language', $config['lang'], $config); }
        $args = apply_filters('m360_editorial_widget_query_args', $args, $config);
        $query = new WP_Query($args);
        return array_values(array_filter($query->posts, static fn($post) => $post instanceof WP_Post));
    }

    private static function layout_one(array $posts, array $config): string
    {
        $html = '<div class="m360-editorial-widget__layout">' . self::article($posts[0], 'hero-overlay', true, $config['excerpt_words']) . '<div class="m360-editorial-widget__grid m360-editorial-widget__grid--3">';
        foreach (array_slice($posts, 1) as $post) { $html .= self::article($post, 'card', false); }
        return $html . '</div></div>';
    }

    private static function layout_two(array $posts, array $config): string
    {
        $html = '<div class="m360-editorial-widget__split">' . self::article($posts[0], 'lead', true, $config['excerpt_words']) . '<div class="m360-editorial-widget__list">';
        foreach (array_slice($posts, 1) as $post) { $html .= self::article($post, 'mini', false); }
        return $html . '</div></div>';
    }

    private static function layout_three(array $posts, array $config): string
    {
        $extras = [[],[]];
        foreach (array_slice($posts, 2) as $index => $post) { $extras[$index % 2][] = $post; }
        $html = '<div class="m360-editorial-widget__grid m360-editorial-widget__grid--2">';
        for ($column = 0; $column < 2; $column++) {
            if (!isset($posts[$column])) { continue; }
            $html .= '<div class="m360-editorial-widget__column">' . self::article($posts[$column], 'lead', true, $config['excerpt_words']) . '<div class="m360-editorial-widget__list">';
            foreach ($extras[$column] as $post) { $html .= self::article($post, 'mini', false); }
            $html .= '</div></div>';
        }
        return $html . '</div>';
    }

    private static function layout_four(array $posts, array $config): string
    {
        $extras = [[],[],[]];
        foreach (array_slice($posts, 3) as $index => $post) { $extras[$index % 3][] = $post; }
        $html = '<div class="m360-editorial-widget__grid m360-editorial-widget__grid--3">';
        for ($column = 0; $column < 3; $column++) {
            if (!isset($posts[$column])) { continue; }
            $html .= '<div class="m360-editorial-widget__column">' . self::article($posts[$column], 'portrait', true, $config['excerpt_words']) . '<div class="m360-editorial-widget__list">';
            foreach ($extras[$column] as $post) { $html .= self::article($post, 'mini', false); }
            $html .= '</div></div>';
        }
        return $html . '</div>';
    }

    private static function layout_five(array $posts, array $config): string
    {
        $html = '<div class="m360-editorial-widget__carousel" data-m360-widget-carousel data-interval="' . esc_attr((string) $config['interval']) . '" data-autoplay="' . ($config['autoplay'] ? 'true' : 'false') . '"><div class="m360-editorial-widget__viewport"><div class="m360-editorial-widget__track">';
        foreach ($posts as $post) { $html .= self::article($post, 'portrait', false); }
        $html .= '</div></div>';
        if (count($posts) > 1) { $html .= '<div class="m360-editorial-widget__controls"><span data-m360-widget-status aria-live="polite"></span><button type="button" data-m360-widget-prev aria-label="' . esc_attr__('Previous story', 'm360-core') . '">&#8249;</button><button type="button" data-m360-widget-next aria-label="' . esc_attr__('Next story', 'm360-core') . '">&#8250;</button></div>'; }
        return $html . '</div>';
    }

    private static function article(WP_Post $post, string $variant, bool $excerpt, int $excerpt_words = 22): string
    {
        $image = get_the_post_thumbnail_url($post, in_array($variant, ['hero-overlay','lead'], true) ? 'large' : 'medium_large');
        $permalink = get_permalink($post);
        $html = '<article class="m360-editorial-widget__article m360-editorial-widget__article--' . esc_attr($variant) . '">';
        if ($image) { $html .= '<a class="m360-editorial-widget__media" href="' . esc_url($permalink) . '"><img src="' . esc_url($image) . '" alt="" loading="lazy"></a>'; }
        $html .= '<div class="m360-editorial-widget__content">' . self::category($post) . '<h3><a href="' . esc_url($permalink) . '">' . esc_html(get_the_title($post)) . '</a></h3>' . self::meta($post);
        if ($excerpt && $excerpt_words > 0) { $summary = wp_trim_words((string) get_the_excerpt($post), $excerpt_words); if ($summary !== '') { $html .= '<p>' . esc_html($summary) . '</p>'; } }
        return $html . '</div></article>';
    }

    private static function view_all_url(array $config): string
    {
        if (!$config['show_view_all']) { return ''; }
        if ($config['view_all_url'] !== '') { return $config['view_all_url']; }
        if (count($config['categories']) !== 1) { return ''; }
        $term = get_term_by('slug', $config['categories'][0], 'category');
        if (!$term instanceof WP_Term) { return ''; }
        $url = get_term_link($term);
        return is_wp_error($url) ? '' : (string) $url;
    }

    private static function category(WP_Post $post): string
    {
        $terms = get_the_terms($post, 'category');
        if (!is_array($terms) || !$terms || !$terms[0] instanceof WP_Term) { return ''; }
        return '<span class="m360-editorial-widget__category">' . esc_html($terms[0]->name) . '</span>';
    }

    private static function meta(WP_Post $post): string
    {
        $timestamp = (int) get_post_time('U', false, $post);
        $author = get_the_author_meta('display_name', (int) $post->post_author);
        return '<div class="m360-editorial-widget__meta"><time datetime="' . esc_attr(get_the_date('c', $post)) . '">' . esc_html(date_i18n(get_option('date_format'), $timestamp)) . '</time><span>' . esc_html((string) $author) . '</span></div>';
    }
}
