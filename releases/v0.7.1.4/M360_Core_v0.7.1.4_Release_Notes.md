# M360 Core v0.7.1.4 - Editorial Newsroom Carousel

Status: candidato a homologacao.

## Entrega

- transforma a materia principal do Newsroom em carrossel;
- adiciona controles manuais anterior e proximo;
- adiciona rotacao automatica configuravel;
- pausa no hover, foco e aba em segundo plano;
- respeita `prefers-reduced-motion`;
- preserva os quatro cards laterais;
- reforca o contraste branco do titulo e resumo contra sobrescrita do tema.

## Shortcode EN-US

```text
[m360_editorial_newsroom title="Top Stories" lang="en" featured_tag="featured-en" featured_limit="5" card_categories="brazilian-team,flamengo-en,transfers,lineups,injuries" international_category="international" cards="4" interval="6500" autoplay="true"]
```

## Parametros do carrossel

- `featured_limit`: entre 1 e 10 slides;
- `interval`: minimo de 2500 ms;
- `autoplay`: `true` ou `false`;
- botoes permanecem ativos quando autoplay esta desligado.

## Rollback

Reinstalar a 0.7.1.3 ou restaurar o shortcode legado `m360_news_hero`. O Home Editorial 0.1.2 permanece ativo.
