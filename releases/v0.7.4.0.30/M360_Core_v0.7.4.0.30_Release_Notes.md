# M360 - Core Editorial v0.7.4.0.30

- corrige a quebra visual observada após a versão 0.7.4.0.29;
- carrega o botão de voltar ao topo com CSS e JavaScript isolados, sem ativar estilos gerais do cabeçalho;
- restaura o seletor de idioma ao fluxo automático anterior do Core, sem injeção PHP adicional no footer;
- adiciona a lupa de pesquisa nativa pelo shortcode `[m360_search_toggle]`;
- mantém o formulário, destino, textos e acessibilidade de pesquisa sob controle do Core para pt-BR e en-US.

## Migração da lupa

Substitua o elemento HTML da lupa no cabeçalho por um widget de shortcode contendo apenas:

```text
[m360_search_toggle]
```

Não mantenha o script HTML antigo junto ao shortcode.
