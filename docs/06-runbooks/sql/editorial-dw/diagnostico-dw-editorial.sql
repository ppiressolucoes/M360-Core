-- M360 Editorial: diagnostico somente leitura; NAO executado em producao.
-- Rodar no schema do DW. Nao consulta credenciais nem dados do Bolao.
-- Q1-Q3 podem ser executadas individualmente sem parametros.
-- Q4+ sao templates de prepared statements: preencher/bindar parametros tipados.
-- Resultados sao limitados, mas agregados ainda exigem avaliar custo/EXPLAIN.

-- Q1: fuso da sessao NAO comprova fuso armazenado nos DATETIME.
SELECT DATABASE() AS schema_atual,
       @@session.time_zone AS session_time_zone,
       @@global.time_zone AS global_time_zone,
       @@system_time_zone AS system_time_zone;

-- Q2: conferir schema atual antes de executar consultas dependentes.
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('dim_times', 'dim_competicoes', 'fato_classificacao',
                     'fato_jogos', 'dim_competicao_fase_jogo')
ORDER BY TABLE_NAME, ORDINAL_POSITION
LIMIT 200;

-- Q3: catalogo para selecionar os IDs reais das seis competicoes.
-- Nao e uma allowlist automatica. Se atingir 200, paginar por id.
SELECT id, nome, slug, codigo, temporada, modelo_id, ativo, updated_at
FROM dim_competicoes
WHERE ativo = 1
ORDER BY id
LIMIT 200;

-- Q4: conferir time escolhido editorialmente; :team_id e ID INTERNO do DW.
SELECT id, nome, nome_popular, slug, sigla, pais, ativo, updated_at
FROM dim_times
WHERE id = :team_id
LIMIT 1;

-- Q5: inventario de escopos de classificacao para uma competicao.
-- :competition_id inteiro validado no catalogo.
SELECT temporada, fase, grupo, COUNT(*) AS linhas,
       COUNT(DISTINCT time_id) AS times_distintos,
       MIN(atualizado_em) AS menor_atualizacao,
       MAX(atualizado_em) AS maior_atualizacao,
       SUM(CASE WHEN atualizado_em IS NULL THEN 1 ELSE 0 END) AS sem_atualizacao
FROM fato_classificacao
WHERE competicao_id = :competition_id
GROUP BY temporada, fase, grupo
ORDER BY temporada DESC, fase, grupo
LIMIT 100;

-- Q6: detectar ambiguidade/duplicidade no escopo exato, sem escolher a ultima linha.
-- :standings_season inteiro EXPLICITAMENTE mapeado; :phase/:group_code podem ser NULL.
-- <=> e comparacao null-safe de MySQL/MariaDB.
SELECT id, competicao_id, temporada, fase, grupo, time_id,
       posicao, pontos, jogos, vitorias, empates, derrotas,
       gols_pro, gols_contra, saldo_gols, atualizado_em
FROM fato_classificacao
WHERE competicao_id = :competition_id
  AND temporada = :standings_season
  AND time_id = :team_id
  AND fase <=> :phase
  AND grupo <=> :group_code
ORDER BY id
LIMIT 2;

-- Q7: vocabulario de status em intervalo explicito (DATETIME no fuso confirmado do DW).
SELECT status_jogo, COUNT(*) AS total,
       MIN(data_jogo) AS primeira_data, MAX(data_jogo) AS ultima_data
FROM fato_jogos
WHERE competicao_id = :competition_id
  AND data_jogo >= :window_start
  AND data_jogo < :window_end
GROUP BY status_jogo
ORDER BY total DESC, status_jogo
LIMIT 50;

-- Q8: amostra de cobertura do vinculo; o intervalo nao atribui temporada ao jogo.
-- :fixture_season texto canonico confirmado para a tabela de vinculo.
SELECT fj.id, fj.jogo_uid, CHAR_LENGTH(fj.jogo_uid) AS uid_length,
       fj.data_jogo, fj.status_jogo,
       EXISTS (
           SELECT 1 FROM dim_competicao_fase_jogo cfj
           WHERE cfj.competicao_id = fj.competicao_id
             AND cfj.jogo_uid = fj.jogo_uid
             AND cfj.temporada = :fixture_season
             AND cfj.ativo = 1
       ) AS possui_vinculo_na_edicao
FROM fato_jogos fj
WHERE fj.competicao_id = :competition_id
  AND fj.data_jogo >= :window_start
  AND fj.data_jogo < :window_end
ORDER BY fj.data_jogo, fj.id
LIMIT 100;

-- Q9: agenda candidata; dois binds distintos para o mesmo time em PDO nativo.
-- :home_team_id = :away_team_id, validados pelo chamador.
-- :now_dw e :horizon_dw no fuso confirmado; nao usar ano civil como temporada.
-- Status abaixo sao proposta conservadora, sujeitos a validacao pelo Q7.
SELECT fj.id, fj.jogo_uid, fj.competicao_id, cfj.temporada,
       cfj.cod_fase, cfj.grupo_codigo,
       fj.data_jogo, fj.status_jogo, fj.rodada,
       fj.mandante_id, tm.nome AS mandante_nome,
       fj.visitante_id, tv.nome AS visitante_nome,
       fj.estadio_nome, fj.updated_at AS jogo_dw_updated_at,
       cfj.updated_at AS vinculo_dw_updated_at
FROM fato_jogos fj
INNER JOIN dim_competicao_fase_jogo cfj
  ON cfj.competicao_id = fj.competicao_id
 AND cfj.jogo_uid = fj.jogo_uid
 AND cfj.temporada = :fixture_season
 AND cfj.ativo = 1
INNER JOIN dim_times tm ON tm.id = fj.mandante_id AND tm.ativo = 1
INNER JOIN dim_times tv ON tv.id = fj.visitante_id AND tv.ativo = 1
WHERE fj.competicao_id = :competition_id
  AND (fj.mandante_id = :home_team_id OR fj.visitante_id = :away_team_id)
  AND fj.mandante_id <> fj.visitante_id
  AND fj.data_jogo > :now_dw
  AND fj.data_jogo < :horizon_dw
  AND UPPER(TRIM(fj.status_jogo)) IN ('AGENDADO', 'TIMED', 'SCHEDULED')
ORDER BY fj.data_jogo, fj.id
LIMIT 3;

-- Q10: definicoes atuais das views esportivas. Sem parametros.
-- VIEW_DEFINITION nula/consulta negada indica falta de visibilidade; nao prova ausencia.
SELECT TABLE_NAME, VIEW_DEFINITION
FROM information_schema.VIEWS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN (
    'vw_frontend_classificacao', 'vw_frontend_classificacao_calculada',
    'vw_frontend_liga_classificacao', 'vw_frontend_liga_jogos',
    'vw_frontend_grupo_jogos', 'vw_frontend_competicao_estatisticas',
    'vw_frontend_liga_estatisticas', 'vw_frontend_competicao_times'
  )
ORDER BY TABLE_NAME
LIMIT 20;

-- Q11: vinculos ativos em varias edicoes para o MESMO UID; custo por competicao.
-- Ajuda a verificar risco de multiplicacao nas views sem filtro de temporada.
SELECT jogo_uid, COUNT(DISTINCT temporada) AS edicoes,
       MIN(temporada) AS menor_rotulo_edicao, MAX(temporada) AS maior_rotulo_edicao
FROM dim_competicao_fase_jogo
WHERE competicao_id = :competition_id
  AND ativo = 1
GROUP BY jogo_uid
HAVING COUNT(DISTINCT temporada) > 1
ORDER BY jogo_uid
LIMIT 100;
