# Mengão 360 — Inventário Técnico DW Esportivo

Versão: v1.0

## Stack

API externa → n8n → MariaDB DW → Views frontend → cache_widgets → WordPress.

## Tabelas principais

- dim_competicoes
- dim_times
- dim_estadios
- dim_estadio_alias
- fato_jogos
- fato_mata_mata
- fato_classificacao
- fato_artilharia
- cache_widgets
- m360_i18n_publico
- m360_idiomas_publicacao

## cache_widgets

Campos observados:
id, widget_nome, html, origem, atualizado_em, created_at.

## Views principais

- vw_frontend_competicao_resumo
- vw_frontend_competicao_menu
- vw_frontend_grupos
- vw_frontend_grupo_jogos
- vw_frontend_classificacao
- vw_frontend_mata_mata
- vw_frontend_liga_classificacao
- vw_frontend_liga_artilharia
- vw_frontend_liga_estatisticas

## Views extras

vw_frontend_liga_artilharia:
competicao_id, competicao_nome, slug, codigo, temporada, posicao, jogador_nome, jogador_nacionalidade, jogador_posicao, time_id, time_nome, time_sigla, time_escudo, gols, assistencias, penalti_gols, jogos.

vw_frontend_liga_estatisticas:
competicao_id, competicao_nome, temporada, total_jogos, jogos_finalizados, jogos_agendados, jogos_ao_vivo, total_gols, media_gols, vitorias_mandante, vitorias_visitante, empates, maior_publico, primeiro_jogo, ultimo_jogo.

## Fluxos n8n

- ETL [1] Carga FATO Jogos.
- HTML [1] Publicar Jogos Fases Competição.
- ETL [2] Carga FATO Artilharia.
- HTML [2] Artilharia e Estatística.

## Padrão de tempo SQL

Usar DATE_SUB(UTC_TIMESTAMP(), INTERVAL 3 HOUR).

## Competições consolidadas

| ID | Nome | Slug | Código | Temporada | Modelo |
|---|---|---|---|---|---|
| 1 | Brasileirão Série A | campeonato-brasileiro-serie-a | BSA | 2026 | Pontos corridos |
| 11 | Copa Libertadores | copa-libertadores | CLI | 2026 | Copa ida/volta |
| 13 | FIFA World Cup | fifa-world-cup | WC | 2026 | Copa jogo único |

## Checklist de nova competição

1. Confirmar disponibilidade na API.
2. Validar dim_competicoes.
3. Definir modelo de publicação.
4. Rodar ETL [1].
5. Rodar HTML [1].
6. Rodar ETL [2].
7. Rodar HTML [2].
8. Criar página WordPress.
9. Inserir shortcode.
10. Validar pt-BR/en-US, mobile, cache e dark mode.
