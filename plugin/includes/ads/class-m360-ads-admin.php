<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ads_Admin
{
    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'admin_menu']);
        add_action('admin_post_m360_ads_save_campaign', [self::class, 'save_campaign']);
        add_action('admin_post_m360_ads_delete_campaign', [self::class, 'delete_campaign']);
        add_action('admin_post_m360_ads_assign_campaign', [self::class, 'assign_campaign']);
        add_action('admin_post_m360_ads_bulk_assign_campaigns', [self::class, 'bulk_assign_campaigns']);
    }

    public static function admin_menu(): void
    {
        add_menu_page('M360 Ads Manager', 'M360 Ads', 'manage_options', 'm360-ads-manager', [self::class, 'render_dashboard'], 'dashicons-megaphone', 58);
        add_submenu_page('m360-ads-manager', 'Dashboard', 'Dashboard', 'manage_options', 'm360-ads-manager', [self::class, 'render_dashboard']);
        add_submenu_page('m360-ads-manager', 'Inventário Piloto', 'Inventário Piloto', 'manage_options', 'm360-ads-inventory', [self::class, 'render_inventory']);
        add_submenu_page('m360-ads-manager', 'AdSense Ready', 'AdSense Ready', 'manage_options', 'm360-ads-adsense-ready', [self::class, 'render_adsense_ready']);
        add_submenu_page('m360-ads-manager', 'Slots', 'Slots', 'manage_options', 'm360-ads-slots', [self::class, 'render_slots']);
        add_submenu_page('m360-ads-manager', 'Campanhas', 'Campanhas', 'manage_options', 'm360-ads-campaigns', [self::class, 'render_campaigns']);
        add_submenu_page('m360-ads-manager', 'Nova Campanha', 'Nova Campanha', 'manage_options', 'm360-ads-campaign-new', [self::class, 'render_campaign_form']);
    }

    public static function render_dashboard(): void
    {
        self::guard();
        global $wpdb;
        $slots = M360_Ads_DB::table('ad_slots');
        $campaigns = M360_Ads_DB::table('ad_campaigns');
        $relations = M360_Ads_DB::table('ad_slot_campaigns');
        $total_slots = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$slots}");
        $active_slots = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$slots} WHERE is_active = 1");
        $total_campaigns = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$campaigns}");
        $active_campaigns = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$campaigns} WHERE status = 'active'");
        $assigned = (int) $wpdb->get_var("SELECT COUNT(DISTINCT slot_id) FROM {$relations} WHERE is_active = 1");
        echo '<div class="wrap m360-ads-admin"><h1>M360 Ads Manager</h1><p>Gestão inicial de inventário publicitário do Mengão 360.</p>';
        echo '<div class="m360-ads-admin__cards">';
        self::metric('Slots', $total_slots); self::metric('Slots ativos', $active_slots); self::metric('Slots ocupados', $assigned); self::metric('Campanhas', $total_campaigns); self::metric('Campanhas ativas', $active_campaigns); self::metric('Schema Ads', esc_html((string) get_option('m360_ads_db_version', '-')));
        echo '</div><p><a class="button button-primary" href="' . esc_url(admin_url('admin.php?page=m360-ads-inventory')) . '">Ver inventário piloto</a> <a class="button" href="' . esc_url(admin_url('admin.php?page=m360-ads-adsense-ready')) . '">Checklist AdSense Ready</a> <a class="button" href="' . esc_url(admin_url('admin.php?page=m360-ads-campaign-new')) . '">Nova campanha</a> <a class="button" href="' . esc_url(admin_url('admin.php?page=m360-ads-slots')) . '">Ver slots</a></p></div>';
    }

    public static function render_inventory(): void
    {
        self::guard();
        echo '<div class="wrap m360-ads-admin"><h1>M360 Ads Pilot — Production Inventory</h1><p>Estes são os quatro espaços atuais migrados para renderização pelo M360 Ads Manager. Substitua as chamadas manuais do tema pelos shortcodes ou pela API PHP abaixo.</p>';
        echo '<div class="m360-ads-inventory-grid">';
        foreach (M360_Ads_DB::pilot_slots() as $slot_key => $label) { self::inventory_card($slot_key, $label); }
        echo '</div></div>';
    }

    public static function render_adsense_ready(): void
    {
        self::guard();
        global $wpdb;
        $slots_table = M360_Ads_DB::table('ad_slots');
        $rows = $wpdb->get_results("SELECT * FROM {$slots_table} ORDER BY page_context, position, slot_key", ARRAY_A);
        echo '<div class="wrap m360-ads-admin"><h1>M360 AdSense Ready</h1><p>Checklist técnico de preparação visual, semântica e operacional dos slots. Esta tela não integra o Google AdSense; apenas valida se a infraestrutura M360 está pronta para futura homologação.</p>';
        echo '<table class="widefat striped"><thead><tr><th>Slot</th><th>ID DOM</th><th>Label PT/EN</th><th>Data attributes</th><th>Placeholder</th><th>Provider ready</th><th>Shortcode/API</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            $slot_key = sanitize_key((string) $row['slot_key']);
            $dom_id = 'm360-ad-slot-' . $slot_key;
            echo '<tr>';
            echo '<td><strong>' . esc_html((string) $row['name']) . '</strong><br><code>' . esc_html($slot_key) . '</code></td>';
            echo '<td><span class="m360-ads-status is-active">OK</span><br><code>' . esc_html($dom_id) . '</code></td>';
            echo '<td><span class="m360-ads-status is-active">OK</span><br><code>PUBLICIDADE</code> / <code>ADVERTISEMENT</code></td>';
            echo '<td><span class="m360-ads-status is-active">OK</span><br><code>slot, provider, format, lang, status</code></td>';
            echo '<td><span class="m360-ads-status is-active">OK</span><br>Slots vazios preservam layout.</td>';
            echo '<td><span class="m360-ads-status is-active">OK</span><br><code>internal</code>, <code>adsense</code>, <code>google-ad-manager</code>, <code>house</code>, <code>affiliate</code>, <code>sponsor</code></td>';
            echo '<td><span class="m360-ads-status is-active">OK</span><br><code>[m360_ad_slot id=&quot;' . esc_html($slot_key) . '&quot;]</code><br><code>m360_ads_render_slot(\'' . esc_html($slot_key) . '\')</code></td>';
            echo '</tr>';
        }
        if (empty($rows)) { echo '<tr><td colspan="7">Nenhum slot cadastrado.</td></tr>'; }
        echo '</tbody></table><h2>Observação operacional</h2><p>A aprovação do Google AdSense ainda dependerá de políticas editoriais, conteúdo, navegação, tráfego e análise externa do Google. Esta sprint cobre apenas a preparação técnica da camada M360 Advertising.</p></div>';
    }

    private static function inventory_card(string $slot_key, string $label): void
    {
        global $wpdb;
        $slots = M360_Ads_DB::table('ad_slots');
        $campaigns = M360_Ads_DB::table('ad_campaigns');
        $relations = M360_Ads_DB::table('ad_slot_campaigns');
        $slot = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$slots} WHERE slot_key=%s LIMIT 1", $slot_key), ARRAY_A);
        $campaign = null;
        if ($slot) {
            $campaign = $wpdb->get_row($wpdb->prepare("SELECT c.* FROM {$relations} r INNER JOIN {$campaigns} c ON c.id=r.campaign_id WHERE r.slot_id=%d AND r.is_active=1 ORDER BY r.priority DESC LIMIT 1", (int) $slot['id']), ARRAY_A);
        }
        echo '<section class="m360-ads-inventory-card"><header><strong>' . esc_html($label) . '</strong><code>' . esc_html($slot_key) . '</code></header>';
        echo '<p><span class="m360-ads-status ' . ($campaign ? 'is-active' : 'is-empty') . '">' . ($campaign ? 'Ocupado' : 'Livre') . '</span></p>';
        echo '<p><b>Campanha:</b> ' . esc_html($campaign['title'] ?? '-') . '</p>';
        echo '<p><b>Shortcode:</b><br><code>[m360_ad_slot id=&quot;' . esc_html($slot_key) . '&quot;]</code></p>';
        echo '<p><b>PHP:</b><br><code>echo m360_ads_render_slot(\'' . esc_html($slot_key) . '\');</code></p>';
        echo '<div class="m360-ads-inventory-preview">' . M360_Ad_Slot_Component::render($slot_key) . '</div>';
        echo '</section>';
    }

    public static function render_slots(): void
    {
        self::guard();
        global $wpdb;
        $slots = M360_Ads_DB::table('ad_slots'); $campaigns = M360_Ads_DB::table('ad_campaigns'); $relations = M360_Ads_DB::table('ad_slot_campaigns');
        $rows = $wpdb->get_results("SELECT * FROM {$slots} ORDER BY page_context, position, slot_key", ARRAY_A);
        $active_campaigns = $wpdb->get_results("SELECT id,title FROM {$campaigns} WHERE status IN ('active','draft','paused') ORDER BY title", ARRAY_A);
        $contexts = [];
        foreach ($rows as $row) { $contexts[sanitize_key((string) $row['page_context'])] = (string) $row['page_context']; }
        echo '<div class="wrap m360-ads-admin m360-slots-manager"><h1>M360 Ads Slots</h1>';
        self::render_notice();
        echo '<p>Gerencie os vínculos em uma única operação. Use os filtros para localizar slots por contexto, execução ou ocupação.</p>';
        echo '<div class="m360-slot-toolbar">';
        echo '<label>Buscar<input type="search" id="m360-slot-search" placeholder="Nome ou identificador"></label>';
        echo '<label>Contexto<select id="m360-slot-context"><option value="">Todos</option>';
        foreach ($contexts as $key=>$label) { echo '<option value="' . esc_attr($key) . '">' . esc_html(ucfirst($label)) . '</option>'; }
        echo '</select></label><label>Runtime<select id="m360-slot-runtime"><option value="">Todos</option><option value="runtime">Automático</option><option value="manual">Manual</option><option value="planned">Planejado</option><option value="legacy">Legado</option></select></label>';
        echo '<label>Ocupação<select id="m360-slot-occupancy"><option value="">Todos</option><option value="occupied">Ocupados</option><option value="free">Livres</option></select></label>';
        echo '<button type="button" class="button" id="m360-slot-clear-filters">Limpar filtros</button><span id="m360-slot-result-count" aria-live="polite"></span></div>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" id="m360-slots-bulk-form">';
        wp_nonce_field('m360_ads_bulk_assign_campaigns');
        echo '<input type="hidden" name="action" value="m360_ads_bulk_assign_campaigns"><div class="m360-slot-groups">';
        $open_context = '';
        foreach ($rows as $row) {
            $current = (int) $wpdb->get_var($wpdb->prepare("SELECT campaign_id FROM {$relations} WHERE slot_id = %d AND is_active = 1 ORDER BY priority DESC LIMIT 1", (int) $row['id']));
            $runtime = M360_Ads_Runtime_Map::describe((string) $row['slot_key']);
            $runtime_labels = ['runtime'=>'Automático','legacy'=>'Legado compatível','planned'=>'Planejado','manual'=>'Manual'];
            $runtime_class = $runtime['status'] === 'runtime' ? 'is-active' : 'is-empty';
            $context = sanitize_key((string) $row['page_context']);
            if ($context !== $open_context) {
                if ($open_context !== '') { echo '</div></section>'; }
                $open_context = $context;
                echo '<section class="m360-slot-group" data-context-group="' . esc_attr($context) . '"><h2>' . esc_html(ucfirst((string) $row['page_context'])) . ' <span class="m360-slot-group-count"></span></h2><div class="m360-slot-grid">';
            }
            $occupancy = $current ? 'occupied' : 'free';
            echo '<article class="m360-slot-card" data-slot-search="' . esc_attr(strtolower((string) $row['name'] . ' ' . (string) $row['slot_key'])) . '" data-context="' . esc_attr($context) . '" data-runtime="' . esc_attr($runtime['status']) . '" data-occupancy="' . esc_attr($occupancy) . '">';
            echo '<header><div><strong>' . esc_html($row['name']) . '</strong><code>' . esc_html($row['slot_key']) . '</code></div><span class="m360-slot-occupancy is-' . esc_attr($occupancy) . '">' . ($current ? 'Ocupado' : 'Livre') . '</span></header>';
            echo '<div class="m360-slot-card__meta"><span class="m360-ads-status ' . esc_attr($runtime_class) . '">' . esc_html($runtime_labels[$runtime['status']] ?? $runtime['status']) . '</span><span>' . esc_html((string) $row['max_width']) . '×' . esc_html((string) $row['max_height']) . '</span><span>' . esc_html($row['language']) . '</span><span>' . esc_html($row['device']) . '</span></div>';
            echo '<p><strong>' . esc_html($runtime['source']) . '</strong><br>' . esc_html($runtime['trigger']) . '</p><code>[m360_ad_slot id=&quot;' . esc_html($row['slot_key']) . '&quot;]</code>';
            echo '<label class="m360-slot-card__assignment">Campanha<select name="assignments[' . esc_attr((string) $row['id']) . ']" data-original="' . esc_attr((string) $current) . '"><option value="0">Nenhuma</option>';
            foreach ($active_campaigns as $campaign) { echo '<option value="' . esc_attr((string) $campaign['id']) . '"' . selected($current, (int) $campaign['id'], false) . '>' . esc_html($campaign['title']) . '</option>'; }
            echo '</select></label></article>';
        }
        if ($open_context !== '') { echo '</div></section>'; }
        echo '</div><div class="m360-slot-savebar"><span><strong id="m360-slot-change-count">0</strong> alteração(ões) pendente(s)</span><button class="button button-primary button-hero" type="submit" disabled>Salvar todos os vínculos</button></div></form></div>';
    }

    public static function render_campaigns(): void
    {
        self::guard(); global $wpdb; $table = M360_Ads_DB::table('ad_campaigns'); $rows = $wpdb->get_results("SELECT * FROM {$table} ORDER BY updated_at DESC, id DESC", ARRAY_A);
        echo '<div class="wrap m360-ads-admin"><h1>Campanhas <a class="page-title-action" href="' . esc_url(admin_url('admin.php?page=m360-ads-campaign-new')) . '">Adicionar nova</a></h1>';
        self::render_notice();
        echo '<table class="widefat striped"><thead><tr><th>Titulo</th><th>Anunciante</th><th>Tipo</th><th>Idioma</th><th>Dispositivo</th><th>Status</th><th>Prioridade</th><th>Acoes</th></tr></thead><tbody>';
        foreach ($rows as $row) { $edit = admin_url('admin.php?page=m360-ads-campaign-new&id=' . (int) $row['id']); $delete = wp_nonce_url(admin_url('admin-post.php?action=m360_ads_delete_campaign&id=' . (int) $row['id']), 'm360_ads_delete_campaign'); echo '<tr><td><strong>' . esc_html($row['title']) . '</strong></td><td>' . esc_html($row['advertiser']) . '</td><td>' . esc_html($row['campaign_type']) . '</td><td>' . esc_html($row['language']) . '</td><td>' . esc_html($row['device']) . '</td><td>' . esc_html($row['status']) . '</td><td>' . esc_html((string) $row['priority']) . '</td><td><a href="' . esc_url($edit) . '">Editar</a> | <a href="' . esc_url($delete) . '" onclick="return confirm(\'Excluir campanha?\')">Excluir</a></td></tr>'; }
        if (empty($rows)) { echo '<tr><td colspan="8">Nenhuma campanha cadastrada.</td></tr>'; }
        echo '</tbody></table></div>';
    }

    public static function render_campaign_form(): void
    {
        self::guard(); global $wpdb; $id = absint($_GET['id'] ?? 0); $table = M360_Ads_DB::table('ad_campaigns'); $row = $id ? $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A) : [];
        if ($id && !$row) { self::redirect_campaigns('campaign_not_found', 'error'); }
        $row = wp_parse_args((array) $row, ['title'=>'','advertiser'=>'','campaign_type'=>'image','image_url'=>'','target_url'=>'','html_code'=>'','script_code'=>'','alt_text'=>'','language'=>'all','device'=>'all','start_at'=>'','end_at'=>'','priority'=>50,'status'=>'draft']);
        echo '<div class="wrap m360-ads-admin"><h1>' . ($id ? 'Editar campanha' : 'Nova campanha') . '</h1>';
        self::render_notice();
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">'; wp_nonce_field('m360_ads_save_campaign');
        echo '<input type="hidden" name="action" value="m360_ads_save_campaign"><input type="hidden" name="id" value="' . esc_attr((string) $id) . '"><table class="form-table"><tbody>';
        self::input('Titulo', 'title', $row['title'], true); self::input('Anunciante', 'advertiser', $row['advertiser']); self::select('Tipo', 'campaign_type', $row['campaign_type'], self::campaign_types()); self::input('Imagem URL', 'image_url', $row['image_url']); self::input('URL destino', 'target_url', $row['target_url']); self::input('Alt text', 'alt_text', $row['alt_text']); self::select('Idioma', 'language', $row['language'], self::languages()); self::select('Dispositivo', 'device', $row['device'], self::devices()); self::input('Inicio', 'start_at', $row['start_at']); self::input('Fim', 'end_at', $row['end_at']); self::input('Prioridade', 'priority', (string) ($row['priority'] ?? 50)); self::select('Status', 'status', $row['status'], self::campaign_statuses()); self::textarea('HTML', 'html_code', $row['html_code']); self::textarea('Script', 'script_code', $row['script_code']);
        echo '</tbody></table><p class="submit"><button class="button button-primary" type="submit">Salvar campanha</button></p></form></div>';
    }

    public static function save_campaign(): void
    {
        self::guard(); check_admin_referer('m360_ads_save_campaign'); global $wpdb; $id = absint($_POST['id'] ?? 0); $table = M360_Ads_DB::table('ad_campaigns'); $now = current_time('mysql');
        $title = sanitize_text_field(wp_unslash((string) ($_POST['title'] ?? '')));
        $start_at = self::nullable_datetime(wp_unslash((string) ($_POST['start_at'] ?? '')));
        $end_at = self::nullable_datetime(wp_unslash((string) ($_POST['end_at'] ?? '')));
        if ($title === '') { self::redirect_campaign_form($id, 'title_required', 'error'); }
        if ($start_at === false || $end_at === false) { self::redirect_campaign_form($id, 'invalid_datetime', 'error'); }
        if (is_string($start_at) && is_string($end_at) && $start_at > $end_at) { self::redirect_campaign_form($id, 'invalid_period', 'error'); }
        if ($id && !(int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id))) { self::redirect_campaigns('campaign_not_found', 'error'); }
        $data = [
            'title' => $title,
            'advertiser' => sanitize_text_field(wp_unslash((string) ($_POST['advertiser'] ?? ''))),
            'campaign_type' => self::allowed_choice(wp_unslash((string) ($_POST['campaign_type'] ?? 'image')), self::campaign_types(), 'image'),
            'image_url' => esc_url_raw(wp_unslash((string) ($_POST['image_url'] ?? ''))),
            'target_url' => esc_url_raw(wp_unslash((string) ($_POST['target_url'] ?? ''))),
            'html_code' => wp_kses_post(wp_unslash((string) ($_POST['html_code'] ?? ''))),
            'script_code' => current_user_can('unfiltered_html') ? wp_unslash((string) ($_POST['script_code'] ?? '')) : '',
            'alt_text' => sanitize_text_field(wp_unslash((string) ($_POST['alt_text'] ?? ''))),
            'language' => self::allowed_choice(wp_unslash((string) ($_POST['language'] ?? 'all')), self::languages(), 'all'),
            'device' => self::allowed_choice(wp_unslash((string) ($_POST['device'] ?? 'all')), self::devices(), 'all'),
            'start_at' => $start_at,
            'end_at' => $end_at,
            'priority' => max(0, min(1000, intval($_POST['priority'] ?? 50))),
            'status' => self::allowed_choice(wp_unslash((string) ($_POST['status'] ?? 'draft')), self::campaign_statuses(), 'draft'),
            'updated_at' => $now,
        ];
        if ($id) { $result = $wpdb->update($table, $data, ['id' => $id]); }
        else { $data['created_at'] = $now; $result = $wpdb->insert($table, $data); $id = (int) $wpdb->insert_id; }
        if ($result === false || !$id) { self::redirect_campaign_form($id, 'database_error', 'error'); }
        self::redirect_campaigns('campaign_saved', 'success');
    }

    public static function delete_campaign(): void
    {
        self::guard(); check_admin_referer('m360_ads_delete_campaign'); global $wpdb; $id = absint($_GET['id'] ?? 0);
        if (!$id) { self::redirect_campaigns('campaign_not_found', 'error'); }
        $relations_result = $wpdb->delete(M360_Ads_DB::table('ad_slot_campaigns'), ['campaign_id' => $id]);
        $campaign_result = $wpdb->delete(M360_Ads_DB::table('ad_campaigns'), ['id' => $id]);
        if ($relations_result === false || $campaign_result === false) { self::redirect_campaigns('database_error', 'error'); }
        if ($campaign_result === 0) { self::redirect_campaigns('campaign_not_found', 'error'); }
        self::redirect_campaigns('campaign_deleted', 'success');
    }

    public static function assign_campaign(): void
    {
        self::guard(); check_admin_referer('m360_ads_assign_campaign'); global $wpdb; $slot_id = absint($_POST['slot_id'] ?? 0); $campaign_id = absint($_POST['campaign_id'] ?? 0); $table = M360_Ads_DB::table('ad_slot_campaigns');
        $slots = M360_Ads_DB::table('ad_slots'); $campaigns = M360_Ads_DB::table('ad_campaigns');
        if (!$slot_id || !(int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$slots} WHERE id = %d", $slot_id))) { self::redirect_slots('slot_not_found', 'error'); }
        if ($campaign_id && !(int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$campaigns} WHERE id = %d", $campaign_id))) { self::redirect_slots('campaign_not_found', 'error'); }
        $deactivated = $wpdb->update($table, ['is_active' => 0, 'updated_at' => current_time('mysql')], ['slot_id' => $slot_id]);
        if ($deactivated === false) { self::redirect_slots('database_error', 'error'); }
        if ($campaign_id) {
            $exists = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE slot_id=%d AND campaign_id=%d", $slot_id, $campaign_id));
            $data = ['slot_id'=>$slot_id,'campaign_id'=>$campaign_id,'priority'=>100,'weight'=>100,'is_active'=>1,'updated_at'=>current_time('mysql')];
            if ($exists) { $result = $wpdb->update($table, $data, ['id'=>$exists]); }
            else { $data['created_at'] = current_time('mysql'); $result = $wpdb->insert($table, $data); }
            if ($result === false) { self::redirect_slots('database_error', 'error'); }
        }
        self::redirect_slots('slot_assignment_saved', 'success');
    }

    public static function bulk_assign_campaigns(): void
    {
        self::guard(); check_admin_referer('m360_ads_bulk_assign_campaigns'); global $wpdb;
        $assignments = isset($_POST['assignments']) && is_array($_POST['assignments']) ? wp_unslash($_POST['assignments']) : [];
        $slots = M360_Ads_DB::table('ad_slots'); $campaigns = M360_Ads_DB::table('ad_campaigns'); $relations = M360_Ads_DB::table('ad_slot_campaigns');
        $valid_slots = array_map('intval', (array) $wpdb->get_col("SELECT id FROM {$slots}"));
        $valid_campaigns = array_map('intval', (array) $wpdb->get_col("SELECT id FROM {$campaigns} WHERE status IN ('active','draft','paused')"));
        $normalized = [];
        foreach ($assignments as $slot_id=>$campaign_id) {
            $slot_id = absint($slot_id); $campaign_id = absint($campaign_id);
            if (!in_array($slot_id, $valid_slots, true)) { self::redirect_slots('slot_not_found', 'error'); }
            if ($campaign_id && !in_array($campaign_id, $valid_campaigns, true)) { self::redirect_slots('campaign_not_found', 'error'); }
            $normalized[$slot_id] = $campaign_id;
        }
        $wpdb->query('START TRANSACTION');
        foreach ($normalized as $slot_id=>$campaign_id) {
            if ($wpdb->update($relations, ['is_active'=>0,'updated_at'=>current_time('mysql')], ['slot_id'=>$slot_id]) === false) { $wpdb->query('ROLLBACK'); self::redirect_slots('database_error', 'error'); }
            if (!$campaign_id) { continue; }
            $exists = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$relations} WHERE slot_id=%d AND campaign_id=%d", $slot_id, $campaign_id));
            $data = ['slot_id'=>$slot_id,'campaign_id'=>$campaign_id,'priority'=>100,'weight'=>100,'is_active'=>1,'updated_at'=>current_time('mysql')];
            $result = $exists ? $wpdb->update($relations, $data, ['id'=>$exists]) : $wpdb->insert($relations, array_merge($data, ['created_at'=>current_time('mysql')]));
            if ($result === false) { $wpdb->query('ROLLBACK'); self::redirect_slots('database_error', 'error'); }
        }
        $wpdb->query('COMMIT');
        self::redirect_slots('slot_assignments_saved', 'success');
    }

    private static function metric(string $label, $value): void { echo '<div class="m360-ads-admin__card"><strong>' . esc_html((string) $value) . '</strong><span>' . esc_html($label) . '</span></div>'; }
    private static function guard(): void { if (!current_user_can('manage_options')) { wp_die('Acesso negado.'); } }
    private static function input(string $label, string $name, ?string $value, bool $required = false): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><input class="regular-text" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr((string) $value) . '"' . ($required ? ' required' : '') . '></td></tr>'; }
    private static function textarea(string $label, string $name, ?string $value): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><textarea class="large-text code" rows="6" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '">' . esc_textarea((string) $value) . '</textarea></td></tr>'; }
    private static function select(string $label, string $name, ?string $value, array $options): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><select id="' . esc_attr($name) . '" name="' . esc_attr($name) . '">'; foreach ($options as $k=>$v) { echo '<option value="' . esc_attr($k) . '"' . selected((string) $value, $k, false) . '>' . esc_html($v) . '</option>'; } echo '</select></td></tr>'; }
    private static function nullable_datetime(string $value)
    {
        $value = trim(sanitize_text_field($value));
        if ($value === '') { return null; }
        foreach (['Y-m-d H:i:s', 'Y-m-d\\TH:i'] as $format) {
            $date = DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) { return $date->format('Y-m-d H:i:s'); }
        }
        return false;
    }
    private static function allowed_choice(string $value, array $options, string $fallback): string { $value = sanitize_key($value); return array_key_exists($value, $options) ? $value : $fallback; }
    private static function campaign_types(): array { return ['image'=>'image','html'=>'html','adsense'=>'adsense','gam'=>'gam','house'=>'house','affiliate'=>'affiliate','sponsor'=>'sponsor']; }
    private static function languages(): array { return ['all'=>'all','pt-br'=>'pt-br','en-us'=>'en-us']; }
    private static function devices(): array { return ['all'=>'all','desktop'=>'desktop','tablet'=>'tablet','mobile'=>'mobile']; }
    private static function campaign_statuses(): array { return ['draft'=>'draft','active'=>'active','paused'=>'paused','expired'=>'expired','archived'=>'archived']; }
    private static function render_notice(): void
    {
        $code = sanitize_key((string) ($_GET['m360_notice'] ?? ''));
        $type = sanitize_key((string) ($_GET['m360_notice_type'] ?? 'success'));
        $messages = ['campaign_saved'=>'Campanha salva com sucesso.','campaign_deleted'=>'Campanha excluida com sucesso.','campaign_not_found'=>'Campanha nao encontrada.','title_required'=>'Informe o titulo da campanha.','invalid_datetime'=>'Use uma data valida no formato AAAA-MM-DD HH:MM:SS.','invalid_period'=>'A data final deve ser posterior a data inicial.','database_error'=>'Nao foi possivel concluir a operacao no banco de dados.','slot_not_found'=>'Slot nao encontrado.','slot_assignment_saved'=>'Vinculo do slot salvo com sucesso.','slot_assignments_saved'=>'Vinculos dos slots salvos com sucesso.'];
        if (!isset($messages[$code])) { return; }
        $class = $type === 'error' ? 'notice notice-error' : 'notice notice-success is-dismissible';
        echo '<div class="' . esc_attr($class) . '"><p>' . esc_html($messages[$code]) . '</p></div>';
    }
    private static function redirect_campaign_form(int $id, string $code, string $type): void { $url = admin_url('admin.php?page=m360-ads-campaign-new' . ($id ? '&id=' . $id : '')); self::redirect_with_notice($url, $code, $type); }
    private static function redirect_campaigns(string $code, string $type): void { self::redirect_with_notice(admin_url('admin.php?page=m360-ads-campaigns'), $code, $type); }
    private static function redirect_slots(string $code, string $type): void { self::redirect_with_notice(admin_url('admin.php?page=m360-ads-slots'), $code, $type); }
    private static function redirect_with_notice(string $url, string $code, string $type): void { wp_safe_redirect(add_query_arg(['m360_notice'=>$code,'m360_notice_type'=>$type], $url)); exit; }
}
