# Sprint 11 - UI Components Foundation

Status: pronto para execucao do workflow
Versao: 0.4.2.0
Fluxo: GitHub First

## Objetivo

Criar uma base visual isolada e segura para componentes do M360 Core, sem interferir no tema, no M360 Home Editorial, no Bolao ou no M360 Semantic Relations.

## Contexto

A v0.4.1.2 mostrou que CSS global pode gerar regressao na Home EN. A v0.4.1.3 estabilizou o ambiente ao escopar os estilos. A v0.4.2.0 inicia a arquitetura correta: componentes visuais com classe raiz `.m360-ui` e dependencias controladas.

## Escopo

- Classe `M360_UI_Components`.
- CSS isolado `m360-ui-components.css`.
- Shortcode de container.
- Shortcode de chip.
- Shortcode de badge.
- Shortcode de card.
- Registro seguro de assets.
- Nenhum seletor global de tema ou Elementor.

## Shortcodes adicionados

```text
[m360_ui_container]
[m360_ui_chip]
[m360_ui_badge]
[m360_ui_card]
```

## Arquivos adicionados

- plugin/includes/ui/class-m360-ui-components.php
- plugin/assets/css/m360-ui-components.css

## Arquivos atualizados

- plugin/includes/class-m360-core.php
- plugin/m360-core.php
- VERSION.md

## Criterios de aceite

- Plugin ativa sem erro fatal.
- Home EN permanece estavel.
- Home PT permanece estavel.
- Search, Author, Category e Tag sem regressao.
- Shortcodes UI renderizam apenas elementos `.m360-ui`.
- CSS nao afeta tema, Elementor, Home Editorial, Bolao ou Semantic Relations.

## Proxima entrega

v0.4.3.0 - Taxonomy Intelligence Foundation.
