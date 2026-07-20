<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Newsletter_Settings
{
    private const OPTION = 'm360_newsletter_settings';

    public static function activate(): void
    {
        if (get_option(self::OPTION, null) === null) { add_option(self::OPTION, self::defaults(), '', false); }
    }
    public static function get(): array
    {
        $saved=get_option(self::OPTION,[]);
        return wp_parse_args(is_array($saved)?$saved:[],self::defaults());
    }
    public static function update(array $input): bool
    {
        $current=self::get();
        $settings=array_merge($current,[
            'list_id'=>max(1,absint($input['list_id']??$current['list_id'])),
            'consent_version'=>sanitize_key((string)($input['consent_version']??$current['consent_version'])),
            'consent_text_pt'=>sanitize_text_field((string)($input['consent_text_pt']??$current['consent_text_pt'])),
            'consent_text_en'=>sanitize_text_field((string)($input['consent_text_en']??$current['consent_text_en'])),
            'minimum_form_seconds'=>max(1,min(30,absint($input['minimum_form_seconds']??$current['minimum_form_seconds']))),
            'ip_limit'=>max(1,min(100,absint($input['ip_limit']??$current['ip_limit']))),
            'ip_window_minutes'=>max(1,min(1440,absint($input['ip_window_minutes']??$current['ip_window_minutes']))),
            'article_end_enabled'=>isset($input['article_end_enabled'])?!empty($input['article_end_enabled']):!empty($current['article_end_enabled']),
            'form_title'=>sanitize_text_field((string)($input['form_title']??$current['form_title'])),
            'form_description'=>sanitize_text_field((string)($input['form_description']??$current['form_description'])),
            'form_button'=>sanitize_text_field((string)($input['form_button']??$current['form_button'])),
            'form_title_en'=>sanitize_text_field((string)($input['form_title_en']??$current['form_title_en'])),
            'form_description_en'=>sanitize_text_field((string)($input['form_description_en']??$current['form_description_en'])),
            'form_button_en'=>sanitize_text_field((string)($input['form_button_en']??$current['form_button_en'])),
            'hide_days'=>max(1,min(365,absint($input['hide_days']??$current['hide_days']))),
        ]);
        if ($settings['consent_version']==='' || $settings['consent_text_pt']==='' || $settings['consent_text_en']==='' || $settings['form_title']==='' || $settings['form_button']==='' || $settings['form_title_en']==='' || $settings['form_button_en']==='') { return false; }
        update_option(self::OPTION,$settings,false);
        return true;
    }
    public static function update_delivery(array $input): bool
    {
        $settings=self::get();
        $settings['sender_email']=sanitize_email((string)($input['sender_email']??''));
        $settings['editorial_owner']=sanitize_text_field((string)($input['editorial_owner']??''));
        $settings['dkim_confirmed']=!empty($input['dkim_confirmed']);
        $settings['dmarc_confirmed']=!empty($input['dmarc_confirmed']);
        $settings['sending_test_confirmed']=!empty($input['sending_test_confirmed']);
        if ($settings['sending_test_confirmed']) { $settings['sending_test_at']=current_time('mysql',true); }
        if (!$settings['sending_test_confirmed']) { $settings['sending_test_at']=''; }
        if (!is_email($settings['sender_email']) || $settings['editorial_owner']==='') { return false; }
        update_option(self::OPTION,$settings,false); return true;
    }
    private static function defaults(): array
    {
        return ['list_id'=>3,'consent_version'=>'v0.6.1','consent_text_pt'=>'Concordo em receber a Newsletter M360.','consent_text_en'=>'I agree to receive the M360 Newsletter.','minimum_form_seconds'=>2,'ip_limit'=>5,'ip_window_minutes'=>10,'article_end_enabled'=>false,'form_title'=>'Receba as notícias do Mengão 360','form_description'=>'As principais notícias do Flamengo diretamente no seu e-mail.','form_button'=>'Inscrever-me','form_title_en'=>'Get the latest Mengão 360 news','form_description_en'=>'The latest Flamengo news delivered directly to your inbox.','form_button_en'=>'Subscribe','hide_days'=>30,'sender_email'=>'','editorial_owner'=>'','dkim_confirmed'=>false,'dmarc_confirmed'=>false,'sending_test_confirmed'=>false,'sending_test_at'=>''];
    }
}
