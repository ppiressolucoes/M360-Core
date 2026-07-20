<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Newsletter_Component
{
    public static function register(): void
    {
        add_action('init', [self::class, 'register_rewrite_rules']);
        add_action('template_redirect', [self::class, 'handle_confirmation']);
        add_action('rest_api_init', [self::class, 'register_routes']);
        add_action('wp_enqueue_scripts', [self::class, 'register_assets']);
        add_filter('the_content', [self::class, 'inject_article_end'], 999);
        add_action('m360_newsletter_sync_pending', [self::class, 'sync_statuses']);
        add_action('m360_newsletter_daily_cleanup', ['M360_Newsletter_DB','cleanup']);
        if (!wp_next_scheduled('m360_newsletter_sync_pending')) { wp_schedule_event(time() + HOUR_IN_SECONDS, 'hourly', 'm360_newsletter_sync_pending'); }
        if (!wp_next_scheduled('m360_newsletter_daily_cleanup')) { wp_schedule_event(time() + DAY_IN_SECONDS, 'daily', 'm360_newsletter_daily_cleanup'); }
    }
    public static function register_rewrite_rules(): void { add_rewrite_rule('^newsletter/confirm/([a-f0-9]{64})/?$', 'index.php?m360_newsletter_token=$matches[1]', 'top'); add_rewrite_tag('%m360_newsletter_token%', '([a-f0-9]{64})'); }
    public static function register_shortcodes(): void { add_shortcode('m360_newsletter_form', [self::class, 'shortcode']); }
    public static function sync_statuses(): void
    {
        $summary=(new M360_Newsletter_Service())->sync_statuses();
        update_option('m360_newsletter_last_sync_at',current_time('mysql',true),false);
        update_option('m360_newsletter_last_sync_summary',$summary,false);
    }
    public static function sync_pending(): void { self::sync_statuses(); }
    public static function register_routes(): void
    {
        register_rest_route('m360/v1', '/newsletter/subscribe', ['methods'=>'POST','callback'=>[self::class,'subscribe'],'permission_callback'=>'__return_true']);
    }
    public static function subscribe(WP_REST_Request $request)
    {
        $allowed=['manual','article_end','home','sidebar','footer','archive']; $source=sanitize_key((string)$request->get_param('source')); if (!in_array($source,$allowed,true)) { $source='manual'; }
        $result = (new M360_Newsletter_Service())->subscribe((string)$request->get_param('email'), (string)$request->get_param('name'), (bool)$request->get_param('consent'),$source,['website'=>(string)$request->get_param('website'),'rendered_at'=>absint($request->get_param('rendered_at')),'lang'=>sanitize_key((string)$request->get_param('lang'))]);
        $is_en=sanitize_key((string)$request->get_param('lang'))==='en';
        return is_wp_error($result) ? $result : new WP_REST_Response(['message'=>$is_en?'Check your email to confirm your subscription.':__('Verifique seu e-mail para confirmar a inscrição.', 'm360-core')], 202);
    }
    public static function handle_confirmation(): void
    {
        $token = (string)get_query_var('m360_newsletter_token'); if (!$token) { return; }
        $result = (new M360_Newsletter_Service())->confirm($token); status_header(is_wp_error($result) ? 400 : 200);
        wp_die(is_wp_error($result) ? esc_html($result->get_error_message()) : esc_html__('Assinatura confirmada. Obrigado por acompanhar a Newsletter M360!', 'm360-core'), esc_html__('Newsletter M360', 'm360-core'), ['response'=>is_wp_error($result) ? 400 : 200]);
    }
    public static function register_assets(): void
    {
        wp_register_script('m360-core-newsletter', M360_CORE_URL . 'assets/js/m360-newsletter.js', [], M360_CORE_VERSION, true);
        wp_register_style('m360-core-newsletter', M360_CORE_URL . 'assets/css/m360-newsletter.css', [], M360_CORE_VERSION);
        wp_localize_script('m360-core-newsletter', 'M360Newsletter', ['endpoint'=>esc_url_raw(rest_url('m360/v1/newsletter/subscribe')),'hideDays'=>(int)M360_Newsletter_Settings::get()['hide_days']]);
    }
    public static function shortcode(array $atts = []): string
    {
        wp_enqueue_script('m360-core-newsletter'); wp_enqueue_style('m360-core-newsletter');
        $settings=M360_Newsletter_Settings::get();
        $atts=shortcode_atts(['variant'=>'card','source'=>'manual','title'=>'','description'=>'','button'=>''],$atts,'m360_newsletter_form');
        $variant=sanitize_key((string)$atts['variant']); if (!in_array($variant,['card','compact','inline','footer'],true)) { $variant='card'; }
        $source=sanitize_key((string)$atts['source']); if (!in_array($source,['manual','article_end','home','sidebar','footer','archive'],true)) { $source='manual'; }
        $is_en=self::is_en();
        $title=trim((string)$atts['title'])?:($is_en?$settings['form_title_en']:$settings['form_title']); $description=trim((string)$atts['description']); if ($description==='') { $description=$is_en?$settings['form_description_en']:$settings['form_description']; } $button=trim((string)$atts['button'])?:($is_en?$settings['form_button_en']:$settings['form_button']);
        ob_start(); ?>
        <section class="m360-newsletter m360-newsletter--<?php echo esc_attr($variant); ?>" data-m360-newsletter-root data-source="<?php echo esc_attr($source); ?>"><div class="m360-newsletter__intro"><h2><?php echo esc_html((string)$title); ?></h2><?php if ($description!==''): ?><p><?php echo esc_html((string)$description); ?></p><?php endif; ?></div><form data-m360-newsletter-form>
          <label><?php echo esc_html($is_en?'Name (optional)':'Nome (opcional)'); ?><input type="text" name="name" autocomplete="name"></label>
          <label><?php echo esc_html($is_en?'Email':'E-mail'); ?><input type="email" name="email" autocomplete="email" required></label>
          <div aria-hidden="true" style="position:absolute;left:-9999px"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
          <input type="hidden" name="rendered_at" value="<?php echo esc_attr((string)time()); ?>">
          <input type="hidden" name="source" value="<?php echo esc_attr($source); ?>">
          <input type="hidden" name="lang" value="<?php echo $is_en?'en':'pt'; ?>">
          <label><input type="checkbox" name="consent" value="1" required> <?php echo esc_html((string)($is_en?$settings['consent_text_en']:$settings['consent_text_pt'])); ?></label>
          <button type="submit"><?php echo esc_html((string)$button); ?></button><p tabindex="-1" aria-live="polite" data-m360-newsletter-message></p>
        </form><p class="m360-newsletter__return" data-m360-newsletter-return hidden><?php echo esc_html($is_en?'A subscription was already requested in this browser.':'Inscrição já solicitada neste navegador.'); ?></p><p class="m360-newsletter__fineprint"><svg aria-hidden="true" viewBox="0 0 24 24" focusable="false"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Zm-1-9h2v6h-2v-6Zm0-4h2v2h-2V7Z"/></svg><span><?php echo esc_html($is_en?'You can unsubscribe at any time.':'Você pode cancelar quando quiser.'); ?></span></p></section><?php return (string)ob_get_clean();
    }

    public static function inject_article_end(string $content): string
    {
        $settings=M360_Newsletter_Settings::get();
        if (empty($settings['article_end_enabled'])||is_admin()||is_feed()||wp_doing_ajax()||!is_singular('post')||!in_the_loop()||!is_main_query()) { return $content; }
        if (str_contains($content,'data-m360-newsletter-root')) { return $content; }
        return $content . self::shortcode(['variant'=>'inline','source'=>'article_end']);
    }
    private static function is_en(): bool
    {
        if (function_exists('pll_current_language')) { return pll_current_language('slug')==='en'; }
        return str_starts_with(strtolower((string)get_locale()),'en');
    }
}
