<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Opt-in public cutover renderer. It consumes only active Core snapshots and
 * never generates or repairs data during a public request.
 */
final class M360_Discovery_Content_Injector
{
    public static function register(): void
    {
        add_filter('the_content', [self::class, 'process'], 29);
    }

    public static function process(string $content): string
    {
        $settings = M360_Content_Discovery_Module::settings();
        if (($settings['public_render_mode'] ?? 'shortcode') !== 'automatic') { return $content; }
        if (($settings['mode'] ?? 'off') !== 'shadow') { return $content; }
        if (is_admin() || is_feed() || !is_singular('post')) { return $content; }
        if (str_contains($content, 'm360-discovery-auto-footer') || str_contains($content, 'm360-sr-post-footer')) { return $content; }

        $queried_id = max(0, (int) get_queried_object_id());
        $post_id = max(0, (int) get_the_ID());
        if ($post_id < 1) { $post_id = $queried_id; }
        if ($post_id < 1 || ($queried_id > 0 && $post_id !== $queried_id)) { return $content; }

        $resolver = new M360_Discovery_Locale_Resolver();
        $locale = $resolver->resolve($post_id);
        if ($locale === '' || !in_array($locale, (array) $settings['supported_locales'], true)) { return $content; }

        if (!str_contains($content, 'm360-discovery-context-link')) {
            $content = self::contextual_links($content, $post_id, $locale, max(0, min(3, (int) $settings['contextual_links_max'])));
        }

        $inline = M360_Canary_Renderer::shortcode([
            'type' => 'read_more',
            'post_id' => $post_id,
            'limit' => 1,
        ]);
        if ($inline !== '' && !str_contains($content, 'm360-sr-inline-related')) {
            $content = self::after_paragraph($content, $inline, 2);
        }

        $related = M360_Canary_Renderer::shortcode([
            'type' => 'related_posts',
            'post_id' => $post_id,
            'limit' => 3,
            'offset' => $inline !== '' ? 1 : 0,
        ]);
        $topics = M360_Canary_Renderer::shortcode([
            'type' => 'topics',
            'post_id' => $post_id,
            'limit' => 6,
        ]);
        if ($related !== '' || $topics !== '') {
            $content .= '<div class="m360-discovery-auto-footer m360-sr-post-footer">' . $related . $topics . '</div>';
        }
        return $content;
    }

    private static function contextual_links(string $content, int $post_id, string $locale, int $max_links): string
    {
        if ($max_links < 1 || !class_exists('DOMDocument')) { return $content; }
        $resolver = new M360_Discovery_Locale_Resolver();
        $repository = new M360_Discovery_DB();
        $candidates = [];

        foreach ($repository->active($post_id, $locale, 'internal_link', 8) as $row) {
            $term = self::term_item((array) $row, $locale, $resolver);
            if ($term !== null) { $candidates[] = $term; }
        }
        foreach ($repository->active($post_id, $locale, 'related_post', 6) as $row) {
            $candidate = self::post_item((array) $row, $content, $locale, $resolver);
            if ($candidate !== null) { $candidates[] = $candidate; }
        }
        if (!$candidates) { return $content; }

        $previous_errors = libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'UTF-8');
        $loaded = $doc->loadHTML('<?xml encoding="UTF-8"><div id="m360-discovery-context-root">' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_errors);
        if (!$loaded) { return $content; }
        $root = $doc->getElementById('m360-discovery-context-root');
        if (!$root instanceof DOMElement) { return $content; }
        $xpath = new DOMXPath($doc);
        $linked = 0;
        $targets = [];
        foreach ($candidates as $candidate) {
            if ($linked >= $max_links) { break; }
            $target_key = (string) $candidate['target_type'] . ':' . (string) $candidate['target_id'];
            if (isset($targets[$target_key])) { continue; }
            if (self::link_first($doc, $xpath, $root, $candidate)) {
                $targets[$target_key] = true;
                $linked++;
            }
        }
        if ($linked < 1) { return $content; }
        $html = '';
        foreach ($root->childNodes as $child) { $html .= $doc->saveHTML($child); }
        return $html !== '' ? $html : $content;
    }

    /** @return array<string,mixed>|null */
    private static function term_item(array $row, string $locale, M360_Discovery_Locale_Resolver $resolver): ?array
    {
        if (sanitize_key((string) ($row['target_type'] ?? '')) !== 'term') { return null; }
        $term = get_term(max(0, (int) ($row['target_id'] ?? 0)));
        if (!$term instanceof WP_Term || is_wp_error($term)) { return null; }
        if (function_exists('pll_get_term_language')) {
            $term_locale = (string) pll_get_term_language($term->term_id, 'locale');
            if ($term_locale === '') { $term_locale = (string) pll_get_term_language($term->term_id, 'slug'); }
            if (strcasecmp($resolver->normalize_supported($term_locale), $locale) !== 0) { return null; }
        }
        $url = get_term_link($term);
        if (is_wp_error($url)) { return null; }
        return [
            'target_type' => 'term',
            'target_id' => (int) $term->term_id,
            'phrases' => self::term_variants($term->name, $locale),
            'url' => (string) $url,
        ];
    }

    /** @return array<string,mixed>|null */
    private static function post_item(array $row, string $content, string $locale, M360_Discovery_Locale_Resolver $resolver): ?array
    {
        if (sanitize_key((string) ($row['target_type'] ?? '')) !== 'post') { return null; }
        $target_id = max(0, (int) ($row['target_id'] ?? 0));
        $post = get_post($target_id);
        if (!$post instanceof WP_Post || $post->post_status !== 'publish' || strcasecmp($resolver->resolve($target_id), $locale) !== 0) { return null; }
        $url = get_permalink($post);
        if (!is_string($url) || $url === '') { return null; }
        $phrase = self::post_phrase(get_the_title($post), wp_strip_all_tags(strip_shortcodes($content)), $locale);
        if ($phrase === '') { return null; }
        return ['target_type' => 'post', 'target_id' => $target_id, 'phrases' => [$phrase], 'url' => $url];
    }

    /** @return string[] */
    private static function term_variants(string $name, string $locale): array
    {
        $variants = [trim($name)];
        $key = self::normalize($name);
        $maps = $locale === 'en-US'
            ? ['world cup' => ['World Cup'], 'brazilian team' => ['Brazilian Team', 'Brazil'], 'games' => ['Games', 'game', 'matches']]
            : ['copa do mundo' => ['Copa do Mundo', 'Mundial'], 'selecao brasileira' => ['Seleção Brasileira', 'Seleção', 'Brasil'], 'jogos' => ['Jogos', 'jogo', 'partidas']];
        if (isset($maps[$key])) { $variants = array_merge($variants, $maps[$key]); }
        $variants = array_values(array_unique(array_filter(array_map('trim', $variants), static fn(string $value): bool => mb_strlen($value, 'UTF-8') >= 3)));
        usort($variants, static fn(string $a, string $b): int => mb_strlen($b, 'UTF-8') <=> mb_strlen($a, 'UTF-8'));
        return $variants;
    }

    private static function post_phrase(string $title, string $visible_content, string $locale): string
    {
        $title = trim(wp_strip_all_tags($title));
        $visible = self::normalize($visible_content);
        if ($title === '' || $visible === '') { return ''; }
        if (mb_strlen($title, 'UTF-8') <= 120 && self::contains($visible, self::normalize($title))) { return $title; }

        $stop = $locale === 'en-US'
            ? ['a','an','the','and','or','of','for','to','in','on','at','with','from','by','as','is','are','was','were','new']
            : ['a','o','as','os','um','uma','e','ou','de','do','da','dos','das','para','por','em','no','na','com','novo','nova'];
        $clean = preg_replace('/[^\p{L}\p{N}\s\-\'’]/u', ' ', html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $raw = preg_split('/\s+/u', trim((string) $clean)) ?: [];
        $tokens = array_values(array_filter($raw, static function (string $token) use ($stop): bool {
            $normalized = self::normalize($token);
            return mb_strlen($normalized, 'UTF-8') >= 3 && !in_array($normalized, $stop, true);
        }));
        for ($size = min(6, count($tokens)); $size >= 2; $size--) {
            for ($index = 0; $index <= count($tokens) - $size; $index++) {
                $phrase = implode(' ', array_slice($tokens, $index, $size));
                if (mb_strlen($phrase, 'UTF-8') >= 10 && self::contains($visible, self::normalize($phrase))) { return $phrase; }
            }
        }
        return '';
    }

    /** @param array<string,mixed> $candidate */
    private static function link_first(DOMDocument $doc, DOMXPath $xpath, DOMElement $root, array $candidate): bool
    {
        $nodes = $xpath->query('.//text()[normalize-space(.) != ""]', $root);
        if (!$nodes) { return false; }
        foreach ((array) $candidate['phrases'] as $phrase) {
            $pattern = '~(?<![\p{L}\p{N}_])' . preg_quote((string) $phrase, '~') . '(?![\p{L}\p{N}_])~iu';
            foreach ($nodes as $node) {
                if (!$node instanceof DOMText || self::protected_node($node)) { continue; }
                if (!preg_match($pattern, (string) $node->nodeValue, $match, PREG_OFFSET_CAPTURE)) { continue; }
                $text = (string) $node->nodeValue;
                $found = (string) $match[0][0];
                $offset = (int) $match[0][1];
                $fragment = $doc->createDocumentFragment();
                if ($offset > 0) { $fragment->appendChild($doc->createTextNode(substr($text, 0, $offset))); }
                $link = $doc->createElement('a');
                $link->setAttribute('href', esc_url((string) $candidate['url']));
                $link->setAttribute('class', 'm360-discovery-context-link m360-sr-context-link m360-sr-context-post-link');
                $link->setAttribute('data-m360-target-type', (string) $candidate['target_type']);
                $link->setAttribute('data-m360-target-id', (string) $candidate['target_id']);
                $link->appendChild($doc->createTextNode($found));
                $fragment->appendChild($link);
                $after = substr($text, $offset + strlen($found));
                if ($after !== '') { $fragment->appendChild($doc->createTextNode($after)); }
                $node->parentNode->replaceChild($fragment, $node);
                return true;
            }
        }
        return false;
    }

    private static function protected_node(DOMNode $node): bool
    {
        $protected = ['a','script','style','code','pre','textarea','button','select','option','h1','h2','h3','h4','h5','h6','figcaption','caption','blockquote','nav','form','svg'];
        $parent = $node->parentNode;
        while ($parent instanceof DOMNode) {
            if (in_array(strtolower((string) $parent->nodeName), $protected, true)) { return true; }
            if ($parent instanceof DOMElement) {
                $class = ' ' . strtolower($parent->getAttribute('class')) . ' ';
                if (str_contains($class, ' m360-discovery-') || str_contains($class, ' m360-sr-') || str_contains($class, ' gallery ') || str_contains($class, ' wp-caption ')) { return true; }
            }
            $parent = $parent->parentNode;
        }
        return false;
    }

    private static function after_paragraph(string $content, string $html, int $position): string
    {
        $parts = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($parts) || count($parts) < 4) { return $content . $html; }
        $output = '';
        $paragraph = 0;
        foreach ($parts as $part) {
            $output .= $part;
            if (preg_match('/<\/p>/i', $part)) {
                $paragraph++;
                if ($paragraph === $position) { $output .= $html; }
            }
        }
        return $output;
    }

    private static function normalize(string $text): string
    {
        $text = mb_strtolower(html_entity_decode(wp_strip_all_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'UTF-8');
        $text = remove_accents($text);
        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    private static function contains(string $haystack, string $needle): bool
    {
        return $needle !== '' && preg_match('~(?<![\p{L}\p{N}_])' . preg_quote($needle, '~') . '(?![\p{L}\p{N}_])~iu', $haystack) === 1;
    }
}
