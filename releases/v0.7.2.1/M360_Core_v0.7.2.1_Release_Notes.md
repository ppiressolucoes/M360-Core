# M360 Core v0.7.2.1 — Portable Storage & Shadow Generator

## Resultado

O Core passa a gerar snapshots semânticos em storage próprio e isolado. O M360 Semantic Relations 0.9.0 continua sendo o único writer legado e o único renderer público.

## Entregas

- `m360_discovery_runs` e `m360_discovery_relations` em InnoDB;
- schema próprio versão `1`;
- provider `wordpress` independente do catálogo DW;
- resolvedor estrito de locale;
- algoritmo determinístico `portable-v1`;
- promoção atômica e preservação do snapshot saudável anterior;
- geração manual imediata ou enfileirada por WP-Cron;
- painel agregado de runs e relações do Core.

## Limites deliberados

- nenhum processamento automático em `save_post`, REST ou visita pública;
- nenhum shortcode ou alias legado;
- nenhum renderer ou filtro `the_content`;
- nenhuma escrita em `m360_semantic_*`, `_m360_semantic_*` ou opções `m360_sr_*`;
- nenhuma dependência do DW Esportivo;
- nenhum transporte de conteúdo, dados pessoais ou segredos.

## Homologação

1. atualizar o M360 Core e manter o Semantic Relations 0.9.0 ativo;
2. abrir `M360 Platform > Content Discovery`;
3. confirmar modo `shadow` e saúde `healthy`;
4. confirmar as tabelas próprias `Runs` e `Relations` como disponíveis em InnoDB;
5. executar um post publicado PT-BR pelo botão `Gerar em shadow`;
6. confirmar um run `active` e relações `topic`, `related_post` e/ou `internal_link`;
7. repetir com um post EN-US;
8. confirmar ausência de relações entre locales;
9. testar a opção assíncrona e confirmar a execução do evento `m360_discovery_generate_shadow`;
10. validar que o front-end, os shortcodes legados e a administração permanecem inalterados.

## Critérios de aceite

- storage próprio saudável e transacional;
- geração termina sem erro crítico, warning ou notice;
- um resultado vazio não substitui snapshot ativo anterior;
- targets são posts publicados ou termos existentes;
- nenhum self-link e nenhum locale cruzado;
- zero HTML público produzido pelo Core;
- precursor permanece operacional e exclusivo na saída pública.

## Rollback

1. desativar `Content Discovery & SEO` na Plataforma;
2. manter o M360 Semantic Relations 0.9.0 ativo;
3. reinstalar a v0.7.2 caso seja necessário remover o gerador da execução;
4. preservar `m360_discovery_*` para diagnóstico;
5. não apagar nem modificar `m360_semantic_*` ou postmeta legado.

## Próximo gate

A v0.7.2.2 poderá comparar coverage, ranks, tipos e destinos entre Core e precursor. Nenhum renderer canário está autorizado por esta entrega.

## Evidência de homologação — geração manual

Status: **aprovada** em 22/07/2026.

| Verificação | Resultado |
|---|---|
| Modo | `shadow` |
| Saúde | `healthy` — shadow mode efetivo, storage próprio saudável e zero HTML público |
| Schema próprio | `1` |
| `m360_discovery_runs` | disponível — InnoDB |
| `m360_discovery_relations` | disponível — InnoDB |
| Front-end | sem impacto visual |
| Renderer público do Core | inexistente |
| Ownership público | M360 Semantic Relations 0.9.0 |

Runs ativos observados:

| Locale | Estado | Total |
|---|---|---:|
| `pt-BR` | `active` | 1 |
| `en-US` | `active` | 1 |

Relações ativas observadas:

| Locale | Tipo | Total |
|---|---|---:|
| `pt-BR` | `internal_link` | 4 |
| `pt-BR` | `related_post` | 6 |
| `pt-BR` | `topic` | 4 |
| `en-US` | `internal_link` | 4 |
| `en-US` | `related_post` | 6 |
| `en-US` | `topic` | 4 |

Conclusão: geração manual, separação por locale, persistência e promoção dos snapshots foram homologadas.

## Evidência de homologação — execução assíncrona

Status: **aprovada** em 22/07/2026.

A geração foi enfileirada por WP-Cron e executada sem alteração visual. O estado final confirmou a promoção correta nos dois locales:

| Locale | Runs | Relações ativas | Relações superseded |
|---|---|---|---|
| `pt-BR` | 1 `active` + 1 `superseded` | 4 `internal_link`, 6 `related_post`, 4 `topic` | 4 `internal_link`, 6 `related_post`, 4 `topic` |
| `en-US` | 1 `active` + 1 `superseded` | 4 `internal_link`, 6 `related_post`, 4 `topic` | 4 `internal_link`, 6 `related_post`, 4 `topic` |

Isso comprova a promoção transacional: o snapshot novo ficou ativo e o anterior foi preservado como histórico, sem locale cruzado ou HTML público do Core.

### Inventário legado remanescente

O diagnóstico do precursor registrou relações em estado `candidate` no legado e um evento pendente `m360_sr_retry_post`. Esses itens pertencem exclusivamente ao M360 Semantic Relations 0.9.0 e **não são falha do storage do Core**. Eles ficam preservados, sem cancelamento ou modificação, como entrada obrigatória da v0.7.2.2 — Comparator & Diagnostics.
