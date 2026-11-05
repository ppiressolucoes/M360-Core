# M360 Core v0.7.4.0.59 — Últimas Notícias por cursor

## Objetivo

Eliminar paginação numérica profunda do componente `[m360_latest_news]` sem retirar a navegação necessária nas páginas dedicadas de Últimas Notícias.

## Mudanças de comportamento

### Posts e barra lateral

Use os shortcodes atuais sem paginação. O componente mantém limite curto, exclusão do post aberto, imagens, categorias, datas e anúncios configuráveis. Quando encontra uma página dedicada, ele adiciona o link acessível **Ver todas as notícias** / **View all news**.

A URL dedicada pode ser definida por `archive_url`, pela opção `m360_latest_news_archive_urls` indexada por idioma (`pt`, `en`, ou slug do Polylang) ou pelo filtro `m360_latest_news_archive_url`. Sem configuração, o Core procura páginas com os slugs `ultimas-noticias` e `latest-news`/`news`.

```text
[m360_latest_news layout="list" limit="8" pagination="false" show_ads="false"]
[m360_latest_news layout="sidebar" limit="6" pagination="false" show_ads="false"]
```

### Página dedicada

Use `navigation="cursor"`. Por compatibilidade, o antigo `pagination="true"` também ativa cursor, nunca a paginação numérica antiga.

```text
[m360_latest_news layout="list" limit="10" navigation="cursor" show_ads="true"]
```

A consulta traz `limit + 1` itens, usa `post_date DESC, ID DESC`, determina se existe próxima leva sem contar o total e gera um cursor assinado com a data, o ID e o idioma do último item. O link **Carregar mais notícias** funciona sem JavaScript e não executa polling.

## URLs legadas e SEO

- URLs com `m360_news_page`, inclusive valores inválidos ou extremos, recebem redirecionamento 301 para a URL limpa antes da renderização do bloco.
- Cursores inválidos, adulterados, longos ou de outro idioma também recebem redirecionamento 301 para a URL limpa.
- Páginas de cursor válidas recebem `noindex,follow`; são navegação para leitores, não novas páginas de indexação.

## Cache e banco

A chave do transient de dez minutos passa a incluir idioma, post atual excluído, cursor, modo de navegação, URL de destino e opções visuais. A consulta do cursor usa `no_found_rows=true`, não envia `paged` e não executa contagem integral. Esta versão não cria índices, tabelas, endpoints REST ou tarefas WP-Cron.

## Roteiro de homologação

1. Staging PT-BR e EN-US: carregar a primeira e a segunda leva, conferindo ordem, ausência de repetição e ausência de lacunas quando há datas iguais.
2. Posts e sidebar: confirmar a exclusão do post atual e o link para a página dedicada no idioma correto.
3. Abrir URLs legadas com `m360_news_page=0`, texto inválido e número extremo; confirmar redirecionamento ao URL limpo.
4. Abrir cursor adulterado; confirmar redirecionamento antes do bloco consultar notícias.
5. Com Query Monitor ou ferramenta equivalente, registrar total da requisição, tempo SQL, número de consultas e pico de memória em cache frio e quente, comparando o mesmo número de itens com a versão anterior.
6. Validar anúncios opcionais na página dedicada e blocos sem anúncios em posts/sidebar.

## Reversão

Retornar ao ZIP v0.7.4.0.58 restaura a paginação anterior. A configuração temporária atual com `pagination="false"` não é alterada por esta atualização.