<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ads_DB
{
    public const SCHEMA_VERSION = '0.4.4.1';

    public static function install(): void
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset_collate = $wpdb->get_charset_collate();
        $slots = self::table('ad_slots');
        $campaigns = self::table('ad_campaigns');
        $relations = self::table('ad_slot_campaigns');
        $creatives = self::table('ad_creatives');

        dbDelta("CREATE TABLE {$slots} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            slot_key VARCHAR(120) NOT NULL,
            name VARCHAR(180) NOT NULL,
            description TEXT NULL,
            position VARCHAR(80) NULL,
            page_context VARCHAR(80) NOT NULL DEFAULT 'global',
            language VARCHAR(10) NOT NULL DEFAULT 'all',
            device VARCHAR(20) NOT NULL DEFAULT 'all',
            max_width INT UNSIGNED NULL,
            max_height INT UNSIGNED NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY slot_key (slot_key),
            KEY page_context (page_context),
            KEY language (language),
            KEY device (device),
            KEY is_active (is_active)
        ) {$charset_collate};");

        dbDelta("CREATE TABLE {$campaigns} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(220) NOT NULL,
            advertiser VARCHAR(180) NULL,
            campaign_type VARCHAR(40) NOT NULL DEFAULT 'image',
            image_url TEXT NULL,
            target_url TEXT NULL,
            html_code LONGTEXT NULL,
            script_code LONGTEXT NULL,
            alt_text VARCHAR(220) NULL,
            language VARCHAR(10) NOT NULL DEFAULT 'all',
            device VARCHAR(20) NOT NULL DEFAULT 'all',
            start_at DATETIME NULL,
            end_at DATETIME NULL,
            priority INT NOT NULL DEFAULT 50,
            status VARCHAR(30) NOT NULL DEFAULT 'draft',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY campaign_type (campaign_type),
            KEY language (language),
            KEY device (device),
            KEY status (status),
            KEY priority (priority),
            KEY start_at (start_at),
            KEY end_at (end_at)
        ) {$charset_collate};");

        dbDelta("CREATE TABLE {$relations} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            slot_id BIGINT UNSIGNED NOT NULL,
            campaign_id BIGINT UNSIGNED NOT NULL,
            priority INT NOT NULL DEFAULT 50,
            weight INT NOT NULL DEFAULT 100,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY slot_campaign (slot_id, campaign_id),
            KEY slot_id (slot_id),
            KEY campaign_id (campaign_id),
            KEY priority (priority),
            KEY is_active (is_active)
        ) {$charset_collate};");

        dbDelta("CREATE TABLE {$creatives} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            campaign_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            title VARCHAR(220) NOT NULL,
            slug VARCHAR(180) NOT NULL,
            creative_type VARCHAR(40) NOT NULL DEFAULT 'image',
            image_url TEXT NULL,
            target_url TEXT NULL,
            html_code LONGTEXT NULL,
            script_code LONGTEXT NULL,
            alt_text VARCHAR(220) NULL,
            language VARCHAR(10) NOT NULL DEFAULT 'all',
            device VARCHAR(20) NOT NULL DEFAULT 'all',
            width INT UNSIGNED NULL,
            height INT UNSIGNED NULL,
            mime VARCHAR(120) NULL,
            filesize BIGINT UNSIGNED NULL,
            checksum VARCHAR(80) NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'draft',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY campaign_id (campaign_id),
            KEY slug (slug),
            KEY creative_type (creative_type),
            KEY language (language),
            KEY device (device),
            KEY status (status)
        ) {$charset_collate};");

        self::seed_inventory_library();
        self::seed_production_pilot();
        self::ensure_upload_dir();
        update_option('m360_ads_inventory_library_version', M360_Ads_Inventory_Library::VERSION, false);
        update_option('m360_ads_inventory_seeded_at', current_time('mysql'), false);
        update_option('m360_ads_db_version', self::SCHEMA_VERSION, false);
    }

    public static function maybe_upgrade(): void
    {
        $current = (string) get_option('m360_ads_db_version', '');
        if ($current !== self::SCHEMA_VERSION) { self::install(); }
    }

    public static function table(string $name): string
    {
        global $wpdb;
        return $wpdb->prefix . 'm360_' . $name;
    }

    public static function ensure_upload_dir(): string
    {
        $upload = wp_upload_dir();
        $base = trailingslashit((string) ($upload['basedir'] ?? '')) . 'm360-ads';
        if (!is_dir($base)) { wp_mkdir_p($base); }
        return $base;
    }

    public static function pilot_slots(): array
    {
        return [
            'header-top' => 'Header Top 728x140',
            'content-bottom' => 'Content Bottom WhatsApp',
            'sidebar-community' => 'Sidebar Community CTA',
            'sidebar-square' => 'Sidebar Square 1:1',
        ];
    }

    public static function seed_inventory_library(): void
    {
        foreach (M360_Ads_Inventory_Library::slots() as $slot) { self::upsert_slot($slot); }
    }

    public static function inventory_count(): int
    {
        return count(M360_Ads_Inventory_Library::slots());
    }

    public static function inventory_contexts(): array
    {
        return M360_Ads_Inventory_Library::contexts();
    }

    private static function upsert_slot(array $slot): void
    {
        global $wpdb;
        $table = self::table('ad_slots');
        $now = current_time('mysql');
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE slot_key = %s LIMIT 1", $slot[0]));
        $data = [
            'slot_key' => sanitize_key((string) $slot[0]),
            'name' => sanitize_text_field((string) $slot[1]),
            'description' => sanitize_textarea_field((string) $slot[2]),
            'position' => sanitize_key((string) $slot[3]),
            'page_context' => sanitize_key((string) $slot[4]),
            'language' => sanitize_key((string) $slot[5]),
            'device' => sanitize_key((string) $slot[6]),
            'max_width' => absint($slot[7]),
            'max_height' => absint($slot[8]),
            'updated_at' => $now,
        ];
        if ($exists) { $wpdb->update($table, $data, ['id' => (int) $exists]); }
        else { $data['is_active'] = 1; $data['created_at'] = $now; $wpdb->insert($table, $data); }
    }

    private static function seed_production_pilot(): void
    {
        $mega = self::upsert_campaign('m360-pilot-mega-bolao', 'M360 Pilot - Mega Bolão 360', 'Mega Bolão 360', 'house', 100);
        $whatsapp = self::upsert_campaign('m360-pilot-whatsapp', 'M360 Pilot - Comunidade WhatsApp', 'Mengão 360', 'house', 90);
        self::upsert_creative($mega, 'm360-pilot-header-mega-bolao', 'Header Mega Bolão 728x140', 'image', '/wp-content/uploads/2026/06/BANNER-HORIZONTAL-MEGA-BOLAO-360-728X140.jpg', '', '', 728, 140);
        self::upsert_creative($mega, 'm360-pilot-sidebar-mega-bolao', 'Sidebar Mega Bolão 1:1', 'html', '', '', self::mega_square_html(), 300, 300);
        self::upsert_creative($whatsapp, 'm360-pilot-content-whatsapp', 'Content Bottom Comunidade WhatsApp', 'html', '', '', self::whatsapp_horizontal_html(), 1200, 250);
        self::upsert_creative($whatsapp, 'm360-pilot-sidebar-whatsapp', 'Sidebar Comunidade WhatsApp', 'html', '', '', self::whatsapp_sidebar_html(), 300, 300);
        self::assign_slot('header-top', $mega, 100);
        self::assign_slot('content-bottom', $whatsapp, 100);
        self::assign_slot('sidebar-community', $whatsapp, 90);
        self::assign_slot('sidebar-square', $mega, 80);
    }

    private static function upsert_campaign(string $slug, string $title, string $advertiser, string $type, int $priority): int
    {
        global $wpdb;
        $table = self::table('ad_campaigns');
        $now = current_time('mysql');
        $id = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE title = %s LIMIT 1", $title));
        $data = ['title'=>$title,'advertiser'=>$advertiser,'campaign_type'=>$type,'language'=>'all','device'=>'all','priority'=>$priority,'status'=>'active','updated_at'=>$now];
        if ($id) { $wpdb->update($table, $data, ['id'=>$id]); return $id; }
        $data['created_at'] = $now;
        $wpdb->insert($table, $data);
        return (int) $wpdb->insert_id;
    }

    private static function upsert_creative(int $campaign_id, string $slug, string $title, string $type, string $image_url, string $target_url, string $html, int $width, int $height): void
    {
        global $wpdb;
        $table = self::table('ad_creatives');
        $now = current_time('mysql');
        $id = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE slug = %s LIMIT 1", $slug));
        $data = ['campaign_id'=>$campaign_id,'title'=>$title,'slug'=>$slug,'creative_type'=>$type,'image_url'=>$image_url,'target_url'=>$target_url,'html_code'=>$html,'alt_text'=>$title,'language'=>'all','device'=>'all','width'=>$width,'height'=>$height,'mime'=>$type === 'image' ? 'image/jpeg' : 'text/html','status'=>'active','updated_at'=>$now];
        if ($id) { $wpdb->update($table, $data, ['id'=>$id]); }
        else { $data['created_at'] = $now; $wpdb->insert($table, $data); }
    }

    private static function assign_slot(string $slot_key, int $campaign_id, int $priority): void
    {
        global $wpdb;
        $slots = self::table('ad_slots');
        $relations = self::table('ad_slot_campaigns');
        $now = current_time('mysql');
        $slot_id = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$slots} WHERE slot_key = %s LIMIT 1", $slot_key));
        if (!$slot_id || !$campaign_id) { return; }
        $id = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$relations} WHERE slot_id = %d AND campaign_id = %d LIMIT 1", $slot_id, $campaign_id));
        $data = ['slot_id'=>$slot_id,'campaign_id'=>$campaign_id,'priority'=>$priority,'weight'=>100,'is_active'=>1,'updated_at'=>$now];
        if ($id) { $wpdb->update($relations, $data, ['id'=>$id]); }
        else { $data['created_at'] = $now; $wpdb->insert($relations, $data); }
    }

    private static function whatsapp_horizontal_html(): string
    {
        return '<div class="m360-house-ad m360-house-ad--whatsapp-horizontal"><div class="m360-house-ad__icon">💬</div><div class="m360-house-ad__body"><span>COMUNIDADE OFICIAL</span><strong>Entre na comunidade Mega Bolão 360</strong><p>Novidades, notícias e atualizações das competições em um só lugar.</p></div><a href="#" class="m360-house-ad__button">WHATSAPP ›</a></div>';
    }

    private static function whatsapp_sidebar_html(): string
    {
        return '<div class="m360-house-ad m360-house-ad--whatsapp-card"><span>🏆 MEGA BOLÃO 360</span><strong>O maior torneio da história:</strong><p>Crie seu bolão grátis e acompanhe tudo em tempo real.</p><a href="#" class="m360-house-ad__button">Entrar na comunidade</a></div>';
    }

    private static function mega_square_html(): string
    {
        return '<div class="m360-house-ad m360-house-ad--mega-square"><span>MEGA BOLÃO 360</span><strong>Crie, convide e acompanhe</strong><p>Monte seu grupo e dispute rankings em tempo real.</p><a href="#" class="m360-house-ad__button">Criar bolão grátis</a></div>';
    }
}
