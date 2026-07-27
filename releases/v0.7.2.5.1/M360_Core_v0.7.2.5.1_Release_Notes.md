# M360 Core v0.7.2.5.1 — Discovery Operations Console

## Objetivo

Simplificar o painel Content Discovery & SEO durante o cutover do writer, mantendo o backfill e todos os dados operacionais.

## Interface

- quatro indicadores: saúde, cobertura, backfill e ownership;
- progresso e contadores do backfill em uma única área;
- fila recente em indicadores compactos;
- controles de writer e renderer separados;
- diagnósticos e execução manual recolhidos;
- compatibilidade legada disponível em área avançada;
- visual responsivo por CSS administrativo próprio.

## Continuidade

O hotfix preserva:

- opção e cursor do backfill;
- fila do writer;
- snapshots ativos;
- modos de writer e renderer;
- tabelas Core e legado.

Se o backfill estiver `running` e não houver próximo evento agendado após a atualização, o Core rearma automaticamente o lote seguinte.

## Rollback

Reinstalar a v0.7.2.5 restaura somente a interface anterior. Nenhum dado precisa ser migrado ou removido.
