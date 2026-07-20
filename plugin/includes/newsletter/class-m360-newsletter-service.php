<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Newsletter_Service
{
    private M360_Newsletter_Provider_Interface $provider;
    public function __construct(?M360_Newsletter_Provider_Interface $provider = null) { $this->provider = $provider ?: new M360_MailPoet_Adapter(); }

    public function subscribe(string $email, string $name, bool $consent, string $source = 'm360_form', array $form_context=[])
    {
        global $wpdb;
        $email = sanitize_email($email); $name = sanitize_text_field($name);
        $is_en=($form_context['lang']??'')==='en';
        if (!is_email($email)) { return new WP_Error('m360_newsletter_email', $is_en?'Enter a valid email address.':__('Informe um e-mail válido.', 'm360-core'), ['status' => 400]); }
        if (!$consent) { return new WP_Error('m360_newsletter_consent', $is_en?'Consent to receive the newsletter is required.':__('O consentimento para receber a newsletter é obrigatório.', 'm360-core'), ['status' => 400]); }
        $settings=M360_Newsletter_Settings::get();
        if (trim((string)($form_context['website']??''))!=='') { M360_Newsletter_Audit::record('spam_rejected',$email,['reason'=>'honeypot']); return new WP_Error('m360_newsletter_rejected',$is_en?'We could not complete your subscription.':'Não foi possível concluir a inscrição.',['status'=>400]); }
        $rendered_at=absint($form_context['rendered_at']??0);
        if (!$rendered_at || time()-$rendered_at<(int)$settings['minimum_form_seconds']) { M360_Newsletter_Audit::record('spam_rejected',$email,['reason'=>'too_fast']); return new WP_Error('m360_newsletter_rejected',$is_en?'Wait a few seconds and try again.':'Aguarde alguns segundos e tente novamente.',['status'=>400]); }
        // Limit a repeated address briefly, while allowing legitimate test recipients on the same connection.
        $email_limit = 'm360_newsletter_rate_email_' . md5($email);
        if (get_transient($email_limit)) { return new WP_Error('m360_newsletter_rate', $is_en?'This email has just received a request. Wait one minute and try again.':__('Este e-mail acabou de receber uma solicitação. Aguarde um minuto antes de tentar novamente.', 'm360-core'), ['status' => 429]); }
        $ip_limit = 'm360_newsletter_rate_ip_' . md5($this->ip());
        $ip_attempts = absint(get_transient($ip_limit));
        if ($ip_attempts >= (int)$settings['ip_limit']) { return new WP_Error('m360_newsletter_rate', $is_en?'Too many requests from this connection. Try again in a few minutes.':__('Muitas solicitações deste acesso. Tente novamente em alguns minutos.', 'm360-core'), ['status' => 429]); }
        $now = current_time('mysql', true); $token = bin2hex(random_bytes(32));
        $wpdb->query($wpdb->prepare("DELETE FROM " . M360_Newsletter_DB::tokens_table() . " WHERE email = %s AND used_at IS NULL", $email));
        $wpdb->replace(M360_Newsletter_DB::consents_table(), ['email'=>$email,'name'=>$name,'consent_version'=>(string)$settings['consent_version'],'accepted_at'=>$now,'ip'=>$this->ip(),'user_agent'=>substr(sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ?? '')),0,1000),'source'=>sanitize_key($source),'status'=>'pending','provider'=>'mailpoet','provider_id'=>'','created_at'=>$now,'updated_at'=>$now]);
        $wpdb->insert(M360_Newsletter_DB::tokens_table(), ['email'=>$email,'token_hash'=>hash('sha256',$token),'expires_at'=>gmdate('Y-m-d H:i:s', time() + 72 * HOUR_IN_SECONDS),'created_at'=>$now]);
        $sent = $this->provider->subscribe($email, $name, ['confirmation_url'=>home_url('/newsletter/confirm/' . $token),'lang'=>$is_en?'en':'pt']);
        if (is_wp_error($sent)) { do_action('m360_newsletter_provider_error', $sent, $email); return $sent; }
        set_transient($email_limit, 1, MINUTE_IN_SECONDS);
        set_transient($ip_limit, $ip_attempts + 1, (int)$settings['ip_window_minutes'] * MINUTE_IN_SECONDS);
        if (is_array($sent)) {
            $wpdb->update(M360_Newsletter_DB::consents_table(), ['provider'=>(string)($sent['provider'] ?? 'mailpoet'), 'provider_id'=>(string)($sent['provider_id'] ?? ''), 'updated_at'=>current_time('mysql', true)], ['email'=>$email]);
        }
        do_action('m360_newsletter_subscribed', $email);
        return true;
    }

    public function confirm(string $token)
    {
        global $wpdb; $hash = hash('sha256', $token);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . M360_Newsletter_DB::tokens_table() . " WHERE token_hash = %s", $hash));
        if (!$row || $row->used_at || strtotime($row->expires_at . ' UTC') < time()) { return new WP_Error('m360_newsletter_token', __('Este link é inválido ou expirou.', 'm360-core')); }
        $result = $this->provider->confirm($row->email); if (is_wp_error($result)) { do_action('m360_newsletter_confirmation_failed', $result, $row->email); return $result; }
        $now = current_time('mysql', true); $wpdb->update(M360_Newsletter_DB::tokens_table(), ['used_at'=>$now], ['id'=>$row->id]);
        $wpdb->update(M360_Newsletter_DB::consents_table(), ['status'=>'confirmed','provider'=>(string)($result['provider'] ?? 'mailpoet'),'provider_id'=>(string)($result['provider_id'] ?? ''),'updated_at'=>$now], ['email'=>$row->email]);
        do_action('m360_newsletter_confirmed', $row->email); return true;
    }

    public function unsubscribe(string $email)
    {
        global $wpdb; $email = sanitize_email($email); if (!is_email($email)) { return new WP_Error('m360_newsletter_email', __('Informe um e-mail válido.', 'm360-core'), ['status'=>400]); }
        $result = $this->provider->unsubscribe($email); if (is_wp_error($result)) { return $result; }
        $wpdb->update(M360_Newsletter_DB::consents_table(), ['status'=>'unsubscribed','updated_at'=>current_time('mysql', true)], ['email'=>$email]); do_action('m360_newsletter_unsubscribed', $email); return true;
    }
    public function sync_statuses(): array
    {
        global $wpdb;
        $summary = ['checked' => 0, 'updated' => 0, 'errors' => 0];
        $rows = $wpdb->get_results("SELECT email, status FROM " . M360_Newsletter_DB::consents_table() . " WHERE status IN ('pending', 'confirmed', 'unsubscribed', 'blocked') ORDER BY updated_at ASC LIMIT 500");
        foreach ($rows as $row) {
            $summary['checked']++;
            $remote = $this->provider->status((string) $row->email);
            if (is_wp_error($remote)) {
                $summary['errors']++;
                do_action('m360_newsletter_provider_error', $remote, (string) $row->email);
                continue;
            }
            $provider_status = strtolower((string) ($remote['status'] ?? ''));
            $status_map = ['unconfirmed'=>'pending', 'subscribed'=>'confirmed', 'unsubscribed'=>'unsubscribed', 'bounced'=>'blocked'];
            $local_status = $status_map[$provider_status] ?? '';
            if ($local_status === '' || $local_status === (string) $row->status) { continue; }
            $updated = $wpdb->update(
                M360_Newsletter_DB::consents_table(),
                ['status'=>$local_status, 'provider'=>'mailpoet', 'provider_id'=>(string)($remote['id'] ?? ''), 'updated_at'=>current_time('mysql', true)],
                ['email'=>$row->email]
            );
            if ($updated === false) { $summary['errors']++; continue; }
            $summary['updated']++;
            if ($local_status === 'confirmed') { do_action('m360_newsletter_confirmed', $row->email); }
            if ($local_status === 'unsubscribed') { do_action('m360_newsletter_unsubscribed', $row->email); }
            if ($local_status === 'blocked') { do_action('m360_newsletter_blocked', $row->email, $provider_status); }
            do_action('m360_newsletter_status_synced', $row->email, (string) $row->status, $local_status, $provider_status);
        }
        return $summary;
    }

    /** Backward-compatible alias for the original v0.6.1 cron callback. */
    public function sync_pending(): void { $this->sync_statuses(); }
    private function ip(): string { return substr(sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? '')), 0, 64); }
}
