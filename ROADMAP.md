# Roadmap — M360 Core

## Visão

O M360 Core é o framework de interface do Projeto Mengão 360. Sua missão é reduzir progressivamente a dependência do tema News Portal e do Theme Builder do Elementor, consolidando componentes próprios, multilíngues e reutilizáveis.

## Release 2.x — Navigation + Dynamic Views

### Concluído

- Main Navigation.
- Section Navigation.
- Breadcrumb inteligente.
- Author Hub.
- Search Engine funcional.
- Layout Foundation.

### Em transição

- Search ainda possui dependência residual do Theme Builder para header/footer/layout.
- Author Hub estabilizado via rota canônica M360.

## Release 3.x — View Engine e Radar News

### v0.3.3 — Radar News Engine + View Engine Consolidation

Objetivo:

- Criar o M360 View Engine.
- Entregar Radar News / Latest News independente do News Portal.
- Reutilizar Grid, Cards, Paginação, Breadcrumb e Schema.

Shortcodes previstos:

```text
[m360_latest_news]
[m360_view view="latest_news"]
```

Critérios de aceite:

- `/ultimas-noticias/` funcional.
- `/en/latest-news/` funcional.
- PT-BR e EN-US consistentes.
- Modo claro/escuro sem conflito com News Portal.
- Schema `CollectionPage`, `ItemList` e `BreadcrumbList`.

## Release 3.4 — Category Engine

Objetivo:

- Migrar arquivos de categoria para M360 Core.
- Padronizar cards, paginação e breadcrumb.
- Preparar PT-BR / EN-US / ES-ES.

## Release 3.5 — Tag Engine

Objetivo:

- Migrar arquivos de tags para M360 Core.
- Usar o mesmo View Engine.

## Release 3.6 — Competition Hub

Objetivo:

- Criar navegação e landing dinâmica por competição.
- Integrar com DW Esportivo e Competition Registry.

## Release 4.x — Layout Engine

Objetivo:

- Unificar header, footer, container, sidebar e template routing.
- Reduzir dependência do tema a uma camada de compatibilidade.

Componentes previstos:

- Header Manager.
- Footer Manager.
- Sidebar Manager.
- Template Router.
- Layout Slots.
- Container Manager.

## Release 5.x — Platform UI

Objetivo:

- Consolidar o M360 Core como framework visual completo do portal.
- Preparar suporte ampliado a ES-ES.
- Integrar componentes com M360 Community e Mega Bolão 360.
