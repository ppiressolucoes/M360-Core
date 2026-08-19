# M360 - Core Editorial v0.7.4.0.4 — PEL Discovery Dictionary, Admin Shortcuts & Branding

## Content Discovery & SEO

- adiciona adapter opcional e somente leitura para `${prefix}links_internos`;
- seleciona categorias e tags do mesmo locale cuja palavra-chave já exista no corpo do post;
- preserva como destino o arquivo nativo do termo no WordPress;
- incorpora a revisão do dicionário ao hash do snapshot e usa o algoritmo `portable-v3-keyword-dictionary`;
- mantém limite público de três âncoras, um destino por link e proteção de links, títulos, código, citações, formulários e componentes M360;
- mantém o comportamento anterior quando a tabela local não existe.

## Administração

O menu **M360 Dashboard** passa a exibir atalhos para Editorial, Discovery & SEO, Ads, Newsletter, Privacy & Consent e Site Profile e módulos. As rotas e permissões existentes são preservadas.

## Site Profile e branding

- Site Profile schema `3`;
- `branding.primary_color` e `branding.secondary_color` são portáteis e validados como cores hexadecimais;
- fallback do Core: vermelho `#d71920` / `#b81218`;
- perfil PEL: laranja `#ff3d00` / `#fc893c`;
- Discovery, Dashboard, UI foundation, seletor de idioma e Post Info passam a consumir as variáveis do perfil.

## Gates preservados no PEL

- renderer `canary` limitado aos IDs `77976, 77991`;
- writer `manual`;
- backfill parado;
- Ads, Newsletter, Consent e takeover de templates continuam desligados;
- controle `77939` deve permanecer sem componentes Discovery.

## Procedimento

1. Atualizar o Core para `0.7.4.0.4`.
2. Em Site Profile, salvar primária `#ff3d00` e secundária `#fc893c`.
3. Manter a allowlist canária e gerar novamente o snapshot de `77976`; o novo algoritmo invalida corretamente o hash anterior.
4. Limpar cache e validar no máximo três âncoras em ocorrências existentes.
5. Repetir o controle negativo em `77939`.

## Rollback

Retornar o renderer a `Shortcode — rollback` e reinstalar `0.7.4.0.3` se necessário. Snapshots e a tabela local de links não devem ser removidos.
