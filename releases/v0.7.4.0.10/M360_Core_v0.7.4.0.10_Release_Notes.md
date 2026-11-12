# M360 Core v0.7.4.0.10 — Dedicated Prospective Queue Runner

## Contexto

O PEL mantém `DISABLE_WP_CRON=true` e possui backlog elevado no Action
Scheduler. Embora os eventos do Core sejam criados, o ciclo `wp-cron.php` não
chega ao worker prospectivo com previsibilidade.

## Runner CLI

`m360-discovery-cron.php` é acessível somente via PHP CLI e carrega o
WordPress localmente. Ele processa até cinco itens já presentes na fila
prospectiva por execução; não procura posts, não faz backfill e não inicia
renderer global.

## Operação

No tipo **PHP** do hPanel, configurar a tarefa Hostinger com o caminho relativo:

```text
domains/energialimpa.live/public_html/wp-content/plugins/m360-core/m360-discovery-cron.php
```

Esperar na saída: `M360 Discovery queue: considered=N processed=N skipped=N`.

## Rollback

Remover a tarefa dedicada e retornar writer a `manual`. O runner não remove
snapshots, relações ou conteúdo do WordPress.
