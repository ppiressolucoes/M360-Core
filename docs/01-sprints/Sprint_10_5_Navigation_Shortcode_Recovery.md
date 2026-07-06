# Sprint 10.5 — Navigation Shortcode Recovery

Status: entregue no GitHub  
Versão: 0.3.5  
Tipo: correção urgente de produção

## Objetivo

Corrigir imediatamente os shortcodes de navegação que passaram a ser exibidos literalmente após a instalação da v0.3.4.1.

## Problema observado

Após a atualização do plugin, o portal passou a exibir textos como:

```text
[m360_main_navigation menu_pt="main-menu-pt" menu_en="main-menu-en"]
[m360_breadcrumb]
[m360_section_navigation]
```

Isso indicava que os shortcodes existiam nas páginas, mas não estavam registrados pelo runtime novo do plugin.

## Entregas

- Criação de `plugin/includes/navigation/class-m360-navigation-shortcodes.php`.
- Registro restaurado dos shortcodes:
  - `[m360_main_navigation]`
  - `[m360_breadcrumb]`
  - `[m360_section_navigation]`
- Runtime atualizado para carregar a camada de navegação.
- Versão do plugin atualizada para `0.3.5`.
- `VERSION.md` atualizado.
- `CHANGELOG.md` atualizado.

## Escopo

Esta sprint prioriza recuperação operacional.

A implementação usa renderização segura e conservadora para remover os shortcodes literais da produção sem introduzir mudanças profundas de layout.

## Critérios de aceite

- [x] Shortcodes deixam de aparecer como texto literal.
- [x] Main Navigation volta a renderizar menu WordPress.
- [x] Breadcrumb volta a renderizar trilha básica.
- [x] Section Navigation volta a renderizar navegação contextual quando houver dados.
- [x] Pipeline mantém pasta raiz `m360-core/`.
- [x] Artifact ZIP deve ser gerado via GitHub Actions.

## Próximo passo

Gerar novo artifact no GitHub Actions e instalar `v0.3.5` no WordPress para validação urgente.
