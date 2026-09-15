# M360 Core v0.7.4.0.42

## Alinhamento da nuvem de Tags no Footer

A variante `surface="dark"` do `[m360_tag_cloud]` não aplica mais margem ou preenchimento no contêiner interno. O título, o sublinhado e a borda esquerda passam a acompanhar os blocos de menus do Footer.

O shortcode utilizado na versão 0.41 permanece válido, sem qualquer alteração no Elementor:

```text
[m360_tag_cloud surface="dark" limit="12" min_size="13" max_size="16" order="mixed" show_counts="false"]
```

O fundo do componente passa a ser transparente, herdando visualmente a superfície `#191919` do Footer. A alteração não afeta a variante clara usada em sidebars.