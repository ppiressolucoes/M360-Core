# M360 Core v0.7.4.0.7 — External Dictionary Run Identifier Hotfix

## Correção

A `0.7.4.0.6` detectava e consultava o provider externo, mas seu identificador
de algoritmo excedia a coluna `algorithm_version varchar(32)`. Como resultado,
o snapshot externo não era criado e o run ativo permanecia em
`portable-v3-keyword-dictionary`.

A versão `0.7.4.0.7` utiliza `portable-v6-ext-dict`, preservando o schema e os
snapshots anteriores.

## Gate PEL

Após atualizar o Core, gerar somente os snapshots `77976` e `77991`. O run
ativo deve registrar `portable-v6-ext-dict`. Writer continua `manual`, backfill
continua `idle`, renderer continua `canary` e `77939` permanece excluído.
