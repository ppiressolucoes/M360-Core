# M360 Inventory Library v1.0

Status: Oficial (Foundation)
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Módulo: M360 Advertising
Documento mestre: Inventário Comercial da Plataforma

Este documento estabelece a Biblioteca Oficial de Inventário Comercial da plataforma M360.

## 1. Objetivo

A Inventory Library passa a ser a fonte oficial para definição de todos os slots comerciais da plataforma, independentemente do tema WordPress, Elementor ou provedor de anúncios.

Ela orienta a evolução de:

- M360 Ads Manager;
- M360 AdSense Ready;
- Plataforma Comercial M360;
- Google Ad Manager;
- House Ads;
- Affiliate;
- Sponsor;
- Widgets comerciais;
- Marketplace Comercial M360.

## 2. Referências oficiais relacionadas

Este documento deve ser lido em conjunto com:

| Documento | Função |
|---|---|
| `docs/00-platform/M360_Platform_Architecture_v2.md` | Documento mestre da arquitetura da plataforma |
| `docs/00-platform/M360_Arquitetura_Ads_Manager_v1.md` | Arquitetura técnica do M360 Ads Manager |
| `docs/01-modules/M360_Advertising_Plugin_Guide_v1.md` | Guia operacional do módulo M360 Advertising |
| `docs/02-sprints/Sprint_v0.4.4.0_M360_AdSense_Ready.md` | Sprint de preparação AdSense Ready |
| `docs/03-releases/M360_Release_History_v2.md` | Histórico oficial de releases |
| `docs/04-operations/M360_Core_Plugin_Publication_Workflow_v1.md` | Workflow de publicação do plugin M360 Core |
| `releases/v0.4.4.0/M360_Core_v0.4.4.0_Release_Checklist.md` | Checklist de release da versão v0.4.4.0 |

## 3. Princípios

- Independência do tema.
- Independência do Elementor.
- Renderização por componentes.
- Inventário centralizado.
- Compatibilidade PT-BR/EN-US.
- Compatibilidade com Internal, AdSense, Google Ad Manager, Affiliate, Sponsor e House Ads.
- Governança por documentação, sprint, release e PR.

## 4. Contextos oficiais

- Home.
- Artigo.
- Sidebar.
- Busca.
- Categorias.
- Tags.
- Autor.
- Últimas Notícias.
- Arquivos.
- Widgets.
- Comunidade (roadmap).

## 5. Inventário inicial homologado

### 5.1 Home

```text
header-top
home-hero
home-after-highlight
home-inline-feed-1
home-inline-feed-2
content-bottom
home-before-footer
```

### 5.2 Artigos

```text
article-top
article-after-paragraph-2
article-inline-1
article-inline-2
article-inline-3
article-before-related
article-after-related
article-bottom
```

### 5.3 Sidebar

```text
sidebar-top
sidebar-community
sidebar-square
sidebar-middle
sidebar-bottom
sidebar-sticky
```

### 5.4 Busca

```text
search-top
search-inline
search-bottom
search-empty
```

### 5.5 Categorias

```text
category-top
category-inline
category-bottom
```

### 5.6 Tags

```text
tag-top
tag-inline
tag-bottom
```

### 5.7 Autor

```text
author-top
author-inline
author-bottom
```

### 5.8 Últimas Notícias

```text
latest-top
latest-inline
latest-bottom
```

### 5.9 Arquivos

```text
archive-top
archive-inline
archive-bottom
```

### 5.10 Widgets

```text
widget-before
widget-inline
widget-after
```

### 5.11 Comunidade

```text
community-top
community-feed-inline
community-sidebar
community-bottom
```

## 6. Relação com o M360 Ads Manager

A Inventory Library define os slots oficiais.

O M360 Ads Manager executa a operação desses slots por meio de:

```text
Slot → Campanha → Criativo → Idioma → Dispositivo → Formato → Renderer → Front-end
```

A tabela `m360_ad_slots` deverá refletir os slugs oficiais desta biblioteca.

## 7. Relação com AdSense Ready

A Sprint `v0.4.4.0 — M360 AdSense Ready` usa esta biblioteca como base para padronizar:

- wrapper semântico;
- IDs únicos;
- data attributes;
- labels `PUBLICIDADE` / `ADVERTISEMENT`;
- placeholders;
- CSS centralizado;
- providers futuros.

## 8. Governança

Todo novo slot comercial deverá ser registrado nesta biblioteca antes de ser implementado no código ou no banco de dados.

Esta biblioteca torna-se um Documento Mestre da arquitetura M360 ao lado de:

- Platform Architecture;
- Navigation Library;
- Ads Manager Architecture;
- Release History.

## 9. Próxima evolução

A próxima etapa técnica recomendada é criar o **Seeder Oficial do Inventário**, sincronizando esta biblioteca com o banco de dados do M360 Ads Manager durante instalação/upgrade do plugin.
