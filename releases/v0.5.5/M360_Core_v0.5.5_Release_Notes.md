# M360 Core v0.5.5 — Breadcrumb Navigation UX

## Resumo

Evolução do breadcrumb oficial do M360 Core com hierarquia editorial real, internacionalização, acessibilidade, responsividade e dados estruturados.

## Principais entregas

- breadcrumb mobile sem barra de rolagem horizontal, com quebra responsiva do item atual;
- hierarquia de páginas e categorias;
- categoria primária integrada ao Yoast quando disponível;
- suporte a todos os contextos públicos relevantes do portal;
- página inicial localizada pelo Polylang;
- experiência visual refinada em desktop e mobile;
- `BreadcrumbList` JSON-LD com emissão única;
- filtros de integração e opção de desativar o schema do shortcode;
- compatibilidade integral com `[m360_breadcrumb]`.

## Instalação

O pacote deve preservar a pasta raiz canônica:

```text
m360-core/m360-core.php
```

## Homologação recomendada

1. Validar post PT-BR e EN-US.
2. Validar página institucional com e sem hierarquia.
3. Validar categoria, tag, autor, busca e arquivo por data.
4. Validar título longo em desktop e mobile.
5. Validar foco por teclado e leitor de tela.
6. Validar `BreadcrumbList` em ferramenta de resultados avançados.
7. Limpar caches do LiteSpeed, CDN e navegador após a atualização.

## Roadmap

A próxima linha planejada é `v0.6.0 — M360 Privacy & Consent Foundation`.
