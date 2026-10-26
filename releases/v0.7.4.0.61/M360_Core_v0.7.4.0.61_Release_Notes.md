# M360 Core v0.7.4.0.61 — Botão disponível na primeira leva

## Correção

Na v0.7.4.0.60, a página dedicada já era reconhecida corretamente, mas a consulta buscava o registro adicional somente depois que um cursor aparecia na URL. Assim, a primeira abertura retornava exatamente o limite configurado e não conseguia determinar se havia uma próxima leva.

A v0.7.4.0.61 passa a buscar `limit + 1` sempre que o modo cursor estiver ativo. O registro adicional não é renderizado: ele serve exclusivamente para decidir se o botão **CARREGAR MAIS** deve aparecer.

## Contenção de recursos

- primeira e demais levas: no máximo `limit + 1` registros;
- blocos-resumo: exatamente `limit` registros;
- `no_found_rows=true`;
- nenhuma contagem total, paginação numérica, offset crescente, polling ou endpoint adicional.

## Reversão

Reinstalar o ZIP v0.7.4.0.60. Não há alteração de banco, índice, conteúdo ou WP-Cron.
