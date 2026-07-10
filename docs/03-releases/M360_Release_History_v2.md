# M360 Release History v2.0 — Snapshot GitHub

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Finalidade: consolidar o histórico de sprints em uma linha de releases da plataforma.

## 1. Release 1.0 — Fundação DW Esportivo

Escopo:

- API externa integrada.
- DW Esportivo estruturado.
- Tabelas de dimensões e fatos.
- Views frontend.
- Publicação via cache_widgets.

Módulo principal: M360 Sports Platform.

## 2. Release 1.1 — Publicadores HTML

Escopo:

- Publicador HTML [1] estabilizado.
- Publicador HTML [2] evoluído para artilharia e estatísticas.
- Widgets principais e extras.
- Shortcode `[m360_competicao]`.
- Responsividade e dark mode.

Módulos principais:

- M360 Sports Platform.
- M360 Infrastructure.

## 3. Release 1.2 — Competições consolidadas

Escopo:

- FIFA World Cup 2026.
- CONMEBOL Libertadores 2026.
- Brasileirão Série A 2026.
- Modelos de grupos, mata-mata jogo único, mata-mata ida/volta e pontos corridos.

Módulo principal: M360 Sports Platform.

## 4. Release 1.3 — Bolão WC operacional

Escopo:

- Palpites.
- Ranking.
- Ligas.
- Convite WhatsApp.
- Bloqueio por horário.
- Apuração.
- Dashboard/resumo.

Módulo principal: M360 Community.

## 5. Release 1.4 — Internacionalização PT-BR / EN-US

Escopo:

- Home EN publicada em `/en/`.
- WordPress, Elementor e Polylang integrados.
- Workflow n8n de tradução PT → EN consolidado.
- Plugin M360 Home Editorial 0.1.2.
- Header, Footer, Search e Language Switcher operacionais.
- Competições publicadas em PT-BR e EN-US.

Módulos principais:

- M360 Editorial.
- M360 Core.
- M360 Infrastructure.

## 6. Release 1.5 — M360 Semantic Relations

Escopo:

- Links internos contextuais.
- Related posts.
- Related topics.
- Snapshots semânticos.
- Separação PT-BR / EN-US.
- Renderização dinâmica sem alterar post_content.

Módulo principal: M360 Editorial.

## 7. Release 1.6 — Observabilidade e Reprocessamento SEO

Escopo:

- Diagnóstico administrativo por Post ID.
- Runs e relations observáveis.
- Botão Reprocessar agora.
- Botão Limpar estado semântico.
- Geração síncrona.
- Renderização resiliente no front-end.

Módulos principais:

- M360 Editorial.
- M360 Admin.

## 8. Release 1.7 — Search Console Ready

Escopo:

- PROCESSAR SEO.
- Janela operacional de pendências recentes.
- Listas PT-BR e EN-US prontas para Search Console.
- Ações Abrir post, Copiar URL e Abrir Search Console.
- Marcação de prioridade editorial.
- Fluxo seguro sem uso indevido da Google Indexing API.

Módulos principais:

- M360 Editorial.
- M360 Admin.

## 9. Release 2.0 — M360 Navigation Library

Status: Foundation validada.

Escopo:

- M360 Main Navigation.
- M360 Section Navigation.
- M360 Mobile Navigation.
- M360 Breadcrumb.
- M360 Competition Navigation.
- M360 Competition Registry.
- Independência progressiva do News Portal.
- Independência progressiva do Elementor Nav Menu.
- Integração Polylang.
- Biblioteca de componentes reutilizáveis.

Módulo principal: M360 Core.

## 10. Release 2.1 — Dynamic Views Foundation

Status: validada parcialmente.

Escopo:

- M360 Author Hub.
- M360 Search Results.
- M360 Layout Foundation.
- M360 Router inicial.
- Shortcodes de páginas dinâmicas.

Módulo principal: M360 Core.

## 11. Release 2.2 — View Engine / Radar News

Status: evolução contínua.

Escopo:

- M360 View Engine.
- Radar News / Latest News.
- CollectionPage Schema.
- ItemList Schema.
- Grid e Paginação reutilizáveis.

## 12. Release 2.3 — M360 Ads Manager Pilot

Status: homologada em produção até `M360 Core v0.4.3.5`.

Módulos principais:

- M360 Advertising.
- M360 Core.
- M360 Admin.

Objetivo:

Consolidar o primeiro motor funcional de inventário publicitário do Mengão 360, migrando espaços de publicidade antes manuais para slots renderizados pelo M360 Ads Manager.

### Linha de entregas

| Versão | Entrega | Status |
|---|---|---|
| `0.4.2.4` | Estrutura de banco do Ads Manager | Concluída |
| `0.4.2.5` | Painel administrativo do Ads Manager | Concluída |
| `0.4.2.6` | Biblioteca de Criativos | Concluída |
| `0.4.2.7` | Integração Media Library e formatos | Concluída |
| `0.4.2.8` | Preview e UX de criativos | Concluída |
| `0.4.3.0` | Production Inventory Pilot | Concluída |
| `0.4.3.1` | Shortcodes em widgets e seleção por slot | Concluída |
| `0.4.3.2` | Estabilização do formulário de criativos | Concluída |
| `0.4.3.3` | Estabilização do renderer | Concluída |
| `0.4.3.4` | Persistência de markup confiável (`style`/`script`) | Concluída |
| `0.4.3.5` | Fallback por intenção de slot e idioma | Homologada |

### Inventário homologado

| Slot | Uso | PT-BR | EN-US | Resultado |
|---|---|---:|---:|---|
| `header-top` | Banner 728x140 | OK | OK | Homologado |
| `content-bottom` | Banner HTML horizontal | OK | OK | Homologado |
| `sidebar-community` | HTML 300x300 | OK | OK | Homologado |
| `sidebar-square` | Imagem 1:1 | OK | OK | Homologado |

### Entregas consolidadas

- Tabelas próprias para slots, campanhas, relações e criativos.
- Painel `M360 Ads` no WordPress.
- Cadastro e edição de campanhas.
- Cadastro e edição de criativos.
- Upload/seleção via Media Library.
- Suporte a imagem, HTML, script/markup administrado, house ads, affiliate e sponsor.
- Shortcode `[m360_ad_slot id="..."]`.
- API PHP `m360_ads_render_slot()`.
- Renderização em PT-BR e EN-US.
- Fallback por idioma e formato.
- Fallback por intenção do slot: horizontal vs. 1:1.
- Renderização de HTML com CSS e scripts preservados para administradores.
- Validação em widgets do tema/Elementor.

### Decisão histórica

O M360 Ads Manager passa a ser a base da futura Plataforma Comercial M360. A sprint seguinte, `v0.4.4.0 — M360 AdSense Ready`, foi criada para padronizar visualmente e semanticamente todos os espaços publicitários antes da integração com provedores externos.

## 13. Release 2.4 — M360 AdSense Ready

Status: **homologada**.

Baseline de encerramento:

```text
M360 Core v0.4.4.5
```

Módulos principais:

- M360 Advertising.
- M360 Core.
- M360 Admin.

Objetivo:

Preparar toda a infraestrutura visual, semântica e técnica dos espaços publicitários do Mengão 360 para futura integração com Google AdSense e outros provedores, sem alterar a arquitetura homologada do Ads Manager.

### Linha de entregas concluídas

| Versão | Entrega | Status |
|---|---|---|
| `0.4.4.0` | Ad Slot Component, wrappers semânticos, labels, placeholders e CSS centralizado | Homologada |
| `0.4.4.1` | Inventory Library e Inventory Seeder | Homologada |
| `0.4.4.2` | Context Renderer | Homologada |
| `0.4.4.3` | Inline Ads Engine em artigos | Homologada |
| `0.4.4.4` | Archive Ads Engine em Search, Category, Tag, Author e Latest News | Homologada em PT-BR e EN-US |
| `0.4.4.5` | Universal Slot Renderer e API única de renderização | Homologada em PT-BR e EN-US |

### Componentes consolidados

- M360 Ad Slot Component.
- M360 Ads Inventory Library.
- M360 Inventory Seeder.
- M360 Ads Context Renderer.
- M360 Ads Inline Engine.
- M360 Ads Archive Engine.
- M360 Universal Slot Renderer.
- CSS centralizado do M360 Ads.
- Checklist administrativo AdSense Ready.

### Pipeline oficial homologado

```text
Elementor / News Portal / Widgets / Templates / Shortcodes / APIs
                              ↓
                  M360 Universal Slot Renderer
                              ↓
                    M360 Ad Slot Component
                              ↓
                 Slot → Campanha → Criativo
                              ↓
                   Provider / Placeholder
                              ↓
                    Front-end PT-BR / EN-US
```

### API oficial

```php
echo m360_render_ad_slot('header-top');
```

Compatibilidade preservada:

```php
m360_ads_render_slot();
m360_ad_slot();
```

```text
[m360_ad_slot id="header-top"]
[m360_ads_slot id="header-top"]
```

### Inventário dinâmico homologado

| Contexto | Slot | PT-BR | EN-US | Status |
|---|---|---:|---:|---|
| Artigo | `article-after-paragraph-2` | OK | OK | Homologado |
| Search | `search-inline` | OK | OK | Homologado |
| Category | `category-inline` | OK | OK | Homologado |
| Tag | `tag-inline` | OK | OK | Homologado |
| Author | `author-inline` | OK | OK | Homologado |
| Latest News | `latest-inline` | OK | OK | Homologado |
| Search vazio | `search-empty` | OK | OK | Homologado |

### Critérios de aceite atendidos

- HTML semântico padronizado para os slots.
- IDs únicos e classes CSS consistentes.
- Labels automáticas `PUBLICIDADE` e `ADVERTISEMENT`.
- Data attributes de slot, idioma, formato, provider, status e dimensões.
- Placeholders para ausência de campanha ativa.
- CSS centralizado no M360 Core.
- Compatibilidade com Elementor, News Portal e Polylang.
- Renderização por shortcode e API PHP preservada.
- Homologação visual em PT-BR e EN-US.
- Build instalável gerada por GitHub Actions.
- Nenhuma integração oficial com AdSense realizada nesta etapa.

### Marco arquitetural

A Release 2.4 materializa o ADR-0007 e consolida o M360 Core como camada oficial de interface, renderização e preparação para monetização da Plataforma Mengão 360.

Tema News Portal e Elementor permanecem como camadas de compatibilidade e composição, não como fonte primária da lógica publicitária.

### Documentos oficiais relacionados

```text
docs/00-platform/M360_Platform_Architecture_v2.md
docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md
docs/00-platform/M360_Arquitetura_Ads_Manager_v1.md
docs/01-modules/M360_Ads_Archive_Engine_v1.md
docs/01-modules/M360_Universal_Slot_Renderer_v1.md
docs/02-sprints/Sprint_v0.4.4.0_M360_AdSense_Ready.md
releases/v0.4.4.5/M360_Core_v0.4.4.5_Release_Notes.md
```

## 14. Release 2.5 — Plataforma Comercial M360

Status: próxima fase planejada (`v0.5.x`).

Objetivo:

Evoluir a infraestrutura homologada de publicidade para uma plataforma comercial operacional, sem refatorar o pipeline consolidado na Release 2.4.

### Backlog inicial

- Campaign Engine avançado.
- Priorização comercial.
- Rotação de campanhas e criativos.
- Regras por contexto, idioma, dispositivo e período.
- Métricas de impressões e cliques.
- Dashboard Comercial.
- Google AdSense.
- Google Ad Manager.
- House Ads inteligentes.
- Patrocinadores e afiliados.
- Auditoria e observabilidade comercial.

### Regra de evolução

Toda funcionalidade comercial deverá consumir:

```text
M360 Universal Slot Renderer
```

Nenhuma nova integração publicitária deverá renderizar diretamente no tema, Elementor ou templates externos.

## 15. Release 3.0 — M360 Layout Engine

Status: visão futura.

Escopo:

- Header Manager.
- Footer Manager.
- Sidebar Manager.
- Template Router.
- Layout Slots.
- Containers independentes do tema.

## 16. Regra de atualização

Toda sprint concluída deve ser consolidada em uma release.

Toda release deve atualizar:

- Documento Mestre, quando houver impacto arquitetural.
- Documento do módulo correspondente.
- Release History.
- Roadmap, quando o item sair de planejado para concluído.
- ADR, quando houver decisão estrutural.
- Evidências de homologação.
- Procedimento de rollback.
