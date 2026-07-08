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
    }

    public static function admin_menu(): void
    {
        add_menu_page('M360 Ads Manager', 'M360 Ads', 'manage_options', 'm360-ads-manager', [self::class, 'render_dashboard'], 'dashicons-megaphone', 58);
        add_submenu_page('m360-ads-manager', 'Dashboard', 'Dashboard', 'manage_options', 'm360-ads-manager', [self::class, 'render_dashboard']);
        add_submenu_page('m360-ads-manager', 'Inventário Piloto', 'Inventário Piloto', 'manage_options', 'm360-ads-inventory', [self::class, 'render_inventory']);
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
        echo '</div><p><a class="button button-primary" href="' . esc_url(admin_url('admin.php?page=m360-ads-inventory')) . '">Ver inventário piloto</a> <a class="button" href="' . esc_url(admin_url('admin.php?page=m360-ads-campaign-new')) . '">Nova campanha</a> <a class="button" href="' . esc_url(admin_url('admin.php?page=m360-ads-slots')) . '">Ver slots</a></p></div>';
    }

    public static function render_inventory(): void
    {
        self::guard();
        echo '<div class="wrap m360-ads-admin"><h1>M360 Ads Pilot — Production Inventory</h1><p>Estes são os quatro espaços atuais migrados para renderização pelo M360 Ads Manager. Substitua as chamadas manuais do tema pelos shortcodes ou pela API PHP abaixo.</p>';
        echo '<div class="m360-ads-inventory-grid">';
        foreach (M360_Ads_DB::pilot_slots() as $slot_key => $label) { self::inventory_card($slot_key, $label); }
        echo '</div></div>';
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
        echo '<div class="wrap m360-ads-admin"><h1>M360 Ads Slots</h1><table class="widefat striped"><thead><tr><th>Slot</th><th>Contexto</th><th>Idioma</th><th>Dispositivo</th><th>Tamanho</th><th>Shortcode</th><th>Vincular campanha</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            $current = (int) $wpdb->get_var($wpdb->prepare("SELECT campaign_id FROM {$relations} WHERE slot_id = %d AND is_active = 1 ORDER BY priority DESC LIMIT 1", (int) $row['id']));
            echo '<tr><td><strong>' . esc_html($row['name']) . '</strong><br><code>' . esc_html($row['slot_key']) . '</code></td><td>' . esc_html($row['page_context']) . '</td><td>' . esc_html($row['language']) . '</td><td>' . esc_html($row['device']) . '</td><td>' . esc_html((string) $row['max_width']) . 'x' . esc_html((string) $row['max_height']) . '</td><td><code>[m360_ad_slot id=&quot;' . esc_html($row['slot_key']) . '&quot;]</code></td><td>';
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">'; wp_nonce_field('m360_ads_assign_campaign');
            echo '<input type="hidden" name="action" value="m360_ads_assign_campaign"><input type="hidden" name="slot_id" value="' . esc_attr((string) $row['id']) . '"><select name="campaign_id"><option value="0">Nenhuma</option>';
            foreach ($active_campaigns as $campaign) { echo '<option value="' . esc_attr((string) $campaign['id']) . '"' . selected($current, (int) $campaign['id'], false) . '>' . esc_html($campaign['title']) . '</option>'; }
            echo '</select> <input class="button" type="submit" value="Salvar"></form></td></tr>';
        }
        echo '</tbody></table></div>';
    }

    public static function render_campaigns(): void
    {
        self::guard(); global $wpdb; $table = M360_Ads_DB::table('ad_campaigns'); $rows = $wpdb->get_results("SELECT * FROM {$table} ORDER BY updated_at DESC, id DESC", ARRAY_A);
        echo '<div class="wrap m360-ads-admin"><h1>Campanhas <a class="page-title-action" href="' . esc_url(admin_url('admin.php?page=m360-ads-campaign-new')) . '">Adicionar nova</a></h1><table class="widefat striped"><thead><tr><th>Titulo</th><th>Anunciante</th><th>Tipo</th><th>Idioma</th><th>Dispositivo</th><th>Status</th><th>Prioridade</th><th>Acoes</th></tr></thead><tbody>';
        foreach ($rows as $row) { $edit = admin_url('admin.php?page=m360-ads-campaign-new&id=' . (int) $row['id']); $delete = wp_nonce_url(admin_url('admin-post.php?action=m360_ads_delete_campaign&id=' . (int) $row['id']), 'm360_ads_delete_campaign'); echo '<tr><td><strong>' . esc_html($row['title']) . '</strong></td><td>' . esc_html($row['advertiser']) . '</td><td>' . esc_html($row['campaign_type']) . '</td><td>' . esc_html($row['language']) . '</td><td>' . esc_html($row['device']) . '</td><td>' . esc_html($row['status']) . '</td><td>' . esc_html((string) $row['priority']) . '</td><td><a href="' . esc_url($edit) . '">Editar</a> | <a href="' . esc_url($delete) . '" onclick="return confirm(\'Excluir campanha?\')">Excluir</a></td></tr>'; }
        if (empty($rows)) { echo '<tr><td colspan="8">Nenhuma campanha cadastrada.</td></tr>'; }
        echo '</tbody></table></div>';
    }

    public static function render_campaign_form(): void
    {
        self::guard(); global $wpdb; $id = absint($_GET['id'] ?? 0); $table = M360_Ads_DB::table('ad_campaigns'); $row = $id ? $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A) : [];
        $row = wp_parse_args((array) $row, ['title'=>'','advertiser'=>'','campaign_type'=>'image','image_url'=>'','target_url'=>'','html_code'=>'','script_code'=>'','alt_text'=>'','language'=>'all','device'=>'all','start_at'=>'','end_at'=>'','priority'=>50,'status'=>'draft']);
        echo '<div class="wrap m360-ads-admin"><h1>' . ($id ? 'Editar campanha' : 'Nova campanha') . '</h1><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">'; wp_nonce_field('m360_ads_save_campaign');
        echo '<input type="hidden" name="action" value="m360_ads_save_campaign"><input type="hidden" name="id" value="' . esc_attr((string) $id) . '"><table class="form-table"><tbody>';
        self::input('Titulo', 'title', $row['title'], true); self::input('Anunciante', 'advertiser', $row['advertiser']); self::select('Tipo', 'campaign_type', $row['campaign_type'], ['image'=>'image','html'=>'html','adsense'=>'adsense','gam'=>'gam','house'=>'house','affiliate'=>'affiliate','sponsor'=>'sponsor']); self::input('Imagem URL', 'image_url', $row['image_url']); self::input('URL destino', 'target_url', $row['target_url']); self::input('Alt text', 'alt_text', $row['alt_text']); self::select('Idioma', 'language', $row['language'], ['all'=>'all','pt-br'=>'pt-br','en-us'=>'en-us']); self::select('Dispositivo', 'device', $row['device'], ['all'=>'all','desktop'=>'desktop','tablet'=>'tablet','mobile'=>'mobile']); self::input('Inicio', 'start_at', $row['start_at']); self::input('Fim', 'end_at', $row['end_at']); self::input('Prioridade', 'priority', (string) $row['priority']); self::select('Status', 'status', $row['status'], ['draft'=>'draft','active'=>'active','paused'=>'paused','expired'=>'expired','archived'=>'archived']); self::textarea('HTML', 'html_code', $row['html_code']); self::textarea('Script', 'script_code', $row['script_code']);
        echo '</tbody></table><p class="submit"><button class="button button-primary" type="submit">Salvar campanha</button></p></form></div>';
    }

    public static function save_campaign(): void
    {
        self::guard(); check_admin_referer('m360_ads_save_campaign'); global $wpdb; $id = absint($_POST['id'] ?? 0); $table = M360_Ads_DB::table('ad_campaigns'); $now = current_time('mysql');
        $data = ['title'=>sanitize_text_field((string) ($_POST['title'] ?? '')),'advertiser'=>sanitize_text_field((string) ($_POST['advertiser'] ?? '')),'campaign_type'=>sanitize_key((string) ($_POST['campaign_type'] ?? 'image')),'image_url'=>esc_url_raw((string) ($_POST['image_url'] ?? '')),'target_url'=>esc_url_raw((string) ($_POST['target_url'] ?? '')),'html_code'=>wp_kses_post((string) ($_POST['html_code'] ?? '')),'script_code'=>current_user_can('unfiltered_html') ? (string) ($_POST['script_code'] ?? '') : '','alt_text'=>sanitize_text_field((string) ($_POST['alt_text'] ?? '')),'language'=>sanitize_key((string) ($_POST['language'] ?? 'all')),'device'=>sanitize_key((string) ($_POST['device'] ?? 'all')),'start_at'=>self::nullable_datetime((string) ($_POST['start_at'] ?? '')),'end_at'=>self::nullable_datetime((string) ($_POST['end_at'] ?? '')),'priority'=>intval($_POST['priority'] ?? 50),'status'=>sanitize_key((string) ($_POST['status'] ?? 'draft')),'updated_at'=>$now];
        if ($data['title'] === '') { $data['title'] = 'Campanha sem titulo'; }
        if ($id) { $wpdb->update($table, $data, ['id' => $id]); } else { $data['created_at'] = $now; $wpdb->insert($table, $data); }
        wp_safe_redirect(admin_url('admin.php?page=m360-ads-campaigns')); exit;
    }

    public static function delete_campaign(): void
    {
        self::guard(); check_admin_referer('m360_ads_delete_campaign'); global $wpdb; $id = absint($_GET['id'] ?? 0);
        if ($id) { $wpdb->delete(M360_Ads_DB::table('ad_slot_campaigns'), ['campaign_id' => $id]); $wpdb->delete(M360_Ads_DB::table('ad_campaigns'), ['id' => $id]); }
        wp_safe_redirect(admin_url('admin.php?page=m360-ads-campaigns')); exit;
    }

    public static function assign_campaign(): void
    {
        self::guard(); check_admin_referer('m360_ads_assign_campaign'); global $wpdb; $slot_id = absint($_POST['slot_id'] ?? 0); $campaign_id = absint($_POST['campaign_id'] ?? 0); $table = M360_Ads_DB::table('ad_slot_campaigns');
        if ($slot_id) { $wpdb->update($table, ['is_active' => 0, 'updated_at' => current_time('mysql')], ['slot_id' => $slot_id]); if ($campaign_id) { $exists = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE slot_id=%d AND campaign_id=%d", $slot_id, $campaign_id)); $data = ['slot_id'=>$slot_id, 'campaign_id'=>$campaign_id, 'priority'=>100, 'weight'=>100, 'is_active'=>1, 'updated_at'=>current_time('mysql')]; if ($exists) { $wpdb->update($table, $data, ['id'=>$exists]); } else { $data['created_at'] = current_time('mysql'); $wpdb->insert($table, $data); } } }
        wp_safe_redirect(admin_url('admin.php?page=m360-ads-slots')); exit;
    }

    private static function metric(string $label, $value): void { echo '<div class="m360-ads-admin__card"><strong>' . esc_html((string) $value) . '</strong><span>' . esc_html($label) . '</span></div>'; }
    private static function guard(): void { if (!current_user_can('manage_options')) { wp_die('Acesso negado.'); } }
    private static function input(string $label, string $name, string $value, bool $required = false): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><input class="regular-text" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '"' . ($required ? ' required' : '') . '></td></tr>'; }
    private static function textarea(string $label, string $name, string $value): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><textarea class="large-text code" rows="6" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '">' . esc_textarea($value) . '</textarea></td></tr>'; }
    private static function select(string $label, string $name, string $value, array $options): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><select id="' . esc_attr($name) . '" name="' . esc_attr($name) . '">'; foreach ($options as $k=>$v) { echo '<option value="' . esc_attr($k) . '"' . selected($value, $k, false) . '>' . esc_html($v) . '</option>'; } echo '</select></td></tr>'; }
    private static function nullable_datetime(string $value): ?string { $value = trim($value); return $value === '' ? null : sanitize_text_field($value); }
}
