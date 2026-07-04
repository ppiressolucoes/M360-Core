# DW Esportivo — Transição para Mata-Mata em Competições de Copa

Data de consolidação: 27/06/2026
Projeto: Mengão 360 / DW Esportivo
Status: procedimento validado em produção
Escopo: API externa, ETL n8n, MariaDB, publicação HTML, cache_widgets, WordPress e Bolão 360.

## 1. Objetivo

Documentar o cenário, as decisões e o procedimento operacional usado na transição da fase de grupos para o mata-mata, quando a competição já possui jogos oficialmente definidos, mas a API externa ainda entrega confrontos incompletos.

Este documento deve servir como runbook reutilizável para Copa do Mundo, Libertadores, Copa do Brasil, Mundial de Clubes e outras competições eliminatórias.

## 2. Arquitetura envolvida

Fluxo principal:

- API externa de dados esportivos.
- Normalização e lookup de dimensões no n8n.
- UPSERT na tabela fato_jogos.
- Views frontend do DW Esportivo.
- Publicador HTML [1] de jogos e fases.
- Gravação do widget em cache_widgets.
- WordPress/Elementor por shortcode.
- Tabelas auxiliares e interface do Bolão 360.

A página da competição consome HTML cacheado. O Bolão consulta o DW e suas tabelas auxiliares, podendo refletir uma atualização antes da página pública quando o widget ainda não foi regenerado.

## 3. Cenário observado em 27/06/2026

- A fase de grupos da WC2026 estava encerrando.
- A fase LAST_32 começaria em 28/06/2026.
- A FIFA já havia confirmado parte dos confrontos.
- A API football-data retornava 16 jogos LAST_32, inicialmente com homeTeam.id e awayTeam.id nulos.
- O jogo 537417, África do Sul x Canadá, precisava ser publicado antes da atualização da API.
- A página da competição mostrava “A Definir”.
- O Bolão precisava impedir palpites em confrontos sem os dois times definidos.

## 4. Diagnóstico técnico

### 4.1 API e normalização

O JSON bruto confirmou que o jogo 537417 chegava inicialmente com os dois times nulos. O node de normalização estava correto e propagava os IDs recebidos.

Conclusão: o atraso inicial estava na fonte externa.

### 4.2 LOOKUP DIM

O LOOKUP associava external_id nas tabelas dim_competicoes, dim_times e dim_estadios. Com IDs vazios, retornava corretamente mandante_id e visitante_id nulos.

### 4.3 UPSERT da fato_jogos

O UPSERT atualizava estádio, status, placares, vencedor, árbitro e fonte, mas não atualizava data_jogo, rodada, mandante_id, visitante_id e competicao_id.

A correção passou a atualizar esses campos apenas com valores válidos, preservando dados reais quando a API enviar NULL ou 9999.

### 4.4 Publicador HTML

O node “Busca View Mata-Mata ÚNICO” já entregava data_ida, data_ida_iso, estadio_unico_nome, time_a e time_b.

O gerador exibia somente a data e usava o campo de estádio de jogos de ida. A correção passou a extrair a hora de data_ida_iso e usar estadio_unico_nome em jogos únicos.

### 4.5 Bolão

Confrontos incompletos eram exibidos com campos de palpite ativos. A decisão foi manter o card visível, ocultar inputs e botão, mostrar “Aguardando definição dos times” e bloquear também no AJAX.

## 5. Ações executadas e validadas

- Atualização manual controlada do jogo 537417.
- Validação de África do Sul, ID interno 524, contra Canadá, ID interno 542.
- Regeneração do Publicador HTML [1] e do cache_widgets.
- Inclusão de horário e estádio nos cards do mata-mata.
- Proteção do UPSERT contra NULL e placeholder 9999.
- Atualização automática posterior do jogo pela API, devolvendo a fonte para football-data.
- Bloqueio visual e server-side de palpites para confrontos incompletos.
- Validação em desktop e mobile.

## 6. Regra operacional definitiva

### Confronto completo

Condições:

- mandante_id e visitante_id preenchidos;
- ambos diferentes de 9999;
- IDs diferentes entre si.

Comportamento:

- exibir normalmente;
- liberar palpites conforme horário e status;
- publicar no widget.

### Confronto parcial ou totalmente indefinido

Comportamento:

- manter o jogo visível;
- mostrar data, horário, estádio e time conhecido;
- ocultar inputs e botão;
- exibir “Aguardando definição dos times”;
- rejeitar chamada direta no AJAX.

### Jogo finalizado ou em andamento

Manter regras existentes de bloqueio, resultado, apuração e ranking.

## 7. Procedimento para futuras transições

### Antes do fim da fase atual

- Confirmar jogos futuros na fato_jogos.
- Verificar rodada, jogo_uid, external_id, data e estádio.
- Identificar times nulos ou 9999.
- Validar a proteção do UPSERT.
- Confirmar o bloqueio do Bolão.

### Quando confrontos forem oficializados

- Executar o ETL da API.
- Conferir os IDs externos.
- Se a API estiver atrasada e houver urgência, aplicar atualização manual transacional somente em confrontos oficialmente confirmados.
- Não editar cache_widgets diretamente.
- Não criar duplicidades.
- Preservar jogo_uid e external_id.

### Após atualizar o DW

- Executar Publicador HTML [1].
- Confirmar cache_widgets.
- Executar sincronizações auxiliares do Bolão.
- Limpar cache WordPress/hosting.
- Validar desktop, mobile, idiomas e dark mode.

## 8. Consultas de validação

### Conferir um jogo

```sql
SELECT
    id, jogo_uid, external_id, data_jogo, rodada,
    mandante_id, visitante_id, status_jogo, fonte_dados
FROM fato_jogos
WHERE competicao_id = 13
  AND jogo_uid = '537417';
```

### Localizar confrontos incompletos

```sql
SELECT
    id, jogo_uid, data_jogo, rodada,
    mandante_id, visitante_id, status_jogo
FROM fato_jogos
WHERE competicao_id = 13
  AND rodada = 'LAST_32'
  AND (
      mandante_id IS NULL
      OR visitante_id IS NULL
      OR mandante_id = 9999
      OR visitante_id = 9999
      OR mandante_id = visitante_id
  )
ORDER BY data_jogo;
```

### Validar o cache

```sql
SELECT widget_nome, CHAR_LENGTH(html) AS tamanho_html, atualizado_em
FROM cache_widgets
WHERE widget_nome LIKE 'widget_competicao_fifa-world-cup_2026%';
```

## 9. Proteção do UPSERT

O UPSERT deve atualizar times somente com IDs válidos; preservar times reais diante de NULL/9999; atualizar rodada e data; impedir regressão de FINISHED; manter MANUAL_FIFA enquanto a API estiver incompleta; e devolver a fonte para football-data quando a API fornecer o confronto completo.

## 10. Rollback

- Registrar o estado anterior.
- Usar START TRANSACTION.
- Atualizar somente por jogo_uid/external_id.
- Conferir com SELECT.
- Executar COMMIT após validação.
- Usar ROLLBACK em divergências.

Nunca executar UPDATE amplo sem competicao_id e identificador do jogo.

## 11. Pontos de atenção

- fato_mata_mata não participa atualmente desse fluxo.
- vw_frontend_mata_mata_jogos é a fonte efetiva do Publicador HTML para jogos únicos.
- Horários do DW estão em America/Sao_Paulo.
- data_ida_iso já chega no horário local.
- 9999 representa “A Definir”.
- Pastas locais de versionamento histórico não pertencem ao pacote de produção.
