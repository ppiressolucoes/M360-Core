<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Explicit, shortcode-only renderer backed exclusively by Core snapshots.
 * No content filters, legacy fallback or write side effects are permitted.
 */
final class M360_Canary_Renderer
{
    private const SHORTCODE = 'm360_discovery_canary';

    public static function register(): void
    {
        add_shortcode(self::SHORTCODE, [self::class, 'shortcode']);
    }

    public static function shortcode(array $atts = []): string
    {
        $atts = shortcode_atts([
            'type' => 'related_posts',
            'post_id' => 0,
            'title' => '',
            'limit' => 0,
            'offset' => 0,
            'debug' => '0',
        ], $atts, self::SHORTCODE);

        $debug = in_array(strtolower((string) $atts['debug']), ['1', 'true', 'yes', 'on'], true);
        $source_id = self::source_id(max(0, (int) $atts['post_id']));
        if (!self::allowed($source_id)) {
            return $debug ? self::diagnostic('módulo inativo, modo diferente de shadow ou post de origem inválido') : '';
        }

        $type = sanitize_key((string) $atts['type']);
        $definitions = [
            'read_more' => ['kind' => 'related_post', 'limit' => 1, 'variant' => 'inline'],
            'related_posts' => ['kind' => 'related_post', 'limit' => 3, 'variant' => 'cards'],
            'topics' => ['kind' => 'topic', 'limit' => 8, 'variant' => 'terms'],
            'internal_links' => ['kind' => 'internal_link', 'limit' => 4, 'variant' => 'terms'],
        ];
        if (!isset($definitions[$type])) {
            return $debug ? self::diagnostic('tipo de relação inválido') : '';
        }

        $definition = $definitions[$type];
        $requested_limit = max(0, (int) $atts['limit']);
        $limit = $requested_limit > 0 ? min(20, $requested_limit) : (int) $definition['limit'];
        $offset = max(0, min(19, (int) $atts['offset']));
        $locale_resolver = new M360_Discovery_Locale_Resolver();
        $locale = $locale_resolver->resolve($source_id);
        if ($locale === '') {
            return $debug ? self::diagnostic('locale não resolvido para o post ' . $source_id) : '';
        }

        $kind = (string) $definition['kind'];
        $rows = (new M360_Discovery_DB())->active($source_id, $locale, $kind, min(20, $limit + $offset));
        if ($offset > 0 && $rows) { $rows = array_slice($rows, $offset, $limit); }
        if (!$rows) {
            return $debug ? self::diagnostic('nenhuma relação active para post ' . $source_id . ', locale ' . $locale . ' e tipo ' . $kind) : '';
        }

        $items = [];
        foreach ($rows as $row) {
            $item = self::item((array) $row, $locale, $locale_resolver);
            if ($item !== null) { $items[] = $item; }
        }
        if (!$items) {
            return $debug ? self::diagnostic('relações encontradas, mas nenhum destino publicado e compatível com o locale ' . $locale) : '';
        }

        $title = sanitize_text_field((string) $atts['title']);
        if ($title === '') { $title = self::default_title($type, $locale); }
        $variant = (string) $definition['variant'];

        if ($variant === 'inline') {
            return self::render_inline($items[0], $title, $locale);
        }
        if ($variant === 'cards') {
            return self::render_cards($items, $title, $locale);
        }
        return self::render_terms($items, $title, $locale, $kind);
    }

    /**
     * On a public singular request the queried post is authoritative. The
     * explicit post_id remains a fallback for Elementor/admin preview only.
     */
    private static function source_id(int $explicit_id): int
    {
        $queried_id = max(0, (int) get_queried_object_id());
        if (self::valid_source($queried_id)) { return $queried_id; }
        return self::valid_source($explicit_id) ? $explicit_id : 0;
    }

    private static function valid_source(int $post_id): bool
    {
        if ($post_id < 1) { return false; }
        $post = get_post($post_id);
        if (!$post instanceof WP_Post || $post->post_status !== 'publish') { return false; }
        $settings = M360_Content_Discovery_Module::settings();
        return in_array($post->post_type, (array) $settings['post_types'], true);
    }

    private static function allowed(int $post_id): bool
    {
        if ($post_id < 1 || !M360_Platform::instance()->registry()->is_enabled('content-discovery-seo')) { return false; }
        $settings = M360_Content_Discovery_Module::settings();
        return $settings['mode'] === 'shadow';
    }

    private static function diagnostic(string $message): string
    {
        return '<div class="m360-discovery-canary m360-discovery-canary--diagnostic" role="status"><strong>M360 Canary:</strong> ' . esc_html($message) . '.</div>';
    }

    private static function default_title(string $type, string $locale): string
    {
        $pt = stripos($locale, 'pt-') === 0 || strcasecmp($locale, 'pt') === 0;
        $titles = $pt
            ? ['read_more' => 'LEIA TAMBÉM', 'related_posts' => 'NOTÍCIAS RELACIONADAS', 'topics' => 'TÓPICOS RELACIONADOS', 'internal_links' => 'EXPLORE TÓPICOS']
            : ['read_more' => 'RELATED STORY', 'related_posts' => 'RELATED NEWS', 'topics' => 'RELATED TOPICS', 'internal_links' => 'EXPLORE TOPICS'];
        return $titles[$type] ?? '';
    }

    /** @param array<int,array<string,mixed>> $items */
    private static function render_cards(array $items, string $title, string $locale): string
    {
        ob_start();
        ?>
        <section class="m360-discovery-canary m360-discovery-canary--related-post" lang="<?php echo esc_attr($locale); ?>" aria-label="<?php echo esc_attr($title); ?>">
            <h2 class="m360-discovery-canary__title"><?php echo esc_html($title); ?></h2>
            <div class="m360-discovery-canary__cards">
                <?php foreach ($items as $item): ?>
                    <article class="m360-discovery-canary__card<?php echo empty($item['image']) ? ' m360-discovery-canary__card--no-image' : ''; ?>">
                        <a class="m360-discovery-canary__card-link" href="<?php echo esc_url((string) $item['url']); ?>">
                            <?php if (!empty($item['image'])): ?>
                                <img class="m360-discovery-canary__thumbnail" src="<?php echo esc_url((string) $item['image']); ?>" alt="" loading="lazy" decoding="async">
                            <?php endif; ?>
                            <span class="m360-discovery-canary__card-title"><?php echo esc_html((string) $item['label']); ?></span>
                            <span class="m360-discovery-canary__meta">
                                <time datetime="<?php echo esc_attr((string) $item['date_iso']); ?>"><?php echo esc_html((string) $item['date']); ?></time>
                                <?php if ((string) $item['category'] !== ''): ?><span aria-hidden="true"> · </span><span><?php echo esc_html((string) $item['category']); ?></span><?php endif; ?>
                            </span>
                            <span class="m360-discovery-canary__cta"><?php echo esc_html(self::cta_label($locale)); ?></span>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
        return (string) ob_get_clean();
    }

    /** @param array<string,mixed> $item */
    private static function render_inline(array $item, string $title, string $locale): string
    {
        ob_start();
        ?>
        <aside class="m360-discovery-canary m360-discovery-canary--read-more m360-sr-inline-related" lang="<?php echo esc_attr($locale); ?>" aria-label="<?php echo esc_attr($title); ?>">
            <span class="m360-discovery-canary__inline-label"><?php echo esc_html($title); ?></span>
            <div class="m360-discovery-canary__inline-row">
                <a class="m360-discovery-canary__inline-thumb" href="<?php echo esc_url((string) $item['url']); ?>">
                    <?php if (!empty($item['image'])): ?><img src="<?php echo esc_url((string) $item['image']); ?>" alt="" loading="lazy" decoding="async"><?php else: ?><span aria-hidden="true"></span><?php endif; ?>
                </a>
                <div class="m360-discovery-canary__inline-body">
                    <a class="m360-discovery-canary__inline-link" href="<?php echo esc_url((string) $item['url']); ?>"><?php echo esc_html((string) $item['label']); ?></a>
                    <span class="m360-discovery-canary__meta">
                        <time datetime="<?php echo esc_attr((string) $item['date_iso']); ?>"><?php echo esc_html((string) $item['date']); ?></time>
                        <?php if ((string) $item['category'] !== ''): ?><span aria-hidden="true"> · </span><span><?php echo esc_html((string) $item['category']); ?></span><?php endif; ?>
                    </span>
                    <a class="m360-discovery-canary__cta" href="<?php echo esc_url((string) $item['url']); ?>"><?php echo esc_html(self::cta_label($locale)); ?></a>
                </div>
            </div>
        </aside>
        <?php
        return (string) ob_get_clean();
    }

    /** @param array<int,array<string,mixed>> $items */
    private static function render_terms(array $items, string $title, string $locale, string $kind): string
    {
        ob_start();
        ?>
        <section class="m360-discovery-canary m360-discovery-canary--<?php echo esc_attr($kind); ?>" lang="<?php echo esc_attr($locale); ?>" aria-label="<?php echo esc_attr($title); ?>">
            <h2 class="m360-discovery-canary__title"><?php echo esc_html($title); ?></h2>
            <ul class="m360-discovery-canary__terms">
                <?php foreach ($items as $item): ?>
                    <li class="m360-discovery-canary__term"><a href="<?php echo esc_url((string) $item['url']); ?>"><?php echo esc_html((string) $item['label']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php
        return (string) ob_get_clean();
    }

    private static function cta_label(string $locale): string
    {
        return stripos($locale, 'pt-') === 0 || strcasecmp($locale, 'pt') === 0 ? 'LEIA MAIS' : 'READ MORE';
    }

    /** @return array{url:string,label:string,image:string,date:string,date_iso:string,category:string}|null */
    private static function item(array $row, string $source_locale, M360_Discovery_Locale_Resolver $resolver): ?array
    {
        $type = sanitize_key((string) ($row['target_type'] ?? ''));
        $id = max(0, (int) ($row['target_id'] ?? 0));
        if ($type === 'post') {
            $post = get_post($id);
            if (!$post instanceof WP_Post || $post->post_status !== 'publish') { return null; }
            $target_locale = $resolver->resolve($id);
            if ($target_locale === '' || strcasecmp($target_locale, $source_locale) !== 0) { return null; }
            $url = get_permalink($post);
            if (!is_string($url) || $url === '') { return null; }
            $image = get_the_post_thumbnail_url($post, 'medium_large');
            $categories = get_the_category((int) $post->ID);
            $category = !empty($categories) && $categories[0] instanceof WP_Term ? (string) $categories[0]->name : '';
            $pt = stripos($source_locale, 'pt-') === 0 || strcasecmp($source_locale, 'pt') === 0;
            return [
                'url' => $url,
                'label' => get_the_title($post),
                'image' => is_string($image) ? $image : '',
                'date' => get_the_date($pt ? 'd/m/Y H:i' : 'M j, Y H:i', $post),
                'date_iso' => get_the_date(DATE_W3C, $post),
                'category' => $category,
            ];
        }
        if ($type === 'term') {
            $term = get_term($id);
            if (!$term instanceof WP_Term || is_wp_error($term)) { return null; }
            if (!self::term_matches_locale($term, $source_locale, $resolver)) { return null; }
            $url = get_term_link($term);
            if (is_wp_error($url)) { return null; }
            return ['url' => (string) $url, 'label' => $term->name, 'image' => '', 'date' => '', 'date_iso' => '', 'category' => ''];
        }
        return null;
    }

    private static function term_matches_locale(WP_Term $term, string $source_locale, M360_Discovery_Locale_Resolver $resolver): bool
    {
        if (!function_exists('pll_get_term_language')) { return true; }
        $term_locale = (string) pll_get_term_language($term->term_id, 'locale');
        if ($term_locale === '') { $term_locale = (string) pll_get_term_language($term->term_id, 'slug'); }
        $term_locale = $resolver->normalize_supported($term_locale);
        return $term_locale !== '' && strcasecmp($term_locale, $source_locale) === 0;
    }
}
