# M360 Core v0.6.4 — Newsletter Delivery Readiness

## Resultado

Adiciona uma camada operacional para decidir se a Newsletter M360 está pronta para envios editoriais controlados.

## Verificações

- MailPoet ativo;
- lista configurada disponível;
- WP-Cron agendado;
- e-mail remetente válido;
- DKIM declarado como confirmado;
- DMARC declarado como confirmado;
- teste de envio aprovado nos últimos 30 dias.

As confirmações DKIM e DMARC são declarações registradas pelo administrador. O M360 Core não consulta nem certifica automaticamente o DNS.

## Alertas

O painel apresenta erros de provedor ocorridos nas últimas 24 horas e bloqueios/bounces registrados nos últimos sete dias.

## Homologação concluída

- `7/7 verificações atendidas` confirmado;
- MailPoet, lista configurada e WP-Cron aprovados;
- remetente válido, DKIM e DMARC confirmados operacionalmente;
- teste recente registrado em `2026-07-16 23:06:20 UTC`;
- evento `delivery_readiness_updated` registrado;
- release liberada para o baseline estável.
