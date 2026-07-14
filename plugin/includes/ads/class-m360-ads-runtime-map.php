<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ads_Runtime_Map
{
    private const MAP = [
        'header-top' => ['status'=>'runtime','source'=>'header-orchestrator','trigger'=>'Primeira etapa: campanha elegível'],
        'header-adsense' => ['status'=>'runtime','source'=>'header-orchestrator','trigger'=>'Segunda etapa: fallback AdSense'],
        'article-inline-1' => ['status'=>'runtime','source'=>'inline-engine','trigger'=>'Após o segundo parágrafo'],
        'article-after-paragraph-2' => ['status'=>'legacy','source'=>'inline-engine','trigger'=>'Fallback compatível após o segundo parágrafo'],
        'search-inline' => ['status'=>'runtime','source'=>'archive-engine','trigger'=>'Após o terceiro resultado'],
        'category-inline' => ['status'=>'runtime','source'=>'archive-engine','trigger'=>'Após o terceiro item'],
        'tag-inline' => ['status'=>'runtime','source'=>'archive-engine','trigger'=>'Após o terceiro item'],
        'author-inline' => ['status'=>'runtime','source'=>'archive-engine','trigger'=>'Após o terceiro item'],
        'latest-inline' => ['status'=>'runtime','source'=>'archive-engine','trigger'=>'Após o quarto item'],
        'archive-inline' => ['status'=>'runtime','source'=>'archive-engine','trigger'=>'Após o terceiro item'],
        'search-empty' => ['status'=>'runtime','source'=>'archive-engine','trigger'=>'Estado vazio da busca'],
        'article-inline-2' => ['status'=>'planned','source'=>'-','trigger'=>'Reservado; sem inserção automática'],
        'article-inline-3' => ['status'=>'planned','source'=>'-','trigger'=>'Reservado; sem inserção automática'],
    ];

    public static function describe(string $slot_key): array
    {
        $slot_key = sanitize_key($slot_key);
        return self::MAP[$slot_key] ?? ['status'=>'manual','source'=>'shortcode/api','trigger'=>'Renderização manual por shortcode, widget ou template'];
    }

    public static function article_primary_slot(): string
    {
        if (self::has_active_campaign('article-inline-1')) { return 'article-inline-1'; }
        if (self::has_active_campaign('article-after-paragraph-2')) { return 'article-after-paragraph-2'; }
        return 'article-inline-1';
    }

    public static function has_active_campaign(string $slot_key): bool
    {
        global $wpdb;
        $slots = M360_Ads_DB::table('ad_slots');
        $relations = M360_Ads_DB::table('ad_slot_campaigns');
        $campaigns = M360_Ads_DB::table('ad_campaigns');
        $now = current_time('mysql');
        $sql = $wpdb->prepare("SELECT COUNT(*) FROM {$slots} s INNER JOIN {$relations} r ON r.slot_id=s.id INNER JOIN {$campaigns} c ON c.id=r.campaign_id WHERE s.slot_key=%s AND s.is_active=1 AND r.is_active=1 AND c.status='active' AND (c.start_at IS NULL OR c.start_at<=%s) AND (c.end_at IS NULL OR c.end_at>=%s)", sanitize_key($slot_key), $now, $now);
        return (int) $wpdb->get_var($sql) > 0;
    }
}
