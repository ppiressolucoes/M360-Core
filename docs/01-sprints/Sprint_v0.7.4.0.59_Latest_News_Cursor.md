# Sprint — Evolução do componente Últimas Notícias por cursor

> Complemento v0.7.4.0.60: a página dedicada é reconhecida pelo URL resolvido e ativa o cursor automaticamente mesmo quando o shortcode ainda conserva `pagination="false"`. Fora dela, o bloco permanece um resumo com link para o arquivo.

## Contexto

A telemetria agregada da Hostinger registrou acessos a URLs com `m360_news_page` e números extremos. Isso torna a paginação numérica anterior um mecanismo plausível de amplificação de consultas, pois ela aceitava um número de página sem limite e exigia o cálculo do total. A métrica compartilhada entre domínios não permite atribuir àquele componente a saturação de CPU e memória.

## Decisão técnica

- A paginação numérica é substituída por cursor assinado baseado em `post_date`, `ID` e idioma.
- A ordem é `post_date DESC, ID DESC`; o ID remove ambiguidade em posts publicados no mesmo segundo.
- Cada próxima leva usa predicado keyset: data menor, ou mesma data com ID menor. Não há offset crescente nem contagem integral.
- A página dedicada usa `navigation="cursor"`; `pagination="true"` é compatibilidade para cursor.
- Posts e sidebar continuam sem navegação profunda, com link para a página dedicada.
- `m360_news_page` é removido por 301, antes do shortcode executar consulta. Cursores inválidos seguem a mesma regra. Cursores válidos são `noindex,follow`.

## Fora de escopo

- Não criar REST/AJAX, polling, WP-Cron ou eventos novos.
- Não criar índices, alterar tabelas ou concluir causalidade sobre a saturação da Hostinger sem telemetria por processo e consulta.
- Não reativar paginação profunda em posts ou barra lateral.

## Evidência necessária antes da produção

A homologação deve registrar tempo total, tempo SQL, número de consultas e pico de memória em staging PT-BR e EN-US, cache frio e quente, usando a mesma amostra de conteúdo. Se o cursor não reduzir o custo de forma material, manter `pagination="false"` e investigar a origem com logs de PHP/MySQL da hospedagem.
