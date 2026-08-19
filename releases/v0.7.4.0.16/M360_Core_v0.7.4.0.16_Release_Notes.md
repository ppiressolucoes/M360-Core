# M360 Core v0.7.4.0.16 — Newsroom Desktop Card Grid

## Correção

O carrossel de cards do Newsroom calculava a quantidade visível usando a
largura da coluna lateral. Em telas desktop essa coluna é naturalmente menor
que 560px, fazendo o Core mostrar um único card indevidamente.

Agora o breakpoint usa a largura da janela:

- desktop: quatro cards, em grade 2×2;
- tablet: dois cards;
- mobile: um card.

Não há mudança na consulta editorial: os cards continuam vindo das categorias
selecionadas para a instância e permanecem independentes dos destaques.
