-- M360: levantamento inicial somente leitura, sem parametros.
-- Executar individualmente no schema do DW. Nenhuma consulta foi executada pelo agente.
-- Q1: fuso da sessao nao comprova o fuso armazenado. Q10: definicoes podem exigir visibilidade de views.

-- Q1
SELECT DATABASE() AS schema_atual,
       @@session.time_zone AS session_time_zone,
       @@global.time_zone AS global_time_zone,
       @@system_time_zone AS system_time_zone;

-- Q2
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('dim_times', 'dim_competicoes', 'fato_classificacao',
                     'fato_jogos', 'dim_competicao_fase_jogo')
ORDER BY TABLE_NAME, ORDINAL_POSITION
LIMIT 200;

-- Q3
SELECT id, nome, slug, codigo, temporada, modelo_id, ativo, updated_at
FROM dim_competicoes
WHERE ativo = 1
ORDER BY id
LIMIT 200;

-- Q10
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
