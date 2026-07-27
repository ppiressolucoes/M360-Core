# M360 Core v0.7.3.0 — Unified Admin Dashboard

## Objetivo

Refletir a arquitetura modular do M360 Core na administração do WordPress, substituindo os menus paralelos por um único ponto de gestão.

## Entrega

- um único menu lateral `M360 Dashboard`;
- abas para Visão Geral, Editorial, Discovery & SEO, Ads, Newsletter e Privacidade & Plataforma;
- cards operacionais com acesso às telas especializadas;
- indicadores de saúde dos módulos e cobertura do Content Discovery;
- páginas de CRUD preservadas como rotas internas;
- slugs anteriores preservados para compatibilidade;
- rota histórica `m360-ads-inventory` redirecionada para `m360-ads-slots`;
- Inventário Piloto removido da navegação e dos atalhos do Ads Manager.

## Preservação

Esta release não altera:

- front-end, shortcodes ou templates;
- schemas e tabelas;
- slots, campanhas e criativos;
- Inventory Library e Inventory Seeder;
- snapshots Core ou legado;
- fila, cursor, status ou eventos do backfill;
- writer e renderer do Content Discovery;
- configurações do Site Profile, Newsletter ou Consent.

## Homologação

1. confirmar que há apenas um item lateral `M360 Dashboard`;
2. validar as seis abas em desktop e mobile;
3. abrir cada card e executar operações somente leitura;
4. confirmar acesso a Slots, Campanhas, Criativos, AdSense Ready e Header Delivery;
5. abrir a URL antiga `admin.php?page=m360-ads-inventory` e confirmar o redirecionamento para Slots;
6. confirmar que o progresso do backfill foi preservado;
7. validar que não houve mudança visual no front-end.

## Rollback

Reinstalar a v0.7.2.5.1 restaura a navegação anterior. Não há migração de dados a desfazer.
