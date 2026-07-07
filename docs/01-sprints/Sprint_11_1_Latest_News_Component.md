# Sprint 11.1 - M360 Latest News Component

Versao: 0.4.2.3
Status: pronto para workflow

## Objetivo

Recuperar o bloco Ultimas Noticias como componente reutilizavel do M360 Core.

## Escopo

- Componente M360_Latest_News_Component.
- Shortcode m360_latest_news.
- Funcao PHP m360_latest_news().
- Consulta por posts recentes publicada por idioma atual.
- Suporte a Polylang quando disponivel.
- Cache por transient.
- CSS isolado m360-latest-news.css.
- Layout lista com destaque no primeiro item.
- Opcoes para imagem, categoria, data, limite e layout compacto.

## Arquivos adicionados

- plugin/includes/latest-news/class-m360-latest-news-component.php
- plugin/assets/css/m360-latest-news.css

## Arquivos atualizados

- plugin/includes/class-m360-core.php
- plugin/m360-core.php
- VERSION.md

## Uso

Shortcode basico:

[m360_latest_news]

Shortcode com parametros:

[m360_latest_news limit="5" layout="compact" show_image="false"]

PHP:

m360_latest_news(['limit' => 6]);

## Criterios de aceite

- Plugin ativa sem erro fatal.
- Shortcode renderiza ultimas noticias em PT-BR.
- Shortcode renderiza Latest News em EN-US.
- Home PT e EN permanecem estaveis.
- Search, Author, Category e Tag sem regressao.
- Sem impacto em Home Editorial, Bolao e Semantic Relations.

## Proxima entrega

v0.4.3.0 - Taxonomy Intelligence Foundation.
