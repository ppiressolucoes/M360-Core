# M360 Core v0.7.4.0.8 — Prospective Discovery Cutover

## Escopo autorizado

Cutover público de Content Discovery & SEO somente para novos posts do Portal
Energia Limpa. Não há backfill nem alteração retroativa do acervo.

## Controles

- renderer `prospective`: canários homologados e posts publicados após o gate;
- writer `prospective`: somente transição inicial para `publish`;
- atualizações e mudanças de termos em posts antigos não entram na fila;
- backfill indisponível no modo prospectivo;
- até três links contextuais por post;
- PT-BR e EN-US resolvidos pelo Polylang;
- provider externo permanece somente leitura.

## Sequência

1. Salvar renderer em `prospective`, mantendo limite `3`.
2. Salvar writer em `prospective`.
3. Confirmar backfill parado e fila sem itens anteriores.
4. Publicar um novo post controlado e aguardar o WP-Cron.
5. Validar snapshot, links e componentes públicos.

## Rollback

Retornar writer a `manual` e renderer a `canary` ou `shortcode`. Os snapshots
já criados são preservados e nenhum conteúdo do WordPress é reescrito.
