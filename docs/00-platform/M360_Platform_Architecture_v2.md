# M360 Platform Architecture v2.0 — Snapshot GitHub

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Função: bíblia técnica, arquitetural e operacional do projeto.

## 1. Visão executiva

O Mengão 360 evoluiu de portal editorial WordPress para uma plataforma esportiva modular baseada em dados, automação editorial, internacionalização, widgets esportivos, SEO semântico e produtos de comunidade.

O Documento Mestre é a fonte única de verdade da plataforma. Todo desenvolvimento estrutural futuro deve preservar rastreabilidade por ADR, roadmap, sprint, release e histórico.

## 2. Princípio central

O Mengão 360 deve ser tratado como plataforma, não apenas como site.

Isso significa:

- arquitetura modular;
- documentação viva;
- componentes próprios independentes de tema;
- governança por decisões arquiteturais;
- evolução por releases;
- preservação histórica;
- operação rastreável;
- internacionalização contínua;
- suporte a novas competições e produtos.

## 3. Arquitetura macro

Fluxo consolidado:

```text
API externa → n8n → MariaDB / DW Esportivo → Views frontend → cache_widgets → WordPress → Elementor / Theme Builder → Componentes M360 → Front-end PT-BR / EN-US
```

Camada editorial:

```text
n8n / CRON / REST → WordPress → Polylang → M360 Semantic Relations → Snapshots semânticos → Search Console Ready
```

Camada de comunidade:

```text
DW Esportivo → Bolão / Palpites → Rankings → Ligas → Convites → Mega Bolão 360
```

Camada comercial:

```text
M360 Ads Manager → Slots → Campanhas → Criativos → Renderer → Front-end PT-BR / EN-US → AdSense Ready / Plataforma Comercial
```

## 4. Módulos oficiais

### M360 Core

Responsável pela camada própria de interface da plataforma.

Componentes oficiais:

- M360 Header;
- M360 Footer;
- M360 Navigation Library;
- M360 Main Navigation;
- M360 Section Navigation;
- M360 Mobile Navigation;
- M360 Breadcrumb;
- M360 Competition Navigation;
- M360 Competition Registry;
- M360 Search;
- M360 Language Switcher;
- M360 Scroll Top;
- M360 Core UI;
- M360 Mega Menu;
- M360 Dynamic Views;
- M360 Router;
- M360 View Engine;
- M360 Layout Engine.

Missão:

Reduzir gradualmente dependências do tema News Portal e do Nav Menu do Elementor, criando uma camada de interface própria, reutilizável, multilíngue e preparada para novas competições.

### M360 Editorial

Responsável pela camada editorial, internacionalização e SEO.

Componentes oficiais:

- Internacionalização PT-BR / EN-US;
- Polylang Integration;
- M360 Home Editorial;
- M360 Semantic Relations;
- SEO Semântico;
- Search Console Ready;
- Links internos contextuais;
- Related News;
- Related Topics;
- Editorial Engine.

### M360 Sports Platform

Responsável pelo motor esportivo baseado em dados.

Componentes oficiais:

- DW Esportivo;
- API externa de dados esportivos;
- ETLs n8n;
- MariaDB;
- Views frontend;
- cache_widgets;
- Publicador HTML [1];
- Publicador HTML [2];
- Competition Engine;
- Competition Registry;
- Widgets de competições;
- Widgets de artilharia;
- Widgets de estatísticas.

Competições consolidadas:

- FIFA World Cup 2026;
- Brasileirão Série A 2026;
- CONMEBOL Libertadores 2026.

### M360 Community

Responsável pelos produtos de engajamento e comunidade.

Componentes oficiais:

- Bolão WC;
- Mega Bolão 360;
- Rankings;
- Ligas;
- Palpites;
- Convites WhatsApp;
- Comunidade;
- Gamificação;
- Planos Free / Pago.

### M360 Advertising

Responsável pela camada comercial, inventário publicitário e preparação para monetização.

Componente oficial consolidado:

- M360 Ads Manager.

Status:

```text
Piloto funcional homologado até M360 Core v0.4.3.5.
```

Objetivo:

O M360 Ads Manager constitui a camada oficial de gerenciamento de inventário publicitário do ecossistema Mengão 360. O módulo substitui progressivamente inserções HTML espalhadas pelo tema, Elementor e widgets manuais, centralizando slots, campanhas, criativos e renderização em um único motor.

Arquitetura funcional:

```text
Slot → Campanha → Criativo → Idioma → Dispositivo → Renderer → Front-end
```

Componentes:

- Slots de inventário;
- Campanhas;
- Criativos;
- Renderer;
- Fallback por idioma e formato;
- Integração com Media Library;
- Suporte a HTML, scripts e imagens;
- Shortcodes;
- API PHP;
- Renderização PT-BR / EN-US.

Inventário piloto homologado:

```text
header-top
content-bottom
sidebar-community
sidebar-square
```

Tipos suportados no piloto:

- Imagem;
- HTML;
- Script/markup confiável administrado;
- House Ads;
- Affiliate;
- Sponsor.

Internacionalização:

```text
pt-br
en-us
all
```

Dispositivos:

```text
desktop
mobile
all
```

APIs e shortcodes homologados:

```php
echo m360_ads_render_slot('header-top');
echo m360_ads_render_slot('content-bottom');
echo m360_ads_render_slot('sidebar-community');
echo m360_ads_render_slot('sidebar-square');
```

```text
[m360_ad_slot id="header-top"]
[m360_ad_slot id="content-bottom"]
[m360_ad_slot id="sidebar-community"]
[m360_ad_slot id="sidebar-square"]
```

Validação concluída:

- Header 728x140 em PT-BR e EN-US;
- Sidebar 1:1 em PT-BR e EN-US;
- Sidebar HTML 300x300 em PT-BR e EN-US;
- Bottom HTML horizontal em PT-BR e EN-US;
- Links de criativos;
- HTML com `<style>`;
- Markup com `<script>` para administradores;
- Fallback por idioma;
- Fallback por intenção do slot;
- Shortcodes em widgets/Elementor;
- API PHP para integração futura com tema/componentes.

Roadmap comercial:

```text
v0.4.4.x — M360 AdSense Ready
v0.5.x   — Plataforma Comercial M360
v0.6.x   — AdServer / Analytics / Rotação
v1.x     — Marketplace Comercial M360
```

Próxima frente imediata:

A Sprint v0.4.4.0 — M360 AdSense Ready deverá padronizar os slots publicitários com etiquetas, IDs únicos, comentários HTML, placeholders para espaços vazios, data attributes, CSS unificado e checklist de preparação para Google AdSense.

### M360 Admin

Responsável pela camada administrativa e operacional dentro do WordPress.

Componentes previstos:

- Dashboard;
- Painel de Competições;
- Painel de Widgets;
- Painel de Bolões;
- Painel M360 Ads;
- Painel de Slots;
- Painel de Campanhas;
- Painel de Criativos;
- Logs / Auditoria;
- Reprocessamentos;
- Configurações;
- Integração futura com n8n.

### M360 Infrastructure

Responsável pela base técnica de execução.

Componentes oficiais:

- n8n;
- MariaDB;
- WordPress;
- Elementor;
- Polylang;
- Yoast SEO;
- cache_widgets;
- WP-Cron / CRON;
- REST API;
- Cache WordPress / Hosting;
- Observabilidade;
- Deploy.

## 5. Sprint 9 — Marco arquitetural

A Sprint 9 — M360 Navigation Library é considerada o marco oficial de transição para a plataforma Core UI.

Problema original:

- PT-BR renderizado principalmente pelo tema News Portal;
- EN-US renderizado principalmente via Elementor Theme Builder;
- menus com HTML, CSS, JavaScript e breakpoints diferentes;
- inconsistências de dropdown, responsividade, submenus e navegação contextual;
- crescimento futuro de competições sem base escalável.

Decisão:

Criar a M360 Navigation Library como biblioteca própria de navegação, reutilizando menus do WordPress, mas controlando HTML, CSS, JavaScript, integração Polylang e componentes responsivos por meio da camada M360.

Componentes validados:

- M360 Main Navigation;
- M360 Section Navigation;
- M360 Breadcrumb.

## 6. Atualização Sprint 10 — Dynamic Views

A Sprint 10 iniciou a transição do M360 Core de biblioteca de componentes para camada de renderização dinâmica.

Componentes validados:

- M360 Author Hub;
- M360 Search Results;
- M360 Layout Foundation.

Próxima frente:

- M360 Radar News Engine;
- M360 View Engine;
- M360 Archive Engine;
- M360 Layout Engine.

## 7. Atualização Sprint 11.7 — M360 Ads Pilot

A Sprint 11.7 consolidou o M360 Ads Manager como subsistema funcional da plataforma.

Marco de homologação:

```text
M360 Core v0.4.3.5 — Ads Slot Intent Fallback
```

Resultado:

O portal passou a renderizar inventário publicitário real a partir do M360 Ads Manager, reduzindo a dependência de HTML manual, widgets dispersos e trechos colados em tema/Elementor.

Fluxo validado:

```text
Cadastro de Campanha → Cadastro de Criativo → Vinculação ao Slot → Shortcode/API → Renderização no front-end
```

Inventário validado:

| Slot | Uso | PT-BR | EN-US | Status |
|---|---|---:|---:|---|
| `header-top` | Banner 728x140 | OK | OK | Homologado |
| `content-bottom` | Banner horizontal HTML | OK | OK | Homologado |
| `sidebar-community` | HTML 300x300 | OK | OK | Homologado |
| `sidebar-square` | Imagem 1:1 | OK | OK | Homologado |

Decisão arquitetural:

O M360 Ads Manager passa a ser tratado como base da futura Plataforma Comercial M360 e deverá ser evoluído em documentação própria: `M360_Arquitetura_Ads_Manager_v1.md`.

## 8. Governança oficial

Toda evolução estrutural deve responder:

1. A qual módulo pertence?
2. Existe ADR?
3. Está no roadmap?
4. Existe sprint ou tarefa de execução?
5. Será consolidada em qual release?
6. O módulo afetado foi atualizado?
7. O Documento Mestre precisa ser atualizado?
8. Existe evidência de validação?
9. Existe rollback documentado?

Se qualquer item essencial estiver ausente, a entrega é considerada incompleta do ponto de vista de governança.

## 9. Regra de ouro

O Documento Mestre nunca deve ser tratado como arquivo histórico estático.

Ele é o ponto focal vivo da plataforma. Sempre que a arquitetura muda, este documento deve mudar.
