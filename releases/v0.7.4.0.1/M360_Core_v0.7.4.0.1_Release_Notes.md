# M360 Core v0.7.4.0.1 — Existing Installation Detection Hotfix

## Objetivo

Garantir a transição segura entre instalações históricas do M360 Core e instalações novas da Publisher Platform.

## Correção

A v0.7.4.0 dependia da opção `m360_core_version` para distinguir upgrade de instalação nova. O Mengão 360, cuja trajetória antecede essa opção, foi inicialmente classificado como `portable-safe`.

O hotfix substitui esse critério único por evidências persistentes:

- opções de ativação, Site Profile, módulos, Ads, Newsletter e Discovery;
- tabelas próprias de Ads, Newsletter e Discovery;
- Runtime Profile já existente, que sempre é preservado.

## Comportamento esperado

### Upgrade histórico sem Runtime Profile

- política inicial: `legacy-compatible`;
- capacidades públicas: habilitadas;
- diagnóstico: `historical-installation-evidence`;
- nenhuma alteração em módulos, conteúdo ou dados.

### Instalação realmente nova

- política inicial: `portable-safe`;
- capacidades públicas: desabilitadas;
- diagnóstico: `fresh-installation-no-evidence`;
- nenhum ownership público automático.

### Instalação com Runtime Profile

- modo e capacidades permanecem exatamente como salvos;
- diagnóstico: perfil existente preservado;
- o hotfix não tenta reclassificar a instalação.

## Escopo negativo

Esta versão não altera:

- Site Profile;
- estados dos módulos;
- schemas ou tabelas;
- Content Discovery, writer, backfill ou snapshots;
- inventário Ads;
- listas e assinantes MailPoet;
- consentimentos;
- frontend quando o perfil existente é preservado.

## Homologação concluída — Mengão 360

Data: 27/07/2026.

Evidências:

- M360 Core `0.7.4.0.1`;
- política `legacy-compatible`;
- origem `preserved-existing-profile`;
- capacidades públicas existentes preservadas;
- Publisher Platform Foundation `healthy`;
- Editorial Layout & Home `healthy`;
- Content Discovery & SEO `healthy`;
- nenhum schema, conteúdo, snapshot ou provider reconfigurado pelo hotfix.

A release fica homologada no ambiente precursor. O próximo gate é uma instalação limpa no staging do Portal Energia Limpa, que deve iniciar em `portable-safe` e sem capacidades públicas.

Rollback: reinstalar o pacote canônico anterior. Nenhuma migração de banco precisa ser revertida.
