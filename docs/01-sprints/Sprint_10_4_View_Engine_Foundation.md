# Sprint 10.4 — M360 View Engine Foundation

Status: entregue no GitHub  
Versão: 0.3.4  
Fluxo: GitHub First

## Objetivo

Criar a fundação do M360 View Engine para centralizar o registro, carregamento e renderização de views do M360 Core.

## Entregas de código

- `plugin/includes/ViewEngine/class-m360-view-registry.php`
- `plugin/includes/ViewEngine/class-m360-view-loader.php`
- `plugin/includes/ViewEngine/class-m360-view-renderer.php`
- Integração inicial no runtime `plugin/includes/class-m360-core.php`
- Placeholder de view em `plugin/views/default/`

## Entregas de governança

- `VERSION.md` atualizado para `0.3.4`.
- `CHANGELOG.md` atualizado com a entrega `v0.3.4`.
- Documento desta sprint criado em `docs/01-sprints/`.

## Escopo seguro

Esta sprint não altera visual público do portal.

O shortcode `[m360_view]` permanece restrito a administradores nesta fase, evitando exposição pública de placeholders.

## Impacto no portal

- Nenhum impacto visual esperado para visitantes.
- Plugin passa a ter base interna para registrar e renderizar views.
- Preparação para migração futura de Search, Author e Radar News.

## Critérios de aceite

- [x] View Registry criado.
- [x] View Loader criado.
- [x] View Renderer criado.
- [x] Runtime do plugin conectado à fundação do View Engine.
- [x] Versionamento atualizado para `0.3.4`.
- [x] Changelog atualizado.
- [x] Documentação da sprint criada.

## Próxima sprint

Sprint 10.5 — Dynamic View Migration.

Objetivo: migrar os primeiros componentes reais para o View Engine, iniciando por Search/Author ou Radar News conforme prioridade operacional.
