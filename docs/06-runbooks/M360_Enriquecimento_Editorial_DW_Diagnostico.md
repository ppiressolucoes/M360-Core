# Diagnóstico — Enriquecimento Editorial DW

Status: **Coleta de produção e réplica local concluídas em 07/09/2026**.

Objetivo: confirmar o contrato de leitura necessário ao [módulo proposto](../01-sprints/Sprint_Enriquecimento_Editorial_DW_Esportivo.md). A conexão TLS, o dump e a réplica local foram validados. [Mapeamento, resultados e limitações](../02-architecture/M360_Enriquecimento_Editorial_DW_Mapeamento_v1.md).

## Coleta por dump local

Quando não houver conector de banco disponível no ambiente Codex, usar o [pacote de exportação PowerShell](../../scripts/dw-editorial/README.md). Ele produz a estrutura completa do schema e os dados das cinco tabelas centrais em `local-data/dw-esportivo/incoming/`, diretório protegido pelo `.gitignore`.

O dump registra um recorte temporal do DW. Ele é suficiente para fechar o primeiro mapa de schema, preparar fixtures e implementar o adaptador inicial. Latência, concorrência e atualização dos dados ainda deverão ser confirmadas posteriormente por uma conexão somente leitura.

## Recorte validado

- pacote: `20260907-105104-dw-esportivo-production.zip`;
- SHA-256: `2b007bc4634b7b86899005affac58287b574230bf644aad1f31288a09125b61a`;
- origem: MariaDB 11.8.8, transporte TLS `TLS_AES_256_GCM_SHA384`;
- réplica: MariaDB 12.3.3 em `127.0.0.1:3307`, schema `m360_dw_local`;
- usuário local: `m360_reader`, limitado a `SELECT` e `SHOW VIEW`;
- contagem: 46 tabelas, 19 views e 2.005 jogos.

O pacote e as credenciais locais permanecem sob `local-data/` e `local-tools/`, ambos ignorados pelo Git. A [evidência sanitizada](../02-architecture/evidence/editorial-dw-producao-2026-09-07.json) pode ser versionada.

Para operar a réplica já inicializada:

```powershell
.\scripts\dw-editorial\Start-DwLocal.ps1
.\scripts\dw-editorial\Invoke-DwLocalQuery.ps1 -Sql 'SELECT COUNT(*) AS jogos FROM fato_jogos;'
.\scripts\dw-editorial\Stop-DwLocal.ps1
```

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

`fato_classificacao` retornou zero linhas. A fonte inicial de classificação será calculada pelas views de liga/grupos, com a limitação documentada de critérios oficiais e ajustes externos. A agenda aceita apenas `AGENDADO`, `TIMED` e `SCHEDULED`. Foram detectadas 72 linhas do BSA com timestamp no campo `status_jogo`; elas devem ser omitidas e produzir estado `partial` ou `no_data`, nunca uma afirmação de ausência de jogos.

## Custo, segurança e limites

Todos os statements fornecidos são SELECTs, com identificadores fixos. O dump só deve ser importado na réplica local descartável; nunca no DW de produção ou em um WordPress. O módulo futuro precisa de acesso com permissão efetiva somente leitura. A credencial usada na coleta possui privilégios de alteração e não deve ser reutilizada pelo plugin.

LIMIT controla saída, não garante baixo custo. Avaliar planos dos agregados por competição em homologação; reduzir intervalos/amostras conforme necessário. Índices futuros são responsabilidade da administração DW, fora destas consultas. Não disparar diagnóstico na requisição pública de uma notícia.

## Validação realizada

- 11 statements iniciados por SELECT, sem comandos de mutação.
- Parâmetros nomeados sem repetição dentro de cada statement.
- 23 referências distintas de colunas qualificadas conferidas contra o DDL local.
- Quatro consultas iniciais sem parâmetros correspondem a Q1, Q2, Q3 e Q10 do arquivo completo.
- Dump validado por manifesto, tamanhos e SHA-256 antes da importação.
- Importação local concluída e views consultadas com credencial local somente leitura.
- Catálogo, cobertura, Flamengo e anomalias de status registrados em evidência sanitizada.

Ainda faltam EXPLAIN das consultas finais, teste de latência no caminho real do WordPress e testes funcionais do módulo.

## Retomada e reversão

Próxima etapa: implementar o adaptador em plugin específico do Portal Mengão 360, usando fixtures extraídas e sanitizadas da réplica, cache e fallback. Antes da homologação, criar credencial exclusiva somente leitura, corrigir a carga de status do BSA e confirmar fuso/cadência.

Esta entrega altera apenas documentação, scripts locais e evidências sanitizadas. Para desfazê-la após merge, reverter o commit via PR; não há migração nem dado de produção a restaurar.
