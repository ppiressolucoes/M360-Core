<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ads_Creatives_Admin
{
    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'admin_menu'], 20);
        add_action('admin_post_m360_ads_save_creative', [self::class, 'save_creative']);
        add_action('admin_post_m360_ads_delete_creative', [self::class, 'delete_creative']);
    }

    public static function admin_menu(): void
    {
        add_submenu_page('m360-ads-manager', 'Criativos', 'Criativos', 'manage_options', 'm360-ads-creatives', [self::class, 'render_creatives']);
        add_submenu_page('m360-ads-manager', 'Novo Criativo', 'Novo Criativo', 'manage_options', 'm360-ads-creative-new', [self::class, 'render_form']);
    }

    public static function render_creatives(): void
    {
        self::guard();
        global $wpdb;
        $table = M360_Ads_DB::table('ad_creatives');
        $campaigns = M360_Ads_DB::table('ad_campaigns');
        $rows = $wpdb->get_results("SELECT cr.*, c.title AS campaign_title FROM {$table} cr LEFT JOIN {$campaigns} c ON c.id = cr.campaign_id ORDER BY cr.updated_at DESC, cr.id DESC", ARRAY_A);
        echo '<div class="wrap m360-ads-admin"><h1>Criativos <a class="page-title-action" href="' . esc_url(admin_url('admin.php?page=m360-ads-creative-new')) . '">Adicionar novo</a></h1>';
        echo '<p>Biblioteca exclusiva de criativos do Ads Manager. Use o seletor de mídia do WordPress e mantenha os ativos publicitários identificados como Ads.</p>';
        echo '<table class="widefat striped"><thead><tr><th>Preview</th><th>Nome</th><th>Campanha</th><th>Tipo</th><th>Idioma</th><th>Dispositivo</th><th>Tamanho</th><th>Status</th><th>Ações</th></tr></thead><tbody>';
        foreach ($rows as $row) {
            $edit = admin_url('admin.php?page=m360-ads-creative-new&id=' . (int) $row['id']);
            $delete = wp_nonce_url(admin_url('admin-post.php?action=m360_ads_delete_creative&id=' . (int) $row['id']), 'm360_ads_delete_creative');
            $preview = $row['image_url'] ? '<img src="' . esc_url($row['image_url']) . '" style="max-width:90px;max-height:48px;border-radius:4px">' : '<span class="dashicons dashicons-format-image"></span>';
            echo '<tr><td>' . $preview . '</td><td><strong>' . esc_html($row['title']) . '</strong><br><code>' . esc_html($row['slug']) . '</code></td><td>' . esc_html($row['campaign_title'] ?: '-') . '</td><td>' . esc_html($row['creative_type']) . '</td><td>' . esc_html($row['language']) . '</td><td>' . esc_html($row['device']) . '</td><td>' . esc_html((string) $row['width']) . 'x' . esc_html((string) $row['height']) . '</td><td>' . esc_html($row['status']) . '</td><td><a href="' . esc_url($edit) . '">Editar</a> | <a href="' . esc_url($delete) . '" onclick="return confirm(\'Excluir criativo?\')">Excluir</a></td></tr>';
        }
        if (!$rows) { echo '<tr><td colspan="9">Nenhum criativo cadastrado.</td></tr>'; }
        echo '</tbody></table></div>';
    }

    public static function render_form(): void
    {
        self::guard();
        global $wpdb;
        $id = absint($_GET['id'] ?? 0);
        $table = M360_Ads_DB::table('ad_creatives');
        $row = $id ? $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A) : [];
        $row = wp_parse_args((array) $row, ['campaign_id'=>0,'title'=>'','slug'=>'','creative_type'=>'image','image_url'=>'','target_url'=>'','html_code'=>'','script_code'=>'','alt_text'=>'','language'=>'all','device'=>'all','width'=>'','height'=>'','mime'=>'','filesize'=>'','checksum'=>'','status'=>'draft']);
        $campaigns = $wpdb->get_results("SELECT id,title FROM " . M360_Ads_DB::table('ad_campaigns') . " ORDER BY title", ARRAY_A);
        echo '<div class="wrap m360-ads-admin"><h1>' . ($id ? 'Editar criativo' : 'Novo criativo') . '</h1><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        wp_nonce_field('m360_ads_save_creative');
        echo '<input type="hidden" name="action" value="m360_ads_save_creative"><input type="hidden" name="id" value="' . esc_attr((string) $id) . '"><table class="form-table"><tbody>';
        self::input('Título', 'title', $row['title'], true);
        self::input('Slug', 'slug', $row['slug']);
        self::campaign_select($campaigns, (int) $row['campaign_id']);
        self::select('Tipo', 'creative_type', $row['creative_type'], ['image'=>'image','html'=>'html','script'=>'script','adsense'=>'adsense','gam'=>'gam','house'=>'house','affiliate'=>'affiliate','sponsor'=>'sponsor']);
        self::preset_select((string) $row['width'], (string) $row['height']);
        self::media_input('Imagem URL', 'image_url', $row['image_url']);
        self::input('URL destino', 'target_url', $row['target_url']);
        self::input('Alt text', 'alt_text', $row['alt_text']);
        self::select('Idioma', 'language', $row['language'], ['all'=>'all','pt-br'=>'pt-br','en-us'=>'en-us']);
        self::select('Dispositivo', 'device', $row['device'], ['all'=>'all','desktop'=>'desktop','tablet'=>'tablet','mobile'=>'mobile']);
        self::input('Largura', 'width', (string) $row['width']);
        self::input('Altura', 'height', (string) $row['height']);
        self::input('MIME', 'mime', $row['mime']);
        self::input('Filesize', 'filesize', (string) $row['filesize']);
        self::input('Checksum', 'checksum', $row['checksum']);
        self::select('Status', 'status', $row['status'], ['draft'=>'draft','active'=>'active','paused'=>'paused','archived'=>'archived']);
        self::textarea('HTML', 'html_code', $row['html_code']);
        self::textarea('Script', 'script_code', $row['script_code']);
        echo '</tbody></table><p class="submit"><button class="button button-primary" type="submit">Salvar criativo</button></p></form></div>';
    }

    public static function save_creative(): void
    {
        self::guard();
        check_admin_referer('m360_ads_save_creative');
        global $wpdb;
        M360_Ads_DB::ensure_upload_dir();
        $id = absint($_POST['id'] ?? 0);
        $title = sanitize_text_field((string) ($_POST['title'] ?? ''));
        $slug = sanitize_title((string) ($_POST['slug'] ?? ''));
        if ($slug === '') { $slug = sanitize_title($title); }
        $now = current_time('mysql');
        $data = [
            'campaign_id' => absint($_POST['campaign_id'] ?? 0),
            'title' => $title !== '' ? $title : 'Criativo sem titulo',
            'slug' => $slug !== '' ? $slug : ('creative-' . time()),
            'creative_type' => sanitize_key((string) ($_POST['creative_type'] ?? 'image')),
            'image_url' => esc_url_raw((string) ($_POST['image_url'] ?? '')),
            'target_url' => esc_url_raw((string) ($_POST['target_url'] ?? '')),
            'html_code' => wp_kses_post((string) ($_POST['html_code'] ?? '')),
            'script_code' => current_user_can('unfiltered_html') ? (string) ($_POST['script_code'] ?? '') : '',
            'alt_text' => sanitize_text_field((string) ($_POST['alt_text'] ?? '')),
            'language' => sanitize_key((string) ($_POST['language'] ?? 'all')),
            'device' => sanitize_key((string) ($_POST['device'] ?? 'all')),
            'width' => absint($_POST['width'] ?? 0) ?: null,
            'height' => absint($_POST['height'] ?? 0) ?: null,
            'mime' => sanitize_text_field((string) ($_POST['mime'] ?? '')),
            'filesize' => absint($_POST['filesize'] ?? 0) ?: null,
            'checksum' => sanitize_text_field((string) ($_POST['checksum'] ?? '')),
            'status' => sanitize_key((string) ($_POST['status'] ?? 'draft')),
            'updated_at' => $now,
        ];
        $table = M360_Ads_DB::table('ad_creatives');
        if ($id) { $wpdb->update($table, $data, ['id' => $id]); }
        else { $data['created_at'] = $now; $wpdb->insert($table, $data); }
        wp_safe_redirect(admin_url('admin.php?page=m360-ads-creatives'));
        exit;
    }

    public static function delete_creative(): void
    {
        self::guard();
        check_admin_referer('m360_ads_delete_creative');
        global $wpdb;
        $id = absint($_GET['id'] ?? 0);
        if ($id) { $wpdb->delete(M360_Ads_DB::table('ad_creatives'), ['id' => $id]); }
        wp_safe_redirect(admin_url('admin.php?page=m360-ads-creatives'));
        exit;
    }

    private static function guard(): void { if (!current_user_can('manage_options')) { wp_die('Acesso negado.'); } }
    private static function input(string $label, string $name, string $value, bool $required = false): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><input class="regular-text" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '"' . ($required ? ' required' : '') . '></td></tr>'; }
    private static function media_input(string $label, string $name, string $value): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><input class="regular-text m360-ads-media-url" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '"> <button type="button" class="button m360-ads-media-pick">Selecionar imagem</button><div class="m360-ads-media-preview">' . ($value ? '<img src="' . esc_url($value) . '">' : '') . '</div></td></tr>'; }
    private static function textarea(string $label, string $name, string $value): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><textarea class="large-text code" rows="6" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '">' . esc_textarea($value) . '</textarea></td></tr>'; }
    private static function select(string $label, string $name, string $value, array $options): void { echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><select id="' . esc_attr($name) . '" name="' . esc_attr($name) . '">'; foreach ($options as $k=>$v) { echo '<option value="' . esc_attr($k) . '"' . selected($value, $k, false) . '>' . esc_html($v) . '</option>'; } echo '</select></td></tr>'; }
    private static function campaign_select(array $campaigns, int $current): void { echo '<tr><th><label for="campaign_id">Campanha</label></th><td><select id="campaign_id" name="campaign_id"><option value="0">Nenhuma</option>'; foreach ($campaigns as $c) { echo '<option value="' . esc_attr((string) $c['id']) . '"' . selected($current, (int) $c['id'], false) . '>' . esc_html($c['title']) . '</option>'; } echo '</select></td></tr>'; }
    private static function preset_select(string $width, string $height): void
    {
        $current = ($width && $height) ? $width . 'x' . $height : 'custom';
        $options = ['custom'=>'Personalizado','300x250'=>'300x250 - Medium Rectangle','728x90'=>'728x90 - Leaderboard','970x250'=>'970x250 - Billboard','1200x250'=>'1200x250 - Super Banner','320x100'=>'320x100 - Mobile Banner','300x600'=>'300x600 - Half Page','responsive'=>'Responsivo'];
        echo '<tr><th><label for="m360_ad_size_preset">Formato</label></th><td><select id="m360_ad_size_preset" class="m360-ad-size-preset">';
        foreach ($options as $k=>$v) { echo '<option value="' . esc_attr($k) . '"' . selected($current, $k, false) . '>' . esc_html($v) . '</option>'; }
        echo '</select><p class="description">Ao selecionar um formato, largura e altura são preenchidas automaticamente. Use Personalizado para ajustar manualmente.</p></td></tr>';
    }
}
