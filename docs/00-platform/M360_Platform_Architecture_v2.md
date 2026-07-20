# M360 Platform Architecture v2.2 — Baseline Estável

Status: oficial e vigente
Projeto: Mengão 360 | DW Esportivo
Produto central: M360 Core
Função: bíblia técnica, arquitetural e operacional da plataforma
Baseline estável: M360 Core v0.6.5.4 — Newsletter Subscription Placement & UX
Próxima linha planejada: v0.7.x — M360 Publisher Platform

## Atualização canônica — Publisher Platform

O `ADR-0008` define a evolução do M360 Core para uma plataforma editorial modular e reutilizável. O Home Editorial será generalizado como `Editorial Layout & Home`, e o Semantic Relations como `Content Discovery & SEO`.

O Portal Mengão 360 permanece como ambiente precursor. O Portal Energia Limpa — PEL será a segunda implementação, adotando capacidades de forma progressiva e sem migração abrupta. DW Esportivo, Bolão, ETLs e regras esportivas permanecem externos ao núcleo portável.

Esta atualização prevalece sobre trechos históricos que apresentem a independência permanente dos dois plugins editoriais.

## 1. Visão executiva

O Mengão 360 evoluiu de portal editorial WordPress para uma plataforma esportiva modular baseada em dados, automação editorial, internacionalização, widgets esportivos, SEO semântico, comunidade e monetização.

Este Documento Mestre é a fonte única de verdade da plataforma. Toda evolução estrutural deve preservar rastreabilidade por ADR, módulo, sprint, release, workflow, homologação e histórico.

A linha `v0.4.4.x — M360 AdSense Ready` foi concluída e a Plataforma Comercial avançou até a baseline homologada `M360 Core v0.5.1`. O próximo ciclo evolui navegação multilíngue, busca e orquestração do cabeçalho.

Documento oficial de abertura:

```text
docs/00-platform/M360_Commercial_Platform_v1.md
```

## 2. Princípio central

O Mengão 360 deve ser tratado como plataforma, não apenas como site WordPress.

Princípios obrigatórios:

- arquitetura modular;
- documentação viva;
- componentes próprios e reutilizáveis;
- independência progressiva do tema;
- governança por decisões arquiteturais;
- evolução por releases homologáveis;
- internacionalização contínua;
- operação rastreável;
- preservação histórica;
- suporte a novas competições, produtos e provedores;
- separação entre decisão de negócio e renderização.

## 3. Decisão arquitetural vigente

O ADR-0007 consolida o M360 Core como camada oficial de interface da Plataforma Mengão 360.

Referência:

```text
docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md
```

Diretriz oficial:

```text
WordPress / Polylang / Tema / Elementor
        ↓
M360 Core
        ↓
Componentes próprios PT-BR / EN-US
        ↓
Front-end Mengão 360
```

O tema News Portal e o Elementor passam a ser tratados como camadas de compatibilidade e composição. A lógica visual, multilíngue e reutilizável deve nascer prioritariamente no M360 Core.

### Internacionalização estrutural da interface

A transição para PT-BR / EN-US revelou que o News Portal e o Elementor não garantiam isolamento completo dos elementos globais por idioma. Em páginas EN-US, headers, footers e menus podiam herdar conteúdo PT-BR.

A solução homologada foi criar modelos M360 independentes de topo e rodapé por idioma, com navegação e elementos globais integralmente traduzidos. O M360 Core passa a resolver a interface correta pelo contexto linguístico e não permite fallback cruzado de menus ou componentes globais entre PT-BR e EN-US.

```text
Idioma da requisição
        ↓
M360 Core Language Context
        ↓
Header + Navigation + Footer do mesmo idioma
        ↓
Experiência integralmente localizada
```

Essa decisão transforma o M360 Core em solução completa de experiência de interface multilíngue, enquanto News Portal e Elementor permanecem somente como camadas de compatibilidade e composição.

## 4. Arquitetura macro

Conforme o `ADR-0007 — M360 Core Interface Architecture`, o M360 Core é a camada oficial de interface e evolução da plataforma.

O News Portal e o Elementor permanecem como camadas de compatibilidade e composição. Novas funcionalidades visuais, multilíngues, editoriais ou comerciais devem nascer prioritariamente no M360 Core.

```text
API externa → n8n → MariaDB / DW Esportivo → Views frontend → cache_widgets → WordPress → M360 Core → Componentes PT-BR / EN-US → Front-end
```

Consequências:

- nenhuma nova regra estrutural deve depender exclusivamente do tema;
- nenhuma nova lógica de publicidade deve ser implementada em HTML disperso;
- componentes devem possuir API, documentação e estratégia de fallback;
- alterações incompatíveis exigem ADR específico;
- toda sprint deve preservar compatibilidade com PT-BR e EN-US;
- toda lógica comercial deve permanecer separada do renderer.

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
M360 Ads Manager → Inventory Library → Slots → Campanhas → Criativos → Ad Slot Component → Front-end PT-BR / EN-US → AdSense Ready / Plataforma Comercial
```

## 5. Módulos oficiais

### Plugins precursores do ecossistema

Três plugins especializados antecederam a consolidação do M360 Core e permanecem ativos em seus próprios domínios:

- **Mengão 360 — Bolão:** gestão de bolões e palpites, com carga e atualização de dados pela API externa e integração DW Esportivo;
- **M360 Home Editorial:** composição independente das Homes, com blocos configuráveis e modelos internacionais de cabeçalho, conteúdo e rodapé;
- **M360 Semantic Relations:** relações semânticas internas, apoio à descoberta e indexação no Google Search Console e renderização de snapshots persistidos sem IA no front-end.

O M360 Core integra esses módulos pela camada de interface, mas não substitui suas regras de negócio. A referência completa está em:

```text
docs/00-platform/M360_Plugin_Ecosystem_v1.md
```

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

### 4.4 Publicidade homologada — baseline v0.4.4

```text
Consumer
  ↓
M360 Universal Slot Renderer
  ↓
M360 Ad Slot Component
  ↓
Slot / Campanha / Criativo
  ↓
Provider / Fallback
  ↓
Front-end PT-BR / EN-US
```

### 4.5 Plataforma Comercial — alvo v0.5.x

```text
Operação comercial
  ↓
M360 Commercial Platform
  ├── Campaign Engine
  ├── Priority Engine
  ├── Rotation Engine
  ├── Segmentation Engine
  ├── Provider Engine
  ├── Metrics Engine
  ├── Revenue Engine
  └── Audit Layer
  ↓
Decisão normalizada de entrega
  ↓
M360 Universal Slot Renderer
  ↓
M360 Ad Slot Component
  ↓
Front-end PT-BR / EN-US
```

Regra estrutural:

> A Plataforma Comercial decide o que entregar; o Universal Slot Renderer continua sendo o único ponto oficial de saída para o front-end.

## 5. Módulos oficiais

### 5.1 M360 Core

Responsável pela camada própria de interface, renderização e integração dos componentes da plataforma.

Componentes principais:

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
- M360 Layout Engine;
- M360 Advertising Components;
- M360 Inventory Library.

### 5.2 M360 Editorial

Reduzir gradualmente dependências do tema News Portal e do Elementor, criando uma camada de interface própria, reutilizável, multilíngue e preparada para novas competições, views, widgets, comunidade e monetização.

Componentes:

- PT-BR / EN-US;
- Polylang Integration;
- M360 Home Editorial;
- M360 Semantic Relations;
- SEO Semântico;
- Search Console Ready;
- links internos contextuais;
- Related News;
- Related Topics;
- Editorial Engine.

### 5.3 M360 Sports Platform

Responsável pelo motor esportivo baseado em dados.

Componentes:

- DW Esportivo;
- API externa;
- ETLs n8n;
- MariaDB;
- Views frontend;
- cache_widgets;
- publicadores HTML;
- Competition Engine;
- Competition Registry;
- widgets esportivos.

Competições consolidadas:

- FIFA World Cup 2026;
- Brasileirão Série A 2026;
- CONMEBOL Libertadores 2026.

### 5.4 M360 Community

Responsável por engajamento e comunidade.

Componentes:

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

Componentes oficiais consolidados:

- M360 Ads Manager;
- M360 Inventory Library;
- M360 Ad Slot Component;
- M360 Ads Context Renderer;
- M360 Ads Inline Engine;
- M360 AdSense Ready.

Componentes homologados:

O M360 Ads Manager constitui a camada oficial de gerenciamento de inventário publicitário do ecossistema Mengão 360. O módulo substitui progressivamente inserções HTML espalhadas pelo tema, Elementor e widgets manuais, centralizando slots, campanhas, criativos e renderização em um único motor.

Arquitetura funcional:

```text
Slot → Campanha → Criativo → Idioma → Dispositivo → Renderer → Front-end
```

Inventário piloto homologado:

```php
echo m360_render_ad_slot('header-top');
```

Inventário em expansão:

```text
article-after-paragraph-2
article-inline-1
search-top
category-inline
tag-inline
author-inline
latest-inline
```

### M360 Admin

Responsável pela camada administrativa e operacional dentro do WordPress.

Componentes previstos:

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

### 5.8 M360 Infrastructure

Responsável pela base técnica de execução.

Componentes:

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
- GitHub Actions;
- Release Build.

Referência complementar:

```text
docs/00-platform/M360_Infrastructure_Architecture_v1.md
```

## 6. Sprint 9 — Marco arquitetural

A Sprint 9 — M360 Navigation Library é considerada o marco oficial de transição para a plataforma Core UI.

Decisão:

Criar a M360 Navigation Library como biblioteca própria de navegação, reutilizando menus do WordPress, mas controlando HTML, CSS, JavaScript, integração Polylang e componentes responsivos por meio da camada M360.

## 7. Atualização Sprint 10 — Dynamic Views

A Sprint 10 iniciou a transição do M360 Core de biblioteca de componentes para camada de renderização dinâmica.

Componentes validados:

- M360 Author Hub;
- M360 Search Results;
- M360 Layout Foundation.

## 8. Atualização Sprint 11.7 — M360 Ads Pilot

A Sprint 11.7 consolidou o M360 Ads Manager como subsistema funcional da plataforma.

Marco de homologação:

```text
M360 Core v0.5.1 — AdSense Approval Readiness
```

Resultado:

O portal passou a renderizar inventário publicitário real a partir do M360 Ads Manager, reduzindo a dependência de HTML manual, widgets dispersos e trechos colados em tema/Elementor.

## 9. Atualização Sprint v0.4.4.x — M360 AdSense Ready / Inventory Engine

A Sprint v0.4.4.x consolida a transição da publicidade para componentes oficiais do M360 Core.

Entregas:

- M360 Ad Slot Component;
- M360 Inventory Library;
- Inventory Seeder;
- Context Renderer;
- Inline Ads Engine;
- Workflow de publicação;
- checklist de release.

## 10. Governança oficial

### Diretriz comercial vigente

A linha `v0.4.4.x — M360 AdSense Ready` está encerrada. A baseline oficial é `v0.5.1 — AdSense Approval Readiness` e a próxima entrega é `v0.5.2 — Multilingual Post Navigation`.

Toda integração comercial deve consumir o `M360 Universal Slot Renderer`. Tema, Elementor, widgets e templates externos não devem implementar pipelines publicitários paralelos.

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
10. Está aderente ao ADR-0007?

Se qualquer item essencial estiver ausente, a entrega é considerada incompleta do ponto de vista de governança.

## 11. Regra de ouro

O Documento Mestre nunca deve ser tratado como arquivo histórico estático.

Ele é o ponto focal vivo da plataforma. Sempre que arquitetura, baseline, roadmap, governança ou linha evolutiva mudarem, este documento deve ser atualizado.
