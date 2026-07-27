<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Platform_Admin
{
    private static ?M360_Module_Registry $registry = null;

    public static function register(M360_Module_Registry $registry): void
    {
        self::$registry = $registry;
        add_action('admin_menu', [self::class, 'menu']);
        add_filter('parent_file', [self::class, 'highlight_dashboard']);
        add_action('admin_post_m360_platform_save_profile', [self::class, 'save_profile']);
        add_action('admin_post_m360_platform_import_profile', [self::class, 'import_profile']);
        add_action('admin_post_m360_platform_export_profile', [self::class, 'export_profile']);
        add_action('admin_post_m360_platform_toggle_module', [self::class, 'toggle_module']);
        add_action('admin_post_m360_platform_save_editorial_widget', [self::class, 'save_editorial_widget']);
        add_action('admin_post_m360_platform_delete_editorial_widget', [self::class, 'delete_editorial_widget']);
    }

    public static function menu(): void
    {
        add_menu_page(
            'M360 Dashboard',
            'M360 Dashboard',
            'manage_options',
            'm360-dashboard',
            [self::class, 'render_dashboard'],
            'dashicons-screenoptions',
            57
        );
        add_submenu_page(
            'm360-dashboard',
            'M360 Dashboard',
            'M360 Dashboard',
            'manage_options',
            'm360-dashboard',
            [self::class, 'render_dashboard']
        );
        add_submenu_page(
            null,
            'Plataforma e Site Profile',
            'Plataforma e Site Profile',
            'manage_options',
            'm360-platform',
            [self::class, 'render']
        );
        add_submenu_page(
            null,
            'Widgets editoriais',
            'Widgets editoriais',
            'manage_options',
            'm360-editorial-widgets',
            [self::class, 'render_widgets']
        );
    }

    public static function highlight_dashboard(string $parent_file): string
    {
        $page = sanitize_key((string) ($_GET['page'] ?? ''));
        return in_array($page, self::internal_routes(), true) ? 'm360-dashboard' : $parent_file;
    }

    private static function internal_routes(): array
    {
        return [
            'm360-platform',
            'm360-editorial-widgets',
            'm360-content-discovery',
            'm360-ads-manager',
            'm360-ads-inventory',
            'm360-ads-adsense-ready',
            'm360-ads-slots',
            'm360-ads-campaigns',
            'm360-ads-campaign-new',
            'm360-ads-creatives',
            'm360-ads-creative-new',
            'm360-ads-header-delivery',
            'm360-ads-privacy-consent',
            'm360-newsletter-operations',
        ];
    }

    public static function render_dashboard(): void
    {
        if (!current_user_can('manage_options') || !self::$registry) { return; }
        $tabs = self::dashboard_tabs();
        $active = sanitize_key((string) ($_GET['tab'] ?? 'overview'));
        if (!isset($tabs[$active])) { $active = 'overview'; }
        $modules = self::$registry->health_report();
        $writer = class_exists('M360_Discovery_Scheduler') ? M360_Discovery_Scheduler::summary() : [];
        ?>
        <div class="wrap m360-dashboard">
            <div class="m360-dashboard__heading">
                <div>
                    <h1>M360 Dashboard</h1>
                    <p>Gestão unificada da Publisher Platform, sem dependência de tema ou Elementor.</p>
                </div>
                <span class="m360-dashboard__version">v<?php echo esc_html(M360_CORE_VERSION); ?></span>
            </div>
            <nav class="nav-tab-wrapper m360-dashboard__tabs" aria-label="Componentes M360">
                <?php foreach ($tabs as $key => $tab): ?>
                    <a class="nav-tab <?php echo $key === $active ? 'nav-tab-active' : ''; ?>"
                       href="<?php echo esc_url(self::dashboard_url($key)); ?>">
                        <?php echo esc_html($tab['label']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <?php self::render_dashboard_tab($active, $modules, $writer); ?>
        </div>
        <?php
    }

    private static function dashboard_tabs(): array
    {
        return [
            'overview' => ['label' => 'Visão Geral'],
            'editorial' => ['label' => 'Editorial'],
            'discovery' => ['label' => 'Discovery & SEO'],
            'ads' => ['label' => 'Ads'],
            'engagement' => ['label' => 'Newsletter'],
            'governance' => ['label' => 'Privacidade & Plataforma'],
        ];
    }

    private static function dashboard_url(string $tab): string
    {
        return add_query_arg(['page' => 'm360-dashboard', 'tab' => sanitize_key($tab)], admin_url('admin.php'));
    }

    private static function render_dashboard_tab(string $active, array $modules, array $writer): void
    {
        if ($active === 'overview') {
            $healthy = count(array_filter($modules, static fn(array $module): bool => in_array($module['status'], ['healthy', 'ok'], true)));
            $attention = count($modules) - $healthy;
            $coverage = (array) ($writer['coverage'] ?? []);
            $backfill = (array) ($writer['backfill'] ?? []);
            self::metric_cards([
                ['Módulos registrados', count($modules), 'Kernel e capacidades disponíveis'],
                ['Saudáveis', $healthy, 'Módulos com diagnóstico positivo'],
                ['Requerem atenção', $attention, 'Inclui módulos inativos ou em revisão'],
                ['Cobertura Discovery', isset($coverage['ratio']) ? number_format_i18n((float) $coverage['ratio'] * 100, 1) . '%' : '—', 'Snapshots próprios do Core'],
            ]);
            echo '<section class="m360-dashboard__section"><h2>Operação da plataforma</h2><div class="m360-dashboard__cards">';
            self::dashboard_card('Editorial', 'Layouts, newsroom, ticker e widgets editoriais.', 'm360-editorial-widgets', 'Gerenciar widgets');
            self::dashboard_card('Content Discovery & SEO', self::backfill_description($backfill), 'm360-content-discovery', 'Abrir operação');
            self::dashboard_card('Monetização', 'Slots, campanhas, criativos e prontidão AdSense.', 'm360-ads-manager', 'Gerenciar Ads');
            self::dashboard_card('Newsletter', 'Provider, sincronização, entrega e auditoria.', 'm360-newsletter-operations', 'Abrir Newsletter');
            self::dashboard_card('Privacy & Consent', 'Consent Mode, CMP e bloqueio de providers.', 'm360-ads-privacy-consent', 'Abrir governança');
            self::dashboard_card('Site Profile', 'Configurações portáteis, módulos e diagnóstico.', 'm360-platform', 'Abrir plataforma');
            echo '</div></section>';
            return;
        }

        $content = [
            'editorial' => [
                'title' => 'Editorial',
                'description' => 'Composição editorial independente de tema, com instâncias reutilizáveis e shortcodes públicos.',
                'cards' => [
                    ['Widgets editoriais', 'CRUD dos layouts e editorias distribuídos pela home.', 'm360-editorial-widgets', 'Gerenciar widgets'],
                ],
            ],
            'discovery' => [
                'title' => 'Content Discovery & SEO',
                'description' => 'Writer, backfill, snapshots, renderer semântico e diagnóstico operacional.',
                'cards' => [
                    ['Operação Discovery', self::backfill_description((array) ($writer['backfill'] ?? [])), 'm360-content-discovery', 'Abrir painel'],
                ],
            ],
            'ads' => [
                'title' => 'Ads',
                'description' => 'A infraestrutura definitiva de monetização. O inventário piloto foi retirado da gestão.',
                'cards' => [
                    ['Visão geral', 'Indicadores de slots, ocupação e campanhas.', 'm360-ads-manager', 'Abrir Ads'],
                    ['Slots', 'Inventário operacional e vínculos de campanha.', 'm360-ads-slots', 'Gerenciar slots'],
                    ['Campanhas', 'Campanhas, prioridades e períodos de entrega.', 'm360-ads-campaigns', 'Gerenciar campanhas'],
                    ['Criativos', 'Peças vinculadas às campanhas publicitárias.', 'm360-ads-creatives', 'Gerenciar criativos'],
                    ['AdSense Ready', 'Auditoria técnica de prontidão e cobertura.', 'm360-ads-adsense-ready', 'Ver checklist'],
                    ['Header Delivery', 'Orquestração de campanha, AdSense, busca ou recolhimento.', 'm360-ads-header-delivery', 'Configurar header'],
                ],
            ],
            'engagement' => [
                'title' => 'Newsletter',
                'description' => 'Captação, provider, sincronização, prontidão de entrega e auditoria.',
                'cards' => [
                    ['Newsletter', 'Configurações operacionais e integração com o provider.', 'm360-newsletter-operations', 'Abrir Newsletter'],
                ],
            ],
            'governance' => [
                'title' => 'Privacidade & Plataforma',
                'description' => 'Governança, consentimento, configuração portátil e saúde dos módulos.',
                'cards' => [
                    ['Privacy & Consent', 'Consent Mode v2, CMP e políticas de entrega.', 'm360-ads-privacy-consent', 'Abrir privacidade'],
                    ['Site Profile e módulos', 'Perfil portátil, versões, dependências e feature flags.', 'm360-platform', 'Abrir plataforma'],
                ],
            ],
        ];
        $section = $content[$active];
        echo '<section class="m360-dashboard__section"><h2>' . esc_html($section['title']) . '</h2><p class="m360-dashboard__lead">' . esc_html($section['description']) . '</p><div class="m360-dashboard__cards">';
        foreach ($section['cards'] as $card) { self::dashboard_card($card[0], $card[1], $card[2], $card[3]); }
        echo '</div></section>';
    }

    private static function metric_cards(array $metrics): void
    {
        echo '<div class="m360-dashboard__metrics">';
        foreach ($metrics as $metric) {
            echo '<section class="m360-dashboard__metric"><span>' . esc_html((string) $metric[0]) . '</span><strong>' . esc_html((string) $metric[1]) . '</strong><small>' . esc_html((string) $metric[2]) . '</small></section>';
        }
        echo '</div>';
    }

    private static function dashboard_card(string $title, string $description, string $page, string $action): void
    {
        $url = add_query_arg(['page' => sanitize_key($page)], admin_url('admin.php'));
        echo '<article class="m360-dashboard__card"><h3>' . esc_html($title) . '</h3><p>' . esc_html($description) . '</p><a class="button button-secondary" href="' . esc_url($url) . '">' . esc_html($action) . '</a></article>';
    }

    private static function backfill_description(array $backfill): string
    {
        $status = sanitize_key((string) ($backfill['status'] ?? 'idle'));
        $processed = absint($backfill['processed'] ?? 0);
        $failed = absint($backfill['failed'] ?? 0);
        return sprintf('Backfill %s: %s processados e %s falhas.', $status ?: 'idle', number_format_i18n($processed), number_format_i18n($failed));
    }

    public static function render(): void
    {
        if (!current_user_can('manage_options') || !self::$registry) { return; }
        $profile = M360_Site_Profile::get();
        $modules = self::$registry->health_report();
        $notice = sanitize_key((string) ($_GET['m360_notice'] ?? ''));
        if ($notice !== '') {
            $messages = [
                'widget_saved' => 'Widget editorial salvo.',
                'widget_deleted' => 'Widget editorial excluído.',
                'profile_saved' => 'Site Profile atualizado.',
                'profile_imported' => 'Site Profile importado e validado.',
                'module_updated' => 'Estado do módulo atualizado.',
                'error' => 'A operação não pôde ser concluída.',
            ];
            echo '<div class="notice ' . ($notice === 'error' ? 'notice-error' : 'notice-success') . ' is-dismissible"><p>' . esc_html($messages[$notice] ?? 'Operação concluída.') . '</p></div>';
        }
        ?>
        <div class="wrap">
            <h1>M360 Publisher Platform</h1>
            <p>Fundação modular da linha v0.7.x. Editorial incorporado; Content Discovery em shadow mode com storage próprio. Nenhum precursor é desativado automaticamente.</p>

            <h2>Diagnóstico da plataforma</h2>
            <?php $runtime_diagnostics = M360_Runtime_Profile::diagnostics(); ?>
            <table class="widefat striped" style="max-width:1100px">
                <tbody>
                    <tr><th style="width:260px">M360 Core</th><td><?php echo esc_html(M360_CORE_VERSION); ?></td></tr>
                    <tr><th>WordPress</th><td><?php echo esc_html((string) get_bloginfo('version')); ?></td></tr>
                    <tr><th>PHP</th><td><?php echo esc_html(PHP_VERSION); ?></td></tr>
                    <tr><th>Política de implantação</th><td><code><?php echo esc_html((string) $profile['runtime']['mode']); ?></code></td></tr>
                    <tr><th>Origem da política</th><td><code><?php echo esc_html((string) $runtime_diagnostics['reason']); ?></code></td></tr>
                    <tr><th>Evidências da instalação</th><td><?php echo esc_html($runtime_diagnostics['evidence'] ? implode(', ', $runtime_diagnostics['evidence']) : ($runtime_diagnostics['reason'] === 'fresh-installation-no-evidence' ? 'Nenhuma — instalação nova' : 'Nenhuma registrada — perfil existente preservado')); ?></td></tr>
                    <tr><th>Classificação inicial</th><td><?php echo esc_html(trim($runtime_diagnostics['classified_version'] . ' · ' . $runtime_diagnostics['classified_at'], ' ·')); ?></td></tr>
                    <tr><th>Polylang</th><td><?php echo esc_html(function_exists('pll_current_language') ? 'Detectado' : 'Não detectado'); ?></td></tr>
                    <tr><th>MailPoet</th><td><?php echo esc_html(class_exists('MailPoet\\API\\API') ? 'Detectado' : 'Não detectado'); ?></td></tr>
                    <tr><th>Home Editorial precursor</th><td><?php echo esc_html(class_exists('M360_Home_Editorial') ? 'Ativo e preservado' : 'Não detectado'); ?></td></tr>
                    <tr><th>Semantic Relations precursor</th><td><?php echo esc_html(class_exists('M360_Semantic_Relations_Plugin') ? 'Ativo e preservado' : 'Não detectado'); ?></td></tr>
                </tbody>
            </table>

            <h2>Módulos registrados</h2>
            <table class="widefat striped" style="max-width:1100px">
                <thead><tr><th>Módulo</th><th>Versão</th><th>Schema</th><th>Dependências</th><th>Estado</th><th>Saúde</th><th>Ação</th></tr></thead>
                <tbody>
                <?php foreach ($modules as $module): ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($module['label']); ?></strong><br>
                            <code><?php echo esc_html($module['id']); ?></code><br>
                            <small>Permissões: <?php echo esc_html($module['capabilities'] ? implode(', ', $module['capabilities']) : '—'); ?></small><br>
                            <small>Configurações: <?php echo esc_html($module['settings'] ? implode(', ', $module['settings']) : '—'); ?></small><br>
                            <small>Assets: <?php echo esc_html(($module['assets']['styles'] || $module['assets']['scripts']) ? implode(', ', array_merge($module['assets']['styles'], $module['assets']['scripts'])) : '—'); ?></small>
                        </td>
                        <td><?php echo esc_html($module['version']); ?></td>
                        <td><?php echo esc_html($module['schema_version']); ?></td>
                        <td><?php echo esc_html($module['dependencies'] ? implode(', ', $module['dependencies']) : '—'); ?></td>
                        <td><?php echo esc_html($module['enabled'] ? ($module['required'] ? 'Ativo · obrigatório' : 'Ativo') : 'Inativo'); ?></td>
                        <td><strong><?php echo esc_html($module['status']); ?></strong><br><?php echo esc_html($module['message']); ?></td>
                        <td>
                            <?php if ($module['required']): ?>—
                            <?php else: ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                    <input type="hidden" name="action" value="m360_platform_toggle_module">
                                    <input type="hidden" name="module_id" value="<?php echo esc_attr($module['id']); ?>">
                                    <input type="hidden" name="enabled" value="<?php echo $module['enabled'] ? '0' : '1'; ?>">
                                    <?php wp_nonce_field('m360_platform_toggle_' . $module['id']); ?>
                                    <?php submit_button($module['enabled'] ? 'Desativar' : 'Ativar', 'secondary small', 'submit', false); ?>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Site Profile</h2>
            <p>Somente configurações portáveis. Conteúdo, dados pessoais, campanhas, credenciais e segredos não fazem parte do perfil.</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:900px">
                <input type="hidden" name="action" value="m360_platform_save_profile">
                <?php wp_nonce_field('m360_platform_save_profile'); ?>
                <table class="form-table"><tbody>
                    <tr><th><label for="m360-site-key">Chave do portal</label></th><td><input class="regular-text" id="m360-site-key" name="profile[site_key]" value="<?php echo esc_attr($profile['site_key']); ?>" required><p class="description">Identificador técnico sem espaços.</p></td></tr>
                    <tr><th><label for="m360-site-name">Nome do portal</label></th><td><input class="regular-text" id="m360-site-name" name="profile[site_name]" value="<?php echo esc_attr($profile['site_name']); ?>" required></td></tr>
                    <tr><th><label for="m360-vertical">Vertical</label></th><td><input class="regular-text" id="m360-vertical" name="profile[vertical]" value="<?php echo esc_attr($profile['vertical']); ?>" required><p class="description">Ex.: publisher, sports, clean-energy.</p></td></tr>
                    <tr><th><label for="m360-default-locale">Idioma padrão</label></th><td><input class="regular-text" id="m360-default-locale" name="profile[default_locale]" value="<?php echo esc_attr($profile['default_locale']); ?>" required></td></tr>
                    <tr><th><label for="m360-supported-locales">Idiomas suportados</label></th><td><input class="regular-text" id="m360-supported-locales" name="profile[supported_locales]" value="<?php echo esc_attr(implode(', ', $profile['supported_locales'])); ?>" required><p class="description">Separados por vírgula; exemplo: pt-BR, en-US.</p></td></tr>
                    <tr>
                        <th><label for="m360-runtime-mode">Política de implantação</label></th>
                        <td>
                            <select id="m360-runtime-mode" name="profile[runtime][mode]">
                                <option value="portable-safe" <?php selected($profile['runtime']['mode'], 'portable-safe'); ?>>Portable safe — sem ownership público automático</option>
                                <option value="legacy-compatible" <?php selected($profile['runtime']['mode'], 'legacy-compatible'); ?>>Legacy compatible — preserva ambiente homologado</option>
                            </select>
                            <p class="description">Novos portais devem permanecer em portable-safe até concluir preflight, shadow mode e autorização de cutover.</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Capacidades públicas</th>
                        <td>
                            <?php
                            $runtime_labels = [
                                'public_views' => 'Assumir templates de busca, autor, categoria, tag e data',
                                'ads_runtime' => 'Disponibilizar renderização e shortcodes de Ads',
                                'ads_auto_insert' => 'Inserir Ads automaticamente no conteúdo',
                                'newsletter_runtime' => 'Ativar formulários, endpoint, cron e renderização da Newsletter',
                                'consent_runtime' => 'Ativar sinais e interface pública de Consent',
                            ];
                            foreach ($runtime_labels as $runtime_key => $runtime_label):
                            ?>
                                <label style="display:block;margin:0 0 8px">
                                    <input type="checkbox" name="profile[runtime][capabilities][<?php echo esc_attr($runtime_key); ?>]" value="1" <?php checked(!empty($profile['runtime']['capabilities'][$runtime_key])); ?>>
                                    <?php echo esc_html($runtime_label); ?>
                                </label>
                            <?php endforeach; ?>
                            <p class="description">Cada capacidade exige homologação e rollback próprios. Desmarcada, permanece sem hooks públicos automáticos.</p>
                        </td>
                    </tr>
                </tbody></table>
                <?php submit_button('Salvar Site Profile'); ?>
            </form>

            <div style="display:grid;grid-template-columns:minmax(320px,1fr) minmax(320px,1fr);gap:24px;max-width:1100px">
                <section>
                    <h2>Exportar perfil</h2>
                    <p>Gera JSON sem credenciais ou dados editoriais.</p>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="m360_platform_export_profile">
                        <?php wp_nonce_field('m360_platform_export_profile'); ?>
                        <?php submit_button('Baixar JSON', 'secondary', 'submit', false); ?>
                    </form>
                </section>
                <section>
                    <h2>Importar perfil</h2>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="m360_platform_import_profile">
                        <?php wp_nonce_field('m360_platform_import_profile'); ?>
                        <textarea name="profile_json" rows="8" class="large-text code" required></textarea>
                        <?php submit_button('Validar e importar JSON', 'secondary', 'submit', false); ?>
                    </form>
                </section>
            </div>
        </div>
        <?php
    }

    public static function save_profile(): void
    {
        self::guard('m360_platform_save_profile');
        $input = isset($_POST['profile']) && is_array($_POST['profile']) ? wp_unslash($_POST['profile']) : [];
        M360_Site_Profile::update($input);
        self::redirect('profile_saved');
    }

    public static function import_profile(): void
    {
        self::guard('m360_platform_import_profile');
        $json = isset($_POST['profile_json']) ? (string) wp_unslash($_POST['profile_json']) : '';
        $result = M360_Site_Profile::import_json($json);
        self::redirect(is_wp_error($result) ? 'error' : 'profile_imported');
    }

    public static function export_profile(): void
    {
        self::guard('m360_platform_export_profile');
        nocache_headers();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename=m360-site-profile-' . sanitize_file_name(M360_Site_Profile::get()['site_key']) . '.json');
        echo M360_Site_Profile::export_json(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }

    public static function toggle_module(): void
    {
        if (!current_user_can('manage_options') || !self::$registry) { wp_die('Sem permissão.'); }
        $id = sanitize_key((string) ($_POST['module_id'] ?? ''));
        check_admin_referer('m360_platform_toggle_' . $id);
        $result = self::$registry->set_enabled($id, !empty($_POST['enabled']));
        self::redirect(is_wp_error($result) ? 'error' : 'module_updated');
    }

    public static function save_editorial_widget(): void
    {
        self::guard('m360_platform_save_editorial_widget');
        $input = isset($_POST['widget']) && is_array($_POST['widget']) ? wp_unslash($_POST['widget']) : [];
        $original_id = sanitize_key((string) ($_POST['original_id'] ?? ''));
        $result = M360_Editorial_Widgets::save($input, $original_id);
        self::redirect(is_wp_error($result) ? 'error' : 'widget_saved', 'm360-editorial-widgets');
    }

    public static function delete_editorial_widget(): void
    {
        $id = sanitize_key((string) ($_POST['widget_id'] ?? ''));
        self::guard('m360_platform_delete_editorial_widget_' . $id);
        M360_Editorial_Widgets::delete($id);
        self::redirect('widget_deleted', 'm360-editorial-widgets');
    }

    public static function render_widgets(): void
    {
        if (!current_user_can('manage_options')) { return; }
        $notice = sanitize_key((string) ($_GET['m360_notice'] ?? ''));
        if ($notice !== '') {
            $messages = ['widget_saved'=>'Widget editorial salvo.','widget_deleted'=>'Widget editorial excluído.','error'=>'A operação não pôde ser concluída.'];
            echo '<div class="notice ' . ($notice === 'error' ? 'notice-error' : 'notice-success') . ' is-dismissible"><p>' . esc_html($messages[$notice] ?? 'Operação concluída.') . '</p></div>';
        }
        echo '<div class="wrap"><h1>Widgets editoriais</h1>';
        M360_Editorial_Widgets::render_admin();
        echo '</div>';
    }

    private static function guard(string $nonce): void
    {
        if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
        check_admin_referer($nonce);
    }

    private static function redirect(string $notice, string $page = 'm360-platform'): void
    {
        wp_safe_redirect(add_query_arg(['page' => sanitize_key($page), 'm360_notice' => sanitize_key($notice)], admin_url('admin.php')));
        exit;
    }
}
