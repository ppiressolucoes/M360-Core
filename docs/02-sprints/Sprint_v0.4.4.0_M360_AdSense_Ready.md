# Sprint v0.4.4.0 — M360 AdSense Ready

Status: implementação funcional em homologação
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Módulo: M360 Advertising
Subsistema: M360 Ads Manager

## 1. Objetivo

Preparar a infraestrutura visual, semântica e técnica dos espaços publicitários do Mengão 360 para futura integração com provedores externos, especialmente Google AdSense, sem alterar a arquitetura consolidada do M360 Ads Manager.

## 2. Base arquitetural

A sprint preserva inventário, campanhas, criativos, shortcodes, API PHP, internacionalização PT-BR/EN-US, compatibilidade com Elementor/News Portal e independência progressiva do tema, conforme o ADR-0007.

## 3. Linha de entregas

| Versão | Entrega | Status |
|---|---|---|
| `0.4.4.0` | Ad Slot Component, labels, data attributes, placeholders e CSS centralizado | Homologação inicial |
| `0.4.4.1` | Inventory Library e Inventory Seeder | Implementada |
| `0.4.4.2` | Context Renderer | Implementada |
| `0.4.4.3` | Inline Ads Engine em artigos | Homologada inicialmente |
| `0.4.4.4` | Archive Ads Engine em listagens M360 | Implementada; pendente homologação |
| `0.4.4.5` | Universal Slot Renderer | Planejada |
| `0.4.4.6` | AdSense Ready Final | Planejada |

## 4. v0.4.4.4 — M360 Archive Ads Engine

### Objetivo

Inserir slots publicitários automaticamente nas listagens controladas pelo M360 Core, sem editar templates do tema News Portal nem estruturas do Elementor.

### Contextos implementados

| Contexto | Slot | Posição padrão |
|---|---|---:|
| Search | `search-inline` | após o 3º resultado |
| Category | `category-inline` | após o 3º artigo |
| Tag | `tag-inline` | após o 3º artigo |
| Author | `author-inline` | após o 3º artigo |
| Latest News | `latest-inline` | após o 4º item |

Busca sem resultados utiliza `search-empty`.

### Arquivos funcionais

- `plugin/includes/ads/class-m360-ads-archive-engine.php`;
- `plugin/includes/search/class-m360-search-controller.php`;
- `plugin/includes/category/class-m360-category-controller.php`;
- `plugin/includes/tag/class-m360-tag-controller.php`;
- `plugin/includes/author/class-m360-author-controller.php`;
- `plugin/includes/latest-news/class-m360-latest-news-component.php`;
- `plugin/includes/class-m360-core.php`;
- `plugin/assets/css/m360-ads.css`;
- `plugin/m360-core.php`.

### APIs e filtros

```php
m360_ads_render_archive(string $context, array $args = []): string
m360_ads_archive_position(string $context): int
```

Filtros:

```text
m360_ads_archive_enabled
m360_ads_archive_position
m360_ads_archive_slot
```

### Limite arquitetural conhecido

O contexto `archive-inline` está disponível por API, porém a injeção automática em arquivos genéricos ainda controlados pelo tema será implementada pelo futuro Theme Adapter/Universal Slot Renderer. A v0.4.4.4 não modifica templates externos, em aderência ao ADR-0007.

## 5. Critérios de aceite da v0.4.4.4

- plugin ativa sem erro fatal;
- Search insere publicidade após o terceiro resultado;
- Category insere publicidade após o terceiro artigo;
- Tag insere publicidade após o terceiro artigo;
- Author insere publicidade após o terceiro artigo;
- Latest News insere publicidade após o quarto item;
- busca sem resultados renderiza `search-empty`;
- labels PT-BR e EN-US permanecem corretas;
- placeholder aparece sem campanha vinculada;
- layout permanece íntegro em desktop e mobile;
- nenhum template do tema ou Elementor é alterado.

## 6. Fora do escopo

- integração oficial com Google AdSense;
- métricas de impressão/clique;
- rotação avançada;
- Google Ad Manager operacional;
- Dashboard Comercial;
- Marketplace Comercial M360;
- arquivos genéricos do tema sem componente M360.
