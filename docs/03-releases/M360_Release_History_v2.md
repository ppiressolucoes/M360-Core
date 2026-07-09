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

Status: planejada.

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

O M360 Ads Manager passa a ser a base da futura Plataforma Comercial M360. Antes da Plataforma Comercial completa, a próxima sprint será `v0.4.4.0 — M360 AdSense Ready`, com foco em padronização visual e semântica dos espaços publicitários, etiquetas, IDs, placeholders, comentários HTML e preparação para aprovação do Google AdSense.

## 13. Release 3.0 — M360 Layout Engine

Status: visão futura.

Escopo:

- Header Manager.
- Footer Manager.
- Sidebar Manager.
- Template Router.
- Layout Slots.
- Containers independentes do tema.

## 14. Regra de atualização

Toda sprint concluída deve ser consolidada em uma release.

Toda release deve atualizar:

- Documento Mestre, quando houver impacto arquitetural.
- Documento do módulo correspondente.
- Release History.
- Roadmap, quando o item sair de planejado para concluído.
- ADR, quando houver decisão estrutural.
