# M360 Documentation Index v1

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Função: índice de referência documental da plataforma.

## 1. Objetivo

Este índice organiza os documentos mestres, documentos de módulo, sprints, releases, operações e decisões arquiteturais do M360 Core.

A partir da Sprint `v0.4.4.x — M360 AdSense Ready / Inventory Engine`, a decisão ADR-0007 passa a orientar a evolução do M360 Core como camada oficial de interface da Plataforma Mengão 360.

## 2. Documentos mestres da plataforma

| Documento | Caminho | Função |
|---|---|---|
| M360 Platform Architecture | `docs/00-platform/M360_Platform_Architecture_v2.md` | Documento mestre da arquitetura da plataforma |
| M360 Infrastructure Architecture | `docs/00-platform/M360_Infrastructure_Architecture_v1.md` | Referência da infraestrutura e posicionamento do M360 Core |
| M360 Ads Manager Architecture | `docs/00-platform/M360_Arquitetura_Ads_Manager_v1.md` | Arquitetura do subsistema M360 Ads Manager |
| M360 Inventory Library | `docs/00-platform/M360_Inventory_Library_v1.md` | Biblioteca oficial de inventário comercial e slots publicitários |

## 3. Architecture Decision Records

| ADR | Caminho | Decisão |
|---|---|---|
| ADR-0007 — M360 Core Interface Architecture | `docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md` | Consolida o M360 Core como camada oficial de interface da plataforma |

## 4. Documentos de módulo

| Documento | Caminho | Função |
|---|---|---|
| M360 Advertising Plugin Guide | `docs/01-modules/M360_Advertising_Plugin_Guide_v1.md` | Guia operacional do módulo M360 Advertising |
| M360 Ads Inline Engine | `docs/01-modules/M360_Ads_Inline_Engine_v1.md` | Guia técnico da inserção automática de anúncios inline em artigos |
| M360 Ads Archive Engine | `docs/01-modules/M360_Ads_Archive_Engine_v1.md` | Publicidade nas listagens controladas pelo M360 Core |
| M360 Universal Slot Renderer | `docs/01-modules/M360_Universal_Slot_Renderer_v1.md` | Pipeline único homologado na baseline v0.4.4.5 |

## 5. Sprints

| Sprint | Caminho | Função |
|---|---|---|
| Sprint v0.4.4.0 — M360 AdSense Ready | `docs/02-sprints/Sprint_v0.4.4.0_M360_AdSense_Ready.md` | Planejamento, aceite e entregas da preparação AdSense Ready / Inventory Engine |

## 6. Releases

| Documento | Caminho | Função |
|---|---|---|
| M360 Release History | `docs/03-releases/M360_Release_History_v2.md` | Histórico oficial de releases e marcos arquiteturais |
| M360 Core v0.4.4.x Release Checklist | `releases/v0.4.4.0/M360_Core_v0.4.4.0_Release_Checklist.md` | Checklist operacional da release v0.4.4.x |
| M360 Core v0.4.4.5 Release Notes | `releases/v0.4.4.5/M360_Core_v0.4.4.5_Release_Notes.md` | Baseline estável e encerramento da linha AdSense Ready |

### Baseline canônico

```text
Produção: v0.4.4.0 — M360 AdSense Ready
Baseline estável: v0.4.4.5 — M360 Universal Slot Renderer
Arquitetura: M360 Platform Architecture v2.2
Próxima linha: v0.5.x — Plataforma Comercial M360
```

## 7. Operações

| Documento | Caminho | Função |
|---|---|---|
| M360 Core Plugin Publication Workflow | `docs/04-operations/M360_Core_Plugin_Publication_Workflow_v1.md` | Workflow de publicação, build ZIP e rollback |

## 8. Workflows GitHub Actions

| Workflow | Caminho | Função |
|---|---|---|
| Build M360 Core Plugin ZIP | `.github/workflows/build-m360-core-plugin-zip.yml` | Gera o ZIP completo e instalável do plugin |
| Build M360 Ads Inventory Library | `.github/workflows/build-m360-ads-inventory-library.yml` | Gera artifact modular da Inventory Library |
| Build M360 Ads Inline Engine | `.github/workflows/build-m360-ads-inline-engine.yml` | Gera artifact modular do Inline Engine |

## 9. Referência arquitetural da Inventory Library

A **M360 Inventory Library** é o documento oficial para qualquer evolução que envolva slots comerciais, incluindo:

- novos slots em artigos;
- anúncios inline;
- anúncios em resultados de busca;
- anúncios em categorias;
- anúncios em tags;
- anúncios em páginas de autor;
- anúncios em últimas notícias;
- anúncios em widgets esportivos;
- anúncios da futura comunidade;
- AdSense;
- Google Ad Manager;
- House Ads;
- Affiliate;
- Sponsor;
- Marketplace Comercial M360.

## 10. Regra de governança

Toda evolução estrutural deverá atualizar este índice quando criar ou promover um documento para referência oficial.

Nenhum documento mestre deve ficar isolado do índice documental do projeto.

Toda sprint visual, comercial ou de interface deve verificar aderência ao ADR-0007 antes da codificação.
