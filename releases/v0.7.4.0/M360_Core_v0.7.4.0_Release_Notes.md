# M360 Core v0.7.4.0 — Portable Deployment Hardening

Status: candidata à homologação.

## Objetivo

Permitir que o mesmo M360 Core evolua múltiplos portais sem transportar ownership, regras ou dados do ambiente precursor.

## Principais mudanças

- novas instalações iniciam em `portable-safe`;
- upgrades existentes migram para `legacy-compatible`;
- templates públicos, Ads, Ads automático, Newsletter e Consent recebem gates independentes;
- Site Profile evolui para schema 2 e passa a transportar a política de runtime;
- Site Profile schema 1 continua importável;
- novas instalações não recebem campanhas ou criativos esportivos;
- Newsletter nova não presume lista MailPoet `#3`;
- textos iniciais da Newsletter passam a ser genéricos;
- Dashboard diagnostica política, Polylang e MailPoet;
- inventário legado deixa de fazer parte da experiência principal.

## Preservação do Mengão 360

- gates existentes iniciam ativos em upgrades;
- configurações e dados atuais não são removidos;
- Ads, Newsletter, Consent, templates e shortcodes homologados permanecem disponíveis;
- nenhuma tabela legada é excluída.

## Instalação em outro portal

A versão autoriza somente instalação em staging e preflight. Produção continua dependendo de pacote homologado, roteiro, evidências e autorização explícita.

O piloto recomendado ativa primeiro Site Profile, Polylang e Discovery em shadow. Ads, Newsletter, Consent e takeover de templates permanecem desligados na primeira onda.

## Rollback

- desativar gates públicos no Site Profile;
- desativar Editorial ou Discovery no registro modular;
- desativar o plugin, preservando opções e tabelas;
- restaurar o pacote anterior no ambiente de origem se necessário.
