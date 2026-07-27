# M360 Core v0.7.2.2 — Comparator & Diagnostics

## Resultado

Esta versão compara, sob demanda e em modo somente leitura, os snapshots ativos do M360 Core e do M360 Semantic Relations 0.9.0 para um post e locale específicos.

## Entregas

- comparador de `topic`, `internal_link` e `related_post`;
- cobertura compartilhada, destinos exclusivos e diferenças de rank;
- validação de post publicado, termo existente e locale de cada destino post;
- bloqueio técnico para locale cruzado, destino inválido ou snapshot Core ausente;
- latência e contagem de runs com falha do Core e do precursor;
- formulário administrativo em `M360 Platform > Content Discovery`;
- resultado transitório: nenhuma tabela, opção ou snapshot é alterado pela comparação.

## Resultado do gate

| Status | Significado |
|---|---|
| `eligible` | sem destino inválido, locale cruzado ou ausência de snapshot Core; canário ainda requer autorização própria |
| `review` | cobertura abaixo de 50% ou precursor sem snapshot ativo; exige revisão editorial/técnica |
| `blocked` | destino inválido, locale cruzado, storage ruim ou snapshot Core ausente; promoção pública proibida |

## Homologação

1. manter Core em `shadow` e Semantic Relations 0.9.0 ativo;
2. abrir `M360 Platform > Content Discovery`;
3. informar um ID já processado em PT-BR e repetir em EN-US;
4. revisar totais, cobertura, destinos exclusivos, ranks, latência e falhas;
5. confirmar que `invalid_targets` e `cross_locale_targets` são zero;
6. registrar posts com status `review` para análise, sem alterar snapshots;
7. confirmar que front-end e administração permanecem inalterados.

## Limites deliberados

- não há renderer, shortcode, auto append ou filtro `the_content`;
- não há agendamento, geração ou escrita acionada pelo comparador;
- não há alteração de `m360_semantic_*`, `_m360_semantic_*`, opções `m360_sr_*` ou cron legado;
- o precursor continua sendo writer e renderer público exclusivos.

## Próximo gate

A v0.7.2.3 só poderá ser proposta após uma amostra aprovada de comparações por locale. O renderer canário continuará desligado até autorização explícita.
