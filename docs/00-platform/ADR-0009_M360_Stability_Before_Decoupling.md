# ADR-0009 - Estabilidade antes da desacoplagem

Status: aceita.
Data: 2026-07-20.
Complementa: `ADR-0008`.

## Contexto

A Foundation modular `v0.7.0` esta homologada. Os plugins M360 Home Editorial `0.1.2` e M360 Semantic Relations `0.9.0` ainda concentram capacidades que precisam ser absorvidas, estabilizadas e documentadas no M360.

## Decisao

O ciclo atual fica restrito ao proprio M360:

1. absorver Editorial Layout & Home na `v0.7.1`;
2. homologar, estabilizar e documentar a convivencia e o cutover;
3. absorver Content Discovery & SEO na `v0.7.2`;
4. homologar, estabilizar e documentar dados, scheduler, renderizacao e rollback;
5. somente depois abrir uma frente independente para estrategia de desacoplagem e portabilidade.

Qualquer portal externo, migracao entre instalacoes ou piloto de portabilidade fica fora do roadmap executavel atual. Referencias anteriores a um segundo portal representam contexto historico e nao autorizam implementacao, configuracao ou deploy.

## Consequencias

- o Mengao 360 permanece o unico ambiente de homologacao desta linha;
- os precursores continuam preservados ate cutover e estabilizacao;
- nenhuma regra esportiva entra no nucleo generico;
- nenhuma decisao de desacoplagem sera antecipada dentro da absorcao;
- producao depende de pacote, roteiro e autorizacao explicita.

## Gate para a frente posterior

A estrategia de desacoplagem somente pode comecar quando `v0.7.1` e `v0.7.2` estiverem homologadas, com documentacao, metricas, rollback testado e ausencia de ownership concorrente.

## Estado de execucao em 22/07/2026

- `v0.7.1.15` homologada em multiplos dispositivos;
- Home EN-US entregue ao M360 Core;
- `M360 Home Editorial 0.1.2` desativado sem impacto visivel e preservado para rollback;
- `v0.7.2` preparada em nivel de inventario, contratos, preflight e plano de cutover;
- `M360 Semantic Relations 0.9.0` permanece ativo e nao sera desativado antes de shadow mode efetivo, comparacao PT-BR/EN-US e transferencia exclusiva de scheduler, writer e renderer;
- estrategia de desacoplagem continua bloqueada ate a estabilizacao da `v0.7.2`.
