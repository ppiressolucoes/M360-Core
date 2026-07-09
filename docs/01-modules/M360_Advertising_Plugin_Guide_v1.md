# M360 Advertising Plugin Guide v1

Status: oficial em preparação
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Módulo: M360 Advertising
Versão de referência: M360 Core v0.4.4.0

## 1. Objetivo

Documentar o uso operacional do módulo M360 Advertising dentro do plugin M360 Core, com foco no M360 Ads Manager e na preparação AdSense Ready.

## 2. Responsabilidade do módulo

O M360 Advertising centraliza a renderização de espaços publicitários do portal Mengão 360.

Ele substitui gradualmente:

- banners manuais no tema;
- HTML solto em widgets;
- scripts dispersos em Elementor;
- códigos comerciais sem rastreabilidade.

## 3. Componentes principais

### 3.1 Slots

Representam espaços publicitários fixos ou contextuais.

Slots homologados nesta fase:

| Slot | Uso |
|---|---|
| `header-top` | topo do portal |
| `content-bottom` | fim de conteúdo/editorial |
| `sidebar-community` | sidebar comunidade |
| `sidebar-square` | sidebar quadrado |

### 3.2 Campanhas

Representam uma ação publicitária, institucional ou comercial.

Tipos suportados:

- `image`;
- `html`;
- `adsense`;
- `gam`;
- `house`;
- `affiliate`;
- `sponsor`.

### 3.3 Criativos

Representam variações visuais ou técnicas vinculadas a uma campanha.

Podem ter:

- imagem;
- HTML;
- script/markup administrado;
- idioma;
- dispositivo;
- dimensão.

## 4. Shortcode

Uso padrão:

```text
[m360_ad_slot id="header-top"]
```

Alias compatível:

```text
[m360_ads_slot id="header-top"]
```

Com fallback customizado:

```text
[m360_ad_slot id="sidebar-square" fallback="Espaço comercial disponível"]
```

## 5. API PHP

Uso padrão:

```php
echo m360_ads_render_slot('header-top');
```

Alias compatível:

```php
echo m360_ad_slot('header-top');
```

Com argumentos:

```php
echo m360_ads_render_slot('content-bottom', [
    'class' => 'minha-classe-extra',
    'fallback' => 'Espaço publicitário disponível.',
]);
```

## 6. Estrutura HTML renderizada

A partir da versão `0.4.4.0`, todo slot usa wrapper semântico:

```html
<section
  id="m360-ad-slot-header-top"
  class="m360-ad m360-ad-slot m360-ad-slot--header-top m360-ad-slot--provider-internal m360-ad-slot--format-leaderboard m360-ad-slot--status-filled"
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

## 7. Labels automáticas

| Idioma | Label |
|---|---|
| PT-BR | `PUBLICIDADE` |
| EN-US | `ADVERTISEMENT` |

As labels são renderizadas automaticamente conforme idioma detectado pelo Polylang ou locale do WordPress.

## 8. Providers preparados

| Provider | Uso |
|---|---|
| `internal` | campanhas internas, HTML, imagem e script administrado |
| `adsense` | preparação futura para Google AdSense |
| `google-ad-manager` | preparação futura para Google Ad Manager |
| `house` | campanhas próprias |
| `affiliate` | afiliados |
| `sponsor` | patrocinadores |

## 9. CSS

Arquivo central:

```text
plugin/assets/css/m360-ads.css
```

Classes principais:

- `.m360-ad`;
- `.m360-ad-slot`;
- `.m360-ad-slot__label`;
- `.m360-ad-slot__content`;
- `.m360-ad-slot__placeholder`;
- `.m360-ad__image`;
- `.m360-ad__html`;
- `.m360-ad__script`.

## 10. Tela AdSense Ready

Caminho no WordPress:

```text
M360 Ads → AdSense Ready
```

A tela apresenta um checklist técnico por slot:

- ID DOM;
- label PT/EN;
- data attributes;
- placeholder;
- provider ready;
- shortcode/API.

## 11. Limites da sprint v0.4.4.0

A versão `0.4.4.0` não faz integração real com Google AdSense.

Ficam fora:

- código AdSense real;
- configuração de conta Google;
- aprovação de site pelo Google;
- métricas de impressão/clique;
- rotação comercial;
- dashboard comercial.

## 12. Validação mínima

Validar:

```text
[m360_ad_slot id="header-top"]
[m360_ad_slot id="content-bottom"]
[m360_ad_slot id="sidebar-community"]
[m360_ad_slot id="sidebar-square"]
```

Em PT-BR e EN-US.

Critérios:

- renderização sem erro fatal;
- label correta;
- placeholder em slot vazio;
- imagem/HTML/script preservado quando houver criativo ativo;
- classes e data attributes presentes;
- layout responsivo preservado.
