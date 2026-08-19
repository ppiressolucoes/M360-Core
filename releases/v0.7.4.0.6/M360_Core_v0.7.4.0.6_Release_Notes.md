# M360 Core v0.7.4.0.6 — External Dictionary Provider

## Escopo

Hotfix controlado para permitir que Content Discovery & SEO consulte um
dicionário de links hospedado em schema externo ao banco do WordPress.

## Segurança e portabilidade

- conexão PDO separada e somente leitura;
- credenciais externas ao plugin e ao Site Profile;
- nenhuma mensagem bruta da conexão é exibida ou registrada pelo Core;
- tabela validada como identificador SQL antes da consulta;
- consultas por locale preparadas;
- ausência ou falha do provider permanece um estado seguro sem escrita.

## Operação PEL

- provider esperado: `external-pdo`;
- schema local do ambiente: `u164126954_energia_limpa`;
- tabela: `WP_links_internos`;
- writer permanece `manual`;
- backfill permanece `idle`;
- renderer permanece restrito aos canários `77976` e `77991`;
- controle negativo `77939` não participa da geração.

## Rollback

Remover a configuração externa e retornar o renderer a `shortcode`. Nenhuma
tabela externa, opção ou snapshot é excluído pelo rollback.
