# M360 Core v0.7.2.5 — Scheduler, Writer & Backfill Cutover

## Entrega

- writer assíncrono acionado por publicação, atualização e taxonomias;
- fila deduplicada e limitada para diagnóstico;
- lock por post e até três retries progressivos;
- idempotência por hash de conteúdo, termos, locale e algoritmo;
- aposentadoria do snapshot Core quando o post deixa de ser publicado;
- backfill em lotes de dez posts via WP-Cron;
- cobertura de posts publicados no painel;
- rollback imediato pelo modo `manual`.

## Segurança de dados

- nenhuma escrita nas tabelas `m360_semantic_*`;
- nenhum transporte literal de snapshots legados;
- nenhuma geração durante requisição pública;
- promoção transacional e preservação do snapshot anterior;
- locale estrito em todas as gerações.

## Comportamento após atualização

O writer do Core inicia em `automatic`, conforme autorização da v0.7.2.5. O precursor permanece ativo durante a homologação e pode continuar mantendo seu próprio storage, mas o renderer automático do Core consome somente `m360_discovery_*`.

O backfill não inicia sozinho. Um administrador deve iniciá-lo no painel Content Discovery.

## Rollback

Selecionar writer `manual` interrompe o backfill e bloqueia novos processamentos automáticos. Snapshots existentes e ambos os storages são preservados.
