<?php
/**
 * Plugin Name: M360 PEL Controlled Deployment
 * Description: Perfil operacional controlado do M360 Core para o Portal Energia Limpa.
 * Version: 0.1.3
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

if (!defined('ABSPATH')) { exit; }

final class M360_PEL_Controlled_Deployment
{
    private const OPTION = 'm360_pel_controlled_deployment';
    private const VERSION = '0.1.3';

    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'menu'], 70);
        add_action('admin_post_m360_pel_apply_profile', [self::class, 'apply_profile']);
        add_action('init', [self::class, 'register_shortcodes'], 30);
        add_action('wp_enqueue_scripts', [self::class, 'register_assets']);
        add_filter('get_search_form', [self::class, 'filter_native_search_form'], 999, 2);
    }

    public static function register_shortcodes(): void
    {
        add_shortcode('m360_pel_search_form', [self::class, 'search_form']);
        add_shortcode('m360_pel_search_results', [self::class, 'search_results']);
    }

    public static function register_assets(): void
    {
        wp_register_style(
            'm360-pel-controlled-deployment',
            plugin_dir_url(__FILE__) . 'assets/m360-pel-deployment.css',
            [],
            self::VERSION
        );
        if (self::profile_applied()) {
            wp_enqueue_style('m360-pel-controlled-deployment');
        }
    }

    public static function filter_native_search_form(string $form, array $args = []): string
    {
        if (is_admin() || !self::profile_applied()) { return $form; }
        return self::search_form([]);
    }

    public static function search_form(array $atts = []): string
    {
        $atts = shortcode_atts([
            'results_pt' => '/resultados-da-pesquisa/',
            'results_en' => '/en/search-results/',
            'placeholder' => '',
            'button_label' => '',
        ], $atts, 'm360_pel_search_form');
        $is_en = self::is_en();
        $action = $is_en ? (string) $atts['results_en'] : (string) $atts['results_pt'];
        $action = esc_url(home_url('/' . ltrim($action, '/')));
        $placeholder = trim((string) $atts['placeholder']) ?: ($is_en ? 'Search Portal Energia Limpa' : 'Pesquisar no Portal Energia Limpa');
        $button = trim((string) $atts['button_label']) ?: ($is_en ? 'Search' : 'Pesquisar');
        self::enqueue_assets();
        return '<form class="m360-pel-search-form" role="search" method="get" action="' . $action . '">'
            . '<label class="screen-reader-text" for="m360-pel-search-q">' . esc_html($button) . '</label>'
            . '<input id="m360-pel-search-q" type="search" name="m360q" required value="' . esc_attr(self::request_text('m360q')) . '" placeholder="' . esc_attr($placeholder) . '">'
            . '<button type="submit" formaction="' . $action . '">' . esc_html($button) . '</button></form>';
    }

    public static function search_results(array $atts = []): string
    {
        $atts = shortcode_atts(['limit' => 10, 'title' => '', 'show_title' => 'true'], $atts, 'm360_pel_search_results');
        $query_text = self::request_text('m360q');
        $is_en = self::is_en();
        $title = trim((string) $atts['title']) ?: ($is_en ? 'Search results' : 'Resultados da pesquisa');
        $show_title = self::enabled($atts['show_title']);
        self::enqueue_assets();
        $form = self::search_form([]);
        if ($query_text === '') {
            return '<section class="m360-pel-search-results">' . ($show_title ? '<h1>' . esc_html($title) . '</h1>' : '') . $form . '</section>';
        }
        $page = max(1, absint(self::request_text('m360_search_page')));
        $posts_per_page = max(1, min(24, absint($atts['limit'])));
        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            's' => $query_text,
            'posts_per_page' => $posts_per_page,
            'paged' => $page,
        ];
        if (function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
            if (is_string($language) && $language !== '') { $args['lang'] = $language; }
        }
        $results = new WP_Query($args);
        ob_start();
        echo '<section class="m360-pel-search-results">' . ($show_title ? '<h1>' . esc_html($title) . '</h1>' : '') . $form;
        echo '<p>' . esc_html($is_en ? 'Search term: ' : 'Termo pesquisado: ') . '<strong>' . esc_html($query_text) . '</strong></p>';
        if ($results->have_posts()) {
            echo '<div class="m360-pel-search-results__items">';
            while ($results->have_posts()) {
                $results->the_post();
                $categories = get_the_category();
                $category = $categories[0] ?? null;
                echo '<article class="m360-pel-search-results__item">';
                if (has_post_thumbnail()) { echo '<a href="' . esc_url(get_permalink()) . '">' . get_the_post_thumbnail(get_the_ID(), 'medium', ['loading' => 'lazy']) . '</a>'; }
                if ($category instanceof WP_Term) { echo '<p><a href="' . esc_url(get_category_link($category)) . '">' . esc_html($category->name) . '</a></p>'; }
                echo '<h2><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h2>';
                echo '<time datetime="' . esc_attr(get_the_date(DATE_W3C)) . '">' . esc_html(get_the_date()) . '</time>';
                echo '<p>' . esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 28)) . '</p></article>';
            }
            echo '</div>';
            $base = str_replace('999999999', '%#%', esc_url_raw(add_query_arg('m360_search_page', 999999999)));
            $links = paginate_links(['base' => $base, 'format' => '', 'current' => $page, 'total' => (int) $results->max_num_pages, 'type' => 'list', 'prev_text' => $is_en ? 'Previous' : 'Anterior', 'next_text' => $is_en ? 'Next' : 'Próxima']);
            if (is_string($links) && $links !== '') { echo '<nav class="m360-pel-search-results__pagination">' . $links . '</nav>'; }
        } else {
            echo '<p>' . esc_html($is_en ? 'No results found.' : 'Nenhum resultado encontrado.') . '</p>';
        }
        wp_reset_postdata();
        echo '</section>';
        return (string) ob_get_clean();
    }

    public static function activate(): void
    {
        if (get_option(self::OPTION, null) === null) {
            add_option(self::OPTION, self::defaults(), '', false);
        }
    }

    public static function menu(): void
    {
        add_submenu_page(
            'm360-dashboard',
            'PEL — Implantação Controlada',
            'PEL — Implantação',
            'manage_options',
            'm360-pel-controlled-deployment',
            [self::class, 'render']
        );
    }

    public static function apply_profile(): void
    {
        if (!current_user_can('manage_options')) { wp_die(esc_html__('Acesso negado.', 'm360-pel-deployment')); }
        check_admin_referer('m360_pel_apply_profile');

        if (!self::core_ready()) {
            self::redirect('core_missing');
        }
        if (!self::target_ready()) {
            self::redirect('target_mismatch');
        }
        if (!self::portable_origin_ready()) {
            self::redirect('origin_mismatch');
        }

        // The Site Profile is the only portable identity written by this add-on.
        // Runtime capabilities remain off: no public template ownership, Ads,
        // Newsletter or Consent output is enabled by this operation.
        M360_Site_Profile::update([
            'schema_version' => 2,
            'site_key' => 'portal-energia-limpa',
            'site_name' => 'Portal Energia Limpa',
            'vertical' => 'clean-energy-publisher',
            'default_locale' => 'pt-BR',
            'supported_locales' => ['pt-BR', 'en-US'],
            'branding' => [
                'primary_color' => '#ff3d00',
                'secondary_color' => '#fc893c',
            ],
            'runtime' => [
                'mode' => 'portable-safe',
                'capabilities' => [
                    'public_views' => false,
                    'ads_runtime' => false,
                    'ads_auto_insert' => false,
                    'newsletter_runtime' => false,
                    'consent_runtime' => false,
                ],
            ],
        ]);

        $registry = M360_Platform::instance()->registry();
        $editorial = $registry->set_enabled('editorial-layout-home', true);
        $discovery = $registry->set_enabled('content-discovery-seo', true);
        if (is_wp_error($editorial) || is_wp_error($discovery)) {
            self::redirect('module_error');
        }

        // Hybrid means explicitly placed shortcodes only; it never takes over
        // Elementor, the active theme, or any existing template.
        update_option('m360_editorial_settings', [
            'mode' => 'hybrid',
            'legacy_shortcodes' => 'precursor',
            'post_type' => 'post',
            'category_taxonomy' => 'category',
            'tag_taxonomy' => 'post_tag',
            'heading_level' => 2,
            'cache_ttl' => 600,
        ], false);

        // Discovery is enabled only to create isolated Core snapshots. It has
        // no automatic writer, no automatic injection and no legacy fallback.
        update_option('m360_discovery_settings', [
            'mode' => 'shadow',
            'legacy_read_fallback' => false,
            'post_types' => ['post'],
            'taxonomies' => ['category', 'post_tag'],
            'supported_locales' => ['pt-BR', 'en-US'],
            'generation_strategy' => 'manual',
            'renderer_canary_posts' => [],
            'public_render_mode' => 'shortcode',
            'contextual_links_max' => 3,
            'writer_mode' => 'manual',
        ], false);

        update_option(self::OPTION, array_merge(self::defaults(), [
            'applied' => true,
            'applied_at' => current_time('mysql'),
            'core_version' => defined('M360_CORE_VERSION') ? M360_CORE_VERSION : '',
        ]), false);

        self::redirect('applied');
    }

    public static function render(): void
    {
        if (!current_user_can('manage_options')) { return; }
        $state = wp_parse_args((array) get_option(self::OPTION, []), self::defaults());
        $profile = self::core_ready() ? M360_Site_Profile::get() : [];
        $runtime = (array) ($profile['runtime']['capabilities'] ?? []);
        $providers = self::providers();
        ?>
        <div class="wrap">
            <h1>PEL — Implantação Controlada</h1>
            <p>Perfil de configuração sem transporte de conteúdo, dados pessoais, campanhas, credenciais, segredos ou snapshots externos.</p>
            <?php $notice = self::request_text('m360_pel_notice'); ?>
            <?php if ($notice !== ''): ?>
                <div class="notice notice-<?php echo $notice === 'applied' ? 'success' : 'error'; ?> is-dismissible"><p><?php echo esc_html(self::notice($notice)); ?></p></div>
            <?php endif; ?>

            <h2>Pré-requisitos detectados</h2>
            <table class="widefat striped"><tbody>
                <?php foreach ($providers as $label => $available): ?>
                    <tr><th><?php echo esc_html($label); ?></th><td><?php echo $available ? 'Detectado' : 'Não detectado'; ?></td></tr>
                <?php endforeach; ?>
            </tbody></table>

            <h2>Gates aplicados</h2>
            <table class="widefat striped"><tbody>
                <tr><th>Site Profile</th><td><?php echo esc_html(($profile['site_key'] ?? '') === 'portal-energia-limpa' ? 'PEL · pt-BR / en-US' : 'Pendente'); ?></td></tr>
                <tr><th>Foundation</th><td>Ativo pelo M360 Core.</td></tr>
                <tr><th>Editorial</th><td>Shortcodes explícitos / Elementor; sem takeover de templates.</td></tr>
                <tr><th>Discovery &amp; SEO</th><td>Shadow manual; máximo de 3 links; sem injeção automática.</td></tr>
                <tr><th>Ads, Newsletter e Consent</th><td><?php echo !empty($runtime['ads_runtime']) || !empty($runtime['newsletter_runtime']) || !empty($runtime['consent_runtime']) ? 'Revisar: capability pública encontrada.' : 'Desligados neste perfil.'; ?></td></tr>
                <tr><th>Estado</th><td><?php echo !empty($state['applied']) ? 'Perfil aplicado em ' . esc_html((string) $state['applied_at']) : 'Não aplicado'; ?></td></tr>
            </tbody></table>

            <h2>Ativação controlada</h2>
            <p>Esta ação grava somente o Site Profile e as configurações de módulos acima. Não publica widgets, não modifica Elementor/Cream Magazine, não injeta anúncios, não cria formulários, não inicia cron de newsletter e não habilita CMP local.</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="m360_pel_apply_profile">
                <?php wp_nonce_field('m360_pel_apply_profile'); ?>
                <?php submit_button('Aplicar perfil PEL controlado', 'primary', 'submit', false, self::application_ready() ? [] : ['disabled' => 'disabled']); ?>
            </form>

            <h2>Blocos liberados para homologação manual</h2>
            <p><code>[m360_search_form variant="header"]</code> · <code>[m360_breadcrumb]</code> · <code>[m360_latest_news pagination="true"]</code> · <code>[m360_post_info]</code> · <code>[m360_editorial_widget id="..."]</code>.</p>
            <p>Para Discovery, valide primeiro os snapshots e os canários; somente após autorização use os shortcodes de renderer no template de post. A injeção automática permanece bloqueada.</p>
        </div>
        <?php
    }

    private static function defaults(): array
    {
        return ['version' => self::VERSION, 'applied' => false, 'applied_at' => '', 'core_version' => ''];
    }

    private static function core_ready(): bool
    {
        return class_exists('M360_Site_Profile') && class_exists('M360_Platform') && defined('M360_CORE_VERSION');
    }

    private static function target_ready(): bool
    {
        $host = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
        $allowed = in_array($host, ['energialimpa.live', 'www.energialimpa.live'], true)
            || str_ends_with($host, '.energialimpa.live');
        return (bool) apply_filters('m360_pel_deployment_target_ready', $allowed, $host);
    }

    private static function portable_origin_ready(): bool
    {
        if (!self::core_ready() || !class_exists('M360_Runtime_Profile')) { return false; }
        $diagnostics = M360_Runtime_Profile::diagnostics();
        $reason = sanitize_key((string) ($diagnostics['reason'] ?? ''));
        $profile = M360_Site_Profile::get();
        return $reason === 'fresh-installation-no-evidence'
            || (($profile['site_key'] ?? '') === 'portal-energia-limpa');
    }

    private static function application_ready(): bool
    {
        return self::core_ready() && self::target_ready() && self::portable_origin_ready();
    }

    private static function profile_applied(): bool
    {
        $state = get_option(self::OPTION, []);
        return is_array($state) && !empty($state['applied']);
    }

    private static function enabled(mixed $value): bool
    {
        return !in_array(strtolower(trim((string) $value)), ['0', 'false', 'no', 'off'], true);
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-pel-controlled-deployment', 'registered')) {
            wp_enqueue_style('m360-pel-controlled-deployment');
        }
    }

    private static function is_en(): bool
    {
        if (function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
            if (is_string($language) && $language !== '') { return strtolower($language) === 'en'; }
        }
        return str_starts_with((string) determine_locale(), 'en');
    }

    private static function providers(): array
    {
        return [
            'M360 Core' => self::core_ready(),
            'Elementor' => did_action('elementor/loaded') > 0 || defined('ELEMENTOR_VERSION'),
            'Polylang' => function_exists('pll_current_language'),
            'MailPoet' => class_exists('MailPoet\\API\\API') || defined('MAILPOET_VERSION'),
            'Tema ativo' => (string) get_option('stylesheet') !== '',
        ];
    }

    private static function notice(string $notice): string
    {
        return match ($notice) {
            'applied' => 'Perfil PEL aplicado. Continue somente pelos gates de homologação.',
            'core_missing' => 'M360 Core ativo é obrigatório antes de aplicar o perfil.',
            'module_error' => 'Não foi possível preparar os módulos. Revise o diagnóstico M360 antes de repetir.',
            'target_mismatch' => 'O perfil PEL só pode ser aplicado em energialimpa.live ou em staging explicitamente autorizado pelo filtro do deployment.',
            'origin_mismatch' => 'A origem não foi classificada como fresh-installation-no-evidence. O perfil não foi aplicado.',
            default => 'Operação não concluída.',
        };
    }

    private static function request_text(string $key): string
    {
        $value = $_GET[$key] ?? '';
        return is_scalar($value) ? sanitize_text_field(wp_unslash((string) $value)) : '';
    }

    private static function redirect(string $notice): void
    {
        wp_safe_redirect(add_query_arg(['page' => 'm360-pel-controlled-deployment', 'm360_pel_notice' => sanitize_key($notice)], admin_url('admin.php')));
        exit;
    }
}

register_activation_hook(__FILE__, ['M360_PEL_Controlled_Deployment', 'activate']);
add_action('plugins_loaded', ['M360_PEL_Controlled_Deployment', 'register'], 20);
