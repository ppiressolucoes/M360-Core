# M360 Core Version Manifest

## Baseline estável

```text
0.5.4.3
```

## Estado operacional

- Última release publicada em produção: `v0.4.4.0 — M360 AdSense Ready`.
- Baseline evolutiva de código: `v0.5.4.3 — Current Post Exclusion`.
- Arquitetura vigente: `M360 Platform Architecture v2.2`.
- Próxima linha: `v0.5.x — Plataforma Comercial M360`.

## Ambiente alvo

- WordPress 6.8+
- PHP 8+
- Elementor
- Polylang
- Yoast SEO
- LiteSpeed Cache

## Linha v0.4 consolidada

- `0.4.4.0`: M360 AdSense Ready publicado em produção.
- `0.4.4.1`: Inventory Library e Inventory Seeder.
- `0.4.4.2`: Context Renderer.
- `0.4.4.3`: Inline Ads Engine.
- `0.4.4.4`: Archive Ads Engine homologado em PT-BR e EN-US.
- `0.4.4.5`: Universal Slot Renderer e API única de renderização.
- `0.4.4.6`: correção e estabilização do CRUD de campanhas do M360 Ads Manager.

## Linha v0.5 — Plataforma Comercial

- `0.5.0`: gestão visual de slots com filtros, agrupamentos, estados e salvamento único.
- `0.5.1`: auditoria de cobertura, elegibilidade e recolhimento de slots vazios.
- `0.5.2`: componente individual e multilíngue de informações do post.
- `0.5.2.1`: recorte circular robusto do avatar e invalidação do cache dos assets.
- `0.5.2.2`: arquivo multilíngue próprio para navegação por dia, mês e ano.
- `0.5.3`: formulário de busca reutilizável, acessível e multilíngue.
- `0.5.3.1`: refinamento visual do hero com botão incorporado e melhor proporção no cabeçalho.
- `0.5.3.2`: correção da largura do widget de busca no contêiner Elementor PT-BR.
- `0.5.3.3`: Search Hero minimalista com apenas o campo e o botão incorporado.
- `0.5.4`: orquestração do cabeçalho entre campanha, AdSense, busca e recolhimento.
- `0.5.4.1`: variante sidebar do componente de últimas notícias, com controle explícito da inserção de publicidade.
- `0.5.4.2`: destaque editorial do primeiro item, títulos regulares na lista e paginação opcional.
- `0.5.4.3`: exclusão automática do artigo aberto das listas de últimas notícias.

## Componente Últimas Notícias

Uso atual, preservado:

```text
[m360_latest_news]
```

Uso recomendado em sidebar sem anúncio interno:

```text
[m360_latest_news layout="sidebar" show_ads="false"]
[m360_latest_news layout="list" pagination="true"]
```

## Componente de entrega do cabeçalho

```text
[m360_header_orchestrator]
[m360_header_orchestrator fallback="collapse"]
```

## Componente de busca

```text
[m360_search_form variant="hero"]
[m360_search_form variant="header"]
[m360_search_form variant="compact"]
```

## Componente Post Info

Uso padrão:

```text
[m360_post_info]
```

Exemplo sem avatar e sem horário:

```text
[m360_post_info avatar="false" time="false"]
```

## API oficial

```php
echo m360_render_ad_slot('header-top');
```

Compatibilidade preservada:

```text
m360_ads_render_slot()
m360_ad_slot()
[m360_ad_slot id="header-top"]
[m360_ads_slot id="header-top"]
```

## Fonte do pacote

O ZIP instalável deve ser gerado a partir de `plugin/`.

```text
branch homologada → GitHub Actions → ZIP → WordPress → validação → main
```
