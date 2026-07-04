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

### M360 Admin

Responsável pela camada administrativa e operacional dentro do WordPress.

Componentes previstos:

- Dashboard;
- Painel de Competições;
- Painel de Widgets;
- Painel de Bolões;
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

## 7. Governança oficial

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

## 8. Regra de ouro

O Documento Mestre nunca deve ser tratado como arquivo histórico estático.

Ele é o ponto focal vivo da plataforma. Sempre que a arquitetura muda, este documento deve mudar.
