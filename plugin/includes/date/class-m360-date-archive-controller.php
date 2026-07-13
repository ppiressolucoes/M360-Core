<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Date_Archive_Controller
{
    public static function template_include(string $template): string
    {
        if (!is_date() || is_admin()) { return $template; }
        $m360_template = M360_CORE_PATH . 'templates/date.php';
        return is_readable($m360_template) ? $m360_template : $template;
    }

    public static function render(): string
    {
        self::enqueue_assets();
        $is_en = self::is_en();
        $archive_title = self::archive_title($is_en);
        global $wp_query;
        $post_count = $wp_query instanceof WP_Query ? (int) $wp_query->found_posts : 0;

        ob_start();
        echo '<main class="m360-dynamic-view m360-date-view"><div class="m360-dynamic-view__container">';
        echo do_shortcode('[m360_breadcrumb]');
        echo '<section class="m360-date-hero">';
        echo '<span class="m360-date-hero__eyebrow">' . esc_html($is_en ? 'Mengão 360 Date Archive' : 'Arquivo por data Mengão 360') . '</span>';
        echo '<h1>' . esc_html($archive_title) . '</h1>';
        echo '<p class="m360-date-hero__description">' . esc_html($is_en ? 'News published during this period.' : 'Notícias publicadas neste período.') . '</p>';
        echo '<div class="m360-date-stats"><span><strong>' . esc_html((string) $post_count) . '</strong> ' . esc_html($is_en ? 'articles' : 'artigos') . '</span></div>';
        echo '</section>';

        if (have_posts()) {
            echo '<section class="m360-date-results" aria-label="' . esc_attr($is_en ? 'Articles by date' : 'Artigos por data') . '">';
            $index = 0;
            while (have_posts()) {
                the_post();
                $index++;
                echo self::render_card();
                echo M360_Ads_Archive_Engine::after_item('archive', $index);
            }
            echo '</section>' . self::pagination();
        } else {
            echo '<section class="m360-date-empty"><h2>' . esc_html($is_en ? 'No articles found for this period' : 'Nenhum artigo encontrado neste período') . '</h2></section>';
        }

        echo '</div></main>';
        wp_reset_postdata();
        return (string) ob_get_clean();
    }

    public static function render_shell(): void
    {
        if (!self::is_en()) {
            get_header();
            echo self::render();
            get_footer();
            return;
        }

        echo '<!doctype html><html ' . get_language_attributes() . '><head><meta charset="' . esc_attr(get_bloginfo('charset')) . '"><meta name="viewport" content="width=device-width, initial-scale=1">';
        wp_head();
        echo '</head><body ' . self::body_class_string('m360-date-en-shell m360-date-en-elementor-shell') . '>';
        wp_body_open();
        echo do_shortcode('[elementor-template id="4123"]');
        echo self::render();
        echo do_shortcode('[elementor-template id="3765"]');
        wp_footer();
        echo '</body></html>';
    }

    public static function archive_title(?bool $is_en = null): string
    {
        $is_en = $is_en ?? self::is_en();
        $year = max(1, (int) get_query_var('year'));
        $month = max(0, min(12, (int) get_query_var('monthnum')));
        $day = max(0, min(31, (int) get_query_var('day')));
        $months_en = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
        $months_pt = [1=>'janeiro',2=>'fevereiro',3=>'março',4=>'abril',5=>'maio',6=>'junho',7=>'julho',8=>'agosto',9=>'setembro',10=>'outubro',11=>'novembro',12=>'dezembro'];

        if ($day > 0 && $month > 0) {
            return $is_en
                ? ($months_en[$month] ?? '') . ' ' . $day . ', ' . $year
                : $day . ' de ' . ($months_pt[$month] ?? '') . ' de ' . $year;
        }
        if ($month > 0) {
            return $is_en
                ? ($months_en[$month] ?? '') . ' ' . $year
                : ($months_pt[$month] ?? '') . ' de ' . $year;
        }
        return (string) $year;
    }

    private static function render_card(): string
    {
        $categories = get_the_category();
        $category = !empty($categories) ? $categories[0]->name : '';
        $excerpt = get_the_excerpt();
        if ($excerpt === '') { $excerpt = wp_trim_words(wp_strip_all_tags((string) get_the_content()), 26); }

        ob_start();
        echo '<article class="m360-date-card">';
        if (has_post_thumbnail()) {
            echo '<a class="m360-date-card__thumb" href="' . esc_url(get_permalink()) . '">';
            the_post_thumbnail('medium_large');
            echo '</a>';
        }
        echo '<div class="m360-date-card__body">';
        if ($category !== '') { echo '<span class="m360-date-card__category">' . esc_html($category) . '</span>'; }
        echo '<h2><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h2>';
        echo '<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time>';
        echo '<p>' . esc_html($excerpt) . '</p></div></article>';
        return (string) ob_get_clean();
    }

    private static function pagination(): string
    {
        $links = paginate_links([
            'type' => 'list',
            'prev_text' => self::is_en() ? 'Previous' : 'Anterior',
            'next_text' => self::is_en() ? 'Next' : 'Próxima',
        ]);
        return is_string($links) && $links !== '' ? '<nav class="m360-date-pagination" aria-label="' . esc_attr(self::is_en() ? 'Date archive pagination' : 'Paginação do arquivo por data') . '">' . $links . '</nav>' : '';
    }

    private static function enqueue_assets(): void
    {
        if (wp_style_is('m360-core-date', 'registered')) { wp_enqueue_style('m360-core-date'); }
        if (wp_style_is('m360-core-foundation', 'registered')) { wp_enqueue_style('m360-core-foundation'); }
    }

    private static function is_en(): bool
    {
        if (function_exists('pll_current_language')) {
            $language = pll_current_language('slug');
            if (is_string($language) && $language !== '') { return strtolower($language) === 'en'; }
        }
        return str_starts_with((string) determine_locale(), 'en');
    }

    private static function body_class_string(string $class = ''): string
    {
        return 'class="' . esc_attr(implode(' ', get_body_class($class))) . '"';
    }
}
