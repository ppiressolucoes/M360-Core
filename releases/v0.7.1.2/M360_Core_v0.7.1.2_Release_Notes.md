# M360 Core v0.7.1.2 - Editorial Newsroom

Status: candidato a homologacao publica controlada.

## Entrega

- adiciona `[m360_editorial_newsroom]` e o alias `[m360_newsroom]`;
- composicao responsiva com uma materia principal e quatro cards laterais;
- categoria de destaque configuravel;
- categoria Internacional incluida por padrao com slug `internacional`;
- filtros de categorias, idioma, quantidade de cards e heading;
- badges de categoria, imagens, titulos e resumo da materia principal;
- modo `hybrid`: shortcodes publicos do Core ativos, contratos legados preservados no precursor.

## Exemplos

PT-BR:

```text
[m360_editorial_newsroom title="Destaques" lang="pt" featured_category="destaque" card_categories="flamengo,libertadores,brasileirao,mercado-da-bola" international_category="internacional" cards="4"]
```

EN-US:

```text
[m360_editorial_newsroom title="Top Stories" lang="en" featured_category="featured-en" card_categories="flamengo-en,libertadores-en,transfers,lineups" international_category="international" cards="4"]
```

## Cutover controlado

1. instalar a 0.7.1.2 mantendo Home Editorial 0.1.2 ativo;
2. confirmar modulo `healthy` em modo hibrido;
3. inserir o novo shortcode em pagina de homologacao;
4. validar conteudo, idioma, responsividade, imagens e links;
5. substituir o bloco precursor somente na pagina aprovada;
6. manter rollback restaurando o shortcode anterior.

Os shortcodes `m360_news_ticker`, `m360_news_hero` e `m360_news_section` continuam pertencendo ao precursor.
