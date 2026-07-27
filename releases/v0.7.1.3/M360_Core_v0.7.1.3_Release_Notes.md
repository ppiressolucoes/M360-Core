# M360 Core v0.7.1.3 - EN-US Editorial Cutover Readiness

Status: candidato a homologacao.

## Correcao funcional

- adiciona `featured_tag` ao shortcode `m360_editorial_newsroom`;
- permite usar a tag legada `featured-en` como fonte da noticia principal;
- preserva categoria de destaque como alternativa;
- nao altera os shortcodes legados nem desativa o precursor.

## Inventario confirmado da Home EN-US

1. ticker `Latest`;
2. hero com tag `featured-en`;
3. Latest News;
4. Brazilian Team;
5. Flamengo;
6. Transfers;
7. Lineups & Injuries.

## Primeiro bloco de cutover

```text
[m360_editorial_ticker lang="en" label="Latest" limit="8"]
[m360_editorial_newsroom title="Top Stories" lang="en" featured_tag="featured-en" card_categories="brazilian-team,flamengo-en,transfers,lineups,injuries" international_category="international" cards="4"]
```

As secoes restantes podem ser migradas individualmente para `m360_editorial_section` depois do aceite do Newsroom.

## Rollback

Restaurar os shortcodes legados na pagina. O Home Editorial 0.1.2 deve permanecer ativo durante todo o cutover.
