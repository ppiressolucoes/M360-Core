# M360 Documentation Index v1

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Função: índice de referência documental da plataforma.

## 1. Objetivo

Este índice organiza os documentos mestres, documentos de módulo, sprints, releases, operações e decisões arquiteturais do M360 Core.

A decisão ADR-0007 orienta o M360 Core como camada oficial de interface. O ADR-0008 amplia essa direção e define a evolução para uma Publisher Platform modular, reutilizável e preparada para o Portal Energia Limpa — PEL.

## 2. Documentos mestres da plataforma

| Documento | Caminho | Função |
|---|---|---|
| M360 Platform Architecture | `docs/00-platform/M360_Platform_Architecture_v2.md` | Documento mestre da arquitetura da plataforma |
| M360 Infrastructure Architecture | `docs/00-platform/M360_Infrastructure_Architecture_v1.md` | Referência da infraestrutura e posicionamento do M360 Core |
| M360 Ads Manager Architecture | `docs/00-platform/M360_Arquitetura_Ads_Manager_v1.md` | Arquitetura do subsistema M360 Ads Manager |
| M360 Inventory Library | `docs/00-platform/M360_Inventory_Library_v1.md` | Biblioteca oficial de inventário comercial e slots publicitários |
| Ecossistema de Plugins M360 | `docs/00-platform/M360_Plugin_Ecosystem_v1.md` | Responsabilidades e limites de Bolão, Home Editorial e Semantic Relations |
| M360 Publisher Platform Strategy | `docs/00-platform/M360_Publisher_Platform_Strategy_v1.md` | Plano de absorção modular, portabilidade e adoção progressiva no PEL |

## 3. Architecture Decision Records

| ADR | Caminho | Decisão |
|---|---|---|
| ADR-0007 — M360 Core Interface Architecture | `docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md` | Consolida o M360 Core como camada oficial de interface da plataforma |
| ADR-0008 — M360 Publisher Platform Modularization | `docs/00-platform/ADR-0008_M360_Publisher_Platform_Modularization.md` | Define a absorção do Home Editorial e Semantic Relations e os limites do núcleo portável |

## 4. Documentos de módulo

| Documento | Caminho | Função |
|---|---|---|
| M360 Advertising Plugin Guide | `docs/01-modules/M360_Advertising_Plugin_Guide_v1.md` | Guia operacional do módulo M360 Advertising |
| M360 Ads Inline Engine | `docs/01-modules/M360_Ads_Inline_Engine_v1.md` | Guia técnico da inserção automática de anúncios inline em artigos |
| M360 Ads Archive Engine | `docs/01-modules/M360_Ads_Archive_Engine_v1.md` | Publicidade nas listagens controladas pelo M360 Core |
| M360 Universal Slot Renderer | `docs/01-modules/M360_Universal_Slot_Renderer_v1.md` | Pipeline único homologado na baseline v0.4.4.5 |
| M360 Newsletter Foundation | `docs/02-architecture/M360_Newsletter_Foundation_v0.6.1.md` | Arquitetura do consentimento, Double Opt-In, adaptador MailPoet e sincronização |
| Auditoria Estática dos Plugins Precursores | `docs/02-architecture/M360_Plugin_Precursors_Static_Audit_v1.md` | Inventário, portabilidade, riscos, migração e rollback do Home Editorial e Semantic Relations |

## 5. Sprints

| Sprint | Caminho | Função |
|---|---|---|
| Sprint v0.4.4.0 — M360 AdSense Ready | `docs/02-sprints/Sprint_v0.4.4.0_M360_AdSense_Ready.md` | Planejamento, aceite e entregas da preparação AdSense Ready / Inventory Engine |
| Sprint v0.5.5 — Breadcrumb Navigation UX | `docs/01-sprints/Sprint_v0.5.5_Breadcrumb_Navigation_UX.md` | Hierarquia, internacionalização, acessibilidade, responsividade e schema do breadcrumb |
| Sprint v0.6.0 — Privacy & Consent Foundation | `docs/01-sprints/Sprint_v0.6.0_M360_Privacy_Consent_Foundation.md` | Contrato independente, Consent Mode v2, central e integração CMP |
| Sprint v0.6.1 — Newsletter Foundation | `docs/01-sprints/Sprint_v0.6.1_M360_Newsletter_Foundation.md` | Fundação, consentimento, Double Opt-In e adaptador MailPoet |
| Sprint v0.6.2 — Newsletter Operations & Audit | `docs/01-sprints/Sprint_v0.6.2_Newsletter_Operations_Audit.md` | Sincronização, diagnóstico e auditoria operacional |
| Sprint v0.6.3 — Newsletter Configuration & Form Hardening | `docs/01-sprints/Sprint_v0.6.3_Newsletter_Configuration_Form_Hardening.md` | Configuração administrativa, proteção e acessibilidade do formulário |
| Sprint v0.6.4 — Newsletter Delivery Readiness | `docs/01-sprints/Sprint_v0.6.4_Newsletter_Delivery_Readiness.md` | Checklist operacional de entrega e evidências 7/7 |
| Sprint v0.6.5 — Newsletter Subscription Placement & UX | `docs/01-sprints/Sprint_v0.6.5_Newsletter_Subscription_Placement_UX.md` | Posicionamento, origens, localização e UX dos cards |
| Sprint v0.7.0 — M360 Publisher Platform Foundation | `docs/01-sprints/Sprint_v0.7.0_M360_Publisher_Platform_Foundation.md` | Kernel modular, Site Profile, diagnósticos e preparação da absorção dos plugins precursores |

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
| M360 Core v0.6.1.3 Release Notes | `releases/v0.6.1.3/M360_Core_v0.6.1.3_Release_Notes.md` | Consolidação da Newsletter Foundation |
| M360 Core v0.6.2 Release Notes | `releases/v0.6.2/M360_Core_v0.6.2_Release_Notes.md` | Newsletter Operations & Audit |
| M360 Core v0.6.3 Release Notes | `releases/v0.6.3/M360_Core_v0.6.3_Release_Notes.md` | Newsletter Configuration & Form Hardening |
| M360 Core v0.6.4 Release Notes | `releases/v0.6.4/M360_Core_v0.6.4_Release_Notes.md` | Newsletter Delivery Readiness |
| M360 Core v0.6.5.4 Release Notes | `releases/v0.6.5.4/M360_Core_v0.6.5.4_Release_Notes.md` | Baseline consolidada da Newsletter Subscription Placement & UX |
| M360 Core v0.7.0 Release Notes | `releases/v0.7.0/M360_Core_v0.7.0_Release_Notes.md` | Baseline homologada da Publisher Platform Foundation |

### Baseline canônico

```text
Release homologada: v0.7.0 — Publisher Platform Foundation
Tag canônica: v0.7.0
Arquitetura: M360 Platform Architecture v2.2 + ADR-0008
Baseline incorporada: v0.6.4 — Newsletter Delivery Readiness
Linha Newsletter incorporada: v0.6.5.4 — Newsletter Subscription Placement & UX
Linha consolidada: v0.7.0 — Publisher Platform Foundation
```

## 7. Operações

| Documento | Caminho | Função |
|---|---|---|
| M360 Core Plugin Publication Workflow | `docs/04-operations/M360_Core_Plugin_Publication_Workflow_v1.md` | Workflow de publicação, build ZIP e rollback |
| Governança do Repositório | `docs/04-operations/M360_Repository_Governance_v1.md` | Fonte da verdade, branches, tags e manutenção |
| M360 Newsletter — Runbook de Entrega Editorial | `docs/06-runbooks/M360_Newsletter_Editorial_Delivery_Runbook.md` | Operação diária do MailPoet, revisão, envio e pós-envio |

## Roadmap vigente

| Documento | Caminho | Função |
|---|---|---|
| Roadmap v0.5.2–v0.5.4 | `docs/04-roadmap/M360_Roadmap_v0.5.2_v0.5.4.md` | Post Info, Breadcrumbs, busca e cabeçalho |
| Backlog Priorizado e Sprints Futuras | `docs/04-roadmap/M360_Backlog_Roadmap_Sprints_Futuras_v1.md` | Prioridades vigentes e linha M360 Publisher Platform |

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
