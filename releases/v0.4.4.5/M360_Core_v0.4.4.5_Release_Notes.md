# M360 Core v0.4.4.5 — Release Notes

Status: baseline estável homologada em PT-BR e EN-US
Entrega: M360 Universal Slot Renderer
Projeto: Mengão 360 | DW Esportivo

## 1. Contexto

A v0.4.4.4 foi homologada em PT-BR e EN-US nos contextos Search, Category, Tag, Author e Latest News. A v0.4.4.5 consolida toda a renderização publicitária em uma única camada do M360 Core.

## 2. Implementado

- classe `M360_Slot_Renderer`;
- API pública `m360_render_ad_slot()`;
- APIs históricas redirecionadas para o renderer universal;
- shortcodes históricos redirecionados para o renderer universal;
- Inline Ads Engine migrado;
- Archive Ads Engine migrado;
- Context Renderer migrado;
- normalização de contexto, provider, idioma, device e origem;
- hooks de extensão;
- contrato de cache preparado, ainda desativado;
- compatibilidade com Elementor, News Portal, widgets e templates.

## 3. Pipeline

```text
Elementor / News Portal / Widgets / Templates / Shortcodes / APIs
                              ↓
                  M360 Universal Slot Renderer
                              ↓
                    M360 Ad Slot Component
                              ↓
                Campaign / Creative / Placeholder
```

## 4. API oficial

```php
echo m360_render_ad_slot('header-top');
```

Compatibilidade preservada:

```php
m360_ads_render_slot();
m360_ad_slot();
```

```text
[m360_ad_slot id="header-top"]
[m360_ads_slot id="header-top"]
```

## 5. Hooks disponíveis

```text
m360_slot_render_args
m360_slot_campaign
m360_slot_provider
m360_slot_placeholder
m360_slot_before_render
m360_slot_after_render
```

A seleção comercial forçada e priorização de campanhas permanecem fora do escopo até a Plataforma Comercial v0.5.x.

## 6. Critérios de homologação

- plugin ativa sem erro fatal;
- quatro slots piloto permanecem operacionais;
- Inline Ads Engine permanece operacional;
- Search, Category, Tag, Author e Latest News permanecem operacionais;
- PT-BR e EN-US preservados;
- placeholders preservados;
- shortcodes históricos preservados;
- APIs históricas preservadas;
- nova API `m360_render_ad_slot()` operacional;
- nenhuma regressão no Elementor ou News Portal.

## 7. Build de publicação

Workflow:

```text
Build M360 Core Plugin ZIP
```

Inputs:

```text
source_ref: sprint/v0.4.4.0-adsense-ready
version: 0.4.4.5
```

Artifact esperado:

```text
m360-core-v0.4.4.5.zip
```

## 8. Próxima etapa

Próxima linha evolutiva:

```text
v0.5.x — Plataforma Comercial M360
```
