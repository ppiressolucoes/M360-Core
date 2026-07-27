# M360 Core — Consolidação operacional de 22/07/2026

Status: homologação funcional concluída

Ambiente: WordPress do Mengão 360

Escopo: absorção do M360 Home Editorial e preparação da frente M360 Semantic Relations

## 1. Resultado executivo

- o M360 Core foi evoluído da linha `v0.7.1` até `v0.7.1.15`;
- o módulo `Editorial Layout & Home` foi homologado em produção controlada;
- Newsroom, seções editoriais, widgets por editoria e ticker passaram a ser renderizados pelo Core;
- o cutover da Home EN-US foi concluído sem impacto visual aparente;
- o plugin precursor `M360 Home Editorial 0.1.2` foi desativado no WordPress e permanece instalado para rollback;
- o plugin `M360 Semantic Relations 0.9.0` permanece ativo, independente e sem alteração;
- Home PT-BR e tema News Portal permaneceram fora do escopo e inalterados;
- nenhum portal externo, conteúdo, dado pessoal, segredo ou credencial participou desta frente.

## 2. Baselines e artefatos

| Item | Estado consolidado |
|---|---|
| Baseline oficial anterior | `M360 Core v0.7.0 — Publisher Platform Foundation` |
| Tag canônica publicada | `v0.7.0` |
| Linha operacional homologada | `M360 Core v0.7.1.15` |
| Branch do workspace | `agent/v0.7.0-publisher-platform-foundation` |
| Commit-base do workspace | `f47d69a` |
| Pacote homologado | `outputs/m360-core-v0.7.1.15-canonical.zip` |
| SHA-256 | `2C283DD8E0DFC2910F9F887A31F3FA4EA2ECD1ADFB03115EDA95D5FBC1D70A70` |
| Raiz do ZIP | `m360-core/` |
| Precursor Home | desativado e preservado |
| Precursor Semantic Relations | ativo e preservado em `0.9.0` |

A linha `v0.7.1.x` está homologada operacionalmente, mas sua consolidação em commit, PR, `main` e tag permanece uma atividade de publicação do repositório, separada da validação WordPress.

## 3. Linha de atividades

### v0.7.1 e v0.7.1.1

- introdução do módulo `Editorial Layout & Home` em shadow mode;
- correção do pacote para manter a raiz canônica `m360-core/` e atualizar a instalação existente;
- validação de Foundation e Editorial como `healthy`;
- correção do texto de diagnóstico com mojibake;
- Home Editorial e Semantic Relations preservados durante a convivência inicial.

### v0.7.1.2 a v0.7.1.7 — Newsroom

- criação do shortcode público `m360_editorial_newsroom`;
- uma notícia principal e quatro cards secundários;
- categoria Internacional incorporada;
- carrossel manual e automático;
- fallback quando a tag de destaque possui poucos itens;
- componente limitado a 1200 px no desktop;
- proporções responsivas e controles ampliados;
- cards secundários com editoria e título sobre a imagem e degradê de contraste;
- regressão visual de cabeçalho/rodapé tratada com rollback temporário para `v0.7.1.1`, remoção dos CTAs EN-US e reativação controlada da linha Newsroom.

### v0.7.1.8 a v0.7.1.12 — Seções e widgets editoriais

- evolução dos layouts `grid`, `featured-list` e `compact`;
- criação de cinco modelos configuráveis de widgets por editoria;
- submenu exclusivo, cadastro retraído e CRUD de instâncias;
- dropdown de editorias, quantidade de notícias e tamanho de resumo configuráveis;
- `VIEW ALL`, metadados legíveis e preview no Elementor;
- carrossel Latest News com até 12 itens e seis visíveis no desktop;
- Newsroom incorporado como preset configurável, sem item nativo duplicado no CRUD;
- gate visual aprovado para avançar ao ticker e ao cutover.

### v0.7.1.13 a v0.7.1.15 — Ticker e cutover

- ticker próprio do Core, restrito à Home EN-US nesta homologação;
- categoria, manchete, navegação manual, autoplay e preview no Elementor;
- setas SVG, controles contrastantes e tipografia isolada do tema;
- diagnóstico publicado confirmou `data-autoplay="true"`, oito itens e inicializador ativo;
- causa final do autoplay inativo: `prefers-reduced-motion: reduce` cancelava silenciosamente o timer;
- `v0.7.1.15` tornou `autoplay="true"` efetivo e adicionou controle visível de pausa/retomada;
- validação concluída em múltiplos dispositivos;
- `M360 Home Editorial 0.1.2` desativado sem impacto visível.

## 4. Matriz final de homologação

| Capacidade | Resultado |
|---|---|
| Foundation modular | saudável |
| Editorial Layout & Home | saudável e público no escopo aprovado |
| Home EN-US | renderizada pelo Core |
| Newsroom | aprovado |
| Widgets editoriais | aprovados |
| Ticker | autoplay, manual e pausa/retomada aprovados |
| Preview Elementor | aprovado |
| Desktop e mobile | aprovados |
| Múltiplos dispositivos | aprovados |
| Home PT-BR / News Portal | preservados |
| Header e footer | estáveis |
| Posts PT-BR e EN-US | sem regressão aparente |
| Ads, Newsletter e Privacy & Consent | sem regressão aparente |
| M360 Home Editorial 0.1.2 | desativado; rollback disponível |
| M360 Semantic Relations 0.9.0 | ativo; ainda proprietário da camada semântica |

## 5. Ownership após o cutover

| Contrato | Proprietário atual |
|---|---|
| Newsroom e Top Header Section | M360 Core |
| Seções e widgets editoriais | M360 Core |
| Ticker da Home EN-US | M360 Core |
| Shortcodes editoriais legados após desativação do precursor | M360 Core |
| Relações, snapshots e injeção semântica | M360 Semantic Relations 0.9.0 |
| Home PT-BR e composição News Portal | tema News Portal, fora do Core |

Não existe mais ownership editorial concorrente na Home EN-US. Ainda existe uma dependência paralela legítima para descoberta e SEO semântico, que será tratada na linha `v0.7.2`.

## 6. Política de rollback vigente

1. manter `M360 Home Editorial 0.1.2` instalado e desativado durante a estabilização;
2. em regressão editorial, desativar `Editorial Layout & Home` ou reinstalar o último ZIP aprovado;
3. reativar o precursor somente depois de impedir ownership duplicado dos shortcodes;
4. não excluir opções, transients ou arquivos do precursor durante o período de observação;
5. não remover o precursor até a consolidação documental e publicação da linha `v0.7.1`.

## 7. Próxima frente autorizada

A próxima frente é `M360 Core v0.7.2 — Content Discovery & SEO`, preparada em `docs/01-sprints/Sprint_v0.7.2_Content_Discovery_SEO.md`.

A preparação não autoriza:

- escrita concorrente nas tabelas do precursor;
- alteração de opções `m360_sr_*` em produção;
- limpeza de cron, postmeta, runs ou relações;
- troca de renderer ou shortcodes sem pacote e roteiro;
- desativação do Semantic Relations antes de shadow mode, comparação e aceite explícito.

## 8. Evolução Content Discovery — v0.7.2 e v0.7.2.1

A v0.7.2 foi instalada e homologada com o módulo `Content Discovery & SEO` ativo em `legacy-read`. O adapter detectou corretamente o M360 Semantic Relations 0.9.0, suas tabelas legadas compatíveis e InnoDB, mantendo o precursor como writer e renderer exclusivo.

Na v0.7.2.1, o Core passou a operar em `shadow` com storage próprio:

| Item | Resultado |
|---|---|
| Saúde | `healthy` |
| Schema | `1` |
| Runs do Core | disponível — InnoDB |
| Relations do Core | disponível — InnoDB |
| Run ativo PT-BR | 1 |
| Run ativo EN-US | 1 |
| Relações por locale | 4 internal links, 6 related posts e 4 topics |
| HTML público do Core | zero |
| Impacto visual | nenhum observado |
| Renderer público | M360 Semantic Relations 0.9.0 |

O storage, a geração manual e a execução assíncrona por WP-Cron estão homologados. A promoção preservou o snapshot anterior como `superseded` e ativou o novo snapshot em PT-BR e EN-US sem mudança visual. O precursor ainda apresenta relações `candidate` e um evento `m360_sr_retry_post`; ambos ficam preservados para a comparação da v0.7.2.2. Até essa comparação, o precursor não deve ser desativado e nenhum renderer do Core deve ser promovido ao front-end.

## 9. Comparator & Diagnostics — v0.7.2.2

A v0.7.2.2 introduz uma comparação transitória por post e locale entre os snapshots ativos do Core e do precursor. Ela mede coverage, destinos exclusivos, ranks, latência e falhas, além de bloquear tecnicamente qualquer avanço quando houver destino inválido, locale cruzado, storage não saudável ou snapshot do Core ausente.

O comparador não cria dados, não agenda tarefas e não produz HTML. Resultados `eligible` não autorizam automaticamente o renderer canário: eles apenas formam a baseline para uma decisão explícita da próxima frente.

## 10. Encerramento do Content Discovery & SEO — 24/07/2026

A progressão `legacy-read → shadow → comparator → renderer canary → automatic renderer → writer/backfill` foi concluída.

| Item | Estado homologado |
|---|---|
| M360 Core | v0.7.3.0.1 |
| Content Discovery & SEO | `healthy` |
| Ownership | `automatic` |
| Backfill | `completed` |
| Cobertura | 2.278 / 2.278 — 100% |
| Ausentes | 0 |
| Gerados | 2.256 |
| Inalterados | 17 |
| Novas publicações | processadas automaticamente pelo Core |
| M360 Semantic Relations 0.9.0 | desativado, sem impacto observado |

O Core passa a ser o proprietário exclusivo da geração, persistência e renderização semântica. As tabelas e o plugin precursores não foram absorvidos, mesclados ou excluídos: permanecem congelados como rollback temporário.

O próximo trabalho permitido é observação operacional e definição da política de retenção. Remoção do plugin, limpeza de cron, opções, postmeta ou tabelas legadas exige pacote, backup, roteiro e autorização explícita.
