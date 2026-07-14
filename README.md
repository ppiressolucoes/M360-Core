# M360 Core

Framework oficial de interface, renderização e publicidade do Projeto Mengão 360 / DW Esportivo.

## Estado atual

- Arquitetura vigente: `M360 Platform Architecture v2.2`.
- Release oficial homologada: `v0.5.1 — AdSense Approval Readiness`.
- Tag canônica: `v0.5.1`.
- Próxima entrega: `v0.5.2 — Multilingual Post Navigation`.

## Fonte única da verdade

Este repositório é a base oficial de código, arquitetura, decisões, releases, runbooks e roadmap do M360 Core. Para manutenção e novas evoluções, consulte nesta ordem:

1. tag da última release homologada;
2. `VERSION.md` e `CHANGELOG.md`;
3. `docs/M360_Documentation_Index_v1.md`;
4. arquitetura, ADRs, runbooks e release notes relacionados ao escopo.

A tag `v0.5.1` identifica a baseline imutável homologada. Novas mudanças devem ser entregues por branch e Pull Request, sem alterar tags existentes.

## Componentes consolidados

- M360 Navigation e Dynamic Views.
- M360 Universal Slot Renderer.
- M360 Ad Slot Component.
- M360 Ads Inventory Library e Inventory Seeder.
- M360 Ads Context, Inline e Archive Engines.
- APIs e shortcodes publicitários compatíveis com PT-BR e EN-US.
- Gestão visual de slots, diagnóstico de runtime e auditoria AdSense Readiness.

## Próximas evoluções

- `v0.5.2`: Post Info multilíngue e Breadcrumbs evoluídos.
- `v0.5.3`: M360 Search Experience.
- `v0.5.4`: orquestração Header Search & Ads.
- `v0.5.4.1`: modo sidebar do Últimas Notícias com publicidade configurável.

Consulte `docs/04-roadmap/M360_Roadmap_v0.5.2_v0.5.4.md`.

## Fluxo de desenvolvimento

```text
Issue → Branch → Pull Request → Release → ZIP → WordPress → Homologação
```

Nenhuma alteração estrutural deve chegar à produção sem checklist, evidências de homologação, documentação atualizada e rollback definido.
