# Sprint v0.4.4.0 — M360 AdSense Ready

Status: implementação inicial
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Módulo: M360 Advertising
Subsistema: M360 Ads Manager

## 1. Objetivo

Preparar a infraestrutura visual, semântica e técnica dos espaços publicitários do Mengão 360 para futura avaliação e integração com provedores externos, especialmente Google AdSense, sem alterar a arquitetura consolidada do M360 Ads Manager.

Esta sprint não integra o Google AdSense. Ela apenas deixa o renderer de slots tecnicamente preparado para uma futura camada de monetização externa.

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

Cada slot passa a ser renderizado por um wrapper semântico único:

```html
<section
  id="m360-ad-slot-header-top"
  class="m360-ad m360-ad-slot m360-ad-slot--header-top"
  data-m360-ad-slot="header-top"
  data-m360-ad-provider="internal"
  data-m360-ad-format="leaderboard"
  data-m360-ad-lang="pt-br"
  data-m360-ad-status="filled"
  aria-label="PUBLICIDADE"
>
  <!-- M360 ADS SLOT: header-top | provider: internal | format: leaderboard | lang: pt-br | status: filled -->
  <span class="m360-ad-slot__label">PUBLICIDADE</span>
  <div class="m360-ad-slot__content">...</div>
</section>
```

### 3.2 Labels automáticos

- `PUBLICIDADE` para PT-BR;
- `ADVERTISEMENT` para EN-US.

As labels são neutras e não incentivam cliques.

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

### 3.5 Placeholders

Quando não houver campanha ativa ou criativo elegível, o slot exibe placeholder visual discreto e preserva o layout.

Mensagem PT-BR:

```text
Espaço publicitário disponível.
```

Mensagem EN-US:

```text
Advertising space available.
```

### 3.6 CSS unificado

O CSS da camada publicitária fica centralizado em:

```text
plugin/assets/css/m360-ads.css
```

### 3.7 Checklist administrativo

Foi adicionada a tela:

```text
M360 Ads → AdSense Ready
```

A tela valida conceitualmente:

- ID DOM único;
- labels PT/EN;
- data attributes;
- placeholders;
- providers preparados;
- shortcode/API.

## 4. Fora do escopo

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

Esses itens permanecem previstos para a Plataforma Comercial M360 v0.5.x.

## 5. Critérios de aceite

A sprint será considerada concluída quando:

- todos os slots usarem o wrapper semântico padrão;
- labels forem renderizadas automaticamente conforme idioma;
- slots possuírem IDs e data attributes consistentes;
- placeholders forem exibidos em slots vazios;
- CSS dos anúncios estiver centralizado no Core;
- checklist AdSense Ready estiver disponível no painel;
- shortcodes e API PHP existentes continuarem compatíveis.

## 6. Arquivos alterados nesta etapa

- `plugin/m360-core.php`;
- `plugin/includes/ads/class-m360-ad-slot-component.php`;
- `plugin/includes/ads/class-m360-ads-admin.php`;
- `plugin/assets/css/m360-ads.css`.
