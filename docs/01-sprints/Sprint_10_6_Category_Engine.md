# Sprint 10.6 — Category Engine

Status: pronto para execução do workflow  
Versão: 0.3.9.0  
Fluxo: GitHub First

## Objetivo

Entregar a terceira página dinâmica da Sprint 10.6 controlada pelo M360 Core: páginas de categoria PT-BR e EN-US.

## Escopo

- Category Controller.
- Template próprio para categoria.
- Hero de categoria.
- Grid de artigos da categoria.
- Breadcrumb completo.
- Paginação.
- CSS dedicado.
- EN-US usando Elementor Header 4123 e Footer 3765.
- PT-BR preservando header e footer do tema.

## Arquivos adicionados

- plugin/includes/category/class-m360-category-controller.php
- plugin/templates/category.php
- plugin/assets/css/category.css

## Arquivos atualizados

- plugin/includes/class-m360-core.php
- plugin/includes/navigation/class-m360-navigation-shortcodes.php
- plugin/m360-core.php
- VERSION.md

## Critérios de aceite

- [ ] Página de categoria PT-BR renderiza pelo M360 Core.
- [ ] Página de categoria EN-US renderiza pelo M360 Core.
- [ ] Header e footer PT-BR permanecem estáveis.
- [ ] Header e footer EN-US usam templates Elementor 4123 e 3765.
- [ ] Hero da categoria aparece corretamente.
- [ ] Grid de artigos da categoria aparece corretamente.
- [ ] Paginação funciona.
- [ ] Breadcrumb: Home > Category > Nome.
- [ ] Search e Author permanecem sem regressão.

## Próxima entrega

v0.4.0.0 — Tag Engine.
