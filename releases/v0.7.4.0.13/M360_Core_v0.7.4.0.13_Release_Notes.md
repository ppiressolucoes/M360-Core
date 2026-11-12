# M360 Core v0.7.4.0.13 — Newsroom Source Controls

## Objetivo

Torna o cadastro do Newsroom compreensível e determinístico, separando a
origem do carrossel de destaque da origem dos cards laterais.

## Alterações

- Novo seletor de **categoria dos destaques** no cadastro do widget Newsroom.
- A **tag dos destaques** permanece opcional e tem prioridade sobre a categoria.
  Ela deve existir no WordPress e estar atribuída aos posts do idioma escolhido.
- As editorias principais agora são identificadas como **Editorias dos cards**
  quando o modelo selecionado é Newsroom.
- O campo de quantidade passa a ser identificado como **Slides em destaque**
  no Newsroom; o campo de cards declara que quatro são visíveis e de cinco a
  oito habilitam a alternância lateral.
- Instruções no painel explicam que cards e destaques dependem de posts
  publicados no locale informado.

## Compatibilidade

- Instâncias já cadastradas continuam válidas. Sem categoria de destaque,
  mantém-se o comportamento anterior do shortcode.
- Não altera posts, categorias, tags, Elementor, menus ou tema ativo.
