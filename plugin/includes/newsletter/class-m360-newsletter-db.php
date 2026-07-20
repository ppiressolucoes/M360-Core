<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Newsletter_DB
{
    private const SCHEMA_VERSION = '2';
    public static function consents_table(): string { global $wpdb; return $wpdb->prefix . 'm360_newsletter_consents'; }
    public static function tokens_table(): string { global $wpdb; return $wpdb->prefix . 'm360_newsletter_tokens'; }
    public static function events_table(): string { global $wpdb; return $wpdb->prefix . 'm360_newsletter_events'; }

    public static function install(): void
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset = $wpdb->get_charset_collate();
        dbDelta("CREATE TABLE " . self::consents_table() . " (id bigint(20) unsigned NOT NULL AUTO_INCREMENT, email varchar(254) NOT NULL, name varchar(190) NOT NULL DEFAULT '', consent_version varchar(64) NOT NULL, accepted_at datetime NOT NULL, ip varchar(64) NOT NULL DEFAULT '', user_agent text NOT NULL, source varchar(100) NOT NULL, status varchar(30) NOT NULL DEFAULT 'pending', provider varchar(64) NOT NULL DEFAULT 'mailpoet', provider_id varchar(190) NOT NULL DEFAULT '', created_at datetime NOT NULL, updated_at datetime NOT NULL, PRIMARY KEY (id), UNIQUE KEY email (email), KEY status (status)) $charset;");
        dbDelta("CREATE TABLE " . self::tokens_table() . " (id bigint(20) unsigned NOT NULL AUTO_INCREMENT, email varchar(254) NOT NULL, token_hash char(64) NOT NULL, expires_at datetime NOT NULL, used_at datetime NULL, created_at datetime NOT NULL, PRIMARY KEY (id), UNIQUE KEY token_hash (token_hash), KEY email (email), KEY expires_at (expires_at)) $charset;");
        dbDelta("CREATE TABLE " . self::events_table() . " (id bigint(20) unsigned NOT NULL AUTO_INCREMENT, event_type varchar(80) NOT NULL, email_hash char(64) NOT NULL DEFAULT '', email_masked varchar(254) NOT NULL DEFAULT '', old_status varchar(30) NOT NULL DEFAULT '', new_status varchar(30) NOT NULL DEFAULT '', provider_status varchar(30) NOT NULL DEFAULT '', context text NOT NULL, created_at datetime NOT NULL, PRIMARY KEY (id), KEY event_type (event_type), KEY created_at (created_at), KEY email_hash (email_hash)) $charset;");
        update_option('m360_newsletter_schema_version', self::SCHEMA_VERSION, false);
    }

    public static function maybe_upgrade(): void { if (get_option('m360_newsletter_schema_version') !== self::SCHEMA_VERSION) { self::install(); } }

    public static function cleanup(): void
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM " . self::tokens_table() . " WHERE expires_at < %s", gmdate('Y-m-d H:i:s',time()-30*DAY_IN_SECONDS)));
        $days=max(30,min(730,absint(apply_filters('m360_newsletter_audit_retention_days',365))));
        $wpdb->query($wpdb->prepare("DELETE FROM " . self::events_table() . " WHERE created_at < %s",gmdate('Y-m-d H:i:s',time()-$days*DAY_IN_SECONDS)));
    }
}
