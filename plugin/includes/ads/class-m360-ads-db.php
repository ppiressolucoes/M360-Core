<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ads_DB
{
    public const SCHEMA_VERSION = '0.4.2.6';

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

        self::seed_default_slots();
        self::ensure_upload_dir();
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

    private static function seed_default_slots(): void
    {
        global $wpdb;
        $table = self::table('ad_slots');
        $now = current_time('mysql');
        $slots = [
            ['header-banner', 'Header Banner', 'Banner principal no topo do portal.', 'header', 'global', 'all', 'desktop', 970, 250],
            ['header-mobile', 'Header Mobile', 'Banner principal no topo para mobile.', 'header', 'global', 'all', 'mobile', 320, 100],
            ['home-top', 'Home Top', 'Slot superior da home editorial.', 'top', 'home', 'all', 'all', 1200, 250],
            ['home-middle', 'Home Middle', 'Slot intermediario da home editorial.', 'middle', 'home', 'all', 'all', 1200, 250],
            ['home-bottom', 'Home Bottom', 'Slot inferior da home editorial.', 'bottom', 'home', 'all', 'all', 1200, 250],
            ['article-top', 'Article Top', 'Slot no topo do artigo.', 'top', 'post', 'all', 'all', 970, 250],
            ['article-inline-1', 'Article Inline 1', 'Primeiro slot interno do artigo.', 'inline', 'post', 'all', 'all', 728, 90],
            ['article-inline-2', 'Article Inline 2', 'Segundo slot interno do artigo.', 'inline', 'post', 'all', 'all', 728, 90],
            ['article-bottom', 'Article Bottom', 'Slot inferior do artigo.', 'bottom', 'post', 'all', 'all', 970, 250],
            ['sidebar-top', 'Sidebar Top', 'Slot superior da sidebar.', 'sidebar', 'global', 'all', 'desktop', 300, 250],
            ['sidebar-middle', 'Sidebar Middle', 'Slot intermediario da sidebar.', 'sidebar', 'global', 'all', 'desktop', 300, 250],
            ['sidebar-bottom', 'Sidebar Bottom', 'Slot inferior da sidebar.', 'sidebar', 'global', 'all', 'desktop', 300, 600],
            ['footer-banner', 'Footer Banner', 'Banner no rodape.', 'footer', 'global', 'all', 'all', 970, 250],
            ['category-top', 'Category Top', 'Slot superior em paginas de categoria.', 'top', 'category', 'all', 'all', 970, 250],
            ['tag-top', 'Tag Top', 'Slot superior em paginas de tag.', 'top', 'tag', 'all', 'all', 970, 250],
            ['author-top', 'Author Top', 'Slot superior em paginas de autor.', 'top', 'author', 'all', 'all', 970, 250],
            ['search-top', 'Search Top', 'Slot superior em paginas de busca.', 'top', 'search', 'all', 'all', 970, 250],
            ['latest-news-inline', 'Latest News Inline', 'Slot associado ao componente de ultimas noticias.', 'inline', 'latest-news', 'all', 'all', 728, 90],
        ];

        foreach ($slots as $slot) {
            $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE slot_key = %s LIMIT 1", $slot[0]));
            $data = ['slot_key'=>$slot[0],'name'=>$slot[1],'description'=>$slot[2],'position'=>$slot[3],'page_context'=>$slot[4],'language'=>$slot[5],'device'=>$slot[6],'max_width'=>$slot[7],'max_height'=>$slot[8],'is_active'=>1,'updated_at'=>$now];
            if ($exists) { $wpdb->update($table, $data, ['id' => (int) $exists]); }
            else { $data['created_at'] = $now; $wpdb->insert($table, $data); }
        }
    }
}
