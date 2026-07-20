# M360 Core

Framework oficial de interface, renderização e publicidade do Projeto Mengão 360 / DW Esportivo.

## Estado atual

- Arquitetura vigente: `M360 Platform Architecture v2.2`.
- Release oficial homologada: `v0.7.0 — Publisher Platform Foundation`.
- Tag canônica: `v0.7.0`.
- Linha Newsletter consolidada após homologação funcional em PT-BR e EN-US, com prontidão de entrega `7/7`.
- Linha estratégica vigente: `v0.7.x — M360 Publisher Platform`.

## Fonte única da verdade

Este repositório é a base oficial de código, arquitetura, decisões, releases, runbooks e roadmap do M360 Core. Para manutenção e novas evoluções, consulte nesta ordem:

1. tag da última release homologada;
2. `VERSION.md` e `CHANGELOG.md`;
3. `docs/M360_Documentation_Index_v1.md`;
4. arquitetura, ADRs, runbooks e release notes relacionados ao escopo.

A tag `v0.7.0` identifica a baseline imutável homologada. Novas mudanças devem ser entregues por branch e Pull Request, sem alterar tags existentes.

## Componentes consolidados

- M360 Navigation e Dynamic Views.
- M360 Universal Slot Renderer.
- M360 Ad Slot Component.
- M360 Ads Inventory Library e Inventory Seeder.
- M360 Ads Context, Inline e Archive Engines.
- APIs e shortcodes publicitários compatíveis com PT-BR e EN-US.
- Gestão visual de slots, diagnóstico de runtime e auditoria AdSense Readiness.
- Privacy & Consent Foundation com contrato independente de CMP.
- Newsletter M360 com consentimento independente, Double Opt-In, MailPoet, sincronização, auditoria, proteção, prontidão e componentes PT-BR/EN-US.
- Publisher Platform Foundation com kernel modular, Site Profile portável, diagnóstico e preservação dos plugins precursores.

## Próximas evoluções

- `v0.7.1`: absorção generalizada do Home Editorial;
- `v0.7.2`: absorção generalizada do Semantic Relations e SEO Technical Readiness;
- `v0.7.3`: portabilidade de Newsletter, Ads e Consent;
- piloto progressivo no Portal Energia Limpa — PEL;
- seleção de CMP certificada, conforme estratégia de monetização e regiões atendidas;
- Mega Bolão 360 MVP Comercial;
- Painel Operacional DW;
- SEO programático e expansão de competições.

Consulte `ROADMAP.md` e `docs/04-roadmap/M360_Backlog_Roadmap_Sprints_Futuras_v1.md`.

## Fluxo de desenvolvimento

```text
Issue → Branch → Pull Request → Release → ZIP → WordPress → Homologação
```

Nenhuma alteração estrutural deve chegar à produção sem checklist, evidências de homologação, documentação atualizada e rollback definido.
