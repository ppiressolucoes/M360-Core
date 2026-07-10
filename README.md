# M360 Core

Framework oficial de interface, renderização e publicidade do Projeto Mengão 360 / DW Esportivo.

## Estado atual

- Arquitetura vigente: `M360 Platform Architecture v2.2`.
- Última release publicada em produção: `v0.4.4.0 — M360 AdSense Ready`.
- Baseline estável: `M360 Core v0.4.4.5`.
- Próxima linha evolutiva: `v0.5.x — Plataforma Comercial M360`.

## Componentes consolidados

- M360 Navigation e Dynamic Views.
- M360 Universal Slot Renderer.
- M360 Ad Slot Component.
- M360 Ads Inventory Library e Inventory Seeder.
- M360 Ads Context, Inline e Archive Engines.
- APIs e shortcodes publicitários compatíveis com PT-BR e EN-US.

## Diretriz para v0.5.x

Toda evolução comercial deve consumir o `M360 Universal Slot Renderer`. Nenhuma nova integração publicitária deve renderizar diretamente no tema, Elementor ou templates externos.

## Fluxo de desenvolvimento

```text
Issue → Branch → Pull Request → Release → ZIP → WordPress → Homologação
```

Nenhuma alteração estrutural deve chegar à produção sem checklist, evidências de homologação, documentação atualizada e rollback definido.
