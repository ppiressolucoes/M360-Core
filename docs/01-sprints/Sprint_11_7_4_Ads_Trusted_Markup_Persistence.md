# Sprint 11.7.4 - M360 Ads Trusted Markup Persistence

Versao: 0.4.3.4
Status: pronto para workflow

## Objetivo

Corrigir o salvamento e a renderizacao de criativos HTML que usam `<style>` e `<script>`, evitando que o WordPress remova tags necessarias para pecas publicitarias controladas pelo M360 Ads Manager.

## Problema validado

No formulario Editar Criativo, ao salvar o campo HTML, tags `<script></script>` eram removidas por `wp_kses_post()`. Isso impedia o uso de pecas HTML completas, widgets externos e scripts publicitarios.

## Correcoes entregues

- Campo HTML passa por `wp_unslash()` antes de salvar.
- Campo Script passa por `wp_unslash()` antes de salvar.
- Markup de criativos gerenciado por administradores M360 Ads e preservado como trusted markup.
- Renderer passa a exibir markup armazenado sem KSES no front-end.
- Shortcodes internos continuam sendo executados via `do_shortcode()`.
- Tags `<style>` e `<script>` deixam de ser removidas no fluxo de criativos administrados.

## Arquivos alterados

- plugin/includes/ads/class-m360-ads-creatives-admin.php
- plugin/includes/ads/class-m360-ad-slot-component.php
- plugin/m360-core.php
- VERSION.md

## Criterios de aceite

- Salvar criativo HTML com `<style>` preserva o CSS.
- Salvar criativo HTML com `<script>` preserva o script.
- Renderizacao de `sidebar-community` exibe o widget, nao o codigo como texto.
- Renderizacao de `content-bottom` exibe o widget, nao o codigo como texto.
- Shortcodes de slots continuam funcionando.

## Observacao de seguranca

O fluxo e restrito a usuarios com capacidade `manage_options`, pois se trata de inventario publicitario administrado. A partir da Plataforma Comercial M360, recomenda-se adicionar papeis/capacidades especificas para operadores comerciais.
