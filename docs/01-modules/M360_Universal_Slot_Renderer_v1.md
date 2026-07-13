# M360 Universal Slot Renderer v1

Status: implementação funcional inicial
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Módulo: M360 Advertising
Versão de referência: M360 Core v0.4.4.5

## 1. Objetivo

O M360 Universal Slot Renderer é a fachada oficial de renderização publicitária do M360 Core. Ele unifica chamadas provenientes de shortcodes, APIs PHP, widgets, Elementor, News Portal, templates e engines internas.

## 2. API oficial

```php
echo m360_render_ad_slot('header-top');
```

A API aceita argumentos:

```php
echo m360_render_ad_slot('sidebar-community', [
    'context' => 'home',
    'class' => 'custom-class',
    'fallback' => 'Espaço publicitário disponível.',
    'provider' => 'internal',
]);
```

## 3. Compatibilidade

Continuam disponíveis:

```php
m360_ad_slot();
m360_ads_render_slot();
```

Shortcodes preservados:

```text
[m360_ad_slot id="header-top"]
[m360_ads_slot id="header-top"]
```

Todas essas entradas passam pelo `M360_Slot_Renderer`.

## 4. Pipeline

```text
Consumers
  ↓
M360_Slot_Renderer
  ↓
M360_Ad_Slot_Component
  ↓
Slot / Campaign / Creative / Placeholder
```

O componente homologado continua responsável por seleção de campanha, criativo, idioma, device, fallback e wrapper semântico.

## 5. Engines migradas

- Inline Ads Engine;
- Archive Ads Engine;
- Context Renderer;
- APIs históricas;
- shortcodes de slots.

## 6. Hooks

```text
m360_slot_render_args
m360_slot_campaign
m360_slot_provider
m360_slot_placeholder
m360_slot_before_render
m360_slot_after_render
```

O hook `m360_slot_campaign` expõe metadados de intenção para extensões futuras. A priorização comercial e seleção forçada de campanha permanecem fora do escopo até a Plataforma Comercial v0.5.x.

## 7. Providers reconhecidos

- `internal`;
- `house`;
- `adsense`;
- `google-ad-manager`;
- `affiliate`;
- `sponsor`.

## 8. Cache

O argumento `cache` já faz parte do contrato da API, mas o cache de renderização não é ativado nesta versão. A decisão evita conteúdo publicitário obsoleto antes da definição da política comercial de invalidação.

## 9. Critérios de homologação

- quatro slots piloto preservados;
- Inline Ads Engine preservado;
- Search, Category, Tag, Author e Latest News preservados;
- etiquetas PT-BR / EN-US corretas;
- placeholders corretos;
- shortcodes históricos preservados;
- APIs históricas preservadas;
- nova API `m360_render_ad_slot()` operacional;
- nenhuma regressão no Elementor ou News Portal.
