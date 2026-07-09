# Sprint v0.4.4.0 — M360 AdSense Ready

Status: implementação funcional inicial
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Módulo: M360 Advertising
Subsistema: M360 Ads Manager

## 1. Objetivo

Preparar a infraestrutura visual, semântica e técnica dos espaços publicitários do Mengão 360 para futura avaliação e integração com provedores externos, especialmente Google AdSense, sem alterar a arquitetura consolidada do M360 Ads Manager.

Esta sprint não integra o Google AdSense. Ela deixa o renderer de slots tecnicamente preparado para uma futura camada de monetização externa e inicia a primeira entrega funcional visível no front-end.

## 2. Base arquitetural preservada

A sprint preserva:

- inventário de slots;
- campanhas;
- biblioteca de criativos;
- renderização por shortcode;
- API PHP `m360_ads_render_slot()`;
- fallback por idioma;
- fallback por intenção/formato;
- compatibilidade com Elementor;
- compatibilidade com tema News Portal;
- independência de tema.

## 3. Entregas técnicas iniciais

### 3.1 M360 Ad Slot Component

Cada slot passa a ser renderizado por um wrapper semântico único com label, data attributes, provider, formato, idioma e status.

### 3.2 Labels automáticos

- `PUBLICIDADE` para PT-BR;
- `ADVERTISEMENT` para EN-US.

### 3.3 Data attributes padronizados

- `data-m360-ad-slot`;
- `data-m360-ad-provider`;
- `data-m360-ad-format`;
- `data-m360-ad-lang`;
- `data-m360-ad-status`;
- `data-m360-ad-width` quando disponível;
- `data-m360-ad-height` quando disponível.

### 3.4 Providers preparados

- `internal`;
- `adsense`;
- `google-ad-manager`;
- `house`;
- `affiliate`;
- `sponsor`.

## 4. Primeira entrega de código — Inventory Seeder

Versão técnica:

```text
M360 Core v0.4.4.1
```

Entrega:

- criação da classe `M360_Ads_Inventory_Library`;
- registro oficial dos slots da M360 Inventory Library no código;
- integração da biblioteca ao runtime do M360 Core;
- atualização do schema do Ads Manager para `0.4.4.1`;
- sincronização automática dos slots durante instalação/upgrade;
- preservação do estado `is_active` de slots já existentes;
- criação automática de novos slots como ativos;
- manutenção do inventário piloto e vínculos de campanha já homologados.

## 5. Segunda entrega de código — Context Renderer

Versão técnica:

```text
M360 Core v0.4.4.2
```

Entrega:

- criação da classe `M360_Ads_Context_Renderer`;
- renderização de slots por contexto lógico da Inventory Library;
- detecção automática de contexto WordPress: home, post, search, category, tag, author e archive;
- shortcode `[m360_ad_context]`;
- alias `[m360_ads_context]`;
- API PHP `m360_ads_render_context()`;
- API PHP `m360_ads_render_position()`;
- renderização por posição dentro do contexto.

## 6. Terceira entrega de código — Inline Ads Engine

Versão técnica:

```text
M360 Core v0.4.4.3
```

Entrega funcional visível:

- criação da classe `M360_Ads_Inline_Engine`;
- registro automático no filtro `the_content`;
- inserção automática do slot `article-after-paragraph-2` após o 2º parágrafo de posts individuais;
- execução apenas no loop principal, query principal e posts individuais;
- proteção contra admin, feed, AJAX e REST;
- filtro global `m360_ads_inline_enabled`;
- filtro de placements `m360_ads_inline_article_placements`;
- wrapper `.m360-inline-ad`;
- CSS mínimo de espaçamento no `m360-ads.css`.

Resultado esperado no front-end:

```text
Parágrafo 1
Parágrafo 2
[M360 Ad Slot: article-after-paragraph-2]
Parágrafo 3
```

## 7. Fora do escopo

Permanecem fora desta sprint:

- integração oficial com Google AdSense;
- código real de anúncios AdSense;
- estatísticas de impressões;
- estatísticas de cliques;
- rotação avançada de campanhas;
- priorização comercial;
- Google Ad Manager operacional;
- Dashboard Comercial;
- Marketplace Comercial M360.

## 8. Critérios de aceite

A sprint será considerada concluída quando:

- todos os slots usarem o wrapper semântico padrão;
- labels forem renderizadas automaticamente conforme idioma;
- slots possuírem IDs e data attributes consistentes;
- placeholders forem exibidos em slots vazios;
- CSS dos anúncios estiver centralizado no Core;
- checklist AdSense Ready estiver disponível no painel;
- shortcodes e API PHP existentes continuarem compatíveis;
- Inventory Seeder cadastrar todos os slots oficiais sem duplicidade;
- slots desativados manualmente não forem reativados pelo upgrade;
- Context Renderer renderizar slots por contexto sem exigir alteração de templates;
- Inline Ads Engine inserir `article-after-paragraph-2` após o segundo parágrafo em posts individuais.

## 9. Arquivos alterados nesta etapa

- `plugin/m360-core.php`;
- `plugin/includes/class-m360-core.php`;
- `plugin/includes/ads/class-m360-ads-inventory-library.php`;
- `plugin/includes/ads/class-m360-ads-context-renderer.php`;
- `plugin/includes/ads/class-m360-ads-inline-engine.php`;
- `plugin/includes/ads/class-m360-ads-db.php`;
- `plugin/includes/ads/class-m360-ad-slot-component.php`;
- `plugin/includes/ads/class-m360-ads-admin.php`;
- `plugin/assets/css/m360-ads.css`.
