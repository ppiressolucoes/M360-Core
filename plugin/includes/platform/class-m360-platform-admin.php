<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Platform_Admin
{
    private static ?M360_Module_Registry $registry = null;

    public static function register(M360_Module_Registry $registry): void
    {
        self::$registry = $registry;
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_post_m360_platform_save_profile', [self::class, 'save_profile']);
        add_action('admin_post_m360_platform_import_profile', [self::class, 'import_profile']);
        add_action('admin_post_m360_platform_export_profile', [self::class, 'export_profile']);
        add_action('admin_post_m360_platform_toggle_module', [self::class, 'toggle_module']);
    }

    public static function menu(): void
    {
        add_menu_page(
            'M360 Platform',
            'M360 Platform',
            'manage_options',
            'm360-platform',
            [self::class, 'render'],
            'dashicons-admin-site-alt3',
            57
        );
    }

    public static function render(): void
    {
        if (!current_user_can('manage_options') || !self::$registry) { return; }
        $profile = M360_Site_Profile::get();
        $modules = self::$registry->health_report();
        $notice = sanitize_key((string) ($_GET['m360_notice'] ?? ''));
        if ($notice !== '') {
            $messages = [
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
            <p>Fundação modular da linha v0.7.x. Esta versão não absorve nem desativa os plugins precursores.</p>

            <h2>Diagnóstico da plataforma</h2>
            <table class="widefat striped" style="max-width:1100px">
                <tbody>
                    <tr><th style="width:260px">M360 Core</th><td><?php echo esc_html(M360_CORE_VERSION); ?></td></tr>
                    <tr><th>WordPress</th><td><?php echo esc_html((string) get_bloginfo('version')); ?></td></tr>
                    <tr><th>PHP</th><td><?php echo esc_html(PHP_VERSION); ?></td></tr>
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

    private static function guard(string $nonce): void
    {
        if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
        check_admin_referer($nonce);
    }

    private static function redirect(string $notice): void
    {
        wp_safe_redirect(add_query_arg(['page' => 'm360-platform', 'm360_notice' => sanitize_key($notice)], admin_url('admin.php')));
        exit;
    }
}
