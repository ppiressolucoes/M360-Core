<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Ads_Inventory_Library
{
    public const VERSION = '1.0.0';

    /**
     * Official M360 commercial inventory registry.
     *
     * Shape:
     * [slot_key, name, description, position, page_context, language, device, max_width, max_height]
     */
    public static function slots(): array
    {
        return [
            // Home
            ['header-top', 'Header Top', 'Topo global do portal. Slot piloto homologado.', 'header', 'global', 'all', 'all', 728, 140],
            ['home-hero', 'Home Hero', 'Slot comercial na área hero da home.', 'hero', 'home', 'all', 'all', 1200, 250],
            ['home-after-highlight', 'Home After Highlight', 'Slot após o destaque principal da home.', 'after-highlight', 'home', 'all', 'all', 970, 250],
            ['home-inline-feed-1', 'Home Inline Feed 1', 'Primeiro slot inline no feed da home.', 'inline-1', 'home', 'all', 'all', 728, 90],
            ['home-inline-feed-2', 'Home Inline Feed 2', 'Segundo slot inline no feed da home.', 'inline-2', 'home', 'all', 'all', 728, 90],
            ['content-bottom', 'Content Bottom', 'Slot horizontal ao final do conteúdo. Slot piloto homologado.', 'bottom', 'post', 'all', 'all', 1200, 250],
            ['home-before-footer', 'Home Before Footer', 'Slot comercial antes do footer na home.', 'before-footer', 'home', 'all', 'all', 970, 250],

            // Articles
            ['article-top', 'Article Top', 'Slot no topo do artigo.', 'top', 'post', 'all', 'all', 970, 250],
            ['article-after-paragraph-2', 'Article After Paragraph 2', 'Slot inline após o segundo parágrafo do artigo.', 'after-paragraph-2', 'post', 'all', 'all', 728, 90],
            ['article-inline-1', 'Article Inline 1', 'Primeiro slot inline do artigo.', 'inline-1', 'post', 'all', 'all', 728, 90],
            ['article-inline-2', 'Article Inline 2', 'Segundo slot inline do artigo.', 'inline-2', 'post', 'all', 'all', 728, 90],
            ['article-inline-3', 'Article Inline 3', 'Terceiro slot inline do artigo.', 'inline-3', 'post', 'all', 'all', 728, 90],
            ['article-before-related', 'Article Before Related', 'Slot antes dos conteúdos relacionados.', 'before-related', 'post', 'all', 'all', 970, 250],
            ['article-after-related', 'Article After Related', 'Slot após os conteúdos relacionados.', 'after-related', 'post', 'all', 'all', 970, 250],
            ['article-bottom', 'Article Bottom', 'Slot inferior do artigo.', 'bottom', 'post', 'all', 'all', 970, 250],

            // Sidebar
            ['sidebar-top', 'Sidebar Top', 'Slot superior da sidebar.', 'top', 'sidebar', 'all', 'desktop', 300, 250],
            ['sidebar-community', 'Sidebar Community', 'Slot de comunidade na sidebar. Slot piloto homologado.', 'community', 'sidebar', 'all', 'desktop', 300, 300],
            ['sidebar-square', 'Sidebar Square', 'Slot quadrado na sidebar. Slot piloto homologado.', 'square', 'sidebar', 'all', 'desktop', 300, 300],
            ['sidebar-middle', 'Sidebar Middle', 'Slot intermediário da sidebar.', 'middle', 'sidebar', 'all', 'desktop', 300, 250],
            ['sidebar-bottom', 'Sidebar Bottom', 'Slot inferior da sidebar.', 'bottom', 'sidebar', 'all', 'desktop', 300, 600],
            ['sidebar-sticky', 'Sidebar Sticky', 'Slot sticky da sidebar.', 'sticky', 'sidebar', 'all', 'desktop', 300, 600],

            // Search
            ['search-top', 'Search Top', 'Slot superior em resultados de busca.', 'top', 'search', 'all', 'all', 970, 250],
            ['search-inline', 'Search Inline', 'Slot inline em resultados de busca.', 'inline', 'search', 'all', 'all', 728, 90],
            ['search-bottom', 'Search Bottom', 'Slot inferior em resultados de busca.', 'bottom', 'search', 'all', 'all', 970, 250],
            ['search-empty', 'Search Empty', 'Slot para página de busca sem resultados.', 'empty', 'search', 'all', 'all', 970, 250],

            // Categories
            ['category-top', 'Category Top', 'Slot superior em páginas de categoria.', 'top', 'category', 'all', 'all', 970, 250],
            ['category-inline', 'Category Inline', 'Slot inline em páginas de categoria.', 'inline', 'category', 'all', 'all', 728, 90],
            ['category-bottom', 'Category Bottom', 'Slot inferior em páginas de categoria.', 'bottom', 'category', 'all', 'all', 970, 250],

            // Tags
            ['tag-top', 'Tag Top', 'Slot superior em páginas de tag.', 'top', 'tag', 'all', 'all', 970, 250],
            ['tag-inline', 'Tag Inline', 'Slot inline em páginas de tag.', 'inline', 'tag', 'all', 'all', 728, 90],
            ['tag-bottom', 'Tag Bottom', 'Slot inferior em páginas de tag.', 'bottom', 'tag', 'all', 'all', 970, 250],

            // Authors
            ['author-top', 'Author Top', 'Slot superior em páginas de autor.', 'top', 'author', 'all', 'all', 970, 250],
            ['author-inline', 'Author Inline', 'Slot inline em páginas de autor.', 'inline', 'author', 'all', 'all', 728, 90],
            ['author-bottom', 'Author Bottom', 'Slot inferior em páginas de autor.', 'bottom', 'author', 'all', 'all', 970, 250],

            // Latest news
            ['latest-top', 'Latest Top', 'Slot superior no contexto de últimas notícias.', 'top', 'latest-news', 'all', 'all', 970, 250],
            ['latest-inline', 'Latest Inline', 'Slot inline no contexto de últimas notícias.', 'inline', 'latest-news', 'all', 'all', 728, 90],
            ['latest-bottom', 'Latest Bottom', 'Slot inferior no contexto de últimas notícias.', 'bottom', 'latest-news', 'all', 'all', 970, 250],

            // Archives
            ['archive-top', 'Archive Top', 'Slot superior em arquivos.', 'top', 'archive', 'all', 'all', 970, 250],
            ['archive-inline', 'Archive Inline', 'Slot inline em arquivos.', 'inline', 'archive', 'all', 'all', 728, 90],
            ['archive-bottom', 'Archive Bottom', 'Slot inferior em arquivos.', 'bottom', 'archive', 'all', 'all', 970, 250],

            // Sports widgets
            ['widget-before', 'Widget Before', 'Slot antes de widgets esportivos.', 'before', 'widget', 'all', 'all', 970, 250],
            ['widget-inline', 'Widget Inline', 'Slot inline em widgets esportivos.', 'inline', 'widget', 'all', 'all', 728, 90],
            ['widget-after', 'Widget After', 'Slot após widgets esportivos.', 'after', 'widget', 'all', 'all', 970, 250],

            // Community roadmap
            ['community-top', 'Community Top', 'Slot superior em comunidade.', 'top', 'community', 'all', 'all', 970, 250],
            ['community-feed-inline', 'Community Feed Inline', 'Slot inline no feed da comunidade.', 'inline', 'community', 'all', 'all', 728, 90],
            ['community-sidebar', 'Community Sidebar', 'Slot de sidebar em comunidade.', 'sidebar', 'community', 'all', 'desktop', 300, 300],
            ['community-bottom', 'Community Bottom', 'Slot inferior em comunidade.', 'bottom', 'community', 'all', 'all', 970, 250],
        ];
    }

    public static function slot_keys(): array
    {
        return array_map(static fn(array $slot): string => (string) $slot[0], self::slots());
    }

    public static function contexts(): array
    {
        $contexts = [];
        foreach (self::slots() as $slot) { $contexts[(string) $slot[4]] = true; }
        return array_keys($contexts);
    }
}
