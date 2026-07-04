# Sprint 10 — M360 Dynamic Views

Status: em execução
Projeto: Mengão 360 | DW Esportivo
Módulo oficial: M360 Core

## 1. Objetivo

Evoluir o M360 Core de biblioteca de navegação para camada própria de renderização de páginas dinâmicas do WordPress.

As primeiras páginas priorizadas foram:

- Author Hub;
- Search Results;
- Radar News / Latest News.

## 2. Lição da v0.3.0

A tentativa inicial de inserir um Navigation Context gerou impacto negativo em páginas que não dependiam do tema ativo, afetando formatação, margens, cabeçalho e rodapé.

Decisão de estabilidade:

- Restaurar para v0.2.4.
- Adotar política `stable`, `rc` e `dev`.
- Nenhuma release pode alterar layout global sem validação específica.

## 3. Author Hub

### v0.2.5 — Dynamic Views base

Incluiu:

- `[m360_author_page]`;
- `[m360_search_results]`;
- `[m360_latest_news]`.

Validação:

- instalada sem impacto visual.

### v0.2.6 — Author Hub parametrizado

Incluiu:

- `[m360_author_page id="2"]`;
- `[m360_author_page author="luzia-aires"]`.

Validação:

- página `/autor/` renderizando Author Hub manual.

### v0.2.7 — Author Archive Override

Incluiu:

- Author Archive renderizado pelo M360 Core;
- card do autor;
- avatar;
- biografia;
- contador de artigos;
- grid de notícias;
- breadcrumb M360.

Validação:

- `/author/redacao-mengao-360/` renderizado com M360 Author Hub.

### v0.2.9 — Author Router Hotfix

Incluiu:

- correção do conflito de idioma no Author Archive;
- `/author/luzia-aires/` redirecionando para `/autor/?m360_author=luzia-aires`;
- `/en/author/luzia-aires/` redirecionando para `/en/author/?m360_author=luzia-aires`.

Resultado:

- Author Hub estabilizado em PT-BR e EN-US.

## 4. Search Engine

### v0.3.1 — M360 Search Results

Incluiu:

- M360 Search Results;
- M360 Search Router;
- SearchResultsPage Schema;
- Empty State PT-BR / EN-US;
- Grid padrão M360.

Validação:

- geração de resultados funcionando;
- breadcrumb funcionando;
- contador de resultados funcionando;
- campo de busca funcionando;
- cards funcionando;
- PT-BR e EN-US funcionando.

Pendência conhecida:

- header/footer/layout ainda passam pelo Theme Builder do Elementor.

Decisão:

- aceitar dependência residual temporária;
- resolver de forma completa no futuro M360 Archive Engine.

## 5. Layout Foundation

### v0.3.2 — M360 Layout Engine Foundation

Incluiu:

- shortcode `[m360_layout_shell view="search"]`;
- shortcode `[m360_layout_shell view="author" author="luzia-aires"]`;
- shortcode `[m360_layout_shell view="latest_news"]`.

Escopo seguro:

- sem override novo;
- sem mexer em header/footer;
- sem alterar layout global;
- base para Search, Radar News e demais Dynamic Views.

Resultado:

- v0.3.2 instalada e ativa.

## 6. Radar News

Status: planejado como próxima evolução.

Objetivo:

- substituir a página Últimas Notícias / Radar News dependente do tema News Portal;
- entregar versão PT-BR e EN-US;
- corrigir problemas de modo claro/escuro;
- criar CollectionPage Schema;
- reutilizar Grid, Cards, Paginação e Breadcrumb.

Versão planejada:

```text
v0.3.3 — Radar News Engine + View Engine Consolidation
```

## 7. Decisões arquiteturais

1. M360 Core passa a ser framework de interface do Mengão 360.
2. Dynamic Views é o módulo responsável por páginas especiais.
3. Router será o único responsável por resolver rotas especiais.
4. View Engine será responsável pela renderização das páginas dinâmicas.
5. Layout Engine será desenvolvido quando a camada de Dynamic Views estiver consolidada.
6. News Portal passa a ser tratado como camada de compatibilidade temporária.

## 8. Roadmap imediato

```text
v0.3.3 — Radar News Engine + View Engine
v0.3.4 — Category Engine
v0.3.5 — Tag Engine
v0.3.6 — Competition Hub
v0.4.0 — Layout Engine
```
