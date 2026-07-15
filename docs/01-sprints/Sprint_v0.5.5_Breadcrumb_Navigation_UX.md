# Sprint v0.5.5 — Breadcrumb Navigation UX

## Objetivo

Consolidar o `[m360_breadcrumb]` como componente oficial de localização e hierarquia do portal, independente do tema e do Elementor, preservando PT-BR, EN-US, SEO, acessibilidade e responsividade.

## Escopo entregue

- hierarquia de páginas ancestrais;
- hierarquia de categorias ancestrais e categoria primária do Yoast quando disponível;
- contextos para post, página, busca, categoria, tag, autor, data, arquivo, tipo de conteúdo e 404;
- início resolvido pelo idioma atual do Polylang;
- ícone de início, separadores vetoriais e destaque discreto do item atual;
- foco visível, `aria-label`, lista ordenada e `aria-current`;
- rolagem horizontal controlada em mobile e truncamento visual de títulos longos;
- `BreadcrumbList` em JSON-LD emitido uma única vez por requisição;
- atributo `schema="false"` e filtro `m360_breadcrumb_schema_enabled` para integrações especializadas;
- filtro `m360_breadcrumb_primary_category_id` para definição externa da categoria editorial principal.

## Contrato preservado

```text
[m360_breadcrumb]
[m360_breadcrumb schema="false"]
```

## Critérios de aceite

- trilhas corretas em posts, páginas hierárquicas, busca, categoria, tag, autor e arquivos por data;
- textos e página inicial corretos em PT-BR e EN-US;
- título longo não rompe o layout em desktop ou mobile;
- navegação completa por teclado e identificação adequada por leitor de tela;
- JSON-LD válido, sem duplicação pelo próprio componente;
- ausência de regressão nos shortcodes e Dynamic Views existentes.

## Próxima linha

`v0.6.0 — M360 Privacy & Consent Foundation`, conforme o escopo híbrido registrado no roadmap oficial.
