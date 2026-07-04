# M360 Core

Framework oficial de interface do Projeto Mengão 360 / DW Esportivo.

## Objetivo

O M360 Core concentra os componentes próprios de navegação, páginas dinâmicas, renderização editorial, SEO estrutural e integração multilíngue do portal Mengão 360.

## Estado atual

Versão estável operacional: `v0.3.2`

Componentes validados:

- M360 Main Navigation
- M360 Section Navigation
- M360 Breadcrumb
- M360 Author Hub
- M360 Search Results
- M360 Layout Foundation

Próxima evolução planejada:

- `v0.3.3` — Radar News Engine + View Engine Consolidation

## Estrutura planejada

```text
m360-core/
├── plugin/
│   ├── m360-core.php
│   ├── assets/
│   ├── core/
│   ├── navigation/
│   ├── router/
│   ├── views/
│   ├── schema/
│   ├── seo/
│   └── languages/
├── docs/
├── releases/
├── tests/
├── CHANGELOG.md
└── ROADMAP.md
```

## Fluxo de desenvolvimento

```text
Issue → Branch → Pull Request → Release → ZIP → WordPress
```

## Branches

- `main`: versões estáveis.
- `develop`: desenvolvimento contínuo.
- `release/*`: validação de release.
- `hotfix/*`: correções urgentes.

## Padrão de versionamento

O projeto seguirá versionamento semântico adaptado ao ciclo do M360 Core:

- `v0.2.x`: Navigation + Dynamic Views Foundation.
- `v0.3.x`: Router, Search, Radar News e View Engine.
- `v0.4.x`: Layout Engine.
- `v1.0.0`: primeira versão estável oficial do M360 Core.

## Segurança operacional

Nenhuma alteração estrutural deve ser aplicada diretamente em produção sem:

1. issue registrada;
2. branch dedicada;
3. checklist de validação;
4. changelog atualizado;
5. rollback documentado.
