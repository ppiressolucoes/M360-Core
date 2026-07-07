# Sprint 10.6 — Search Engine

Status: pronto para execução do workflow  
Versão: 0.3.7.0  
Fluxo: GitHub First

## Objetivo

Entregar a primeira página dinâmica da Sprint 10.6 controlada pelo M360 Core: resultados de pesquisa PT-BR e EN-US.

## Escopo

- Search Controller.
- Template próprio para pesquisa.
- Cards de resultado.
- Empty State PT-BR / EN-US.
- Breadcrumb integrado.
- Paginação.
- CSS dedicado.

## Arquivos adicionados

- `plugin/includes/search/class-m360-search-controller.php`
- `plugin/templates/search.php`
- `plugin/assets/css/search.css`

## Arquivos atualizados

- `plugin/includes/class-m360-core.php`
- `plugin/m360-core.php`
- `VERSION.md`
- `CHANGELOG.md`

## Critérios de aceite

- [ ] `/ ?s=flamengo` renderiza pelo M360 Core.
- [ ] `/en/?s=flamengo` renderiza pelo M360 Core.
- [ ] Breadcrumb aparece corretamente.
- [ ] Resultados aparecem em cards.
- [ ] Paginação funciona quando houver múltiplas páginas.
- [ ] Empty State aparece quando não houver resultados.
- [ ] Menu principal e hamburger da v0.3.6.2 continuam funcionando.

## Próxima entrega

`v0.3.7.1` — Author Engine.
