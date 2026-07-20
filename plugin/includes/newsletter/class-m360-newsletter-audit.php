<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Newsletter_Audit
{
    public static function register(): void
    {
        add_action('m360_newsletter_subscribed', static fn($email) => self::record('subscribed_requested', (string)$email));
        add_action('m360_newsletter_confirmed', static fn($email) => self::record('confirmed', (string)$email));
        add_action('m360_newsletter_unsubscribed', static fn($email) => self::record('unsubscribed', (string)$email));
        add_action('m360_newsletter_blocked', static fn($email, $reason='') => self::record('blocked', (string)$email, ['reason'=>(string)$reason]), 10, 2);
        add_action('m360_newsletter_provider_error', static fn($error, $email='') => self::record('provider_error', (string)$email, ['code'=>is_wp_error($error)?$error->get_error_code():'provider_error','message'=>is_wp_error($error)?$error->get_error_message():'']), 10, 2);
        add_action('m360_newsletter_status_synced', [self::class, 'status_synced'], 10, 4);
    }

    public static function status_synced(string $email, string $old, string $new, string $provider): void
    { self::record('status_synced', $email, ['old_status'=>$old, 'new_status'=>$new, 'provider_status'=>$provider]); }

    public static function record(string $type, string $email='', array $context=[]): void
    {
        global $wpdb; $normalized = strtolower(sanitize_email($email));
        $wpdb->insert(M360_Newsletter_DB::events_table(), [
            'event_type'=>sanitize_key($type), 'email_hash'=>$normalized ? hash('sha256',$normalized) : '',
            'email_masked'=>self::mask($normalized), 'old_status'=>sanitize_key((string)($context['old_status']??'')),
            'new_status'=>sanitize_key((string)($context['new_status']??'')), 'provider_status'=>sanitize_key((string)($context['provider_status']??'')),
            'context'=>wp_json_encode(array_diff_key($context, array_flip(['old_status','new_status','provider_status']))), 'created_at'=>current_time('mysql', true),
        ]);
    }

    private static function mask(string $email): string
    {
        if (!$email || !str_contains($email,'@')) { return ''; }
        [$local,$domain]=explode('@',$email,2); return substr($local,0,2) . str_repeat('*',max(2,strlen($local)-2)) . '@' . $domain;
    }
}
