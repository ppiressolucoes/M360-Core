# M360 - Core Editorial v0.7.4.0.21

## Correção

O componente **Últimas Notícias** agora exibe a categoria editorial principal de posts com múltiplas categorias.

Ordem de resolução:

1. meta portátil `_m360_primary_category_id`;
2. categoria principal do Yoast SEO;
3. categoria principal do Rank Math;
4. filtro `m360_latest_news_primary_category_id`;
5. primeira categoria atribuída pelo WordPress, como fallback.

Uma categoria candidata só é exibida se estiver atribuída ao post. Não há alteração de categorias, posts, dados pessoais, cookies ou preferências de visitantes.

## Validação PEL

- Em um post com `Negócios` e `Instagram`, marque `Negócios` como categoria principal no plugin SEO utilizado pelo portal, ou grave o ID em `_m360_primary_category_id`.
- Confirme que o shortcode `[m360_latest_news]` apresenta `Negócios` e que o link aponta para o respectivo arquivo de categoria.
