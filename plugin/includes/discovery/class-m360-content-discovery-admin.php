<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Content_Discovery_Admin
{
    private static ?M360_Module_Registry $registry = null;

    public static function register(M360_Module_Registry $registry): void
    {
        self::$registry = $registry;
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_post_m360_discovery_generate_shadow', [self::class, 'generate_shadow']);
        add_action('admin_post_m360_discovery_save_canary', [self::class, 'save_canary']);
        add_action('admin_post_m360_discovery_save_renderer', [self::class, 'save_renderer']);
        add_action('admin_post_m360_discovery_save_writer', [self::class, 'save_writer']);
        add_action('admin_post_m360_discovery_start_backfill', [self::class, 'start_backfill']);
        add_action('admin_post_m360_discovery_stop_backfill', [self::class, 'stop_backfill']);
    }

    public static function menu(): void
    {
        add_submenu_page(
            null,
            'Content Discovery & SEO',
            'Content Discovery',
            'manage_options',
            'm360-content-discovery',
            [self::class, 'render']
        );
    }

    public static function render(): void
    {
        if (!current_user_can('manage_options') || !self::$registry) { return; }
        $module = self::$registry->get('content-discovery-seo');
        $enabled = self::$registry->is_enabled('content-discovery-seo');
        $summary = M360_Content_Discovery_Module::adapter()->summary();
        $core_summary = M360_Discovery_DB::summary();
        $settings = M360_Content_Discovery_Module::settings();
        $writer_summary = M360_Discovery_Scheduler::summary();
        $health = $enabled && $module ? $module->health() : ['status' => 'disabled', 'message' => 'Modulo desativado; preflight somente leitura disponivel.'];
        $notice = sanitize_key((string) ($_GET['m360_discovery_notice'] ?? ''));
        $comparison_post_id = max(0, (int) ($_GET['m360_compare_post'] ?? 0));
        $comparison_locale = sanitize_text_field((string) ($_GET['m360_compare_locale'] ?? ''));
        $comparison = $comparison_post_id > 0 ? M360_Content_Discovery_Module::comparator()->compare($comparison_post_id, $comparison_locale) : null;
        ?>
        <?php
        $coverage = (array) $writer_summary['coverage'];
        $backfill = (array) $writer_summary['backfill'];
        $queue = (array) $writer_summary['queue'];
        $coverage_percent = round(((float) ($coverage['ratio'] ?? 0)) * 100, 1);
        $backfill_status = sanitize_key((string) ($backfill['status'] ?? 'idle'));
        ?>
        <div class="wrap m360-discovery-admin">
            <header class="m360-discovery-admin__header">
                <div><h1>M360 Content Discovery &amp; SEO</h1><p>Operação do renderer, writer assíncrono e cobertura semântica do Core.</p></div>
                <span class="m360-discovery-admin__version">v<?php echo esc_html(M360_CORE_VERSION); ?></span>
            </header>

            <?php if ($notice !== ''): ?><div class="notice <?php echo in_array($notice, ['success', 'queued', 'canary_saved', 'renderer_saved', 'writer_saved', 'backfill_started', 'backfill_stopped'], true) ? 'notice-success' : 'notice-error'; ?> inline"><p><?php echo esc_html(self::notice_message($notice)); ?></p></div><?php endif; ?>

            <div class="m360-discovery-admin__cards">
                <article class="m360-discovery-admin__metric"><span>Saúde</span><strong class="is-<?php echo esc_attr((string) $health['status']); ?>"><?php echo esc_html((string) $health['status']); ?></strong><small><?php echo esc_html($enabled ? 'Módulo ativo' : 'Módulo inativo'); ?></small></article>
                <article class="m360-discovery-admin__metric"><span>Cobertura</span><strong><?php echo esc_html((string) $coverage_percent); ?>%</strong><small><?php echo esc_html((string) ($coverage['covered'] ?? 0)); ?> de <?php echo esc_html((string) ($coverage['published'] ?? 0)); ?> posts</small></article>
                <article class="m360-discovery-admin__metric"><span>Backfill</span><strong><?php echo esc_html($backfill_status); ?></strong><small><?php echo esc_html((string) ($backfill['processed'] ?? 0)); ?> processados · <?php echo esc_html((string) ($backfill['failed'] ?? 0)); ?> falhas</small></article>
                <article class="m360-discovery-admin__metric"><span>Ownership</span><strong><?php echo esc_html((string) $settings['writer_mode']); ?></strong><small>Renderer <?php echo esc_html((string) $settings['public_render_mode']); ?> · legado <?php echo esc_html(!empty($summary['precursor']['active']) ? 'ativo' : 'inativo'); ?></small></article>
            </div>

            <div class="m360-discovery-admin__layout">
                <main>
                    <section class="m360-discovery-admin__panel">
                        <div class="m360-discovery-admin__panel-head"><div><h2>Writer e backfill</h2><p>Operação principal da transferência para o Core.</p></div><span class="m360-discovery-admin__status is-<?php echo esc_attr($backfill_status); ?>"><?php echo esc_html($backfill_status); ?></span></div>
                        <div class="m360-discovery-admin__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr((string) $coverage_percent); ?>"><span style="width:<?php echo esc_attr((string) min(100, max(0, $coverage_percent))); ?>%"></span></div>
                        <div class="m360-discovery-admin__numbers">
                            <div><span>Publicados</span><strong><?php echo esc_html((string) ($coverage['published'] ?? 0)); ?></strong></div>
                            <div><span>Cobertos</span><strong><?php echo esc_html((string) ($coverage['covered'] ?? 0)); ?></strong></div>
                            <div><span>Ausentes</span><strong><?php echo esc_html((string) ($coverage['missing'] ?? 0)); ?></strong></div>
                            <div><span>Gerados</span><strong><?php echo esc_html((string) ($backfill['generated'] ?? 0)); ?></strong></div>
                            <div><span>Inalterados</span><strong><?php echo esc_html((string) ($backfill['unchanged'] ?? 0)); ?></strong></div>
                            <div><span>Falhas</span><strong><?php echo esc_html((string) ($backfill['failed'] ?? 0)); ?></strong></div>
                        </div>
                        <div class="m360-discovery-admin__actions">
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="m360_discovery_save_writer"><?php wp_nonce_field('m360_discovery_save_writer'); ?><label><span>Modo do writer</span><select name="writer_mode"><option value="manual" <?php selected($settings['writer_mode'], 'manual'); ?>>Manual — rollback</option><option value="automatic" <?php selected($settings['writer_mode'], 'automatic'); ?>>Automatic — Core writer</option></select></label><button type="submit" class="button">Salvar</button></form>
                            <?php if ($backfill_status === 'running'): ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="m360_discovery_stop_backfill"><?php wp_nonce_field('m360_discovery_stop_backfill'); ?><button type="submit" class="button">Parar backfill</button></form>
                            <?php else: ?>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="m360_discovery_start_backfill"><?php wp_nonce_field('m360_discovery_start_backfill'); ?><button type="submit" class="button button-primary"><?php echo esc_html($backfill_status === 'completed' ? 'Reexecutar backfill' : 'Iniciar backfill'); ?></button></form>
                            <?php endif; ?>
                        </div>
                        <div class="m360-discovery-admin__queue"><strong>Fila recente</strong><?php foreach ($queue as $state => $total): ?><span class="is-<?php echo esc_attr((string) $state); ?>"><?php echo esc_html((string) $state); ?> <b><?php echo esc_html((string) $total); ?></b></span><?php endforeach; ?></div>
                    </section>

                    <section class="m360-discovery-admin__panel">
                        <div class="m360-discovery-admin__panel-head"><div><h2>Renderer público</h2><p>Composição automática e limite de links contextuais.</p></div><span class="m360-discovery-admin__status is-<?php echo esc_attr((string) $settings['public_render_mode']); ?>"><?php echo esc_html((string) $settings['public_render_mode']); ?></span></div>
                        <form class="m360-discovery-admin__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                            <input type="hidden" name="action" value="m360_discovery_save_renderer"><?php wp_nonce_field('m360_discovery_save_renderer'); ?>
                            <label><span>Modo público</span><select name="public_render_mode"><option value="shortcode" <?php selected($settings['public_render_mode'], 'shortcode'); ?>>Shortcode — rollback</option><option value="automatic" <?php selected($settings['public_render_mode'], 'automatic'); ?>>Automatic — Core renderer</option></select></label>
                            <label><span>Links contextuais</span><select name="contextual_links_max"><?php for ($max = 0; $max <= 3; $max++): ?><option value="<?php echo esc_attr((string) $max); ?>" <?php selected((int) $settings['contextual_links_max'], $max); ?>><?php echo esc_html((string) $max); ?></option><?php endfor; ?></select></label>
                            <button type="submit" class="button button-primary">Salvar renderer</button>
                        </form>
                    </section>

                    <details class="m360-discovery-admin__panel m360-discovery-admin__details" <?php echo is_array($comparison) ? 'open' : ''; ?>>
                        <summary><span><strong>Diagnósticos e execução manual</strong><small>Comparator, snapshot isolado e contagens do Core</small></span></summary>
                        <div class="m360-discovery-admin__details-body">
                            <h3>Comparar snapshots</h3>
                            <form class="m360-discovery-admin__form" method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>"><input type="hidden" name="page" value="m360-content-discovery"><label><span>ID do post</span><input type="number" min="1" name="m360_compare_post" value="<?php echo esc_attr((string) $comparison_post_id); ?>" required></label><label><span>Locale</span><select name="m360_compare_locale"><option value="">Detectar pelo post</option><?php foreach ((array) $settings['supported_locales'] as $locale): ?><option value="<?php echo esc_attr((string) $locale); ?>" <?php selected($comparison_locale, $locale); ?>><?php echo esc_html((string) $locale); ?></option><?php endforeach; ?></select></label><button type="submit" class="button">Comparar</button></form>
                            <?php if (is_array($comparison)): self::render_comparison($comparison); endif; ?>
                            <h3>Gerar snapshot isolado</h3>
                            <form class="m360-discovery-admin__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="m360_discovery_generate_shadow"><?php wp_nonce_field('m360_discovery_generate_shadow'); ?><label><span>ID do post</span><input type="number" min="1" name="post_id" required></label><label><span>Execução</span><select name="strategy"><option value="now">Agora</option><option value="async">WP-Cron</option></select></label><button type="submit" class="button">Gerar snapshot</button></form>
                            <h3>Storage e agregados do Core</h3>
                            <p>Schema <code><?php echo esc_html((string) $core_summary['storage']['schema_version']); ?></code> · Runs <?php echo esc_html((string) $core_summary['storage']['tables']['runs']['engine']); ?> · Relations <?php echo esc_html((string) $core_summary['storage']['tables']['relations']['engine']); ?></p>
                            <?php self::count_table((array) $core_summary['runs'], ['locale'=>'Locale','status'=>'Estado']); ?>
                            <?php self::count_table((array) $core_summary['relations'], ['locale'=>'Locale','relation_kind'=>'Tipo','status'=>'Estado']); ?>
                        </div>
                    </details>
                </main>

                <aside>
                    <section class="m360-discovery-admin__side"><h2>Próximo gate</h2><ol><li>Backfill <code>completed</code></li><li>Fila sem itens presos</li><li>Falhas zeradas ou explicadas</li><li>PT-BR e EN-US homologados</li></ol></section>
                    <section class="m360-discovery-admin__side"><h2>Rollback</h2><p>Writer em <code>manual</code> e renderer em <code>shortcode</code>. Nenhuma tabela é removida.</p></section>
                    <?php if (!empty($summary['warnings'])): ?><section class="m360-discovery-admin__side is-warning"><h2>Alertas</h2><ul><?php foreach ((array) $summary['warnings'] as $warning): ?><li><?php echo esc_html((string) $warning); ?></li><?php endforeach; ?></ul></section><?php endif; ?>
                </aside>
            </div>

            <details class="m360-discovery-admin__panel m360-discovery-admin__details m360-discovery-admin__advanced">
                <summary><span><strong>Avançado e compatibilidade legada</strong><small>Storage, flags, cron e snapshot técnico</small></span></summary>
                <div class="m360-discovery-admin__details-body">
                    <h3>Storage legado</h3>
                    <table class="widefat striped"><thead><tr><th>Tabela</th><th>Existe</th><th>Schema</th><th>Engine</th><th>Collation</th><th>Linhas</th><th>Colunas ausentes</th></tr></thead><tbody><?php self::table_row('Runs', (array) $summary['tables']['runs']); ?><?php self::table_row('Relations', (array) $summary['tables']['relations']); ?></tbody></table>
                    <h3>Flags não secretas</h3><?php self::key_value_table((array) $summary['options'], 'Opção', 'Valor'); ?>
                    <h3>Eventos cron legados</h3><?php self::key_value_table((array) $summary['cron'], 'Hook', 'Eventos'); ?>
                    <h3>Agregados legados</h3><?php self::count_table((array) $summary['run_counts'], ['language'=>'Idioma','status'=>'Estado']); ?><?php self::count_table((array) $summary['relation_counts'], ['language'=>'Idioma','relation_kind'=>'Tipo','status'=>'Estado']); ?>
                    <h3>Snapshot técnico</h3><textarea class="large-text code" rows="14" readonly><?php echo esc_textarea((string) wp_json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)); ?></textarea>
                </div>
            </details>
        </div>
        <?php
    }

    public static function generate_shadow(): void
    {
        if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
        check_admin_referer('m360_discovery_generate_shadow');
        $post_id = max(0, (int) ($_POST['post_id'] ?? 0));
        $strategy = sanitize_key((string) ($_POST['strategy'] ?? 'now'));
        if (!self::$registry || !self::$registry->is_enabled('content-discovery-seo') || M360_Content_Discovery_Module::settings()['mode'] !== 'shadow' || $post_id < 1) {
            self::redirect_notice('blocked');
        }
        $generator = new M360_Shadow_Generator();
        if ($strategy === 'async') { self::redirect_notice($generator->enqueue($post_id) ? 'queued' : 'error'); }
        $result = $generator->generate($post_id, 'manual_admin');
        self::redirect_notice(!empty($result['ok']) ? 'success' : sanitize_key((string) ($result['code'] ?? 'error')));
    }

    public static function save_canary(): void
    {
        if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
        check_admin_referer('m360_discovery_save_canary');
        $raw = sanitize_text_field((string) ($_POST['canary_posts'] ?? ''));
        $ids = preg_split('/[^0-9]+/', $raw) ?: [];
        $ids = array_slice(array_values(array_unique(array_filter(array_map('intval', $ids), static fn(int $id): bool => $id > 0))), 0, 20);
        update_option('m360_discovery_canary_posts', $ids, false);
        wp_cache_delete('m360_discovery_canary_posts', 'options');
        $persisted = array_values(array_map('intval', (array) get_option('m360_discovery_canary_posts', [])));
        self::redirect_notice($persisted === $ids ? 'canary_saved' : 'canary_save_failed');
    }

    public static function save_renderer(): void
    {
        if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
        check_admin_referer('m360_discovery_save_renderer');
        $mode = sanitize_key((string) ($_POST['public_render_mode'] ?? 'shortcode'));
        $max_links = max(0, min(3, (int) ($_POST['contextual_links_max'] ?? 3)));
        self::redirect_notice(M360_Content_Discovery_Module::update_renderer_settings($mode, $max_links) ? 'renderer_saved' : 'renderer_save_failed');
    }

    public static function save_writer(): void
    {
        if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
        check_admin_referer('m360_discovery_save_writer');
        $mode = sanitize_key((string) ($_POST['writer_mode'] ?? 'manual'));
        $saved = M360_Content_Discovery_Module::update_writer_mode($mode);
        if ($saved && $mode === 'manual') { M360_Discovery_Scheduler::stop_backfill(); }
        self::redirect_notice($saved ? 'writer_saved' : 'writer_save_failed');
    }

    public static function start_backfill(): void
    {
        if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
        check_admin_referer('m360_discovery_start_backfill');
        self::redirect_notice(M360_Discovery_Scheduler::start_backfill() ? 'backfill_started' : 'backfill_start_failed');
    }

    public static function stop_backfill(): void
    {
        if (!current_user_can('manage_options')) { wp_die('Sem permissão.'); }
        check_admin_referer('m360_discovery_stop_backfill');
        M360_Discovery_Scheduler::stop_backfill();
        self::redirect_notice('backfill_stopped');
    }

    private static function redirect_notice(string $notice): void
    {
        wp_safe_redirect(add_query_arg(['page'=>'m360-content-discovery','m360_discovery_notice'=>sanitize_key($notice)], admin_url('admin.php')));
        exit;
    }

    private static function notice_message(string $notice): string
    {
        $messages = [
            'success'=>'Snapshot shadow gerado e promovido no storage próprio.',
            'canary_saved'=>'Lista de posts do Renderer Canary atualizada. Nenhum renderer legado foi alterado.',
            'canary_save_failed'=>'A lista do Renderer Canary não foi persistida. Nenhuma saída pública foi liberada.',
            'renderer_saved'=>'Modo público do renderer atualizado. Nenhuma configuração do precursor foi alterada.',
            'renderer_save_failed'=>'Não foi possível persistir o modo do renderer; a configuração anterior foi preservada.',
            'writer_saved'=>'Modo do writer do Core atualizado. O precursor e suas tabelas permanecem inalterados.',
            'writer_save_failed'=>'Não foi possível persistir o modo do writer; a configuração anterior foi preservada.',
            'backfill_started'=>'Backfill do Core iniciado em lotes de 10 posts pelo WP-Cron.',
            'backfill_start_failed'=>'O backfill exige módulo em shadow e writer automático.',
            'backfill_stopped'=>'Backfill interrompido de forma reversível; snapshots ativos foram preservados.',
            'queued'=>'Geração shadow enfileirada no WP-Cron.',
            'invalid_post'=>'Post inexistente, não publicado ou fora dos tipos configurados.',
            'unsupported_locale'=>'Locale ausente ou não suportado; nenhum fallback cruzado foi aplicado.',
            'no_candidates'=>'Nenhuma relação encontrada; snapshot anterior preservado.',
            'promotion_failed'=>'Falha na promoção; snapshot anterior preservado.',
            'blocked'=>'A operação exige módulo ativo, modo shadow e ID de post válido.',
            'shadow_disabled'=>'A geração foi bloqueada porque o modo shadow não está ativo.',
            'error'=>'Não foi possível iniciar a geração shadow.',
        ];
        return $messages[$notice] ?? 'A geração foi concluída com diagnóstico registrado.';
    }

    /** @param array<string,mixed> $comparison */
    private static function render_comparison(array $comparison): void
    {
        $status = sanitize_key((string) ($comparison['status'] ?? 'blocked'));
        $class = $status === 'eligible' ? 'notice-success' : ($status === 'review' ? 'notice-warning' : 'notice-error');
        $summary = (array) ($comparison['summary'] ?? []);
        ?>
        <div class="notice <?php echo esc_attr($class); ?> inline" style="margin-top:14px"><p><strong>Resultado: <?php echo esc_html($status); ?></strong> — <?php echo esc_html((string) ($comparison['message'] ?? '')); ?></p></div>
        <table class="widefat striped" style="max-width:1100px"><tbody>
            <tr><th style="width:280px">Post / locale</th><td><?php echo esc_html((string) ($comparison['post_id'] ?? 0)); ?> / <code><?php echo esc_html((string) ($comparison['locale'] ?? '')); ?></code></td></tr>
            <tr><th>Relações precursor / Core / compartilhadas</th><td><?php echo esc_html((string) ($summary['legacy_total'] ?? 0)); ?> / <?php echo esc_html((string) ($summary['core_total'] ?? 0)); ?> / <?php echo esc_html((string) ($summary['shared_total'] ?? 0)); ?></td></tr>
            <tr><th>Cobertura compartilhada</th><td><?php echo esc_html(isset($summary['coverage_ratio']) && $summary['coverage_ratio'] !== null ? (string) round(((float) $summary['coverage_ratio']) * 100, 1) . '%' : 'n/a'); ?></td></tr>
            <tr><th>Diferenças de rank</th><td><?php echo esc_html((string) ($summary['rank_differences'] ?? 0)); ?></td></tr>
            <tr><th>Destinos inválidos / locale cruzado</th><td><?php echo esc_html((string) ($summary['invalid_targets'] ?? 0)); ?> / <?php echo esc_html((string) ($summary['cross_locale_targets'] ?? 0)); ?></td></tr>
            <tr><th>Tipos sem destino compartilhado</th><td><?php echo esc_html((string) ($summary['kind_without_overlap'] ?? 0)); ?></td></tr>
            <tr><th>Latência Core / precursor</th><td><?php echo esc_html((string) ($summary['core_duration_ms'] ?? 0)); ?> ms / <?php echo esc_html((string) ($summary['legacy_duration_ms'] ?? 0)); ?> ms</td></tr>
            <tr><th>Runs com falha Core / precursor</th><td><?php echo esc_html((string) ($summary['core_failed_runs'] ?? 0)); ?> / <?php echo esc_html((string) ($summary['legacy_failed_runs'] ?? 0)); ?></td></tr>
        </tbody></table>
        <?php if (!empty($comparison['kinds'])): ?>
            <table class="widefat striped" style="max-width:1100px;margin-top:12px"><thead><tr><th>Tipo</th><th>Precursor</th><th>Core</th><th>Compartilhadas</th><th>Ranks diferentes</th><th>Somente precursor</th><th>Somente Core</th></tr></thead><tbody>
            <?php foreach ((array) $comparison['kinds'] as $kind => $data): $data = (array) $data; ?>
                <tr><th><code><?php echo esc_html((string) $kind); ?></code></th><td><?php echo esc_html((string) ($data['legacy_count'] ?? 0)); ?></td><td><?php echo esc_html((string) ($data['core_count'] ?? 0)); ?></td><td><?php echo esc_html((string) ($data['shared_count'] ?? 0)); ?></td><td><?php echo esc_html((string) ($data['rank_differences'] ?? 0)); ?></td><td><code><?php echo esc_html(implode(', ', (array) ($data['legacy_only'] ?? [])) ?: '—'); ?></code></td><td><code><?php echo esc_html(implode(', ', (array) ($data['core_only'] ?? [])) ?: '—'); ?></code></td></tr>
            <?php endforeach; ?>
            </tbody></table>
        <?php endif;
    }

    private static function table_row(string $label, array $table): void
    {
        $missing = array_values(array_map('sanitize_key', (array) ($table['missing_columns'] ?? [])));
        echo '<tr><th>' . esc_html($label) . '</th>';
        echo '<td>' . esc_html(!empty($table['exists']) ? 'Sim' : 'Nao') . '</td>';
        echo '<td>' . esc_html(!empty($table['compatible']) ? 'Compativel' : 'Incompativel') . '</td>';
        echo '<td>' . esc_html((string) ($table['engine'] ?? '—')) . '</td>';
        echo '<td>' . esc_html((string) ($table['collation'] ?? '—')) . '</td>';
        echo '<td>' . esc_html((string) max(0, (int) ($table['rows'] ?? 0))) . '</td>';
        echo '<td>' . esc_html($missing ? implode(', ', $missing) : '—') . '</td></tr>';
    }

    private static function key_value_table(array $items, string $key_label, string $value_label): void
    {
        echo '<table class="widefat striped" style="max-width:1100px"><thead><tr><th>' . esc_html($key_label) . '</th><th>' . esc_html($value_label) . '</th></tr></thead><tbody>';
        if (!$items) { echo '<tr><td colspan="2">Nenhum dado agregado encontrado.</td></tr>'; }
        foreach ($items as $key => $value) {
            echo '<tr><td><code>' . esc_html((string) $key) . '</code></td><td>' . esc_html((string) $value) . '</td></tr>';
        }
        echo '</tbody></table>';
    }

    private static function count_table(array $rows, array $columns): void
    {
        echo '<table class="widefat striped" style="max-width:1100px"><thead><tr>';
        foreach ($columns as $label) { echo '<th>' . esc_html($label) . '</th>'; }
        echo '<th>Total</th></tr></thead><tbody>';
        if (!$rows) { echo '<tr><td colspan="' . esc_attr((string) (count($columns) + 1)) . '">Nenhum dado agregado encontrado.</td></tr>'; }
        foreach ($rows as $row) {
            echo '<tr>';
            foreach ($columns as $key => $label) { echo '<td>' . esc_html((string) ($row[$key] ?? '')) . '</td>'; }
            echo '<td>' . esc_html((string) max(0, (int) ($row['total'] ?? 0))) . '</td></tr>';
        }
        echo '</tbody></table>';
    }
}
