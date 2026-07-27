# Sprint v0.7.4.0 — Portable Deployment Hardening

Status: implementada; aguardando homologação WordPress.
Data: 24/07/2026.

## Objetivo

Transformar o M360 Core homologado no Mengão 360 em um pacote seguro para instalação paralela em outro WordPress, sem saída pública ou ownership implícito. O Portal Energia Limpa é o primeiro piloto planejado, com prioridade para Internacionalização e Content Discovery & SEO.

## Entregas

- política persistida `legacy-compatible` ou `portable-safe`;
- migração compatível para instalações M360 existentes;
- novas instalações em `portable-safe`;
- gates individuais para templates públicos, Ads, Ads automático, Newsletter e Consent;
- Site Profile schema 2 com runtime exportável;
- importação retrocompatível do Site Profile schema 1;
- schemas de Ads instalados sem seed esportivo em novos portais;
- defaults genéricos de Newsletter e lista MailPoet sem ID presumido;
- diagnóstico administrativo para política, Polylang e MailPoet;
- documentação de implantação paralela, rollback e limites de ownership.

## Comportamento por ambiente

### Instalação existente

- modo inicial: `legacy-compatible`;
- gates públicos preservados;
- opções, campanhas, criativos e integrações existentes preservados;
- nenhuma desativação automática.

### Instalação nova

- modo inicial: `portable-safe`;
- módulos Editorial e Discovery desativados;
- templates do tema preservados;
- nenhuma campanha esportiva criada;
- nenhum anúncio inserido automaticamente;
- nenhum endpoint ou cron de Newsletter registrado;
- nenhuma interface pública de cookies renderizada.

## Sequência do piloto PEL

1. instalar em staging e confirmar `portable-safe`;
2. criar Site Profile `portal-energia-limpa / clean-energy`;
3. inventariar idiomas, post types, taxonomias, plugin SEO, AdSense, MailPoet e CMP;
4. ativar Discovery em shadow e executar backfill;
5. validar locale e comparação amostral;
6. habilitar renderer por canário;
7. liberar automatic somente após aceite;
8. ativar componentes editoriais selecionados em preview;
9. manter Ads, Newsletter e Consent atuais fora do ownership do Core na primeira onda.

## Critérios de aceite

- atualização do Mengão 360 mantém o front-end homologado;
- instalação limpa não altera busca ou arquivos do tema;
- instalação limpa não cria Mega Bolão, Flamengo, comunidade ou URLs de upload do Mengão;
- instalação limpa não agenda cron da Newsletter;
- Site Profile exportado contém política e gates, sem dados operacionais;
- Polylang e MailPoet são apenas detectados; não são configurados automaticamente;
- rollback imediato por gate e por módulo.

## Fora do escopo

- deploy no PEL;
- alteração dos slots AdSense aprovados;
- migração de assinantes MailPoet;
- substituição da CMP;
- takeover do tema;
- importação de conteúdo ou snapshots entre portais.
