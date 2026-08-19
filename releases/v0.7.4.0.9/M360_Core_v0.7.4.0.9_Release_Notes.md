# M360 Core v0.7.4.0.9 — Prospective Queue Recovery & Diagnostics

## Correção

Entradas prospectivas podiam permanecer em `queued` caso o evento único do
WP-Cron desaparecesse antes da execução. O Core agora reconcilia a fila no
`init` e reageenda o hook `m360_discovery_process_post` quando necessário.

## Limites de segurança

- somente origem `post_published` é recuperada no writer prospectivo;
- nenhum post histórico é incluído;
- nenhum backfill é iniciado;
- o agendamento permanece único, não recorrente;
- itens incompatíveis são marcados `ignored`.

## Diagnóstico

O painel passa a mostrar os IDs e estados recentes da fila. Após a atualização,
espera-se `cron_recovered`, seguido de `running` e `active` quando o WP-Cron
for disparado.
