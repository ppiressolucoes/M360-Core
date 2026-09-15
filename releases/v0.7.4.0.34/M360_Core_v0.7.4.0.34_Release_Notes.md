# M360 Core v0.7.4.0.34

## Breadcrumb

O `[m360_breadcrumb]` passa a incluir o título da página aberta em páginas institucionais e páginas que não pertencem a um post editorial. Exemplos:

- `Início > Quem Somos`
- `Início > Últimas Notícias`

Posts continuam sem o título duplicado por padrão; para exibi-lo em um post, use `[m360_breadcrumb show_current="true"]`.

## Menu institucional

O menu renderizado por `[m360_section_navigation]` em páginas institucionais agora usa uma grade vertical estável, com botões alinhados e alturas uniformes. Em telas pequenas, os itens passam para uma coluna.

## Homologação

1. Limpe os caches do Elementor, LiteSpeed e CDN.
2. Valide `/quem-somos/`, `/expediente/` e `/ultimas-noticias/`.
3. Confirme a trilha com o nome da página atual.
4. Confirme o alinhamento dos botões institucionais em desktop e mobile.
