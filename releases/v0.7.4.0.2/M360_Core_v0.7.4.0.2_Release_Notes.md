# M360 - Core Editorial v0.7.4.0.2 — Plugin Identity & PEL Controlled Deployment

## Objetivo

Identificar o produto de forma consistente no WordPress e introduzir o perfil
controlado de implantação do Portal Energia Limpa sem modificar o artefato
homologado `v0.7.4.0.1`.

## Identidade no WordPress

- nome: `M360 - Core Editorial`;
- descrição: `Sistema editorial e de navegação para WordPress com independência de tema M360 Core.`;
- autor: `M360`;
- versão candidata: `0.7.4.0.2`.

O WordPress passa a apresentar a identificação equivalente a
`Versão 0.7.4.0.2 | Por M360`.

## Portal Energia Limpa

O diretório `deployments/portal-energia-limpa` contém o complemento de
configuração controlada, seu perfil declarativo e o procedimento de build.

O perfil:

- preserva Elementor, Polylang, o tema ativo e templates existentes;
- mantém takeover de templates, Ads, Newsletter e Consent Runtime desligados;
- habilita Editorial somente por shortcodes explícitos;
- prepara Discovery em shadow manual, com até três links contextuais e sem
  injeção automática;
- não transporta conteúdo, dados pessoais, campanhas, credenciais, segredos
  ou snapshots de outro portal.

## Relação com v0.7.4.0.1

A v0.7.4.0.1 permanece o marco homologado e foi publicada como GitHub Release
com o SHA-256:

`DD614F644E78A047999EF0B93C184AC9E7C1826B99CB915614FA83B94C2BCFE8`

Esta revisão precisa de build e homologação próprios antes de ser promovida a
release operacional.
