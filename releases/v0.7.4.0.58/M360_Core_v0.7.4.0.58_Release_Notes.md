# M360 Core v0.7.4.0.58 — Contenção de recursos

## Objetivo

Reduzir imediatamente a pressão de CPU, memória e banco de dados em uma instalação WordPress sob incidente, sem retirar a renderização pública de relacionamentos já promovidos.

## Alterações

- A atualização eleva a versão do circuito de proteção do Content Discovery para `3`.
- O circuito coloca o writer em `manual`, mantém o backfill interrompido e remove os eventos assíncronos pendentes do Core.
- O renderer público não é rebaixado: se estava em `automatic`, continua lendo apenas snapshots já promovidos; se estava em `shortcode`, permanece assim.
- As consultas de anúncios repetidas no mesmo request passam a reutilizar cache em memória de processo para slot, campanha, criativo, mapa de anúncio e contexto.

## Efeito esperado

O pacote não atribui o incidente a um único plugin. Ele elimina, por padrão, a fonte mais custosa do Core sob carga — geração e escrita assíncrona — e reduz consultas redundantes no front-end. Não cria cache persistente, não gera transientes, não executa backfill e não altera conteúdo, menus ou configurações editoriais.

## Instalação e observação

1. Faça backup normal do plugin e instale o ZIP sobre a versão `0.7.4.0.57`.
2. Não execute **Purge All** repetidamente. Em LiteSpeed, mantenha Crawler, Guest Mode e Guest Optimization desligados durante a observação.
3. Em **M360 Dashboard > Content Discovery**, confirme que o modo seguro está ativo, writer está manual e backfill está `stopped`.
4. Meça CPU, memória, `guest.vary.php`, `admin-ajax.php` e resposta pública por 30 a 60 minutos sem novas alterações de plugins.
5. Só libere o modo seguro quando a hospedagem permanecer estável. A liberação não inicia backfill por conta própria; o writer deverá ser reativado conscientemente depois da análise.

## Rollback

A reversão para `0.7.4.0.57` restaura o código anterior. As configurações e snapshots são preservados. Se necessário, o modo seguro pode continuar ativo por opção/constante até a estabilização.