# Sprint v0.7.0 — M360 Publisher Platform Foundation

Status: homologada em produção em 20/07/2026
Baseline: `M360 Core v0.6.5.4`
Decisão: `ADR-0008`

## Objetivo

Criar a fundação modular e independente de nicho que permitirá absorver gradualmente o M360 Home Editorial e o M360 Semantic Relations e utilizar o M360 como camada editorial evolutiva no Portal Energia Limpa — PEL.

## Pré-requisitos

- pacotes-fonte atuais dos dois plugins;
- inventário de hooks, shortcodes, opções, tabelas, cron, assets e dependências;
- baseline funcional e visual dos plugins em produção;
- plano de migração e rollback aprovado.

## Evidência da auditoria

Referência: `docs/02-architecture/M360_Plugin_Precursors_Static_Audit_v1.md`.

- Home Editorial identificado na versão funcional `0.1.2`;
- Semantic Relations identificado na versão funcional `0.9.0`;
- contratos públicos, persistência e acoplamentos inventariados;
- Home classificado como primeira absorção;
- Semantic Relations condicionado à extração do provider DW/futebol;
- dados operacionais de produção permanecem necessários antes do corte dos plugins antigos.

## Escopo

- registro e ciclo de vida de módulos;
- dependências e feature flags;
- schema e migração versionados por módulo;
- carregamento condicional de código e assets;
- permissões e configurações por módulo;
- diagnóstico e health check;
- contrato de `Site Profile`;
- exportação e importação segura de configurações;
- separação formal entre capacidades portáveis e domínios específicos;
- preservação integral da baseline `v0.6.5.4`.

## Critérios de aceite

- [x] módulos podem ser registrados, ativados e desativados individualmente;
- [x] dependências inválidas falham com mensagem administrativa segura;
- [x] cada módulo possui versão de schema e diagnóstico;
- [x] assets são carregados somente nos contextos necessários;
- [x] Site Profile não contém dados pessoais, segredos ou regras esportivas;
- [x] recursos atuais do Mengão 360 permanecem funcionais em produção;
- [x] nenhum código do Bolão ou DW Esportivo é incorporado;
- [x] Home Editorial e Semantic Relations continuam operacionais durante a fundação;
- [x] rollback para `v0.6.5.4` está documentado;
- [x] arquitetura está preparada para os perfis Mengão 360 e PEL.

## Evidências de homologação

- atualização em produção de `v0.6.5.4` para `v0.7.0` concluída;
- módulo `Publisher Platform Foundation` ativo, obrigatório e `healthy`;
- Site Profile `mengao360 / sports / pt-BR / en-US` salvo, exportado e reimportado;
- Home Editorial `0.1.2` e Semantic Relations `0.9.0` ativos e preservados;
- Newsletter com MailPoet, lista `#3`, WP-Cron e prontidão `7/7` preservados;
- Home e artigos PT-BR/EN-US sem erro crítico;
- notícias e tópicos relacionados renderizados antes da Newsletter;
- Ads, Consent e localização PT-BR/EN-US preservados;
- nenhum erro de console atribuído ao M360.

## Fora do escopo

- absorção funcional completa dos dois plugins;
- migração do PEL;
- remoção do tema ou Elementor;
- importação de conteúdo entre portais;
- DW Esportivo, Bolão, jogos ou competições;
- automação de publicação;
- mudança visual ampla.

## Entregas subsequentes

- `v0.7.1 — Editorial Layout & Home`;
- `v0.7.2 — Content Discovery & SEO`;
- `v0.7.3 — Portable Newsletter, Ads & Consent`;
- piloto progressivo no Portal Energia Limpa.
