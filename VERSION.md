# M360 Core Version Manifest

## Baseline estável

```text
0.5.1
```

## Estado operacional

- Última release publicada em produção: `v0.4.4.0 — M360 AdSense Ready`.
- Baseline evolutiva de código: `v0.5.1 — AdSense Approval Readiness`.
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
