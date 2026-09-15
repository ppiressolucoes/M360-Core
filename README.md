# M360 Core

Plugin WordPress modular do ecossistema M360 para interface editorial, navegação, publicidade, consentimento, newsletter, busca, páginas dinâmicas e descoberta de conteúdo.

## Baseline oficial

- versão homologada: `0.7.4.0.42`;
- tag imutável: `v0.7.4.0.42`;
- ambiente homologado: Mengão 360, PT-BR e EN-US;
- arquitetura: M360 Publisher Platform modular, com bootstrap `legacy-compatible` no portal existente e `portable-safe` em novas instalações.

## Componentes editoriais e de navegação

A linha consolidada inclui:

- widgets editoriais reutilizáveis por idioma, categoria, modelo e quantidade;
- tipografia editorial configurável;
- breadcrumb, pesquisa, troca de idioma, preloader, header fixo e botão de retorno ao topo;
- blocos independentes de Categorias, Arquivos e Tags;
- menus nativos do WordPress padronizados para o Footer;
- links de redes sociais reutilizáveis.

Shortcodes principais do ciclo 0.7.4.0.26–0.7.4.0.42:

```text
[m360_editorial_widget id="..."]
[m360_breadcrumb]
[m360_search_toggle]
[m360_language_navigation]
[m360_preloader]
[m360_back_to_top]
[m360_categories]
[m360_archives]
[m360_tag_cloud]
[m360_footer_menu menu="..." title="..." surface="dark"]
[m360_social_links]
```

O ticker `[m360_sports_competitions_ticker]` pertence ao **M360 Plus Editorial** e permanece fora deste pacote.

## Estrutura do pacote

```text
plugin/
├── m360-core.php
├── assets/
├── includes/
├── languages/
├── templates/
└── views/
```

O ZIP instalável é gerado exclusivamente a partir de `plugin/`:

```powershell
.\scripts\build-plugin-package.ps1 -Version 0.7.4.0.42
```

Documentação de referência:

- `VERSION.md` e `CHANGELOG.md`;
- `docs/M360_Documentation_Index_v1.md`;
- `docs/03-releases/M360_Core_Editorial_Navigation_Consolidation_2026-09-15.md`;
- `releases/v0.7.4.0.42/M360_Core_v0.7.4.0.42_Release_Notes.md`.
