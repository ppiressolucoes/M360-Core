# M360 Content Discovery & SEO — Cutover Homologado

Data: 24/07/2026
Status: oficial
Core homologado: v0.7.3.0.1
Precursor: M360 Semantic Relations 0.9.0

## Resultado

O M360 Core assumiu integralmente writer, scheduler, storage, renderer e manutenção dos snapshots semânticos. O precursor foi desativado no WordPress sem impacto visual ou administrativo observado.

| Indicador | Evidência |
|---|---:|
| Saúde | `healthy` |
| Ownership | `automatic` |
| Backfill | `completed` |
| Posts publicados | 2.278 |
| Posts cobertos | 2.278 |
| Posts ausentes | 0 |
| Cobertura | 100% |
| Gerados | 2.256 |
| Inalterados | 17 |

Cinco posts já estavam cobertos antes do ciclo final, explicando a diferença entre `2.278` cobertos e `2.256 + 17` processados no backfill homologado.

## Capacidades assumidas

- geração assíncrona em publicação e atualização;
- deduplicação por hash, lock por post e retry controlado;
- storage próprio versionado;
- snapshots `active` e histórico `superseded`;
- links contextuais;
- bloco intermediário “Leia também”;
- notícias e tópicos relacionados;
- locale estrito PT-BR/EN-US;
- diagnóstico de cobertura, fila, cron e backfill;
- renderização automática de novas publicações.

## Estado do legado

O M360 Semantic Relations 0.9.0:

- está desativado;
- não possui ownership operacional;
- permanece instalado para rollback temporário;
- conserva suas tabelas, opções, postmeta e histórico;
- não teve registros importados ou mesclados no storage do Core.

Nenhuma exclusão de dados legados está autorizada.

## Observação pós-cutover

Durante a janela de estabilização:

1. acompanhar saúde, cobertura, fila e WP-Cron;
2. validar novas publicações PT-BR e EN-US;
3. confirmar ausência de duplicidade e locale cruzado;
4. registrar falhas e rollback, se houver;
5. manter plugin e tabelas legadas congelados.

## Próximo gate

Após a janela de observação, elaborar uma frente separada para retenção e possível arquivamento do legado. Qualquer remoção exige inventário, backup verificável, plano de restauração e autorização explícita.
