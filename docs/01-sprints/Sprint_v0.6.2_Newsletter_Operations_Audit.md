# Sprint v0.6.2 — Newsletter Operations & Audit

Status: homologada e consolidada no baseline estável em 2026-07-16

## Critérios de aceite

- [x] painel acessível apenas a administradores;
- [x] MailPoet e lista ID 3 diagnosticados corretamente;
- [x] métricas correspondem aos registros M360;
- [x] sincronização manual executa e apresenta resumo;
- [x] cron horário aparece agendado;
- [x] trilha de auditoria recebe o evento `manual_sync`;
- [x] e-mails aparecem mascarados no painel;
- [x] erros do provedor ficam disponíveis na trilha de auditoria;
- [x] atualização cria o schema Newsletter versão 2 sem perda dos consentimentos.

## Evidência de homologação

- diagnóstico: MailPoet ativo, lista `M360 Newsletter` localizada e WP-Cron agendado;
- execução manual em `2026-07-16 21:14:17 UTC`;
- próxima execução observada em `2026-07-16 21:59:56 UTC`;
- resultado: `4` registros verificados, `0` atualizados e `0` erros;
- conclusão: estados locais já alinhados ao MailPoet.

## Fora do escopo

- campanhas e automações de marketing;
- painel público de preferências;
- analytics de abertura e clique;
- múltiplos provedores ativos.
