<?php
/** Privacy and consent foundation independent from the selected CMP vendor. */
if (!defined('ABSPATH')) { exit; }

final class M360_Consent_Manager
{
    private const OPTION = 'm360_privacy_settings';
    private const COOKIE = 'm360_consent_v1';

    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'register_admin_page'], 30);
        add_action('admin_post_m360_save_privacy_settings', [self::class, 'save_settings']);
        add_action('wp_head', [self::class, 'render_consent_mode_defaults'], 0);
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_assets'], 20);
        add_action('wp_footer', [self::class, 'render_local_interface'], 5);
    }

    public static function activate(): void
    {
        if (get_option(self::OPTION, null) === null) {
            add_option(self::OPTION, self::defaults(), '', false);
        }
    }

    public static function categories(): array
    {
        return [
            'necessary' => ['required' => true, 'pt-br' => 'Necessários', 'en-us' => 'Necessary'],
            'preferences' => ['required' => false, 'pt-br' => 'Preferências', 'en-us' => 'Preferences'],
            'analytics' => ['required' => false, 'pt-br' => 'Medição e analytics', 'en-us' => 'Measurement and analytics'],
            'advertising' => ['required' => false, 'pt-br' => 'Publicidade', 'en-us' => 'Advertising'],
            'external_media' => ['required' => false, 'pt-br' => 'Mídia externa', 'en-us' => 'External media'],
        ];
    }

    public static function settings(): array
    {
        $saved = get_option(self::OPTION, []);
        return wp_parse_args(is_array($saved) ? $saved : [], self::defaults());
    }

    public static function consent_state(): array
    {
        $state = ['necessary' => true, 'preferences' => false, 'analytics' => false, 'advertising' => false, 'external_media' => false];
        if (!empty($_COOKIE[self::COOKIE])) {
            $decoded = json_decode(rawurldecode(wp_unslash((string) $_COOKIE[self::COOKIE])), true);
            if (is_array($decoded) && (int) ($decoded['version'] ?? 0) === 1) {
                foreach (self::categories() as $key => $definition) {
                    $state[$key] = !empty($definition['required']) || !empty($decoded['categories'][$key]);
                }
            }
        }
        $filtered = apply_filters('m360_consent_state', $state, self::settings());
        return is_array($filtered) ? array_merge($state, $filtered) : $state;
    }

    public static function has_consent(string $category): bool
    {
        $category = sanitize_key($category);
        $state = self::consent_state();
        return !empty($state[$category]);
    }

    public static function should_gate_provider(string $provider): bool
    {
        $settings = self::settings();
        if (empty($settings['enabled']) || empty($settings['ads_gate'])) { return false; }
        $provider = sanitize_key($provider);
        $requires = in_array($provider, ['adsense', 'google-ad-manager'], true);
        return (bool) apply_filters('m360_consent_provider_requires_advertising', $requires, $provider, $settings);
    }

    public static function can_deliver_provider(string $provider): bool
    {
        if (!self::should_gate_provider($provider)) { return true; }
        return (bool) apply_filters('m360_consent_can_deliver_provider', self::has_consent('advertising'), $provider, self::consent_state());
    }

    public static function register_admin_page(): void
    {
        add_submenu_page(
            'm360-ads-manager',
            'M360 Privacy & Consent',
            'Privacy & Consent',
            'manage_options',
            'm360-ads-privacy-consent',
            [self::class, 'render_admin_page']
        );
    }

    public static function save_settings(): void
    {
        if (!current_user_can('manage_options')) { wp_die(esc_html__('Acesso negado.', 'm360-core')); }
        check_admin_referer('m360_save_privacy_settings');
        $input = is_array($_POST['m360_privacy'] ?? null) ? wp_unslash($_POST['m360_privacy']) : [];
        $mode = sanitize_key((string) ($input['adapter_mode'] ?? 'external_cmp'));
        if (!in_array($mode, ['external_cmp', 'local_foundation'], true)) { $mode = 'external_cmp'; }
        $settings = [
            'enabled' => !empty($input['enabled']),
            'adapter_mode' => $mode,
            'cmp_name' => sanitize_text_field((string) ($input['cmp_name'] ?? '')),
            'consent_mode_v2' => !empty($input['consent_mode_v2']),
            'ads_gate' => !empty($input['ads_gate']),
            'cookie_days' => max(1, min(365, absint($input['cookie_days'] ?? 180))),
            'privacy_url_pt' => esc_url_raw((string) ($input['privacy_url_pt'] ?? '')),
            'privacy_url_en' => esc_url_raw((string) ($input['privacy_url_en'] ?? '')),
            'cookies_url_pt' => esc_url_raw((string) ($input['cookies_url_pt'] ?? '')),
            'cookies_url_en' => esc_url_raw((string) ($input['cookies_url_en'] ?? '')),
        ];
        update_option(self::OPTION, $settings, false);
        wp_safe_redirect(add_query_arg(['page' => 'm360-ads-privacy-consent', 'updated' => '1'], admin_url('admin.php')));
        exit;
    }

    public static function render_admin_page(): void
    {
        if (!current_user_can('manage_options')) { return; }
        $s = self::settings();
        $state = self::consent_state();
        $ready = !empty($s['enabled'])
            && !empty($s['cmp_name'])
            && !empty($s['privacy_url_pt'])
            && !empty($s['privacy_url_en'])
            && !empty($s['cookies_url_pt'])
            && !empty($s['cookies_url_en']);
        echo '<div class="wrap m360-ads-admin"><h1>M360 Privacy &amp; Consent Foundation</h1>';
        echo '<p>Camada independente de integração. Para publicidade no EEE, Reino Unido e Suíça, configure uma CMP certificada pelo Google e integrada ao IAB TCF.</p>';
        if (!empty($_GET['updated'])) { echo '<div class="notice notice-success is-dismissible"><p>Configurações de privacidade salvas.</p></div>'; }
        echo '<div class="m360-ads-admin__cards">';
        self::metric($ready ? 'Configurado' : 'Pendente', 'Prontidão básica');
        self::metric(!empty($s['adapter_mode']) && $s['adapter_mode'] === 'external_cmp' ? 'CMP externa' : 'Interface M360', 'Adaptador');
        self::metric(!empty($s['consent_mode_v2']) ? 'Ativo' : 'Inativo', 'Consent Mode v2');
        self::metric(!empty($s['ads_gate']) ? 'Ativo' : 'Observação', 'Bloqueio Ads');
        echo '</div>';
        echo '<div class="notice notice-warning inline"><p><strong>Importante:</strong> a interface própria M360 serve para homologação controlada no portal; ela não substitui uma CMP certificada pelo Google para regiões sujeitas ao TCF.</p></div>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="m360_save_privacy_settings">';
        wp_nonce_field('m360_save_privacy_settings');
        echo '<table class="form-table"><tbody>';
        self::checkbox_row('Ativar fundação', 'enabled', !empty($s['enabled']), 'Inicializa o adaptador e os sinais de consentimento no front-end.');
        echo '<tr><th><label for="m360-adapter-mode">Modo do adaptador</label></th><td><select id="m360-adapter-mode" name="m360_privacy[adapter_mode]">';
        echo '<option value="external_cmp"' . selected($s['adapter_mode'], 'external_cmp', false) . '>CMP externa certificada</option>';
        echo '<option value="local_foundation"' . selected($s['adapter_mode'], 'local_foundation', false) . '>Interface própria M360 — homologação controlada</option></select><p class="description">Não exige ambiente local. A CMP externa é a opção recomendada para produção.</p></td></tr>';
        self::text_row('CMP / fornecedor', 'cmp_name', (string) $s['cmp_name'], 'Ex.: Google Privacy & Messaging ou fornecedor certificado escolhido.');
        self::checkbox_row('Google Consent Mode v2', 'consent_mode_v2', !empty($s['consent_mode_v2']), 'Define os sinais como negados por padrão e os atualiza após a decisão do usuário.');
        self::checkbox_row('Aplicar bloqueio ao Ads Manager', 'ads_gate', !empty($s['ads_gate']), 'Bloqueia AdSense/GAM até o adaptador confirmar consentimento publicitário. Ative somente após validar a integração da CMP.');
        self::text_row('Política de privacidade PT-BR', 'privacy_url_pt', (string) $s['privacy_url_pt']);
        self::text_row('Privacy Policy EN-US', 'privacy_url_en', (string) $s['privacy_url_en']);
        self::text_row('Política de cookies PT-BR', 'cookies_url_pt', (string) $s['cookies_url_pt']);
        self::text_row('Cookie Policy EN-US', 'cookies_url_en', (string) $s['cookies_url_en']);
        echo '<tr><th><label for="m360-cookie-days">Validade da preferência</label></th><td><input id="m360-cookie-days" name="m360_privacy[cookie_days]" type="number" min="1" max="365" value="' . esc_attr((string) $s['cookie_days']) . '"> dias</td></tr>';
        echo '</tbody></table>'; submit_button('Salvar configurações'); echo '</form>';
        echo '<h2>Contrato normalizado M360</h2><p><code>necessary</code>, <code>preferences</code>, <code>analytics</code>, <code>advertising</code> e <code>external_media</code>.</p>';
        echo '<p>Estado desta requisição: <code>' . esc_html(wp_json_encode($state)) . '</code></p></div>';
    }

    public static function render_consent_mode_defaults(): void
    {
        $s = self::settings();
        if (empty($s['enabled']) || empty($s['consent_mode_v2'])) { return; }
        $state = self::consent_state();
        $signals = self::google_signals($state);
        echo "\n<script id=\"m360-consent-mode-v2\">window.dataLayer=window.dataLayer||[];window.gtag=window.gtag||function(){dataLayer.push(arguments);};gtag('consent','default',{";
        echo "'ad_storage':'denied','ad_user_data':'denied','ad_personalization':'denied','analytics_storage':'denied','functionality_storage':'denied','personalization_storage':'denied','security_storage':'granted','wait_for_update':500";
        echo "});gtag('set','ads_data_redaction',true);";
        if (!empty($_COOKIE[self::COOKIE])) { echo "gtag('consent','update'," . wp_json_encode($signals) . ");"; }
        echo "</script>\n";
    }

    public static function enqueue_assets(): void
    {
        $s = self::settings();
        if (empty($s['enabled'])) { return; }
        wp_enqueue_style('m360-core-consent', M360_CORE_URL . 'assets/css/m360-consent.css', [], M360_CORE_VERSION);
        wp_enqueue_script('m360-core-consent', M360_CORE_URL . 'assets/js/m360-consent.js', [], M360_CORE_VERSION, true);
        wp_localize_script('m360-core-consent', 'M360ConsentConfig', [
            'cookieName' => self::COOKIE,
            'cookieDays' => (int) $s['cookie_days'],
            'adapterMode' => (string) $s['adapter_mode'],
            'consentModeV2' => !empty($s['consent_mode_v2']),
            'language' => self::language(),
            'state' => self::consent_state(),
        ]);
    }

    public static function render_local_interface(): void
    {
        $s = self::settings();
        if (empty($s['enabled']) || $s['adapter_mode'] !== 'local_foundation') { return; }
        $lang = self::language();
        $en = $lang === 'en-us';
        $privacy = $en ? $s['privacy_url_en'] : $s['privacy_url_pt'];
        $cookies = $en ? $s['cookies_url_en'] : $s['cookies_url_pt'];
        $title = $en ? 'Your privacy choices' : 'Suas escolhas de privacidade';
        $description = $en ? 'We use optional technologies only with your permission. You can change your choices at any time.' : 'Usamos tecnologias opcionais somente com sua permissão. Você pode alterar suas escolhas a qualquer momento.';
        echo '<div class="m360-consent" data-m360-consent-root hidden>';
        echo '<section class="m360-consent__banner" role="dialog" aria-modal="true" aria-labelledby="m360-consent-title"><h2 id="m360-consent-title">' . esc_html($title) . '</h2><p>' . esc_html($description) . '</p>';
        echo '<div class="m360-consent__links">';
        if ($privacy) { echo '<a href="' . esc_url($privacy) . '">' . esc_html($en ? 'Privacy Policy' : 'Política de Privacidade') . '</a>'; }
        if ($cookies) { echo '<a href="' . esc_url($cookies) . '">' . esc_html($en ? 'Cookie Policy' : 'Política de Cookies') . '</a>'; }
        echo '</div><div class="m360-consent__actions"><button type="button" data-m360-consent-reject>' . esc_html($en ? 'Reject optional' : 'Rejeitar opcionais') . '</button><button type="button" data-m360-consent-manage>' . esc_html($en ? 'Manage choices' : 'Gerenciar escolhas') . '</button><button class="is-primary" type="button" data-m360-consent-accept>' . esc_html($en ? 'Accept all' : 'Aceitar todos') . '</button></div></section>';
        echo '<section class="m360-consent__panel" role="dialog" aria-modal="true" aria-labelledby="m360-consent-panel-title" hidden><h2 id="m360-consent-panel-title">' . esc_html($title) . '</h2><form data-m360-consent-form>';
        foreach (self::categories() as $key => $definition) {
            $required = !empty($definition['required']);
            echo '<label class="m360-consent__category"><span>' . esc_html((string) $definition[$lang]) . '</span><input type="checkbox" name="' . esc_attr($key) . '"' . ($required ? ' checked disabled' : '') . '></label>';
        }
        echo '<div class="m360-consent__actions"><button type="button" data-m360-consent-close>' . esc_html($en ? 'Cancel' : 'Cancelar') . '</button><button class="is-primary" type="submit">' . esc_html($en ? 'Save choices' : 'Salvar escolhas') . '</button></div></form></section></div>';
        echo '<button class="m360-consent-launcher" type="button" data-m360-consent-launcher>' . esc_html($en ? 'Cookie settings' : 'Ajustar cookies') . '</button>';
    }

    private static function defaults(): array
    {
        return ['enabled' => false, 'adapter_mode' => 'external_cmp', 'cmp_name' => '', 'consent_mode_v2' => true, 'ads_gate' => false, 'cookie_days' => 180, 'privacy_url_pt' => '', 'privacy_url_en' => '', 'cookies_url_pt' => '', 'cookies_url_en' => ''];
    }

    private static function google_signals(array $state): array
    {
        $value = static fn(bool $granted): string => $granted ? 'granted' : 'denied';
        return [
            'ad_storage' => $value(!empty($state['advertising'])),
            'ad_user_data' => $value(!empty($state['advertising'])),
            'ad_personalization' => $value(!empty($state['advertising'])),
            'analytics_storage' => $value(!empty($state['analytics'])),
            'functionality_storage' => $value(!empty($state['preferences'])),
            'personalization_storage' => $value(!empty($state['preferences'])),
            'security_storage' => 'granted',
        ];
    }

    private static function language(): string
    {
        if (function_exists('pll_current_language')) { $lang = pll_current_language('slug'); if ($lang === 'en') { return 'en-us'; } }
        return str_starts_with((string) get_locale(), 'en') ? 'en-us' : 'pt-br';
    }

    private static function metric(string $value, string $label): void
    {
        echo '<div class="m360-ads-admin__card"><strong>' . esc_html($value) . '</strong><span>' . esc_html($label) . '</span></div>';
    }

    private static function checkbox_row(string $label, string $name, bool $checked, string $description = ''): void
    {
        echo '<tr><th>' . esc_html($label) . '</th><td><label><input type="checkbox" name="m360_privacy[' . esc_attr($name) . ']" value="1"' . checked($checked, true, false) . '> Ativo</label>';
        if ($description !== '') { echo '<p class="description">' . esc_html($description) . '</p>'; }
        echo '</td></tr>';
    }

    private static function text_row(string $label, string $name, string $value, string $description = ''): void
    {
        $type = str_contains($name, 'url') ? 'url' : 'text';
        echo '<tr><th><label for="m360-' . esc_attr($name) . '">' . esc_html($label) . '</label></th><td><input class="regular-text" id="m360-' . esc_attr($name) . '" name="m360_privacy[' . esc_attr($name) . ']" type="' . esc_attr($type) . '" value="' . esc_attr($value) . '">';
        if ($description !== '') { echo '<p class="description">' . esc_html($description) . '</p>'; }
        echo '</td></tr>';
    }
}

if (!function_exists('m360_has_consent')) {
    function m360_has_consent(string $category): bool { return M360_Consent_Manager::has_consent($category); }
}
