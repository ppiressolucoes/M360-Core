<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Search_Form_Component
{
    private const VARIANTS = ['header', 'hero', 'compact'];

    public static function register_shortcodes(): void
    {
        add_shortcode('m360_search_form', [self::class, 'shortcode']);
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
        $placeholder = trim((string) $atts['placeholder']) ?: ($is_en ? 'Search news, teams, competitions and players' : 'Busque notícias, times, competições e jogadores');
        $button_label = trim((string) $atts['button_label']) ?: ($is_en ? 'Search' : 'Buscar');
        $title = trim((string) $atts['title']) ?: ($is_en ? 'What are you looking for?' : 'O que você procura?');
        $description = trim((string) $atts['description']) ?: ($is_en ? 'Search and find content across the entire portal.' : 'Pesquise e encontre conteúdos em todo o portal.');

        self::enqueue_assets();
        $form_id = wp_unique_id('m360-search-form-');
        $input_id = $form_id . '-field';
        $action = home_url($is_en ? '/en/' : '/');
        $query = is_search() ? get_search_query(false) : '';
        $custom_class = sanitize_html_class((string) $atts['class']);
        $classes = 'm360-search-form m360-search-form--' . $variant . ($custom_class !== '' ? ' ' . $custom_class : '');
        $aria_label = $is_en ? 'Search Mengão 360' : 'Buscar no Mengão 360';
        $show_intro = self::enabled($atts['show_intro']) && $variant === 'hero';

        ob_start();
        echo '<div class="' . esc_attr($classes) . '" data-m360-search-form>';
        if ($show_intro) {
            echo '<div class="m360-search-form__intro"><h2>' . esc_html($title) . '</h2><p>' . esc_html($description) . '</p></div>';
        }
        echo '<form id="' . esc_attr($form_id) . '" class="m360-search-form__form" role="search" method="get" action="' . esc_url($action) . '" aria-label="' . esc_attr($aria_label) . '">';
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

    private static function search_icon(): string
    {
        return '<svg class="m360-search-form__icon" aria-hidden="true" viewBox="0 0 24 24" focusable="false"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-4-4"></path></svg>';
    }
}
