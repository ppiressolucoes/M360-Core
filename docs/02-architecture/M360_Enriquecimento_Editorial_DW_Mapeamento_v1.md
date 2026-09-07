# Mapeamento do DW para Enriquecimento Editorial — v1

Status: **Mapeamento validado sobre recorte do DW de produção de 07/09/2026**.

[Sprint e base aceita](../01-sprints/Sprint_Enriquecimento_Editorial_DW_Esportivo.md) · [Diagnóstico](../06-runbooks/M360_Enriquecimento_Editorial_DW_Diagnostico.md)

Data inicial: 04/09/2026. Validação de produção: 07/09/2026. Base Core confirmada: 0.7.4.0.20, commit `0190d99cb5492f7356a5302b813570b3242249ae`.

## Resultado

O schema de produção, o catálogo das seis competições, o Flamengo e as views candidatas foram validados por conexão TLS e por dump local. A agenda pode ser lida de `fato_jogos`, com escopo de edição/fase em `dim_competicao_fase_jogo`. A classificação carregada não pode ser usada neste recorte porque `fato_classificacao` contém zero linhas; as views calculadas entregam classificação para as ligas e grupos da Libertadores.

O acesso usado para a coleta não é efetivamente somente leitura: embora tenha sido apresentado como tal, o usuário autenticado possui privilégios de alteração no schema. Os scripts desta sprint executaram apenas leituras e dump, mas o plugin de produção deverá receber outra credencial limitada a `SELECT` e `SHOW VIEW`.

## Fontes e confiança

1. Repositório oficial [m360-dw-esportivo](https://github.com/ppiressolucoes/m360-dw-esportivo/tree/b7c4c832dd8ddf9049b7a9de4202c9245dfe4c62), única branch retornada `main`, commit `b7c4c832dd8ddf9049b7a9de4202c9245dfe4c62`. Seu inventário contém sete arquivos: documentação e trechos do publicador n8n. Não contém DDL, API de leitura, Node 7 nem configuração de agendamento do ETL.
2. Dump local `M360-Baseline-production-Mega-Bolao/dw-mega-bolao-schema-only-2026-07-28/`, com DDL e índices. Referência de estrutura de 28/07, não prova do estado atual nem da existência de registros.
3. Consumidor local `m360-bolao/includes/class-bolao-db.php`: usa o helper `conectar_dw_esportes_m360()` e PDO. Possui operações de bolão que não pertencem ao enriquecimento. A existência do helper não comprova privilégio somente leitura.
4. Catálogo semântico no dump e adaptador SR examinado anteriormente: vínculo com WordPress, sem chave estrangeira demonstrada para times ou competições.
5. Recorte de produção `20260907-105104-dw-esportivo-production`, coletado por TLS (`TLS_AES_256_GCM_SHA384`) do MariaDB 11.8.8 e validado localmente no MariaDB 12.3.3. O ZIP tem SHA-256 `2b007bc4634b7b86899005affac58287b574230bf644aad1f31288a09125b61a`; o conteúdo permanece fora do Git.

Os fontes externos e o DW foram lidos sem alteração. O [manifesto de fontes](evidence/editorial-dw-fontes.json) e a [evidência sanitizada da validação](evidence/editorial-dw-producao-2026-09-07.json) registram hashes, contagens e decisões sem credenciais nem dados pessoais. O dump local e os arquivos de trabalho não fazem parte deste repositório.

## Mapa de entidades e fatos

| Necessidade editorial | Fonte documentada | Identidade e campos | Regra do adaptador |
| --- | --- | --- | --- |
| Time | `dim_times` | `id`, `nome`, `nome_popular`, `slug`, `sigla`, `pais`, `ativo` | ID interno canônico; nomes e siglas não são chaves de identidade |
| Competição | `dim_competicoes` | `id`, `nome`, `slug`, `codigo`, `temporada`, `modelo_id`, `ativo` | Validar allowlist com IDs reais das seis competições |
| Modelo de competição | `dim_competicao_modelo` | `id`, `nome_modelo`, `slug_modelo`, `tipo_mata_mata`, `ativo` | Não inferir fase atual apenas pelo modelo |
| Classificação de liga | `vw_frontend_liga_classificacao` | competição, time, posição, jogos, pontos, vitórias, empates, derrotas e gols | Fonte inicial calculada; registrar que não inclui punições/ajustes externos e homologar critérios por competição |
| Classificação de grupo | `vw_frontend_classificacao` | competição, grupo, time, posição e estatísticas calculadas | Fonte inicial para grupos da Libertadores; exigir competição e grupo explícitos |
| Classificação carregada | `fato_classificacao` | campos de escopo, posição e estatísticas | Não usar enquanto estiver vazia; reavaliar se o ETL passar a populá-la |
| Próximos jogos | `fato_jogos` | `id`, `jogo_uid`, `competicao_id`, `data_jogo`, `mandante_id`, `visitante_id`, `rodada`, `status_jogo`, `estadio_nome` | Validar horário, status, adversário e temporada; ordenar por data e ID |
| Temporada/fase de cada jogo | `dim_competicao_fase_jogo` | `competicao_id`, `temporada`, `jogo_uid`, `cod_fase`, `grupo_codigo`, `ativo` | Vincular por competição + UID + temporada; medir cobertura antes de depender dessa tabela |
| Contexto de eliminatória | `fato_mata_mata` | `competicao_id`, `temporada`, `fase`, `chave`, IDs dos times, UIDs das partidas | Fora do primeiro bloco até validar semântica de placares e fases |
| Artilharia, possível evolução | `fato_artilharia` | competição, temporada, `time_id`, jogador, gols/assistências | Não tratar agregação parcial de artilheiros como estatística completa do time |
| Proveniência do fornecedor | `dim_competicao_fonte` | competição + `fonte_id`, IDs externos e prioridade | Não trocar ID externo por ID canônico; disponibilidade de fonte não prova atualização |

IDs BIGINT devem trafegar como strings decimais no JSON, preservando precisão fora do PHP/SQL. A representação interna precisa respeitar os limites de cada runtime.

## Achados que afetam correção

### Catálogo e cobertura validados

| Código | ID | Competição | Temporada | Jogos | Finalizados | Próximos válidos | Vínculos de fase |
| --- | ---: | --- | --- | ---: | ---: | ---: | ---: |
| `BSA` | 1 | Campeonato Brasileiro Série A | `2026` | 380 | 235 | 70 | 380 |
| `PL` | 3 | Premier League | `2026` | 380 | 30 | 350 | 380 |
| `FL1` | 6 | Ligue 1 | `2026` | 306 | 27 | 279 | 306 |
| `BL1` | 7 | Bundesliga | `2026` | 306 | 16 | 289 | 306 |
| `CLI` | 11 | Copa Libertadores | `2026` | 149 | 141 | 7 | 133 |
| `PD` | 12 | Primera Division | `2026` | 380 | 39 | 341 | 380 |

As cinco ligas retornaram uma linha calculada por time: BSA 20, PL 20, FL1 18, BL1 18 e PD 20. A Libertadores retornou oito grupos de quatro times. O Flamengo é o time canônico ID 10, nome `CR Flamengo`, nome popular `Flamengo`, slug `flamengo-fla` e sigla `FLA`. No recorte, sua classificação calculada no BSA é 2º lugar, 23 jogos e 45 pontos; esses números são evidência de teste, não conteúdo congelado para publicação.

### Temporadas

`dim_competicoes.temporada` e `dim_competicao_fase_jogo.temporada` são VARCHAR(20), `fato_classificacao.temporada` é INT e `fato_mata_mata.temporada` é YEAR. `fato_jogos` não contém temporada no dump.

Não converter `2025/2026` em inteiro por coerção, usar o ano de `data_jogo` ou presumir que todo jogo pertence ao valor atual de `dim_competicoes.temporada`. O adaptador necessita de correspondência explícita entre edição editorial e valor de cada tabela. Para agenda, o vínculo por UID é candidato verificável. Jogos sem vínculo comprovado devem ser omitidos desse escopo; a cobertura do vínculo em pontos corridos ainda não foi demonstrada.

O UID tem larguras diferentes: 120 caracteres em jogos, 80 no vínculo de fase e 50 nos campos de mata-mata. Não truncar UIDs para fabricar correspondências; o diagnóstico verifica comprimentos e vínculo por igualdade.

### Unicidade da classificação

O dump apresenta PK em `id` e índices em competição/temporada/grupo e time, mas nenhuma restrição UNIQUE para competição + temporada + fase + grupo + time. A consulta de um time deve buscar até dois registros no escopo para detectar duplicidade. Não resolver ambiguidade com `ORDER BY atualizado_em DESC LIMIT 1` sem regra validada de snapshots.

Não há comprovação de histórico de classificação nem garantia de que linhas de uma mesma tabela compartilhem um instante consistente. Por isso, o primeiro bloco usa o registro do próprio time; distância para adversários, evolução e posição histórica exigem contrato adicional.

### Datas, atualização e cache

Os horários são DATETIME, sem fuso embutido. O publicador HTML usa `new Date(...)` e formatação por locale sem `timeZone` explícito. Isso não permite determinar se o DW grava UTC ou horário local. O fuso precisa ser confirmado antes da conversão para ISO 8601 com offset.

`fato_classificacao.atualizado_em` pode ser nulo. Jogos e dimensões possuem `updated_at`, atualizado na modificação da linha. Esses campos documentam atualização de registro no DW; não comprovam a hora em que o fornecedor verificou o dado. `etl_logs` tem processo/status/horário, mas não estabelece sozinho uma frequência por competição.

O contrato deve distinguir `dw_updated_at`, `source_observed_at` (quando comprovado), `fetched_at` e `expires_at`, por seção. Uma classificação antiga não herda a data de um jogo atualizado. Se não houver referência temporal suficiente, não rotular o dado como atual.

Cache proposto: por provedor/versão + time + competição + edição + fase/grupo; idioma adicional quando houver rótulos traduzidos no payload. TTL e limites máximos de idade permanecem configuráveis e serão fixados após conhecer a cadência real. Atualização fora da requisição pública, cache negativo, trava de concorrência e revalidação de identidade e horário da agenda ao renderizar.

### Status de jogos

O código completo do Node 8.2 reconhece `AGENDADO`, `AGUARDANDO`, `TIMED`, `SCHEDULED`, `FINISHED`, `IN_PLAY`, `LIVE`, `PAUSED`, `POSTPONED`, `CANCELLED` e `CANCELED`. O arquivo Vs09 é uma nota de mudança pendente de homologação, não o código completo de produção; menciona outros aliases que ainda precisam ser conferidos nos dados.

Regra conservadora de agenda: aceitar somente `AGENDADO`, `TIMED` e `SCHEDULED`, com horário futuro e dois times conhecidos. Não aceitar `AGUARDANDO` automaticamente como horário confirmado. Excluir encerrados, em andamento, adiados, cancelados e status desconhecidos. Um resultado vazio não deve afirmar que o time está sem jogos: pode significar ausência de dados elegíveis.

O recorte contém 72 linhas do BSA com 51 valores inválidos em `status_jogo`. Esses valores são timestamps ISO, no intervalo de partidas de 29/08 a 16/10/2026, indicando provável deslocamento de coluna no ETL. Cinco jogos do Flamengo entre as rodadas 27 e 31 são afetados. O adaptador não deve interpretar esses timestamps como status ou como nova data; deve omitir as linhas, marcar a seção `partial`/`no_data` conforme o resultado e registrar telemetria. A correção pertence ao ETL do DW.

### WordPress, resolução e idioma

`m360_seo_links_catalog` contém `target_wp_id`, `target_type`, `language_locale` e `catalog_ref_id`. O significado esportivo de `catalog_ref_id` não está demonstrado. Não usá-lo como `dim_times.id` ou `dim_competicoes.id` por suposição.

Não foi encontrada tabela de aliases de times nas estruturas examinadas. O módulo precisa de mapeamento explícito termo/meta WordPress → tipo da entidade → ID canônico, com aliases curados e evidência. Sigla isolada ou duas entidades plausíveis produzem fallback. Os mesmos IDs factuais devem ser usados em PT/EN, com rótulos localizados separados.

### Placares de mata-mata

O Node 8.1 contém uma regra de subtração de pênaltis dos campos `gols_time_a/b` e cria aliases de placar de ida, volta e jogo único. Essa transformação é específica do fluxo e não comprova a semântica universal dos campos. Não incorporá-la ao módulo de notícias como regra geral. O primeiro incremento pode entregar classificação, agenda e estatísticas básicas sem essa dependência.

## Views existentes e decisão de fonte

O dump de produção inclui 19 views, entre elas todas as views esportivas candidatas abaixo. O recorte foi importado em uma instância local isolada e as consultas de classificação e estatísticas foram executadas com usuário local limitado a `SELECT` e `SHOW VIEW`.

| View | Comportamento observado no dump | Condição para uso editorial |
| --- | --- | --- |
| `vw_frontend_classificacao` / `vw_frontend_classificacao_calculada` | Calculam grupos a partir dos jogos `FINISHED`; ordenam pontos, saldo, gols, vitórias e nome | Confirmar regulamento, punições/ajustes, isolamento por edição e consistência com a tabela do publicador |
| `vw_frontend_liga_classificacao` | Calcula pontos 3/1/0 dos jogos `FINISHED`; ordena pontos, vitórias, saldo, gols e nome | Critérios não foram demonstrados como válidos para as cinco ligas; não tratar como classificação oficial sem homologação |
| `vw_frontend_liga_jogos` | Liga jogos ao vínculo ativo de pontos corridos; usa temporada de `dim_competicoes`; aplica `CONVERT_TZ(data_jogo, '+00:00', '-00:00')` | Join no dump não limita `fj.temporada`; transformação de fuso não comprova UTC na origem |
| `vw_frontend_grupo_jogos` | Vincula explicitamente `fj.temporada = c.temporada` e tipo `GRUPOS` | Candidata para agenda da edição atual; conferir definição vigente e hora de origem |
| `vw_frontend_competicao_estatisticas` / `vw_frontend_liga_estatisticas` | Agregam jogos/gols por competição com rótulo da temporada atual; usam `COALESCE` nos placares | São métricas da competição, não do time; confirmar cobertura, nulos e filtro por edição |
| `vw_frontend_competicao_times` | União de mandantes/visitantes por competição | Relação histórica de participação sem temporada; não prova participação na edição atual |

Nas views de liga examinadas, o vínculo por UID não filtra a temporada. Se houver mais de um vínculo ativo de edições diferentes para o mesmo jogo, o join pode multiplicar registros; se houver jogos de edições anteriores, eles podem ser rotulados com a temporada atual. Isso é uma possibilidade identificada no código, não um incidente confirmado em produção.

A classificação calculada de grupos também agrega sem preservar temporada do vínculo. As views calculadas examinadas não expõem um timestamp de atualização factual. Como `fato_classificacao` está vazia, a primeira implementação usará as views calculadas com proveniência explícita e estado de atualização derivado apenas de dados comprováveis. Uma futura tabela oficial preenchida poderá substituir a fonte por configuração, após homologação.

Algumas definições de views no dump apresentam texto aparentemente malformado (por exemplo `not nullunionselect` em `vw_frontend_competicao_times`). Isso reforça que o dump não deve ser reaplicado e que precisamos das definições atuais. A consulta Q10 lê somente metadados dessas views; pode requerer visibilidade das definições.

## Referências locais adicionais

No acervo local de referência do projeto, fora deste repositório (não representa uma versão homologada por si só):

- `DW Esportivo/Carga de Competiçoes DW Esportivo.sql`: contém receita de carga de fases de pontos corridos e indícios para Ligue 1 (`codigo FL1`, `slug ligue-1`, ID 6, modelo 11). Os comentários têm números divergentes; os IDs não serão adotados sem catálogo real. A receita atribui a temporada atual da competição aos jogos selecionados, sem filtro temporal. Não foi executada.
- `Node n8n/node_html2_gerar_html_estatisticas_loop_i18n.txt`: consome um node chamado `Busca View Estatísticas`, mas não contém sua consulta. Seus campos são totais da competição (`total_jogos`, `jogos_finalizados`, `total_gols`, `media_gols`); não oferecem estatísticas avançadas por time.
- `DW Esportivo/Fluxo n8n DW/`: há diversas versões 8.1/8.2/8.3 e o arquivo completo local Vs09. A presença local não comprova homologação nem altera o commit oficial utilizado como referência. O Node 7 não foi localizado nas fontes examinadas.

O dump contém `SET time_zone = '+00:00'`, e rotinas do Bolão usam em alguns pontos UTC menos três horas. Nenhum desses elementos prova o fuso de `fato_jogos.data_jogo` gravado pelo ETL; não adotar conversão automática por inferência.

## Contrato proposto ao Core

`schema_version`, `status`, `entity`, `resolution`, `standings`, `upcoming_fixtures`, `team_statistics` e metadados temporais/proveniência por seção. Estados: `ok`, `partial`, `unmatched`, `ambiguous`, `unsupported`, `no_data`, `stale`, `unavailable`.

Cada seção informa seu escopo, origem no DW e referência temporal. Dados ausentes são nulos ou seções omitidas, nunca zeros criados pelo adaptador. Defaults zero no banco também não provam que uma métrica tenha sido coletada; validar presença e consistência da carga. Sem métricas avançadas, percentuais ou narrativas geradas no primeiro incremento.

O adaptador realiza somente leituras em origem autorizada, com consultas parametrizadas e limites. Não reutilizar a conexão de escrita do Bolão como se ela fosse somente leitura, não alterar atributos de uma conexão compartilhada e não consultar dados de usuários, apostas ou overrides administrativos do Bolão para enriquecer notícias.

## Diagnóstico preparado

O [SQL de diagnóstico](../06-runbooks/sql/editorial-dw/diagnostico-dw-editorial.sql) contém apenas SELECTs. A primeira parte e consultas equivalentes de mapeamento foram executadas no recorte local. O servidor de produção informou sessão/global `SYSTEM` e `system_time_zone=UTC`; isso ainda não prova sozinho a semântica de todos os DATETIME gravados pelo ETL.

Limites de linhas controlam resultados, não garantem baixo custo de execução. Avaliar planos em homologação para os agregados por competição. Índices atuais de jogos são separados por competição, time e data; não há garantia de desempenho para o novo padrão. Qualquer índice futuro é tarefa de administração do DW, fora deste módulo somente leitura.

## Pendências objetivas

1. Criar uma credencial de produção exclusiva com apenas `SELECT` e `SHOW VIEW`, timeout e rotação definidos.
2. Corrigir no ETL as 72 linhas do BSA cujo `status_jogo` contém timestamp e acrescentar validação de domínio na carga.
3. Confirmar a semântica de fuso de `data_jogo`, a cadência do ETL e os limites de idade por seção.
4. Homologar os critérios de desempate das views calculadas e a regra da temporada, sobretudo na Libertadores.
5. Definir o mapeamento WordPress/Semantic Relations → IDs canônicos do time e da competição.

Essas pendências impedem a homologação em produção, mas o schema, as consultas e os casos de fallback já permitem iniciar o adaptador e seus testes. Nenhum componente PHP foi alterado nesta etapa documental.
