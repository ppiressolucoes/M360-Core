# Changelog — M360 Core

Todas as mudanças relevantes do M360 Core serão registradas neste arquivo.

## [Unreleased] — Campaign CRUD Hotfix

### Corrigido

- erro fatal ao editar campanhas com datas inicial/final nulas;
- validação de título, datas, período, enums e prioridade;
- tratamento de falhas do banco em criação, atualização e exclusão;
- validação da existência de campanhas e slots antes de vincular;
- mensagens administrativas de sucesso e erro para o CRUD.

## [v0.4.4.5] — M360 Universal Slot Renderer

Status: baseline estável homologada em PT-BR e EN-US.

### Consolidado

- `M360_Slot_Renderer` e API pública `m360_render_ad_slot()`.
- APIs e shortcodes históricos redirecionados ao renderer universal.
- Inline Ads Engine, Archive Ads Engine e Context Renderer integrados ao pipeline único.
- Compatibilidade com Elementor, News Portal, widgets e templates.
- Linha `v0.4.4.x — M360 AdSense Ready` encerrada como base da Plataforma Comercial.

### Próxima linha

- `v0.5.x — Plataforma Comercial M360`.

## [v0.3.7.0] — Sprint 10.6 Search Engine

Status: pronto para build via GitHub Actions.

### Adicionado

- `M360_Search_Controller`.
- Template dinâmico `plugin/templates/search.php`.
- CSS dedicado `plugin/assets/css/search.css`.
- Override controlado de `template_include` apenas para páginas de pesquisa.
- Layout de resultados com cards, imagem destacada, categoria, data e resumo.
- Empty State PT-BR / EN-US.
- Paginação integrada.
- Breadcrumb integrado via `[m360_breadcrumb]`.

### Observações

- Primeira entrega da Sprint 10.6 Dynamic View Engine.
- Não altera Author, Category ou Tag nesta versão.
- Próxima versão planejada: `v0.3.7.1` Author Engine.

## [v0.3.6.2] — Responsive Navigation Hotfix

Status: homologada.

### Corrigido

- Hamburger mobile/tablet restaurado.
- Menu EN-US mobile deixa de ficar expandido por padrão.
- Navegação principal PT/EN estabilizada.

## [v0.3.5] — Navigation Shortcode Recovery

Status: atualização urgente para produção.

### Corrigido

- Restaura o registro dos shortcodes de produção:
  - `[m360_main_navigation]`
  - `[m360_breadcrumb]`
  - `[m360_section_navigation]`
- Corrige shortcodes renderizados literalmente após a v0.3.4.1.
- Mantém compatibilidade com a fundação do View Engine.

### Observações

- Entrega focada em correção emergencial.
- O pipeline segue com pasta raiz fixa `m360-core/`.
- Próxima evolução funcional permanece planejada para Dynamic View Migration.

## [v0.3.4.1] — Plugin Upgrade Pipeline Fix

Status: homologada.

### Corrigido

- ZIP passa a usar pasta raiz fixa `m360-core/`.
- Proteção de constantes para evitar conflito em instalação paralela.
- Runtime isolado para evitar colisão com versão antiga.

## [v0.3.4] — View Engine Foundation

Status: entregue no GitHub para geração de artifact ZIP.

### Adicionado

- Fundação do M360 View Engine.
- `M360_View_Registry` para registro central de views.
- `M360_View_Loader` para resolução de templates por idioma/fallback.
- `M360_View_Renderer` para renderização segura de templates.
- Integração inicial do View Registry ao runtime do M360 Core.
- Views placeholder iniciais em `plugin/views/default/`.
- Shortcode `[m360_view]` conectado ao novo fluxo interno.

## [v0.3.2] — Layout Foundation

Status: instalada e ativa.

### Adicionado

- M360 Layout Engine Foundation.
- Shortcode `[m360_layout_shell view="search"]`.
- Shortcode `[m360_layout_shell view="author" author="luzia-aires"]`.
- Shortcode `[m360_layout_shell view="latest_news"]`.
