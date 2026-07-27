# M360 Core v0.7.1.6 - Newsroom Proportions

Status: candidato a homologacao visual.

## Correcoes

- remove o titulo do Newsroom por padrao;
- adiciona `show_title="true"` para uso futuro opcional;
- substitui alturas em `rem` por dimensoes responsivas em pixels e viewport;
- equilibra o destaque principal com as duas linhas de cards;
- limita o componente a 1200px;
- amplia a area clicavel dos controles para 42px;
- mantem os controles discretos e aumenta a opacidade no hover ou foco;
- ajusta proporcoes especificas para tablet e mobile.

## Shortcode EN-US

```text
[m360_editorial_newsroom lang="en" featured_tag="featured-en" featured_limit="5" card_categories="brazilian-team,flamengo-en,transfers,lineups,injuries" international_category="international" cards="4" interval="6500" autoplay="true"]
```

O atributo `title` pode permanecer no conteudo sem ser exibido. Para reativa-lo explicitamente, usar `show_title="true"`.
