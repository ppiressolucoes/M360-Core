# M360 Core v0.6.1.3 — Newsletter Foundation Consolidation

## Resultado

Consolida a Newsletter M360 com consentimento independente, Double Opt-In e cancelamento seguro operados por um adaptador desacoplado para MailPoet.

## Entrega

- formulário `[m360_newsletter_form]` com consentimento obrigatório;
- auditoria de consentimento com data UTC, IP, User-Agent e origem;
- lista MailPoet ID `3`, Double Opt-In e cancelamento por links assinados;
- sincronização horária: `unconfirmed` → `pending`, `subscribed` → `confirmed`, `unsubscribed` → `unsubscribed` e `bounced` → `blocked`;
- proteção de taxa por endereço e por IP;
- interface preparada para futuros provedores.

## Segurança

O endpoint público de descadastro por simples e-mail foi removido. O fluxo oficial usa o token assinado do MailPoet.

## Homologação

- instalação, formulário e consentimento: aprovados;
- e-mail de confirmação e Double Opt-In: aprovados;
- ativação na lista e entrega da newsletter: aprovadas;
- cancelamento e página de confirmação: aprovados;
- sincronização dos estados: implementada e consolidada.

## Operação

A sincronização depende do WP-Cron e ocorre a cada hora. Sites com tráfego muito baixo devem acionar `wp-cron.php` por um cron externo.
