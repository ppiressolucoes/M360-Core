# M360 Core v0.5.4 — Header Search & Ad Orchestration

## Objetivo

Transformar o espaço horizontal ao lado da logo em um ponto de entrega útil e previsível, sem áreas vazias no front-end.

## Ordem de entrega

1. Campanha elegível vinculada ao slot `header-top`.
2. Campanha AdSense elegível vinculada ao slot `header-adsense`.
3. Busca M360 multilíngue.
4. Recolhimento completo quando configurado com `fallback="collapse"`.

## Novo componente

```text
[m360_header_orchestrator]
```

O shortcode deve substituir o banner ou formulário isolado atualmente inserido no contêiner do Elementor.

## Administração

- Nova tela `M360 Ads > Header Delivery`.
- Diagnóstico da etapa ativa.
- Identificação dos slots comercial e AdSense.
- Instrução de instalação no Elementor.

## Homologação sugerida

- Campanha ativa em `header-top`: deve exibir a campanha.
- Sem campanha principal e com AdSense ativo em `header-adsense`: deve exibir AdSense.
- Sem entregas elegíveis: deve exibir a busca no idioma corrente.
- Com `fallback="collapse"`: deve recolher o espaço quando não houver anúncio.
- Validar PT-BR e EN-US em desktop e mobile.
