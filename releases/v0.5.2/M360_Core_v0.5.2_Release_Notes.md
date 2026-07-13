# M360 Core v0.5.2 — Multilingual Post Navigation

## Objetivo

Entregar um componente próprio de informações individuais do post, multilíngue e independente do widget Meta Data do Elementor.

## Novo shortcode

```text
[m360_post_info]
```

O componente identifica automaticamente o post atual e o idioma ativo pelo Polylang, com fallback para o locale do WordPress.

## Informações disponíveis

- autor, avatar e link para a página do autor;
- categoria principal e respectivo link;
- data vinculada ao arquivo diário e horário da publicação;
- última atualização, exibida somente quando posterior à publicação;
- traduções próprias para PT-BR e EN-US.

## Personalização

Cada informação pode ser ocultada individualmente:

```text
[m360_post_info avatar="false" category="false" time="false"]
```

Também é possível informar um post específico com `id` e acrescentar uma classe visual com `class`.

## Critérios de homologação

- renderizar corretamente em um post PT-BR;
- renderizar `By`, `at` e `Updated ... ago` em um post EN-US;
- validar links de autor e categoria;
- validar o recorte circular do avatar e o link da data para os posts do mesmo dia;
- confirmar que a atualização não aparece quando publicação e modificação são equivalentes;
- validar quebra responsiva e navegação por teclado em desktop e mobile;
- substituir o widget Meta Data do Elementor por `[m360_post_info]` apenas no template de homologação.

## Compatibilidade

Nenhum shortcode existente foi alterado. A migração dos templates Elementor pode ser gradual e reversível.
