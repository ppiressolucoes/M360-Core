# Sprint 0 — GitHub Build Foundation

Status: em implantação
Release operacional: preparação para v0.3.4-RC1

## Objetivo

Criar um fluxo simples e prático de implantação do M360 Core com GitHub como fonte única de verdade e GitHub Actions como gerador de ZIP do plugin.

## Entregas

- `VERSION.md` criado.
- Runbook de implantação via GitHub criado.
- Workflow `Build M360 Core Artifact` criado.
- Estrutura `plugin/` definida como raiz do pacote instalável.
- Fluxo main → artifact ZIP → WordPress documentado.

## Decisões

1. Não depender de criação manual de tags/releases no início.
2. Usar GitHub Actions como servidor de build.
3. Gerar ZIP a partir de `plugin/`.
4. Manter versionamento humano em `VERSION.md`.
5. Manter documentação e código na mesma sprint.

## Fluxo operacional

```text
Branch de sprint
  ↓
Pull Request
  ↓
Merge na main
  ↓
GitHub Actions
  ↓
Artifact ZIP
  ↓
Instalação no WordPress
```

## Critérios de aceite

- [x] Workflow com `workflow_dispatch`.
- [x] Workflow com push na `main`.
- [x] ZIP gerado a partir de `plugin/`.
- [x] Documentação de implantação criada.
- [x] Manifesto de versão criado.

## Próximo passo

A Sprint 10.4 — M360 View Engine deve usar esse fluxo para gerar a primeira build instalável da nova fase.
