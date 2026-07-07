-- M360 Ads Database Schema
-- Version: 0.4.2.4-docs
-- Purpose: reference SQL for documentation and future dbDelta implementation.
-- Note: WordPress table prefix is represented as wp_. Future runtime must use $wpdb->prefix.

CREATE TABLE IF NOT EXISTS wp_m360_ad_slots (
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
    PRIMARY KEY (id),
    UNIQUE KEY slot_key (slot_key),
    KEY page_context (page_context),
    KEY language (language),
    KEY device (device),
    KEY is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wp_m360_ad_campaigns (
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
    PRIMARY KEY (id),
    KEY campaign_type (campaign_type),
    KEY language (language),
    KEY device (device),
    KEY status (status),
    KEY priority (priority),
    KEY start_at (start_at),
    KEY end_at (end_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wp_m360_ad_slot_campaigns (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slot_id BIGINT UNSIGNED NOT NULL,
    campaign_id BIGINT UNSIGNED NOT NULL,
    priority INT NOT NULL DEFAULT 50,
    weight INT NOT NULL DEFAULT 100,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY slot_campaign (slot_id, campaign_id),
    KEY slot_id (slot_id),
    KEY campaign_id (campaign_id),
    KEY priority (priority),
    KEY is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Future high-volume table. Do not create in the first implementation without retention policy.
-- CREATE TABLE IF NOT EXISTS wp_m360_ad_events (
--     id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
--     slot_id BIGINT UNSIGNED NULL,
--     campaign_id BIGINT UNSIGNED NULL,
--     event_type VARCHAR(30) NOT NULL,
--     page_url TEXT NULL,
--     post_id BIGINT UNSIGNED NULL,
--     language VARCHAR(10) NULL,
--     device VARCHAR(20) NULL,
--     created_at DATETIME NOT NULL,
--     PRIMARY KEY (id),
--     KEY slot_id (slot_id),
--     KEY campaign_id (campaign_id),
--     KEY event_type (event_type),
--     KEY post_id (post_id),
--     KEY created_at (created_at)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
