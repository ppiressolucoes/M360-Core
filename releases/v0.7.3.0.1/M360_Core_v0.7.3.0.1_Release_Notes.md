# M360 Core v0.7.3.0.1 — Hidden Routes Access Hotfix

## Correção

A v0.7.3.0 ocultava as páginas especializadas removendo diretamente os itens do submenu global do WordPress. A mesma estrutura participa da autorização de páginas administrativas e, por isso, os cards retornavam “Sem permissão para acessar esta página”.

O hotfix:

- registra cada funcionalidade como página administrativa oculta com `add_submenu_page(null, ...)`;
- mantém os callbacks, capabilities e slugs existentes;
- mantém apenas `M360 Dashboard` no menu lateral;
- força o destaque do Dashboard ao navegar por uma rota interna;
- preserva o redirecionamento do Inventário Piloto para Slots.

## Homologação

Abrir pelos cards:

- Widgets editoriais;
- Content Discovery & SEO;
- M360 Ads;
- Newsletter;
- Privacy & Consent;
- Site Profile;
- Slots, Campanhas, Criativos, AdSense Ready e Header Delivery.

Todas devem carregar com um usuário que possua `manage_options`, sem reaparecer no menu lateral.

## Homologação operacional — 24/07/2026

- Dashboard e rotas internas acessíveis;
- Content Discovery & SEO `healthy`;
- cobertura `2.278 / 2.278` — 100%;
- backfill `completed`, com `2.256` gerados e `17` inalterados;
- ownership `automatic`;
- M360 Semantic Relations 0.9.0 desativado sem impacto observado;
- novas publicações processadas automaticamente pelo Core.

## Preservação e rollback

Não há alteração de dados, schemas, front-end, backfill ou configurações. Reinstalar a v0.7.3.0 restaura o comportamento anterior.
