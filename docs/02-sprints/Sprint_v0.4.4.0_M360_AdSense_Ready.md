# Sprint v0.4.4.0 — M360 AdSense Ready

Status: encerrada e homologada
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Módulo: M360 Advertising
Subsistema: M360 Ads Manager

## 1. Objetivo

Preparar a infraestrutura visual, semântica e técnica dos espaços publicitários do Mengão 360 para futura integração com provedores externos, especialmente Google AdSense, sem alterar a arquitetura consolidada do M360 Ads Manager.

## 2. Base arquitetural

A sprint preserva inventário, campanhas, criativos, shortcodes, API PHP, internacionalização PT-BR/EN-US, compatibilidade com Elementor/News Portal e independência progressiva do tema, conforme o ADR-0007.

## 3. Linha de entregas

| Versão | Entrega | Status |
|---|---|---|
| `0.4.4.0` | Ad Slot Component, labels, data attributes, placeholders e CSS centralizado | Homologação inicial |
| `0.4.4.1` | Inventory Library e Inventory Seeder | Implementada |
| `0.4.4.2` | Context Renderer | Implementada |
| `0.4.4.3` | Inline Ads Engine em artigos | Homologada inicialmente |
| `0.4.4.4` | Archive Ads Engine em listagens M360 | Homologada em PT-BR e EN-US |
| `0.4.4.5` | Universal Slot Renderer | Baseline estável homologada |

## 4. v0.4.4.4 — M360 Archive Ads Engine

### Objetivo

Inserir slots publicitários automaticamente nas listagens controladas pelo M360 Core, sem editar templates do tema News Portal nem estruturas do Elementor.

### Contextos homologados

| Contexto | Slot | Posição padrão | PT-BR | EN-US |
|---|---|---:|---:|---:|
| Search | `search-inline` | após o 3º resultado | OK | OK |
| Category | `category-inline` | após o 3º artigo | OK | OK |
| Tag | `tag-inline` | após o 3º artigo | OK | OK |
| Author | `author-inline` | após o 3º artigo | OK | OK |
| Latest News | `latest-inline` | após o 4º item | OK | OK |

Busca sem resultados utiliza `search-empty`.

### Resultado da homologação

- etiquetas `PUBLICIDADE` e `ADVERTISEMENT` corretas;
- placeholders visíveis sem campanha vinculada;
- layouts preservados em listagens M360;
- nenhuma alteração em templates do tema ou Elementor;
- compatibilidade PT-BR / EN-US validada.

## 5. v0.4.4.5 — M360 Universal Slot Renderer

### Status

Implementação concluída e homologada como baseline estável da linha v0.4.4.x.

### Objetivo

Unificar toda a renderização publicitária do Mengão 360 em uma única camada do M360 Core, fazendo com que Elementor, News Portal, widgets, templates, shortcodes, APIs e componentes internos consumam o mesmo pipeline de slots.

### Princípio arquitetural

```text
Elementor
News Portal
Widgets
Templates
Shortcodes
APIs PHP
Componentes M360
        ↓
M360 Universal Slot Renderer
        ↓
M360 Ad Slot Component
        ↓
Campanha / Criativo / Placeholder
```

O tema e o Elementor permanecem como consumidores e camadas de compatibilidade. A lógica de publicidade pertence ao M360 Core, em aderência ao ADR-0007.

### Escopo aprovado

- novo núcleo `M360_Slot_Renderer`;
- API pública única `m360_render_ad_slot()`;
- compatibilidade com APIs e shortcodes existentes;
- migração interna de Inline Ads Engine, Archive Ads Engine e Context Renderer para o novo núcleo;
- suporte a providers `internal`, `house`, `adsense`, `google-ad-manager`, `affiliate` e `sponsor`;
- hooks de extensão antes e depois da renderização;
- diagnóstico HTML padronizado;
- camada de compatibilidade para widgets, Elementor, News Portal e templates;
- preparação para cache de renderização sem ativar cache comercial prematuramente.

### Hooks previstos

```text
m360_slot_before_render
m360_slot_after_render
m360_slot_provider
m360_slot_campaign
m360_slot_placeholder
```

### Compatibilidade obrigatória

A entrega não poderá quebrar:

- `[m360_ad_slot]`;
- `[m360_ads_slot]`;
- `m360_ads_render_slot()`;
- `m360_ad_slot()`;
- Inline Ads Engine;
- Archive Ads Engine;
- Context Renderer;
- quatro slots piloto homologados;
- internacionalização PT-BR / EN-US.

### Critérios de aceite

- todas as chamadas internas passam pelo Universal Slot Renderer;
- resultado visual permanece compatível com a v0.4.4.4;
- Header, Sidebar, Content Bottom, Inline e Archive continuam operacionais;
- nenhuma regressão no admin do Ads Manager;
- nenhum erro fatal ou aviso PHP relevante;
- integração com Elementor/News Portal preservada;
- API única documentada e disponível para futuras sprints.

## 6. Próxima etapa

Após o encerramento da v0.4.4.5, o projeto seguirá para:

```text
v0.5.x — Plataforma Comercial M360
```

Essa linha evoluirá Campaign Engine, métricas, Dashboard Comercial e provedores externos sem criar pipelines paralelos ao Universal Slot Renderer.

## 7. Fora do escopo

- integração oficial com Google AdSense;
- métricas de impressão/clique;
- rotação avançada;
- Google Ad Manager operacional;
- Dashboard Comercial;
- Marketplace Comercial M360.
