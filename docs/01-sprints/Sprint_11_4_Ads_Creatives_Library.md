# Sprint 11.4 - M360 Ads Creatives Library

Versao: 0.4.2.6
Status: pronto para workflow

## Objetivo

Complementar o M360 Ads Manager com biblioteca propria de criativos, separando ativos publicitarios das imagens editoriais do WordPress.

## Escopo entregue

- Nova tabela `wp_m360_ad_creatives` via dbDelta.
- Schema Ads atualizado para `0.4.2.6`.
- Diretorio dedicado `/uploads/m360-ads/` criado automaticamente.
- Menu administrativo `Criativos`.
- Tela de listagem de criativos.
- Formulario de criacao e edicao de criativos.
- Exclusao de criativos.
- Associacao de criativo a campanha.
- Renderizador de slot passa a priorizar criativo ativo antes do legado `image_url` da campanha.

## Arquivos adicionados

- plugin/includes/ads/class-m360-ads-creatives-admin.php

## Arquivos atualizados

- plugin/includes/ads/class-m360-ads-db.php
- plugin/includes/ads/class-m360-ad-slot-component.php
- plugin/includes/class-m360-core.php
- plugin/m360-core.php
- VERSION.md

## Fluxo de uso

1. Acessar M360 Ads > Campanhas.
2. Criar ou ativar uma campanha.
3. Acessar M360 Ads > Criativos.
4. Criar criativo vinculado a campanha.
5. Ativar o criativo.
6. Vincular campanha a um slot.
7. Renderizar com shortcode:

[m360_ad_slot id="article-top"]

## Criterios de aceite

- Plugin atualiza sem erro fatal.
- Tabela de criativos e criada automaticamente.
- Diretório `/uploads/m360-ads/` e criado automaticamente.
- Menu Criativos aparece no admin.
- Criativo pode ser criado, editado e excluido.
- Criativo ativo vinculado a campanha e renderizado no slot.
- Fallback legado de campanha continua funcionando.
- Home PT/EN, Search, Author, Category, Tag e Latest News sem regressao.

## Proxima entrega

v0.4.3.0 - Taxonomy Intelligence Foundation.
