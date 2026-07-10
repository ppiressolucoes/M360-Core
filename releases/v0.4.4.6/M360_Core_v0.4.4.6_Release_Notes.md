# M360 Core v0.4.4.6 — Release Notes

Status: pronta para build e homologação
Entrega: Campaign CRUD Hotfix
Projeto: Mengão 360 | DW Esportivo

## Contexto

Campanhas sem datas de início ou fim podiam enviar valores nulos para helpers tipados como string, causando erro fatal no PHP 8 durante a edição.

## Corrigido

- renderização segura de campos nulos;
- validação de título, datas, período, enums e prioridade;
- tratamento de falhas do banco no CRUD;
- validação de campanhas e slots antes de vínculos;
- mensagens administrativas de sucesso e erro.

## Compatibilidade

- nenhuma alteração no schema;
- Universal Slot Renderer preservado;
- campanhas e criativos existentes preservados;
- datas vazias continuam válidas;
- WordPress 6.8+ e PHP 8+.

## Homologação

1. Abrir campanha existente com datas nulas.
2. Salvar sem preencher datas.
3. Criar campanha `house`.
4. Validar título vazio, datas inválidas e período invertido.
5. Editar e excluir campanha de teste.
6. Vincular e desvincular campanha de um slot.
7. Confirmar o front-end e criativos existentes.

## Build

```text
Workflow: Build M360 Core Plugin ZIP
version: 0.4.4.6
artifact: m360-core-v0.4.4.6.zip
```

## Rollback

Reinstalar `m360-core-v0.4.4.5.zip`, limpar caches e validar o M360 Ads Manager. Esta release não executa migração de schema.
