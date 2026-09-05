# Documentação — M360 Core

Este diretório centraliza o histórico técnico, arquitetural e operacional do Projeto Mengão 360 relacionado ao M360 Core e ao ecossistema DW Esportivo.

## Estrutura

```text
docs/
├── 00-platform/
├── 01-sprints/
├── 02-modules/
├── 03-releases/
├── 04-roadmap/
├── 05-adr/
├── 06-runbooks/
└── 99-archive/
```

## Documentos migrados nesta primeira etapa

### Plataforma

- Documento Mestre da Plataforma v1.
- Inventário Técnico DW Esportivo.
- README dos anexos do projeto.

### Sprints e histórico

- Sprints concluídas v1.
- Sprints concluídas v1.2.
- Sprint Internacionalização PT-BR / EN-US.
- Sprint Observabilidade SEO Semântico.

### Roadmap

- Backlog e Roadmap de Sprints Futuras.
- Roadmap Comercial Mega Bolão 360.
- Plano de Internacionalização.

### Runbooks

- DW Esportivo — Transição para Mata-Mata.
- Índice Histórico DW Esportivo.

## Baseline documental vigente

- M360 Platform Architecture v2.2.
- M360 Release History v2.2.
- M360 Core v0.7.4.0.1 como release operacional homologada no WordPress.
- v0.6.0 — Privacy & Consent Foundation incorporada à baseline.
- v0.6.1 a v0.6.5.4 — Newsletter Foundation, Operations, Configuration, Delivery Readiness e Placement & UX consolidadas.
- ADR-0008 — evolução planejada para M360 Publisher Platform modular.
- v0.7.0 — Publisher Platform Foundation homologada em produção em 20/07/2026.
- v0.7.4.0.1 — transição `legacy-compatible` homologada no Mengão 360 em 27/07/2026.
- Portal Energia Limpa — PEL definido como segunda implementação progressiva.

Documentos históricos e snapshots anteriores permanecem preservados, mas não substituem esta baseline.

## Regra de manutenção

Toda nova sprint ou release do M360 Core deve atualizar:

1. `CHANGELOG.md`;
2. `ROADMAP.md` quando houver mudança de prioridade;
3. documento de sprint em `docs/01-sprints/`;
4. ADR em `docs/05-adr/` quando houver decisão estrutural;
5. release notes em `docs/03-releases/`.

## Enriquecimento Editorial com DW Esportivo — análise preliminar

Status: **aguardando validação do DW de produção**. Base de implementação aceita: Core 0.7.4.0.20, commit `0190d99`, referenciado pela PR #27; essa referência não substitui o estado de merge/releases documentado acima.

- [Sprint: escopo, base e critérios de aceite](01-sprints/Sprint_Enriquecimento_Editorial_DW_Esportivo.md).
- [Mapeamento DW: evidências, contrato proposto e pendências](02-architecture/M360_Enriquecimento_Editorial_DW_Mapeamento_v1.md).
- [Runbook e consultas de diagnóstico](06-runbooks/M360_Enriquecimento_Editorial_DW_Diagnostico.md).
- [Registro documental, sem release de software](03-releases/M360_Registro_Documental_Enriquecimento_DW_2026-09-04.md).
