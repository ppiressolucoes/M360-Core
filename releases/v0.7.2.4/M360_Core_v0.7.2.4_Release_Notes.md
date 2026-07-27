# M360 Core v0.7.2.4 — Renderer Canary

## Escopo

Entrega renderização pública limitada, baseada apenas em snapshots `active` do storage próprio do Core. A etapa sucede a paridade elegível de `internal_link` obtida em 7804 (PT-BR) e 7807 (EN-US).

## Shortcode canônico

```text
[m360_discovery_canary type="related_posts"]
[m360_discovery_canary type="topics"]
[m360_discovery_canary type="internal_links"]
```

O shortcode só gera HTML quando está em uma página singular cujo post atual consta na lista administrativa de canário. O atributo opcional `post_id` não permite renderizar um post diferente da página atual.

## Controles e limites

- lista explícita de até 20 posts em `M360 Platform > Content Discovery > Renderer Canary`;
- módulo Content Discovery ativo e modo `shadow` são obrigatórios;
- leitura exclusiva das relações `active` do Core por post e locale;
- links de `related_post` apontam para posts publicados; `topic` e `internal_link` apontam para arquivos de termos válidos;
- não existe `the_content`, auto append, REST público, cron, fallback ao legado ou escrita no precursor.

## Homologação

1. Autorizar inicialmente `7804` e `7807` no painel do canário.
2. Inserir apenas um shortcode por vez no template/conteúdo do próprio post de teste.
3. Confirmar semântica HTML, links, locale, responsividade e ausência de duplicação com o bloco legado.
4. Retirar o shortcode e limpar a lista do canário para retorno imediato a zero HTML do Core.

## Rollback

Remover o shortcode ou esvaziar a lista administrativa. Ambos removem a saída pública sem apagar snapshots nem alterar o M360 Semantic Relations 0.9.0, que permanece writer e renderer exclusivo fora dos posts explicitamente autorizados.
