# Sprint 11.3 - M360 Ads Manager

Versao: 0.4.2.5
Status: pronto para workflow

## Objetivo

Entregar o primeiro painel administrativo do M360 Ads Manager para gestao de slots e campanhas sem acesso manual ao banco.

## Escopo entregue

- Menu administrativo M360 Ads.
- Dashboard com metricas basicas.
- Tela de Slots.
- Tela de Campanhas.
- Formulario de criacao e edicao de campanha.
- Exclusao de campanhas.
- Vinculo simples de campanha a slot.
- CSS administrativo isolado.
- Segurança com manage_options e nonces.

## Arquivos adicionados

- plugin/includes/ads/class-m360-ads-admin.php
- plugin/assets/css/m360-ads-admin.css

## Arquivos atualizados

- plugin/includes/class-m360-core.php
- plugin/m360-core.php
- VERSION.md

## Fluxo de uso

1. Acessar WordPress Admin.
2. Abrir M360 Ads.
3. Criar campanha em Nova Campanha.
4. Acessar Slots.
5. Vincular campanha a um slot.
6. Usar shortcode no front-end:

[m360_ad_slot id="article-top"]

## Criterios de aceite

- Menu M360 Ads aparece no admin.
- Dashboard carrega sem erro.
- Slots aparecem com shortcodes.
- Campanha pode ser criada.
- Campanha pode ser vinculada ao slot.
- Shortcode renderiza a campanha vinculada.
- Home PT e EN permanecem estaveis.
- Search, Author, Category, Tag e Latest News sem regressao.

## Proxima entrega

v0.4.3.0 - Taxonomy Intelligence Foundation.
