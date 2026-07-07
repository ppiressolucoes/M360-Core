-- M360 Ads Default Slots Seed
-- Version: 0.4.2.4-docs
-- Purpose: reference seed for initial ad inventory.
-- Note: WordPress table prefix is represented as wp_. Future runtime must use $wpdb->prefix.

INSERT INTO wp_m360_ad_slots
(slot_key, name, description, position, page_context, language, device, max_width, max_height, is_active, created_at, updated_at)
VALUES
('header-banner', 'Header Banner', 'Banner principal no topo do portal.', 'header', 'global', 'all', 'desktop', 970, 250, 1, NOW(), NOW()),
('header-mobile', 'Header Mobile', 'Banner principal no topo para mobile.', 'header', 'global', 'all', 'mobile', 320, 100, 1, NOW(), NOW()),
('home-top', 'Home Top', 'Slot superior da home editorial.', 'top', 'home', 'all', 'all', 1200, 250, 1, NOW(), NOW()),
('home-middle', 'Home Middle', 'Slot intermediario da home editorial.', 'middle', 'home', 'all', 'all', 1200, 250, 1, NOW(), NOW()),
('home-bottom', 'Home Bottom', 'Slot inferior da home editorial.', 'bottom', 'home', 'all', 'all', 1200, 250, 1, NOW(), NOW()),
('article-top', 'Article Top', 'Slot no topo do artigo.', 'top', 'post', 'all', 'all', 970, 250, 1, NOW(), NOW()),
('article-inline-1', 'Article Inline 1', 'Primeiro slot interno do artigo.', 'inline', 'post', 'all', 'all', 728, 90, 1, NOW(), NOW()),
('article-inline-2', 'Article Inline 2', 'Segundo slot interno do artigo.', 'inline', 'post', 'all', 'all', 728, 90, 1, NOW(), NOW()),
('article-bottom', 'Article Bottom', 'Slot inferior do artigo.', 'bottom', 'post', 'all', 'all', 970, 250, 1, NOW(), NOW()),
('sidebar-top', 'Sidebar Top', 'Slot superior da sidebar.', 'sidebar', 'global', 'all', 'desktop', 300, 250, 1, NOW(), NOW()),
('sidebar-middle', 'Sidebar Middle', 'Slot intermediario da sidebar.', 'sidebar', 'global', 'all', 'desktop', 300, 250, 1, NOW(), NOW()),
('sidebar-bottom', 'Sidebar Bottom', 'Slot inferior da sidebar.', 'sidebar', 'global', 'all', 'desktop', 300, 600, 1, NOW(), NOW()),
('footer-banner', 'Footer Banner', 'Banner no rodape.', 'footer', 'global', 'all', 'all', 970, 250, 1, NOW(), NOW()),
('category-top', 'Category Top', 'Slot superior em paginas de categoria.', 'top', 'category', 'all', 'all', 970, 250, 1, NOW(), NOW()),
('tag-top', 'Tag Top', 'Slot superior em paginas de tag.', 'top', 'tag', 'all', 'all', 970, 250, 1, NOW(), NOW()),
('author-top', 'Author Top', 'Slot superior em paginas de autor.', 'top', 'author', 'all', 'all', 970, 250, 1, NOW(), NOW()),
('search-top', 'Search Top', 'Slot superior em paginas de busca.', 'top', 'search', 'all', 'all', 970, 250, 1, NOW(), NOW()),
('latest-news-inline', 'Latest News Inline', 'Slot associado ao componente de ultimas noticias.', 'inline', 'latest-news', 'all', 'all', 728, 90, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    description = VALUES(description),
    position = VALUES(position),
    page_context = VALUES(page_context),
    language = VALUES(language),
    device = VALUES(device),
    max_width = VALUES(max_width),
    max_height = VALUES(max_height),
    is_active = VALUES(is_active),
    updated_at = NOW();
