# M360 Core v0.7.4.0.32

## Correção

O shortcode `[m360_language_navigation]` passa a renderizar no Elementor Theme Builder mesmo quando o objeto usado na prévia não possui uma tradução publicada no Polylang.

## Comportamento

- Em páginas e notícias com tradução publicada, o botão aponta para a tradução correspondente.
- Na prévia de templates ou em conteúdo sem tradução vinculada, o botão inline aponta para a página inicial do outro idioma.
- O botão flutuante `[m360_language_switcher]` mantém o comportamento anterior e permanece oculto em conteúdo singular sem tradução publicada.

## Homologação sugerida

1. Abra o template de cabeçalho no Elementor Theme Builder.
2. Insira `[m360_language_navigation]` em um widget Shortcode.
3. Confirme a renderização da bandeira, do código do idioma ativo e da seta.
4. Publique e valide uma página com tradução vinculada e outra sem tradução.
5. Limpe o cache do Elementor, do LiteSpeed e da CDN antes da validação pública.
