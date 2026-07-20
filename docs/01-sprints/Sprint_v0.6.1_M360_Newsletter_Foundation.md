# Sprint v0.6.1 — M360 Newsletter Foundation

Referência: Issue #19
Status: homologada e consolidada em `v0.6.1.3`

## Critérios validados

- [x] formulário M360 e consentimento específico;
- [x] persistência das evidências de consentimento;
- [x] adaptador MailPoet isolado pela interface de provedor;
- [x] lista ID `3`, Double Opt-In, entrega e cancelamento funcionais;
- [x] sincronização de confirmação, cancelamento, reinscrição e bloqueio;
- [x] rate limit corrigido;
- [x] rota insegura de cancelamento removida;
- [x] documentação e release notes consolidadas.

## Evidências

O piloto foi executado no WordPress real com MailPoet ativo, lista exclusiva, DKIM e DMARC configurados. Foram observados formulário, confirmação, ativação, entrega e cancelamento pelo link do rodapé.

## Fora do escopo

- campanhas e automações avançadas;
- analytics e testes A/B;
- painel próprio de preferências;
- múltiplos provedores simultâneos.
