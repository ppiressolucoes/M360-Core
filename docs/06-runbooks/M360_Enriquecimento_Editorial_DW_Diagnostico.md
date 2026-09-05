# Diagnóstico — Enriquecimento Editorial DW

Status: **Análise técnica preliminar — aguardando validação do DW de produção**.

Objetivo: confirmar o contrato de leitura necessário ao [módulo proposto](../01-sprints/Sprint_Enriquecimento_Editorial_DW_Esportivo.md). As consultas não foram executadas no DW. [Mapeamento e limitações](../02-architecture/M360_Enriquecimento_Editorial_DW_Mapeamento_v1.md).

## Etapa inicial: quatro consultas sem parâmetros

Abrir [diagnostico-inicial-sem-parametros.sql](sql/editorial-dw/diagnostico-inicial-sem-parametros.sql) em uma sessão autorizada no schema do DW. Executar cada SELECT separadamente e exportar resultados tabulares/texto, preservando os nomes das colunas e definições completas de views.

| Consulta | Resultado necessário | Interpretação |
| --- | --- | --- |
| Q1 | Fuso de sessão/global/sistema e schema selecionado | Não comprova o fuso gravado pelo ETL nos DATETIME |
| Q2 | Colunas das cinco tabelas principais | Comparar com o dump de referência de 28/07/2026 |
| Q3 | Catálogo de competições ativas | Identificar IDs reais das seis competições; limite 200, paginar se atingido |
| Q10 | Definições das oito views esportivas selecionadas | Resultado nulo ou acesso negado não comprova ausência; solicitar metadados ao administrador |

Retornar também a confirmação do fuso de armazenamento pelo responsável do ETL. Não enviar senhas, tokens, strings de conexão com segredos nem exportações de dados do Bolão.

## Etapa por competição e time

O arquivo [diagnostico-dw-editorial.sql](sql/editorial-dw/diagnostico-dw-editorial.sql) reúne Q1–Q11. Q4–Q9 e Q11 são templates para prepared statements e não devem ser executados em lote com placeholders não vinculados.

| Parâmetro | Tipo/validação |
| --- | --- |
| `competition_id`, `team_id` | IDs internos, confirmados em catálogo; bind numérico compatível com BIGINT |
| `standings_season` | Inteiro explicitamente mapeado à edição da classificação; nunca converter `2025/2026` por coerção |
| `fixture_season` | Texto da edição na tabela de vínculo de jogos |
| `phase`, `group_code` | Valores exatos do escopo; NULL quando aplicável, preservado pela comparação `<=>` |
| `window_start`, `window_end`, `now_dw`, `horizon_dw` | DATETIME no fuso de armazenamento confirmado; intervalo finito |
| `home_team_id`, `away_team_id` | Mesmo ID de time, em binds distintos para compatibilidade com PDO nativo |

Q4 identifica time; Q5 inventaria escopos e atualização da classificação; Q6 retorna no máximo duas linhas para detectar ambiguidade; Q7 inventaria status; Q8 amostra vínculos de edição; Q9 consulta até três jogos candidatos; Q11 identifica UIDs ativos em mais de uma edição.

A classificação carregada e a calculada precisam ser comparadas e associadas ao Node 7 real antes da escolha de fonte. Status aceitos na agenda são proposta conservadora a validar com Q7. Resultado vazio não comprova que o time não tenha jogos.

## Custo, segurança e limites

Todos os statements fornecidos são SELECTs, com identificadores fixos. Não executar o dump de referência: ele inclui DDL e operações alheias à sprint. O módulo futuro precisa de acesso com permissão efetiva somente leitura; a conexão compartilhada usada pelo Bolão não comprova essa restrição.

LIMIT controla saída, não garante baixo custo. Avaliar planos dos agregados por competição em homologação; reduzir intervalos/amostras conforme necessário. Índices futuros são responsabilidade da administração DW, fora destas consultas. Não disparar diagnóstico na requisição pública de uma notícia.

## Validação documental realizada

- 11 statements iniciados por SELECT, sem comandos de mutação.
- Parâmetros nomeados sem repetição dentro de cada statement.
- 23 referências distintas de colunas qualificadas conferidas contra o DDL local.
- Quatro consultas iniciais sem parâmetros correspondem a Q1, Q2, Q3 e Q10 do arquivo completo.

Essas verificações são estáticas; não equivalem a parse completo MySQL/MariaDB, EXPLAIN ou execução em produção. Testes funcionais do módulo ainda não ocorreram.

## Retomada e reversão

Após receber resultados: atualizar o mapeamento com data/versão do DW, confirmar edição/fuso/IDs, selecionar fonte de classificação e definir transporte, TTLs e fixtures. Em seguida, implementar e homologar na base Core 0.7.4.0.20 fixada na sprint.

Esta entrega altera apenas documentação e consultas não executadas. Para desfazê-la após merge, reverter o commit documental via PR; não há migração, configuração de plugin ou dados a restaurar.
