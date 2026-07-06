# Changelog — M360 Core

Todas as mudanças relevantes do M360 Core serão registradas neste arquivo.

## [v0.3.4] — View Engine Foundation

Status: entregue no GitHub para geração de artifact ZIP.

### Adicionado

- Fundação do M360 View Engine.
- `M360_View_Registry` para registro central de views.
- `M360_View_Loader` para resolução de templates por idioma/fallback.
- `M360_View_Renderer` para renderização segura de templates.
- Integração inicial do View Registry ao runtime do M360 Core.
- Views placeholder iniciais em `plugin/views/default/`.
- Shortcode `[m360_view]` conectado ao novo fluxo interno.

### Observações

- Entrega estrutural sem alteração visual pública.
- Conteúdo de diagnóstico permanece restrito a usuários com `manage_options`.
- Base preparada para a Sprint 10.5 — Dynamic View Migration.

## [v0.3.4-rc1] — Plugin Packaging Foundation

Status: artifact ZIP instalável preparado.

### Adicionado

- `plugin/m360-core.php`.
- `plugin/includes/class-m360-core.php`.
- `plugin/assets/css/m360-core.css`.
- `plugin/uninstall.php`.
- GitHub Actions para geração de ZIP a partir de `plugin/`.

## [v0.3.2] — Layout Foundation

Status: instalada e ativa.

### Adicionado

- M360 Layout Engine Foundation.
- Shortcode `[m360_layout_shell view="search"]`.
- Shortcode `[m360_layout_shell view="author" author="luzia-aires"]`.
- Shortcode `[m360_layout_shell view="latest_news"]`.
- Base para Search, Radar News e demais Dynamic Views.

### Observações

- Sem override novo.
- Sem alteração global de header/footer.
- Sem alteração global de layout.

## [v0.3.1] — Search Engine

Status: instalada e validada funcionalmente.

### Adicionado

- M360 Search Results.
- M360 Search Router.
- Schema `SearchResultsPage`.
- Empty State PT-BR / EN-US.
- Grid padrão M360.

### Observações

- Resultados de busca funcionando em PT-BR e EN-US.
- Dependência residual do Theme Builder aceita temporariamente até a evolução do Archive Engine.

## [v0.2.9] — Author Router Hotfix

Status: validada.

### Adicionado

- Correção do conflito de idioma no Author Archive.
- Redirecionamento de `/author/{slug}/` para `/autor/?m360_author={slug}`.
- Redirecionamento de `/en/author/{slug}/` para `/en/author/?m360_author={slug}`.

## [v0.2.8] — Archive Renderer Foundation

Status: instalada para validação.

### Adicionado

- Base de normalização do Author Hub.
- Dynamic container.
- Preparação para Archive Renderer.

## [v0.2.7] — Author Archive Override

Status: validada em produção.

### Adicionado

- M360 Author Hub em URL nativa de autor.
- Card do autor.
- Biografia.
- Contador de artigos.
- Grid de publicações.
- Breadcrumb M360.

## [v0.2.6] — Author Hub Hotfix

Status: validada.

### Adicionado

- Suporte a `[m360_author_page id="2"]`.
- Suporte a `[m360_author_page author="luzia-aires"]`.

## [v0.2.5] — Dynamic Views Base

Status: instalada sem impacto visual.

### Adicionado

- `[m360_author_page]`.
- `[m360_search_results]`.
- `[m360_latest_news]`.

## [v0.2.4] — Breadcrumb inteligente

Status: validada.

### Adicionado

- Breadcrumb compacto.
- Separador `›`.
- Ícone Home SVG.
- Destaque do item atual.
- Schema.org `BreadcrumbList` em JSON-LD.
- HTML semântico com `nav`, `ol`, `aria-label` e `aria-current`.

## [v0.2.3] — Breadcrumb base

### Adicionado

- Shortcode `[m360_breadcrumb]`.

## [v0.2.2] — Section Navigation refinado

### Adicionado

- Melhor área clicável.
- Espaçamento vertical.
- Hover.
- Item ativo com destaque lateral e linha inferior.

## [v0.2.1] — Main Navigation compatibilidade

### Corrigido

- Resolução automática do menu principal.
- Compatibilidade com `Primary Menu English`.

## [v0.2.0] — M360 Core oficial

### Adicionado

- Consolidação do plugin oficial `m360-core`.
- Shortcode `[m360_section_navigation]`.

## [v0.1.1] — Navigation MVP correções

### Corrigido

- Contraste do menu.
- Resolução do menu EN-US.

## [v0.1.0] — Navigation MVP

### Adicionado

- Shortcode `[m360_main_navigation]`.
- Primeira prova de conceito da Navigation Library.
