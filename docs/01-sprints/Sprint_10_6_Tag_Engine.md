# Sprint 10.6 - Tag Engine

Status: pronto para execucao do workflow
Versao: 0.4.0.0
Fluxo: GitHub First

## Objetivo

Entregar as paginas dinamicas de tags PT-BR e EN-US pelo M360 Core.

## Escopo

- Tag Controller.
- Template proprio para tag.
- Hero de tag.
- Grid de artigos da tag.
- Breadcrumb completo.
- Paginacao.
- CSS dedicado.
- EN-US usando Elementor Header 4123 e Footer 3765.
- PT-BR preservando header e footer do tema.

## Arquivos adicionados

- plugin/includes/tag/class-m360-tag-controller.php
- plugin/templates/tag.php
- plugin/assets/css/tag.css

## Arquivos atualizados

- plugin/includes/class-m360-core.php
- plugin/includes/navigation/class-m360-navigation-shortcodes.php
- plugin/m360-core.php
- VERSION.md

## Criterios de aceite

- Pagina de tag PT-BR renderiza pelo M360 Core.
- Pagina de tag EN-US renderiza pelo M360 Core.
- Header e footer PT-BR permanecem estaveis.
- Header e footer EN-US usam templates Elementor 4123 e 3765.
- Hero da tag aparece corretamente.
- Grid de artigos da tag aparece corretamente.
- Paginacao funciona.
- Breadcrumb: Home > Tag > Nome.
- Search, Author e Category permanecem sem regressao.

## Encerramento esperado

Esta entrega fecha a Sprint 10.6 Dynamic View Engine com Search, Author, Category e Tag sob controle do M360 Core.
