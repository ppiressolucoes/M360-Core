# M360 Platform Architecture v2.1 — Master Baseline v0.4.4

Status: oficial e vigente
Projeto: Mengão 360 | DW Esportivo
Produto central: M360 Core
Função: bíblia técnica, arquitetural e operacional da plataforma
Baseline atual: M360 Core v0.4.4.5

## 1. Visão executiva

O Mengão 360 evoluiu de um portal editorial WordPress para uma plataforma esportiva modular baseada em dados, automação editorial, internacionalização, SEO semântico, componentes próprios de interface, produtos de comunidade e infraestrutura publicitária centralizada.

O Documento Mestre é a fonte única de verdade da plataforma. Toda evolução estrutural deve preservar rastreabilidade por ADR, módulo, sprint, release, workflow, homologação e histórico.

A Sprint `v0.4.4.0 — M360 AdSense Ready` foi concluída e homologada até o `M360 Core v0.4.4.5`. O encerramento desta sprint consolida o M360 Core como camada oficial de interface, renderização e preparação para monetização do Mengão 360.

## 2. Princípio central

O Mengão 360 deve ser tratado como plataforma, não apenas como site WordPress.

Isso significa:

- arquitetura modular;
- documentação viva;
- componentes próprios e reutilizáveis;
- independência progressiva do tema;
- governança por decisões arquiteturais;
- evolução por releases homologáveis;
- internacionalização contínua;
- operação rastreável;
- preservação histórica;
- suporte a novas competições, produtos e provedores.

## 3. Decisão arquitetural do M360 Core

Conforme o `ADR-0007 — M360 Core Interface Architecture`, o M360 Core é a camada oficial de interface e evolução da plataforma.

O tema News Portal e o Elementor permanecem como camadas de compatibilidade e composição. Novas funcionalidades visuais, multilíngues, editoriais ou comerciais devem nascer prioritariamente no M360 Core.

```text
WordPress / CMS
      ↓
M360 Core
      ↓
Componentes próprios
      ↓
Tema / Elementor como compatibilidade
      ↓
Front-end PT-BR / EN-US
```

Consequências obrigatórias:

- nenhuma nova regra estrutural deve depender exclusivamente do tema;
- nenhuma nova lógica de publicidade deve ser implementada em HTML disperso;
- componentes devem possuir API, documentação e estratégia de fallback;
- alterações incompatíveis exigem ADR específico;
- toda sprint deve preservar compatibilidade com PT-BR e EN-US.

Documento normativo:

```text
docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md
```

## 4. Arquitetura macro

### 4.1 Dados esportivos

```text
API externa
  ↓
n8n / ETL
  ↓
MariaDB / DW Esportivo
  ↓
Views frontend
  ↓
cache_widgets
  ↓
WordPress / Componentes M360
  ↓
Front-end PT-BR / EN-US
```

### 4.2 Editorial e SEO

```text
n8n / CRON / REST
  ↓
WordPress / Polylang
  ↓
M360 Editorial
  ↓
Semantic Relations / Search Console Ready
  ↓
Front-end e indexação
```

### 4.3 Comunidade

```text
DW Esportivo
  ↓
Bolão / Palpites
  ↓
Rankings / Ligas
  ↓
Convites / Comunidade
  ↓
Mega Bolão 360
```

### 4.4 Publicidade e monetização

```text
Inventário
  ↓
Campanhas
  ↓
Criativos
  ↓
M360 Universal Slot Renderer
  ↓
M360 Ad Slot Component
  ↓
Front-end PT-BR / EN-US
```

O pipeline comercial atual prepara a plataforma para múltiplos providers, sem integrar ainda o código oficial do Google AdSense ou do Google Ad Manager.

## 5. Módulos oficiais

### 5.1 M360 Core

Responsável pela camada própria de interface, renderização e integração dos componentes da plataforma.

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
- M360 Universal Slot Renderer;
- M360 Layout Engine, em evolução futura.

Missão:

Reduzir gradualmente dependências do News Portal e do Elementor, criando uma camada própria, reutilizável, multilíngue e preparada para novas competições, produtos e modelos de monetização.

### 5.2 M360 Editorial

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

### 5.3 M360 Sports Platform

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

### 5.4 M360 Community

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

### 5.5 M360 Advertising

Responsável pela camada comercial, inventário publicitário e preparação para monetização.

Status consolidado:

```text
M360 Ads Manager homologado até M360 Core v0.4.4.5
Sprint v0.4.4.0 — M360 AdSense Ready concluída
```

Componentes oficiais:

- M360 Ads Manager;
- Inventory Library;
- Inventory Seeder;
- Ad Slot Component;
- Context Renderer;
- Inline Ads Engine;
- Archive Ads Engine;
- Universal Slot Renderer;
- Biblioteca de Criativos;
- Campanhas;
- Placeholders multilíngues;
- Checklist AdSense Ready.

Arquitetura funcional consolidada:

```text
Consumer
  ↓
M360 Universal Slot Renderer
  ↓
M360 Ad Slot Component
  ↓
Slot
  ↓
Campanha
  ↓
Criativo
  ↓
Provider / Fallback
  ↓
Front-end
```

Consumers suportados:

- Elementor;
- News Portal;
- widgets;
- templates;
- shortcodes;
- APIs PHP;
- Inline Ads Engine;
- Archive Ads Engine;
- Context Renderer.

API oficial:

```php
echo m360_render_ad_slot('header-top');
```

APIs históricas preservadas:

```php
echo m360_ads_render_slot('header-top');
echo m360_ad_slot('header-top');
```

Shortcodes preservados:

```text
[m360_ad_slot id="header-top"]
[m360_ads_slot id="header-top"]
```

Providers preparados:

- internal;
- house;
- adsense;
- google-ad-manager;
- affiliate;
- sponsor.

Importante:

A preparação para `adsense` e `google-ad-manager` representa compatibilidade arquitetural futura. A integração oficial dos códigos desses provedores não faz parte da baseline v0.4.4.

### 5.6 M360 Admin

Responsável pela camada administrativa e operacional dentro do WordPress.

Componentes:

- Dashboard;
- Painel de Competições;
- Painel de Widgets;
- Painel de Bolões;
- Painel M360 Ads;
- Inventário;
- Campanhas;
- Criativos;
- Checklist AdSense Ready;
- Logs / Auditoria;
- Reprocessamentos;
- Configurações;
- integrações futuras com n8n.

### 5.7 M360 Infrastructure

Responsável pela base técnica de execução.

Componentes oficiais:

- n8n;
- MariaDB;
- WordPress;
- Elementor;
- News Portal;
- Polylang;
- Yoast SEO;
- cache_widgets;
- WP-Cron / CRON;
- REST API;
- Cache WordPress / Hosting;
- GitHub Actions;
- Observabilidade;
- Deploy;
- workflows de build e publicação.

## 6. Baseline comercial v0.4.4

### 6.1 Inventário piloto homologado

| Slot | Uso | PT-BR | EN-US | Status |
|---|---|---:|---:|---|
| `header-top` | Banner superior | OK | OK | Homologado |
| `content-bottom` | Banner horizontal | OK | OK | Homologado |
| `sidebar-community` | HTML 300x300 | OK | OK | Homologado |
| `sidebar-square` | Imagem 1:1 | OK | OK | Homologado |

### 6.2 Inventário dinâmico homologado

| Contexto | Slot | Posição | PT-BR | EN-US |
|---|---|---:|---:|---:|
| Artigo | `article-after-paragraph-2` | após 2º parágrafo | OK | OK |
| Busca | `search-inline` | após 3º resultado | OK | OK |
| Categoria | `category-inline` | após 3º artigo | OK | OK |
| Tag | `tag-inline` | após 3º artigo | OK | OK |
| Autor | `author-inline` | após 3º artigo | OK | OK |
| Últimas Notícias | `latest-inline` | após 4º item | OK | OK |
| Busca vazia | `search-empty` | estado vazio | OK | OK |

### 6.3 Semântica homologada

Todos os slots renderizados pelo pipeline M360 podem fornecer:

- wrapper HTML semântico;
- ID DOM consistente;
- classes por slot, provider, formato e status;
- etiquetas `PUBLICIDADE` e `ADVERTISEMENT`;
- data attributes;
- comentário HTML de diagnóstico;
- placeholder quando não houver campanha;
- responsividade;
- suporte PT-BR / EN-US.

## 7. Encerramento da Sprint v0.4.4.0 — M360 AdSense Ready

Status oficial:

```text
HOMOLOGADA
Baseline: M360 Core v0.4.4.5
```

Linha de entregas concluída:

| Versão | Entrega | Resultado |
|---|---|---|
| `0.4.4.0` | Ad Slot Component, labels, IDs, data attributes, placeholders e CSS | Homologada |
| `0.4.4.1` | Inventory Library e Seeder | Homologada |
| `0.4.4.2` | Context Renderer | Homologada |
| `0.4.4.3` | Inline Ads Engine | Homologada |
| `0.4.4.4` | Archive Ads Engine | Homologada em PT-BR / EN-US |
| `0.4.4.5` | Universal Slot Renderer | Homologada em PT-BR / EN-US |

Critérios de encerramento cumpridos:

- slots padronizados;
- etiquetas automáticas por idioma;
- IDs e atributos semânticos consistentes;
- placeholders operacionais;
- CSS centralizado no Core;
- APIs e shortcodes compatíveis;
- inventário preservado;
- campanhas e criativos preservados;
- Elementor e News Portal preservados;
- homologação visual PT-BR e EN-US;
- build instalável pelo GitHub Actions;
- documentação técnica e release notes publicadas.

A plataforma está tecnicamente preparada para futura integração com Google AdSense sem necessidade de reestruturação do Ads Manager.

## 8. Marcos arquiteturais anteriores

### 8.1 Sprint 9 — M360 Navigation Library

Marco oficial de transição para a plataforma Core UI.

Componentes validados:

- M360 Main Navigation;
- M360 Section Navigation;
- M360 Breadcrumb.

### 8.2 Sprint 10 — Dynamic Views

Iniciou a transição do M360 Core de biblioteca de componentes para camada de renderização dinâmica.

Componentes validados:

- M360 Author Hub;
- M360 Search Results;
- M360 Layout Foundation.

### 8.3 Sprint 11.7 — M360 Ads Pilot

Consolidou o M360 Ads Manager como subsistema funcional da plataforma até a v0.4.3.5.

### 8.4 Sprint v0.4.4.0 — M360 AdSense Ready

Consolidou a infraestrutura semântica, visual e técnica dos anúncios e materializou o Universal Slot Renderer como ponto focal de renderização.

## 9. Próximos passos do projeto

A próxima fase arquitetural é:

```text
v0.5.x — Plataforma Comercial M360
```

Backlog macro previsto:

- Campaign Engine evoluído;
- regras de prioridade comercial;
- rotação de criativos;
- segmentação por contexto, dispositivo e idioma;
- métricas de impressões e cliques;
- auditoria comercial;
- Dashboard Comercial;
- integração oficial com Google AdSense;
- integração operacional com Google Ad Manager;
- House Ads inteligentes;
- Affiliate e Sponsor avançados;
- Marketplace Comercial M360.

Sequência recomendada:

```text
v0.5.0 — Commercial Foundation
v0.5.1 — Campaign Priority
v0.5.2 — Creative Rotation
v0.5.3 — Metrics Foundation
v0.5.4 — Provider Integrations
v0.6.x — AdServer / Analytics / Automação
v1.x   — Marketplace Comercial M360
```

Antes da abertura da v0.5.x, deve ser criado um documento específico de planejamento da Plataforma Comercial, sem reabrir o escopo técnico da baseline v0.4.4.

## 10. Documentos de referência

```text
docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md
docs/00-platform/M360_Arquitetura_Ads_Manager_v1.md
docs/00-platform/M360_Inventory_Library_v1.md
docs/01-modules/M360_Ads_Inline_Engine_v1.md
docs/01-modules/M360_Ads_Archive_Engine_v1.md
docs/01-modules/M360_Universal_Slot_Renderer_v1.md
docs/02-sprints/Sprint_v0.4.4.0_M360_AdSense_Ready.md
docs/03-releases/M360_Release_History_v2.md
docs/04-operations/M360_Core_Plugin_Publication_Workflow_v1.md
```

## 11. Governança oficial

Toda evolução estrutural deve responder:

1. A qual módulo pertence?
2. Existe ADR aplicável?
3. Está no roadmap?
4. Existe sprint ou tarefa de execução?
5. Será consolidada em qual release?
6. O módulo afetado foi atualizado?
7. O Documento Mestre precisa ser atualizado?
8. Existe evidência de validação?
9. Existe rollback documentado?
10. Existe build reproduzível?

Se qualquer item essencial estiver ausente, a entrega é considerada incompleta do ponto de vista de governança.

## 12. Regra de ouro

O Documento Mestre nunca deve ser tratado como arquivo histórico estático.

Ele é o ponto focal vivo da plataforma. Sempre que a arquitetura, baseline, roadmap ou governança mudarem, este documento deve ser atualizado.
