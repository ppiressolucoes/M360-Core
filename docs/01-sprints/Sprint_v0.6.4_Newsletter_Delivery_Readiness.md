# Sprint v0.6.4 — Newsletter Delivery Readiness

Status: homologada e consolidada no baseline estável em 2026-07-16
Baseline: `M360 Core v0.6.3`
Compatibilidade: incorpora integralmente a v0.6.3

## Critérios de aceite

- [x] painel calcula corretamente as sete verificações;
- [x] remetente válido e responsável editorial persistidos;
- [x] DKIM e DMARC apresentados como declarações, sem alegação de teste automático;
- [x] teste aprovado registra data UTC;
- [x] validade operacional do teste limitada a 30 dias;
- [x] consultas de erros recentes do provedor e bounces habilitadas;
- [x] salvamento gera `delivery_readiness_updated`;
- [x] formulário, Double Opt-In, sincronização e cancelamento preservados.

## Evidência de homologação

- prontidão: `7/7` verificações atendidas;
- MailPoet, lista configurada e WP-Cron: aprovados;
- remetente, DKIM e DMARC: confirmados operacionalmente;
- teste de envio registrado em `2026-07-16 23:06:20 UTC`;
- evento `delivery_readiness_updated` confirmado;
- mensagem `Prontidão de entrega atualizada` confirmada no painel.

## Fora do escopo

- consulta DNS automática;
- envio automático de campanhas;
- analytics de abertura e clique;
- aprovação editorial automatizada.
