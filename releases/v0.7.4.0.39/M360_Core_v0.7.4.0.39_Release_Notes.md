# M360 Core v0.7.4.0.39

## Nuvem de Tags no Footer

Na quarta coluna do Footer escuro do Elementor, use um widget **Shortcode** com:

```text
[m360_tag_cloud limit="10" show_counts="false" min_size="12" max_size="15" surface="dark" primary="#e31b23" secondary="#b81218"]
```

O bloco ocupa a largura da coluna, mantém o título `Tags` em branco, usa etiquetas claras com borda discreta e aplica o vermelho do portal ao foco e ao hover. Não é necessário adicionar CSS ou script no Footer.

Para o Footer em português ou inglês, o título pode ser sobrescrito explicitamente:

```text
[m360_tag_cloud title="Tags" surface="dark"]
```

O shortcode continua independente dos blocos `[m360_categories]` e `[m360_archives]` e pode ser usado em qualquer coluna ou sidebar.
