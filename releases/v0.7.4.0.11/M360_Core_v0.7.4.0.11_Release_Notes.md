# M360 Core v0.7.4.0.11 — Site Profile Branding Tokens

## Contexto

O Site Profile do PEL já mantém as cores portáveis `#ff3d00` (primária) e
`#fc893c` (secundária), mas componentes de navegação e layout ainda possuíam
acentos legados do Mengão 360 em seus estilos.

## Alterações

- aplica `branding.primary_color` e `branding.secondary_color` do Site Profile
  como tokens CSS públicos;
- corrige o acento de Últimas Notícias, incluindo categorias e paginação;
- corrige submenu, breadcrumb, alternador de idioma, busca e Post Info;
- corrige newsroom, ticker e widgets editoriais;
- preserva fallbacks vermelhos para perfis sem branding configurado.

## Escopo e segurança

- nenhuma alteração em tema, templates Elementor, conteúdo ou taxonomias;
- nenhuma ativação de Ads, Newsletter, Consent ou Content Discovery;
- alteração limitada à camada visual dos componentes renderizados pelo M360.

## Validação PEL

1. Em **M360 Dashboard → Site Profile e módulos**, confirmar primária
   `#ff3d00` e secundária `#fc893c`.
2. Limpar cache de página/CDN, se aplicável.
3. Validar `/en/latest-news/` com o shortcode `m360_latest_news`:
   categoria, página atual e hover devem usar laranja.
4. Validar breadcrumb, busca, menu, ticker e widget editorial em pt-BR e en-US.

## Rollback

Reinstalar `v0.7.4.0.10`. O Site Profile e seus valores de branding são
preservados.
