# Sprint 11.2 - M360 Ads Slots Foundation

Versao: 0.4.2.4
Status: pronto para workflow

## Objetivo

Entregar a primeira versao funcional do M360 Ads Slots Foundation com estrutura de banco, seed de slots, shortcode e API PHP.

## Escopo entregue

- Criacao automatica das tabelas via dbDelta.
- Seed automatico dos slots padrao.
- Controle de schema em option m360_ads_db_version.
- Shortcode m360_ad_slot.
- Funcao PHP m360_ad_slot().
- Renderizador de campanha ativa por prioridade.
- Suporte inicial a tipos image, html, adsense, gam, house, affiliate e sponsor.
- CSS isolado m360-ads.css.

## Tabelas criadas

- wp_m360_ad_slots
- wp_m360_ad_campaigns
- wp_m360_ad_slot_campaigns

O prefixo real segue o prefixo configurado no WordPress.

## Uso

Shortcode:

[m360_ad_slot id="article-top"]

PHP:

m360_ad_slot('article-top');

Fallback:

[m360_ad_slot id="article-top" fallback="Espaco publicitario"]

## Arquivos adicionados

- plugin/includes/ads/class-m360-ads-db.php
- plugin/includes/ads/class-m360-ad-slot-component.php
- plugin/assets/css/m360-ads.css

## Arquivos atualizados

- plugin/includes/class-m360-core.php
- plugin/m360-core.php
- VERSION.md

## Criterios de aceite

- Plugin atualiza sem erro fatal.
- Tabelas sao criadas automaticamente.
- Slots padrao sao cadastrados automaticamente.
- Shortcode vazio nao quebra layout quando nao houver campanha.
- Fallback renderiza quando informado.
- Home PT e EN permanecem estaveis.
- Search, Author, Category, Tag e Latest News sem regressao.

## Proxima etapa

v0.4.3.0 - Taxonomy Intelligence Foundation.
