# M360 Core v0.6.2 — Newsletter Operations & Audit

## Resultado

Acrescenta observabilidade e operação administrativa à Newsletter Foundation homologada na linha v0.6.1.

## Capacidades

- tela `M360 Ads > Newsletter`;
- contadores locais de pendentes, confirmados, cancelados e bloqueados;
- health check da API MailPoet e da lista ID `3`;
- situação do cron, última execução e próximo agendamento;
- sincronização manual protegida por `manage_options` e nonce;
- relatório da quantidade verificada, atualizada e com erro;
- eventos de inscrição, confirmação, cancelamento, bloqueio, erro e sincronização;
- e-mail mascarado na interface e hash SHA-256 no armazenamento;
- retenção de eventos por 365 dias, ajustável pelo filtro `m360_newsletter_audit_retention_days`;
- remoção diária de tokens expirados há mais de 30 dias.

## Homologação concluída

- versão 0.6.2 instalada e ativa;
- MailPoet ativo e lista ID `3` localizada;
- WP-Cron agendado;
- sincronização manual executada em `2026-07-16 21:14:17 UTC`;
- quatro registros verificados, zero atualizações necessárias e zero erros;
- evento `manual_sync` registrado;
- release liberada para o baseline estável.
