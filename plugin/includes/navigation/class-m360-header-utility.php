<?php
if (!defined('ABSPATH')) { exit; }

/** Date and institutional links for theme-independent headers. */
final class M360_Header_Utility
{
    public static function register_shortcodes(): void
    {
        add_shortcode('m360_header_utility', [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'lang' => '',
            'about_page' => 0,
            'contact_page' => 0,
            'about_url' => '',
            'contact_url' => '',
            'about_label' => '',
            'contact_label' => '',
            'show_date' => 'true',
            'date_format' => 'full',
            'show_weekday' => 'auto',
            'text_color' => '#ffffff',
            'separator_color' => '',
        ], $atts, 'm360_header_utility');
        $language = trim((string) $atts['lang']);
        if ($language === '' && function_exists('pll_current_language')) {
            $language = (string) pll_current_language('slug');
        }
        if ($language === '') { $language = get_locale(); }
        $is_en = str_starts_with(strtolower($language), 'en');
        $locale = $is_en ? 'en-US' : 'pt-BR';
        $format = strtolower((string) $atts['date_format']);
        if (!in_array($format, ['full', 'long', 'medium', 'short'], true)) { $format = 'full'; }
        $weekday_option = strtolower((string) $atts['show_weekday']);
        $weekday = $weekday_option === 'auto' ? $format === 'full' : in_array($weekday_option, ['true', '1', 'yes'], true);
        $text_color = self::color((string) $atts['text_color'], '#ffffff');
        $separator_color = self::color((string) $atts['separator_color'], $text_color);
        $html = '';
        if (!in_array(strtolower((string) $atts['show_date']), ['false', '0', 'no'], true)) {
            $now = new DateTimeImmutable('now', wp_timezone());
            $html .= '<span class="m360-header-utility__date">'
                . '<svg aria-hidden="true" focusable="false" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/></svg>'
                . '<time data-m360-header-date datetime="' . esc_attr($now->format('Y-m-d')) . '" data-locale="' . esc_attr($locale) . '" data-timezone="' . esc_attr(wp_timezone_string()) . '" data-offset="' . esc_attr((string) (wp_timezone()->getOffset($now) / 60)) . '" data-format="' . esc_attr($format) . '" data-weekday="' . ($weekday ? 'true' : 'false') . '">'
                . esc_html(self::date_label($now, $is_en, $format, $weekday)) . '</time></span>';
        }
        $links = '';
        foreach (['about', 'contact'] as $key) {
            $url = self::page_url($atts, $key, $language);
            if ($url === '') { continue; }
            $label = sanitize_text_field((string) $atts[$key . '_label']);
            if ($label === '') {
                $label = $key === 'about' ? ($is_en ? 'About Us' : 'Sobre') : ($is_en ? 'Contact' : 'Contato');
            }
            $links .= '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
        }
        if ($links !== '') {
            $html .= '<nav class="m360-header-utility__links" aria-label="' . esc_attr($is_en ? 'Institutional links' : 'Links institucionais') . '">' . $links . '</nav>';
        }
        if ($html === '') { return ''; }
        self::enqueue_assets();
        return '<div class="m360-header-utility" style="--m360-header-utility-color:' . esc_attr($text_color) . ';--m360-header-utility-separator:' . esc_attr($separator_color) . '">' . $html . '</div>';
    }

    public static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-header-utility', 'registered')) { wp_enqueue_style('m360-core-header-utility'); }
        if (wp_script_is('m360-core-header-utility', 'registered')) { wp_enqueue_script('m360-core-header-utility'); }
    }

    private static function page_url(array $atts, string $key, string $language): string
    {
        $url = esc_url_raw(trim((string) $atts[$key . '_url']));
        if ($url !== '') { return $url; }
        $id = absint($atts[$key . '_page']);
        if ($id === 0) {
            $slugs = $key === 'about' ? ['quem-somos', 'sobre', 'about-us', 'about'] : ['contato', 'contact'];
            foreach ($slugs as $slug) {
                $page = get_page_by_path($slug, OBJECT, 'page');
                if ($page instanceof WP_Post && $page->post_status === 'publish') { $id = (int) $page->ID; break; }
            }
        }
        if ($id === 0) { return ''; }
        if (function_exists('pll_get_post') && $language !== '') {
            $translated = (int) pll_get_post($id, $language);
            if ($translated > 0) { $id = $translated; }
        }
        if (get_post_status($id) !== 'publish') { return ''; }
        return (string) get_permalink($id);
    }

    private static function date_label(DateTimeImmutable $date, bool $is_en, string $format, bool $weekday): string
    {
        $days = $is_en ? ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] : ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
        $months = $is_en ? ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] : ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
        $month = $months[(int) $date->format('n') - 1];
        if ($format === 'medium') {
            $short = $is_en ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] : ['jan.', 'fev.', 'mar.', 'abr.', 'mai.', 'jun.', 'jul.', 'ago.', 'set.', 'out.', 'nov.', 'dez.'];
            $month = $short[(int) $date->format('n') - 1];
        }
        $label = $format === 'short'
            ? $date->format($is_en ? 'm/d/Y' : 'd/m/Y')
            : ($is_en ? sprintf('%s %s, %s', $month, $date->format('j'), $date->format('Y')) : sprintf('%s de %s de %s', $date->format('j'), $month, $date->format('Y')));
        return $weekday ? $days[(int) $date->format('w')] . ', ' . $label : $label;
    }

    private static function color(string $value, string $fallback): string
    {
        $color = sanitize_hex_color(trim($value));
        return is_string($color) ? $color : $fallback;
    }
}
