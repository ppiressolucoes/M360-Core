# M360 Core v0.7.2.4.3 — Renderer Canary Unrestricted Shortcode

## Alteração autorizada

- remove a allowlist de posts do gate de renderização;
- remove a exigência de correspondência entre `post_id` e o objeto consultado;
- mantém o módulo ativo e o modo `shadow` como gates obrigatórios;
- mantém a saída dependente da presença explícita do shortcode;
- não adiciona auto-append, filtro `the_content`, cron, fallback ou escrita legada.

## Teste

No template singular, use:

```text
[m360_discovery_canary type="related_posts" post_id="7804"]
```

Sem `post_id`, o renderer utiliza o objeto atualmente consultado.

Para obter evidência visível mesmo quando a consulta não retorna relações, use `debug="1"`. O renderer informará se o bloqueio é módulo/modo, locale, tipo ou ausência de snapshot ativo.
