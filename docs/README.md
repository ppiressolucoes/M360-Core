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
- M360 Core v0.6.5.4 como baseline oficial homologada.
- v0.6.0 — Privacy & Consent Foundation incorporada à baseline.
- v0.6.1 a v0.6.5.4 — Newsletter Foundation, Operations, Configuration, Delivery Readiness e Placement & UX consolidadas.
- ADR-0008 — evolução planejada para M360 Publisher Platform modular.
- v0.7.0 — Publisher Platform Foundation homologada em produção em 20/07/2026.
- Portal Energia Limpa — PEL definido como segunda implementação progressiva.

Documentos históricos e snapshots anteriores permanecem preservados, mas não substituem esta baseline.

## Regra de manutenção

Toda nova sprint ou release do M360 Core deve atualizar:

1. `CHANGELOG.md`;
2. `ROADMAP.md` quando houver mudança de prioridade;
3. documento de sprint em `docs/01-sprints/`;
4. ADR em `docs/05-adr/` quando houver decisão estrutural;
5. release notes em `docs/03-releases/`.
