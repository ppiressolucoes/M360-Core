# M360 Documentation Index v1

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Função: índice de referência documental da plataforma.

## 1. Objetivo

Este índice organiza os documentos mestres, documentos de módulo, sprints, releases, operações e decisões arquiteturais do M360 Core.

A partir da Sprint `v0.4.4.x — M360 AdSense Ready / Inventory Engine`, a decisão ADR-0007 passa a orientar a evolução do M360 Core como camada oficial de interface da Plataforma Mengão 360.

A partir da Sprint `v0.6.1 — M360 Editorial Source Connector Pilot`, a decisão ADR-0008 passa a orientar a camada de aquisição técnica de fontes editoriais externas.

## 2. Documentos mestres da plataforma

| Documento | Caminho | Função |
|---|---|---|
| M360 Platform Architecture | `docs/00-platform/M360_Platform_Architecture_v2.md` | Documento mestre da arquitetura da plataforma |
| M360 Infrastructure Architecture | `docs/00-platform/M360_Infrastructure_Architecture_v1.md` | Referência da infraestrutura e posicionamento do M360 Core |
| M360 Ads Manager Architecture | `docs/00-platform/M360_Arquitetura_Ads_Manager_v1.md` | Arquitetura do subsistema M360 Ads Manager |
| M360 Inventory Library | `docs/00-platform/M360_Inventory_Library_v1.md` | Biblioteca oficial de inventário comercial e slots publicitários |
| Ecossistema de Plugins M360 | `docs/00-platform/M360_Plugin_Ecosystem_v1.md` | Responsabilidades e limites de Bolão, Home Editorial e Semantic Relations |

## 3. Architecture Decision Records

| ADR | Caminho | Decisão |
|---|---|---|
| ADR-0007 — M360 Core Interface Architecture | `docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md` | Consolida o M360 Core como camada oficial de interface da plataforma |
| ADR-0008 — M360 Editorial Source Connector | `docs/00-platform/ADR-0008_M360_Editorial_Source_Connector.md` | Institui o conector único, orientado a dados, para descoberta e coleta bruta de fontes editoriais |

## 4. Documentos de módulo

| Documento | Caminho | Função |
|---|---|---|
| M360 Advertising Plugin Guide | `docs/01-modules/M360_Advertising_Plugin_Guide_v1.md` | Guia operacional do módulo M360 Advertising |
| M360 Ads Inline Engine | `docs/01-modules/M360_Ads_Inline_Engine_v1.md` | Guia técnico da inserção automática de anúncios inline em artigos |
| M360 Ads Archive Engine | `docs/01-modules/M360_Ads_Archive_Engine_v1.md` | Publicidade nas listagens controladas pelo M360 Core |
| M360 Universal Slot Renderer | `docs/01-modules/M360_Universal_Slot_Renderer_v1.md` | Pipeline único homologado na baseline v0.4.4.5 |
| M360 Editorial Source Connector | `docs/01-modules/M360_Editorial_Source_Connector_v1.md` | Especificação normativa do conector de fontes editoriais, API, segurança, dados e evolução |

## 5. Sprints

| Sprint | Caminho | Função |
|---|---|---|
| Sprint v0.4.4.0 — M360 AdSense Ready | `docs/02-sprints/Sprint_v0.4.4.0_M360_AdSense_Ready.md` | Planejamento, aceite e entregas da preparação AdSense Ready / Inventory Engine |
| Sprint v0.5.5 — Breadcrumb Navigation UX | `docs/01-sprints/Sprint_v0.5.5_Breadcrumb_Navigation_UX.md` | Hierarquia, internacionalização, acessibilidade, responsividade e schema do breadcrumb |
| Sprint v0.6.0 — Privacy & Consent Foundation | `docs/01-sprints/Sprint_v0.6.0_M360_Privacy_Consent_Foundation.md` | Contrato independente, Consent Mode v2, central e integração CMP |
| Sprint v0.6.1 — Editorial Source Connector Pilot | `docs/01-sprints/Sprint_v0.6.1_M360_Editorial_Source_Connector_Pilot.md` | Piloto funcional, caminho feliz NETFLA, segurança mínima e etapas evolutivas |

## 6. Releases

| Documento | Caminho | Função |
|---|---|---|
| M360 Release History | `docs/03-releases/M360_Release_History_v2.md` | Histórico oficial de releases e marcos arquiteturais |
| M360 Core v0.4.4.x Release Checklist | `releases/v0.4.4.0/M360_Core_v0.4.4.0_Release_Checklist.md` | Checklist operacional da release v0.4.4.x |
| M360 Core v0.4.4.5 Release Notes | `releases/v0.4.4.5/M360_Core_v0.4.4.5_Release_Notes.md` | Baseline estável e encerramento da linha AdSense Ready |
| M360 Core v0.5.5 Release Notes | `releases/v0.5.5/M360_Core_v0.5.5_Release_Notes.md` | Breadcrumb Navigation UX e preparação da linha v0.6.0 |
| M360 Core v0.6.0 Release Notes | `releases/v0.6.0/M360_Core_v0.6.0_Release_Notes.md` | Privacy & Consent Foundation |
| M360 Core v0.6.0.1 Release Notes | `releases/v0.6.0.1/M360_Core_v0.6.0.1_Release_Notes.md` | Consent Frontend Initialization Hotfix |
| M360 Core v0.6.0.2 Release Notes | `releases/v0.6.0.2/M360_Core_v0.6.0.2_Release_Notes.md` | Consent UI Contrast & Layering Hotfix |
| M360 Core v0.6.0.3 Release Notes | `releases/v0.6.0.3/M360_Core_v0.6.0.3_Release_Notes.md` | Proporção do modal e posição do botão permanente |
| M360 Core v0.6.0.4 Release Notes | `releases/v0.6.0.4/M360_Core_v0.6.0.4_Release_Notes.md` | Consolidação homologada da Privacy & Consent Foundation |

### Baseline canônico

```text
Release homologada: v0.6.0.4 — M360 Privacy & Consent Foundation
Tag canônica: v0.6.0.4
Arquitetura: M360 Platform Architecture v2.2
Baseline incorporada: v0.5.5.1 — Breadcrumb Mobile Overflow Hotfix
Linha consolidada: v0.6.0.4 — M360 Privacy & Consent Foundation
Próxima frente aprovada: v0.6.1 — M360 Editorial Source Connector Pilot
```

## 7. Operações

| Documento | Caminho | Função |
|---|---|---|
| M360 Core Plugin Publication Workflow | `docs/04-operations/M360_Core_Plugin_Publication_Workflow_v1.md` | Workflow de publicação, build ZIP e rollback |
| Governança do Repositório | `docs/04-operations/M360_Repository_Governance_v1.md` | Fonte da verdade, branches, tags e manutenção |

## Roadmap vigente

| Documento | Caminho | Função |
|---|---|---|
| Roadmap v0.5.2–v0.5.4 | `docs/04-roadmap/M360_Roadmap_v0.5.2_v0.5.4.md` | Post Info, Breadcrumbs, busca e cabeçalho |

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

## 10. Referência arquitetural do Editorial Source Connector

O **M360 Editorial Source Connector** é o documento oficial para evoluções que envolvam:

- descoberta de notícias em fontes externas;
- acesso intermediado a fontes restritas;
- RSS, Atom e JSON de aquisição editorial;
- coleta de conteúdo bruto;
- allowlist de hosts;
- cache e telemetria de fontes;
- contrato entre WordPress e n8n.

## 11. Regra de governança

Toda evolução estrutural deverá atualizar este índice quando criar ou promover um documento para referência oficial.

Nenhum documento mestre deve ficar isolado do índice documental do projeto.

Toda sprint visual, comercial ou de interface deve verificar aderência ao ADR-0007 antes da codificação.

Toda evolução de aquisição editorial deve verificar aderência ao ADR-0008 antes da codificação.
