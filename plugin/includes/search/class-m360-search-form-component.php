<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Search_Form_Component
{
    private const VARIANTS = ['header', 'hero', 'compact'];

    public static function register_shortcodes(): void
    {
        add_shortcode('m360_search_form', [self::class, 'shortcode']);
        add_shortcode('m360_search_toggle', [self::class, 'toggle_shortcode']);
    }

    public static function toggle_shortcode(array $atts = []): string
    {
        $is_en = self::is_en();
        $label = $is_en ? 'Open search' : 'Abrir pesquisa';
        $panel_id = wp_unique_id('m360-search-toggle-panel-');
        self::enqueue_assets();
        return '<div class="m360-search-toggle" style="' . esc_attr(self::palette_style()) . '" data-m360-search-toggle>'
            . '<button type="button" class="m360-search-toggle__trigger" aria-expanded="false" aria-controls="' . esc_attr($panel_id) . '" aria-label="' . esc_attr($label) . '">' . self::search_icon() . '</button>'
            . '<div id="' . esc_attr($panel_id) . '" class="m360-search-toggle__panel" data-m360-search-toggle-panel hidden>'
            . self::shortcode(['variant' => 'header', 'show_intro' => 'false'])
            . '</div></div>';
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'variant' => 'hero', 'placeholder' => '', 'button_label' => '',
            'title' => '', 'description' => '', 'show_intro' => 'false', 'class' => '',
        ], $atts, 'm360_search_form');

        $variant = sanitize_key((string) $atts['variant']);
        if (!in_array($variant, self::VARIANTS, true)) { $variant = 'hero'; }
        $is_en = self::is_en();
        $placeholder = trim((string) $atts['placeholder']) ?: ($is_en ? 'Search news' : 'Busque notícias');
        $button_label = trim((string) $atts['button_label']) ?: ($is_en ? 'Search' : 'Buscar');
        $title = trim((string) $atts['title']) ?: ($is_en ? 'What are you looking for?' : 'O que você procura?');
        $description = trim((string) $atts['description']) ?: ($is_en ? 'Search and find content across the entire portal.' : 'Pesquise e encontre conteúdos em todo o portal.');

        self::enqueue_assets();
        $form_id = wp_unique_id('m360-search-form-');
        $input_id = $form_id . '-field';
        $language = self::language();
        $action = self::action_url($language);
        $query = is_search() ? get_search_query(false) : '';
        $custom_class = sanitize_html_class((string) $atts['class']);
        $classes = 'm360-search-form m360-search-form--' . $variant . ($custom_class !== '' ? ' ' . $custom_class : '');
        $site_name = class_exists('M360_Site_Profile') ? (string) (M360_Site_Profile::get()['site_name'] ?? '') : '';
        if ($site_name === '') { $site_name = (string) get_bloginfo('name'); }
        $aria_label = $is_en ? 'Search ' . $site_name : 'Buscar no ' . $site_name;
        $show_intro = self::enabled($atts['show_intro']) && $variant === 'hero';

        ob_start();
        echo '<div class="' . esc_attr($classes) . '" style="' . esc_attr(self::palette_style()) . '" data-m360-search-form>';
        if ($show_intro) {
            echo '<div class="m360-search-form__intro"><h2>' . esc_html($title) . '</h2><p>' . esc_html($description) . '</p></div>';
        }
        echo '<form id="' . esc_attr($form_id) . '" class="m360-search-form__form" role="search" method="get" action="' . esc_url($action) . '" aria-label="' . esc_attr($aria_label) . '">';
        if (function_exists('pll_current_language')) { echo '<input type="hidden" name="lang" value="' . esc_attr($language) . '">'; }
        echo '<label class="m360-search-form__label" for="' . esc_attr($input_id) . '">' . esc_html($aria_label) . '</label>';
        echo '<span class="m360-search-form__field">' . self::search_icon();
        echo '<input id="' . esc_attr($input_id) . '" class="m360-search-form__input" type="search" name="s" value="' . esc_attr($query) . '" placeholder="' . esc_attr($placeholder) . '" autocomplete="off" enterkeyhint="search" required aria-required="true" data-m360-search-input>';
        echo '<button class="m360-search-form__button" type="submit">' . self::search_icon() . '<span>' . esc_html($button_label) . '</span></button></span>';
        echo '<span class="m360-search-form__message" role="alert" aria-live="polite" data-m360-search-message hidden></span>';
        echo '</form></div>';
        return (string) ob_get_clean();
    }

    private static function enabled(mixed $value): bool
    {
        return !in_array(strtolower(trim((string) $value)), ['0', 'false', 'no', 'off'], true);
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-search-form', 'registered')) { wp_enqueue_style('m360-core-search-form'); }
        if (wp_script_is('m360-core-search-form', 'registered')) { wp_enqueue_script('m360-core-search-form'); }
        wp_localize_script('m360-core-search-form', 'm360SearchFormI18n', ['empty' => self::is_en() ? 'Enter a search term.' : 'Digite um termo para pesquisar.']);
    }

    private static function is_en(): bool
    {
        if (function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
            if (is_string($language) && $language !== '') { return strtolower($language) === 'en'; }
        }
        return str_starts_with((string) determine_locale(), 'en');
    }

    private static function language(): string
    {
        if (function_exists('pll_current_language')) {
            $language = sanitize_key((string) pll_current_language('slug'));
            if ($language !== '') { return $language; }
        }
        return self::is_en() ? 'en' : 'pt';
    }

    public static function action_url(string $language): string
    {
        $action = home_url('/');
        if (function_exists('pll_home_url')) {
            $language_home = (string) pll_home_url($language);
            $path = (string) wp_parse_url($language_home, PHP_URL_PATH);
            if (in_array($language, array_filter(explode('/', trim($path, '/'))), true)) {
                // The translated front page can have a page slug; search must target the language root.
                $action = home_url('/' . $language . '/');
            } elseif (wp_parse_url($language_home, PHP_URL_HOST) !== wp_parse_url(home_url('/'), PHP_URL_HOST)) {
                $scheme = (string) wp_parse_url($language_home, PHP_URL_SCHEME);
                $host = (string) wp_parse_url($language_home, PHP_URL_HOST);
                if ($scheme !== '' && $host !== '') { $action = $scheme . '://' . $host . '/'; }
            }
        }
        $filtered = apply_filters('m360_search_form_action_url', $action, $language);
        return is_string($filtered) && $filtered !== '' ? $filtered : $action;
    }

    private static function palette_style(): string
    {
        $appearance = class_exists('M360_Site_Profile') ? (M360_Site_Profile::get()['appearance'] ?? []) : [];
        $primary = sanitize_hex_color((string) ($appearance['primary_color'] ?? '')) ?: '#d71920';
        $secondary = sanitize_hex_color((string) ($appearance['secondary_color'] ?? '')) ?: '#111111';
        return '--m360-search-primary:' . $primary . ';--m360-search-secondary:' . $secondary;
    }

    private static function search_icon(): string
    {
        return '<svg class="m360-search-form__icon" aria-hidden="true" viewBox="0 0 24 24" focusable="false"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>';
    }
}
