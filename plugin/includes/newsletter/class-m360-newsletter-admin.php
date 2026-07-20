<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Newsletter_Admin
{
    public static function register(): void
    {
        add_action('admin_menu', [self::class,'menu'], 35);
        add_action('admin_post_m360_newsletter_sync', [self::class,'manual_sync']);
        add_action('admin_post_m360_newsletter_save_settings', [self::class,'save_settings']);
        add_action('admin_post_m360_newsletter_save_delivery', [self::class,'save_delivery']);
    }
    public static function menu(): void
    { add_submenu_page('m360-ads-manager','M360 Newsletter','Newsletter','manage_options','m360-newsletter-operations',[self::class,'render']); }

    public static function manual_sync(): void
    {
        self::guard(); check_admin_referer('m360_newsletter_sync');
        $summary=(new M360_Newsletter_Service())->sync_statuses();
        update_option('m360_newsletter_last_sync_at',current_time('mysql',true),false);
        update_option('m360_newsletter_last_sync_summary',$summary,false);
        M360_Newsletter_Audit::record('manual_sync','',['checked'=>$summary['checked'],'updated'=>$summary['updated'],'errors'=>$summary['errors']]);
        wp_safe_redirect(add_query_arg(['page'=>'m360-newsletter-operations','synced'=>'1'],admin_url('admin.php'))); exit;
    }
    public static function save_settings(): void
    {
        self::guard(); check_admin_referer('m360_newsletter_save_settings');
        $input=is_array($_POST['m360_newsletter']??null)?wp_unslash($_POST['m360_newsletter']):[];
        $list_id=absint($input['list_id']??0); $lists=(new M360_MailPoet_Adapter($list_id?:null))->lists(); $valid=false;
        if (!is_wp_error($lists)) { foreach ($lists as $list) { if ((int)($list['id']??0)===$list_id) { $valid=true; break; } } }
        if (!$valid || !M360_Newsletter_Settings::update($input)) { wp_safe_redirect(add_query_arg(['page'=>'m360-newsletter-operations','settings_error'=>'1'],admin_url('admin.php'))); exit; }
        M360_Newsletter_Audit::record('settings_updated','',['list_id'=>$list_id]);
        wp_safe_redirect(add_query_arg(['page'=>'m360-newsletter-operations','settings_saved'=>'1'],admin_url('admin.php'))); exit;
    }

    public static function save_delivery(): void
    {
        self::guard(); check_admin_referer('m360_newsletter_save_delivery');
        $input=is_array($_POST['m360_delivery']??null)?wp_unslash($_POST['m360_delivery']):[];
        if (!M360_Newsletter_Settings::update_delivery($input)) { wp_safe_redirect(add_query_arg(['page'=>'m360-newsletter-operations','delivery_error'=>'1'],admin_url('admin.php'))); exit; }
        M360_Newsletter_Audit::record('delivery_readiness_updated','',['dkim'=>!empty($input['dkim_confirmed']),'dmarc'=>!empty($input['dmarc_confirmed']),'test'=>!empty($input['sending_test_confirmed'])]);
        wp_safe_redirect(add_query_arg(['page'=>'m360-newsletter-operations','delivery_saved'=>'1'],admin_url('admin.php'))); exit;
    }

    public static function render(): void
    {
        self::guard(); global $wpdb;
        $table=M360_Newsletter_DB::consents_table(); $events=M360_Newsletter_DB::events_table();
        $counts=array_fill_keys(['pending','confirmed','unsubscribed','blocked'],0);
        foreach ((array)$wpdb->get_results("SELECT status,COUNT(*) total FROM {$table} GROUP BY status",ARRAY_A) as $row) { $counts[(string)$row['status']]=(int)$row['total']; }
        $source_counts=$wpdb->get_results("SELECT source,COUNT(*) total FROM {$table} GROUP BY source ORDER BY total DESC",ARRAY_A);
        $health=(new M360_MailPoet_Adapter())->healthCheck();
        $adapter=new M360_MailPoet_Adapter(); $lists=$adapter->lists(); $settings=M360_Newsletter_Settings::get();
        $next=wp_next_scheduled('m360_newsletter_sync_pending'); $last=(string)get_option('m360_newsletter_last_sync_at','');
        $summary=(array)get_option('m360_newsletter_last_sync_summary',[]);
        $recent=$wpdb->get_results("SELECT * FROM {$events} ORDER BY id DESC LIMIT 50",ARRAY_A);
        $provider_errors=(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$events} WHERE event_type='provider_error' AND created_at >= %s",gmdate('Y-m-d H:i:s',time()-DAY_IN_SECONDS)));
        $recent_blocks=(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$events} WHERE event_type='blocked' AND created_at >= %s",gmdate('Y-m-d H:i:s',time()-7*DAY_IN_SECONDS)));
        echo '<div class="wrap m360-ads-admin"><h1>M360 Newsletter — Operations &amp; Audit</h1><p>Saúde da integração, estados locais e trilha de auditoria da lista MailPoet.</p>';
        if (!empty($_GET['synced'])) { echo '<div class="notice notice-success is-dismissible"><p>Sincronização concluída.</p></div>'; }
        if (!empty($_GET['settings_saved'])) { echo '<div class="notice notice-success is-dismissible"><p>Configurações salvas.</p></div>'; }
        if (!empty($_GET['settings_error'])) { echo '<div class="notice notice-error is-dismissible"><p>Não foi possível salvar: selecione uma lista MailPoet válida e revise os campos.</p></div>'; }
        if (!empty($_GET['delivery_saved'])) { echo '<div class="notice notice-success is-dismissible"><p>Prontidão de entrega atualizada.</p></div>'; }
        if (!empty($_GET['delivery_error'])) { echo '<div class="notice notice-error is-dismissible"><p>Informe um remetente válido e o responsável editorial.</p></div>'; }
        echo '<div class="m360-ads-admin__cards">';
        self::metric('Pendentes',$counts['pending']); self::metric('Confirmados',$counts['confirmed']); self::metric('Cancelados',$counts['unsubscribed']); self::metric('Bloqueados',$counts['blocked']);
        self::metric('MailPoet',!empty($health['available'])?'Ativo':'Indisponível'); self::metric('Lista #3',!empty($health['list_available'])?'Disponível':'Não encontrada');
        echo '</div>';
        $healthy=!empty($health['available'])&&!empty($health['list_available'])&&$next;
        echo '<div class="notice ' . ($healthy?'notice-success':'notice-warning') . ' inline"><p><strong>Diagnóstico:</strong> MailPoet ' . (!empty($health['available'])?'ativo':'indisponível') . '; lista ' . esc_html((string)($health['list_name']??'#3')) . ' ' . (!empty($health['list_available'])?'localizada':'não localizada') . '; WP-Cron ' . ($next?'agendado':'sem agendamento') . '.</p></div>';
        echo '<h2>Sincronização</h2><p><strong>Última execução:</strong> ' . esc_html($last?:'Ainda não registrada') . ' UTC<br><strong>Próxima execução:</strong> ' . esc_html($next?gmdate('Y-m-d H:i:s',$next).' UTC':'Não agendada') . '</p>';
        if ($summary) { echo '<p>Último resultado: ' . esc_html((string)($summary['checked']??0)) . ' verificados, ' . esc_html((string)($summary['updated']??0)) . ' atualizados, ' . esc_html((string)($summary['errors']??0)) . ' erros.</p>'; }
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="m360_newsletter_sync">'; wp_nonce_field('m360_newsletter_sync'); submit_button('Sincronizar agora','primary','submit',false); echo '</form>';
        echo '<hr><h2>Configuração do formulário</h2><form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="m360_newsletter_save_settings">'; wp_nonce_field('m360_newsletter_save_settings');
        echo '<table class="form-table"><tbody><tr><th><label for="m360-newsletter-list">Lista MailPoet</label></th><td><select id="m360-newsletter-list" name="m360_newsletter[list_id]" required>';
        if (!is_wp_error($lists)) { foreach ($lists as $list) { $id=(int)($list['id']??0); echo '<option value="'.esc_attr((string)$id).'"'.selected((int)$settings['list_id'],$id,false).'>'.esc_html((string)($list['name']??('Lista #'.$id))).' (#'.esc_html((string)$id).')</option>'; } }
        echo '</select></td></tr>';
        self::field('Versão do consentimento','consent_version',$settings['consent_version']); self::field('Texto do consentimento PT-BR','consent_text_pt',$settings['consent_text_pt']); self::field('Consent text EN-US','consent_text_en',$settings['consent_text_en']); self::field('Tempo mínimo (segundos)','minimum_form_seconds',$settings['minimum_form_seconds'],'number'); self::field('Limite por IP','ip_limit',$settings['ip_limit'],'number'); self::field('Janela do IP (minutos)','ip_window_minutes',$settings['ip_window_minutes'],'number');
        self::field('Título PT-BR','form_title',$settings['form_title']); self::field('Descrição PT-BR','form_description',$settings['form_description']); self::field('Botão PT-BR','form_button',$settings['form_button']); self::field('Title EN-US','form_title_en',$settings['form_title_en']); self::field('Description EN-US','form_description_en',$settings['form_description_en']); self::field('Button EN-US','form_button_en',$settings['form_button_en']); self::field('Ocultar após inscrição (dias)','hide_days',$settings['hide_days'],'number');
        echo '<tr><th>Final dos artigos</th><td><input type="hidden" name="m360_newsletter[article_end_enabled]" value="0"><label><input type="checkbox" name="m360_newsletter[article_end_enabled]" value="1"'.checked(!empty($settings['article_end_enabled']),true,false).'> Inserir automaticamente após o conteúdo principal</label></td></tr>';
        echo '</tbody></table>'; submit_button('Salvar configurações'); echo '</form>';
        echo '<h2>Inscrições por origem</h2><table class="widefat striped"><thead><tr><th>Origem</th><th>Registros</th></tr></thead><tbody>';
        foreach ($source_counts as $source_row) { echo '<tr><td><code>'.esc_html((string)($source_row['source']?:'manual')).'</code></td><td>'.esc_html((string)$source_row['total']).'</td></tr>'; }
        if (!$source_counts) { echo '<tr><td colspan="2">Nenhuma origem registrada.</td></tr>'; } echo '</tbody></table>';
        $test_recent=!empty($settings['sending_test_at'])&&strtotime((string)$settings['sending_test_at'].' UTC')>=time()-30*DAY_IN_SECONDS;
        $checks=['MailPoet'=>!empty($health['available']),'Lista configurada'=>!empty($health['list_available']),'WP-Cron'=>(bool)$next,'Remetente válido'=>is_email((string)$settings['sender_email']),'DKIM confirmado'=>!empty($settings['dkim_confirmed']),'DMARC confirmado'=>!empty($settings['dmarc_confirmed']),'Teste de envio recente'=>$test_recent];
        $ready_count=count(array_filter($checks)); $ready=$ready_count===count($checks);
        echo '<hr><h2>Delivery Readiness</h2><div class="notice '.($ready?'notice-success':'notice-warning').' inline"><p><strong>'.($ready?'Pronto para entrega':'Configuração pendente').':</strong> '.esc_html((string)$ready_count).'/'.esc_html((string)count($checks)).' verificações atendidas. As confirmações DNS são declarações operacionais, não consultas automáticas.</p></div>';
        if ($provider_errors>0||$recent_blocks>0) { echo '<div class="notice notice-warning inline"><p><strong>Alertas:</strong> '.esc_html((string)$provider_errors).' erro(s) de provedor nas últimas 24h; '.esc_html((string)$recent_blocks).' bloqueio(s) nos últimos 7 dias.</p></div>'; }
        echo '<ul class="ul-disc">'; foreach ($checks as $label=>$passed) { echo '<li><strong>'.($passed?'✓':'○').' '.esc_html($label).'</strong></li>'; } echo '</ul>';
        echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="m360_newsletter_save_delivery">'; wp_nonce_field('m360_newsletter_save_delivery'); echo '<table class="form-table"><tbody>';
        self::field('E-mail remetente','sender_email',$settings['sender_email'],'email','m360_delivery'); self::field('Responsável editorial','editorial_owner',$settings['editorial_owner'],'text','m360_delivery');
        self::checkbox('DKIM confirmado no DNS','dkim_confirmed',!empty($settings['dkim_confirmed'])); self::checkbox('DMARC confirmado no DNS','dmarc_confirmed',!empty($settings['dmarc_confirmed'])); self::checkbox('Teste de envio aprovado','sending_test_confirmed',!empty($settings['sending_test_confirmed']));
        echo '</tbody></table><p>Último teste registrado: <code>'.esc_html((string)($settings['sending_test_at']?:'—')).' UTC</code></p>'; submit_button('Salvar prontidão'); echo '</form>';
        echo '<h2>Eventos recentes</h2><p>Os e-mails são mascarados e também representados por hash SHA-256 para reduzir exposição de dados pessoais.</p><table class="widefat striped"><thead><tr><th>Data UTC</th><th>Evento</th><th>Assinante</th><th>Transição</th><th>Provedor</th></tr></thead><tbody>';
        foreach ($recent as $event) { echo '<tr><td>'.esc_html($event['created_at']).'</td><td><code>'.esc_html($event['event_type']).'</code></td><td>'.esc_html($event['email_masked']?:'—').'</td><td>'.esc_html(trim($event['old_status'].' → '.$event['new_status'],' →')).'</td><td>'.esc_html($event['provider_status']?:'—').'</td></tr>'; }
        if (!$recent) { echo '<tr><td colspan="5">Nenhum evento registrado após a atualização para v0.6.2.</td></tr>'; }
        echo '</tbody></table></div>';
    }
    private static function metric(string $label,$value): void { echo '<div class="m360-ads-admin__card"><strong>'.esc_html((string)$value).'</strong><span>'.esc_html($label).'</span></div>'; }
    private static function field(string $label,string $name,$value,string $type='text',string $group='m360_newsletter'): void { echo '<tr><th><label for="m360-newsletter-'.esc_attr($name).'">'.esc_html($label).'</label></th><td><input class="regular-text" id="m360-newsletter-'.esc_attr($name).'" type="'.esc_attr($type).'" min="1" name="'.esc_attr($group).'['.esc_attr($name).']" value="'.esc_attr((string)$value).'" required></td></tr>'; }
    private static function checkbox(string $label,string $name,bool $checked): void { echo '<tr><th>'.esc_html($label).'</th><td><label><input type="checkbox" name="m360_delivery['.esc_attr($name).']" value="1"'.checked($checked,true,false).'> Confirmado</label></td></tr>'; }
    private static function guard(): void { if (!current_user_can('manage_options')) { wp_die(esc_html__('Acesso negado.','m360-core')); } }
}
