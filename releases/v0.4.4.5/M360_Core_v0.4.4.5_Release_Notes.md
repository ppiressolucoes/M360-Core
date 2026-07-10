# M360 Core v0.4.4.5 — Release Notes

Status: planejamento aprovado / desenvolvimento iniciado
Entrega: M360 Universal Slot Renderer
Projeto: Mengão 360 | DW Esportivo

## 1. Contexto

A v0.4.4.4 foi homologada em PT-BR e EN-US nos contextos Search, Category, Tag, Author e Latest News. A próxima evolução consolida toda a renderização publicitária em uma única camada do M360 Core.

## 2. Objetivo

Criar o M360 Universal Slot Renderer como ponto focal para qualquer origem de publicidade:

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
```

## 3. Escopo aprovado

- classe `M360_Slot_Renderer`;
- API pública `m360_render_ad_slot()`;
- compatibilidade com APIs e shortcodes existentes;
- migração interna de Inline Ads Engine, Archive Ads Engine e Context Renderer;
- suporte preparado para providers `internal`, `house`, `adsense`, `google-ad-manager`, `affiliate` e `sponsor`;
- hooks de extensão antes e depois da renderização;
- diagnóstico HTML padronizado;
- integração compatível com widgets, Elementor, News Portal e templates;
- preparação para cache futuro.

## 4. Hooks previstos

```text
m360_slot_before_render
m360_slot_after_render
m360_slot_provider
m360_slot_campaign
m360_slot_placeholder
```

## 5. Compatibilidade obrigatória

- `[m360_ad_slot]`;
- `[m360_ads_slot]`;
- `m360_ads_render_slot()`;
- `m360_ad_slot()`;
- Inline Ads Engine;
- Archive Ads Engine;
- Context Renderer;
- slots `header-top`, `content-bottom`, `sidebar-community` e `sidebar-square`;
- PT-BR / EN-US.

## 6. Critérios de aceite

- todas as chamadas internas utilizam o Universal Slot Renderer;
- nenhuma regressão visual em relação à v0.4.4.4;
- admin do Ads Manager permanece íntegro;
- Elementor e News Portal continuam compatíveis;
- API pública documentada;
- plugin completo gera artifact instalável;
- homologação aprovada antes da v0.4.4.6.

## 7. Próxima etapa

Após a homologação desta release:

```text
v0.4.4.6 — M360 AdSense Ready Final
```

A v0.4.4.6 será dedicada à auditoria, checklist final e documentação de certificação técnica, sem integrar ainda o código oficial do Google AdSense.
