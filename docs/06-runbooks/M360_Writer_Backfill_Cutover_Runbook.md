# M360 Writer & Backfill Cutover Runbook

Status: **executado e homologado em 24/07/2026**.

## Pré-requisitos

- M360 Core v0.7.2.5.1 ou superior instalado e módulo Content Discovery ativo;
- modo do módulo `shadow`;
- renderer `automatic` já homologado em PT-BR e EN-US;
- M360 Semantic Relations 0.9.0 permanece ativo;
- WP-Cron funcional.

## Homologação do writer

1. Em **M360 Platform > Content Discovery**, confirmar `Writer do Core: automatic`.
2. Publicar um post PT-BR e outro EN-US.
3. Aguardar a execução do WP-Cron e atualizar o painel.
4. Confirmar para cada post:
   - run `active` no locale correto;
   - relações `topic`, `internal_link` e `related_post`;
   - fila recente em `active`;
   - saída pública do Core sem bloco duplicado.
5. Alterar conteúdo ou taxonomia de ambos os posts.
6. Confirmar novo run `active`, anterior `superseded` e ausência de locale cruzado.

## Backfill

1. Manter writer em `automatic`.
2. Clicar **Iniciar/reiniciar backfill**.
3. Não recarregar repetidamente o botão: o WP-Cron processa dez posts por lote.
4. Acompanhar:
   - status `running` até `completed`;
   - processados = gerados + inalterados + falhas;
   - cobertura crescente;
   - ausência de fila presa.
5. Investigar falhas antes de qualquer desativação do precursor.

Reiniciar o backfill recomeça pelo menor ID, mas a idempotência evita regravar snapshots com hash igual.

## Rollback

1. Selecionar writer `manual`.
2. O Core interrompe o backfill e ignora eventos automáticos pendentes.
3. Se necessário, retornar renderer a `shortcode`.
4. Manter o precursor ativo e suas tabelas intactas.
5. Não apagar `m360_discovery_*` nem `m360_semantic_*`.

## Autorização posterior

Concluir este runbook não desativa automaticamente o precursor. A desativação exige aceite explícito após backfill completo, cobertura validada e fila drenada.

## Registro de execução

| Indicador | Resultado final |
|---|---:|
| Writer | `automatic` |
| Backfill | `completed` |
| Saúde | `healthy` |
| Publicados / cobertos / ausentes | 2.278 / 2.278 / 0 |
| Cobertura | 100% |
| Gerados / inalterados | 2.256 / 17 |
| Precursor | desativado |
| Impacto observado | nenhum |

Após a conclusão:

1. manter o precursor instalado e desativado durante a observação;
2. não excluir as tabelas `m360_semantic_*`;
3. acompanhar novas publicações, fila, falhas e WP-Cron;
4. registrar qualquer rollback com horário, motivo e estado de writer/renderer;
5. tratar a remoção definitiva do legado como frente separada e explicitamente autorizada.
