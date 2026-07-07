# Sprint 10.6 — Author Engine

Status: pronto para execução do workflow  
Versão: 0.3.8.0  
Fluxo: GitHub First

## Objetivo

Entregar a segunda página dinâmica da Sprint 10.6 controlada pelo M360 Core: páginas de autor PT-BR e EN-US.

## Escopo

- Author Controller.
- Template próprio para autor.
- Hero de autor.
- Grid de artigos do autor.
- Breadcrumb integrado.
- Paginação.
- CSS dedicado.
- EN-US usando Elementor Header 4123 e Footer 3765.
- PT-BR preservando header e footer do tema.

## Arquivos adicionados

- plugin/includes/author/class-m360-author-controller.php
- plugin/templates/author.php
- plugin/assets/css/author.css

## Arquivos atualizados

- plugin/includes/class-m360-core.php
- plugin/m360-core.php
- VERSION.md

## Critérios de aceite

- [ ] Página /author/{slug}/ renderiza pelo M360 Core.
- [ ] Página /en/author/{slug}/ renderiza pelo M360 Core.
- [ ] Header e footer PT-BR permanecem estáveis.
- [ ] Header e footer EN-US usam templates Elementor 4123 e 3765.
- [ ] Hero do autor aparece corretamente.
- [ ] Grid de artigos do autor aparece corretamente.
- [ ] Paginação funciona.
- [ ] Search Engine v0.3.7.4 permanece sem regressão.

## Próxima entrega

v0.3.9.0 — Category Engine.
