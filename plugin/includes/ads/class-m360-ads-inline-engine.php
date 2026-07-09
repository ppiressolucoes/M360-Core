<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ads_Inline_Engine
{
    public static function register(): void
    {
        add_filter('the_content', [self::class, 'inject_article_ads'], 18);
    }

    public static function inject_article_ads(string $content): string
    {
        if (!self::should_inject($content)) { return $content; }

        $placements = apply_filters('m360_ads_inline_article_placements', [
            [
                'after_paragraph' => 2,
                'slot' => 'article-after-paragraph-2',
                'class' => 'm360-inline-ad--after-paragraph-2',
            ],
        ]);

        if (!is_array($placements) || empty($placements)) { return $content; }

        $output = $content;
        foreach ($placements as $placement) {
            if (!is_array($placement)) { continue; }
            $paragraph = absint($placement['after_paragraph'] ?? 0);
            $slot = sanitize_key((string) ($placement['slot'] ?? ''));
            $class = sanitize_html_class((string) ($placement['class'] ?? ''));
            if ($paragraph < 1 || $slot === '') { continue; }

            $ad = self::render_inline_slot($slot, $class);
            if ($ad === '') { continue; }

            $output = self::insert_after_paragraph($output, $ad, $paragraph);
        }

        return $output;
    }

    private static function should_inject(string $content): bool
    {
        if (trim($content) === '') { return false; }
        if (is_admin() || is_feed() || wp_doing_ajax()) { return false; }
        if (defined('REST_REQUEST') && REST_REQUEST) { return false; }
        if (!is_singular('post')) { return false; }
        if (!in_the_loop() || !is_main_query()) { return false; }
        if (strpos($content, 'data-m360-inline-ads="1"') !== false) { return false; }
        return (bool) apply_filters('m360_ads_inline_enabled', true);
    }

    private static function render_inline_slot(string $slot_key, string $class = ''): string
    {
        $rendered = M360_Ad_Slot_Component::render($slot_key, [
            'class' => trim('m360-inline-ad__slot ' . $class),
        ]);

        if (trim($rendered) === '') { return ''; }

        return '<div class="m360-inline-ad" data-m360-inline-ads="1" data-m360-inline-slot="' . esc_attr($slot_key) . '">' . $rendered . '</div>';
    }

    private static function insert_after_paragraph(string $content, string $insertion, int $paragraph_number): string
    {
        $parts = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($parts) || count($parts) < 2) { return $content . $insertion; }

        $paragraph_index = 0;
        $output = '';
        $inserted = false;

        foreach ($parts as $part) {
            $output .= $part;
            if (preg_match('/<\/p>/i', $part)) {
                $paragraph_index++;
                if ($paragraph_index === $paragraph_number) {
                    $output .= $insertion;
                    $inserted = true;
                }
            }
        }

        return $inserted ? $output : $content . $insertion;
    }
}
