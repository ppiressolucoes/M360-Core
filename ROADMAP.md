# Roadmap — M360 Core

## Baseline canônico — julho de 2026

- Arquitetura vigente: `M360 Platform Architecture v2.2`.
- Produção: `v0.4.4.0 — M360 AdSense Ready`.
- Baseline estável: `v0.4.4.5 — M360 Universal Slot Renderer`.
- Próxima linha: `v0.5.x — Plataforma Comercial M360`.

As seções históricas abaixo preservam a evolução original. Para novas implementações, prevalece a linha `v0.5.x` descrita a seguir.

## v0.5.x — Plataforma Comercial M360

1. `v0.5.0`: domínio comercial, permissões, feature flags, migrações e rollback.
2. `v0.5.1`: Campaign Engine, priorização, rotação e regras por contexto, idioma, dispositivo e período.
3. `v0.5.2`: métricas, auditoria, consentimento e Dashboard Comercial.
4. `v0.5.3`: conectores Google AdSense, Google Ad Manager, afiliados e patrocinadores.
5. `v0.5.4`: Smart Delivery, pacing, frequência e otimização contextual.

Toda evolução comercial deve consumir o `M360 Universal Slot Renderer` e preservar PT-BR, EN-US, APIs, shortcodes e slots homologados.

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
