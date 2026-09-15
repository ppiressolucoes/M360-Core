# M360 Core v0.7.4.0.38

## Nuvem de Tags

Use a nuvem de tags como um bloco separado no Elementor ou em qualquer template que processe shortcodes:

```text
[m360_tag_cloud]
```

O alias `[m360_tags]` também é aceito. As tags são ordenadas por utilização, exibidas como etiquetas com borda e recebem tamanho proporcional entre `min_size` e `max_size`.

```text
[m360_tag_cloud limit="24" show_counts="false" min_size="13" max_size="19" primary="#d71920" secondary="#b81218"]
```

O bloco usa automaticamente o título `Tags` em PT-BR e en-US. O visual segue o mesmo padrão de Categorias e Arquivos: fundo branco, título preto, sublinhado vermelho e cores configuráveis por portal.

## Composição sugerida da sidebar

```text
[m360_categories]
[m360_archives]
[m360_tag_cloud]
```

Como cada shortcode gera seu próprio bloco, outros componentes podem ser inseridos entre eles sem alterar o Core.
