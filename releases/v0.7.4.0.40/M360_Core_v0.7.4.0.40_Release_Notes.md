# M360 Core v0.7.4.0.40

## Nuvem de Tags no Footer

A variante escura agora usa `#191919`, integrando o bloco à superfície do Footer do Mengão 360. A ordem padrão `mixed` alterna tags mais e menos utilizadas, produzindo uma composição visual equilibrada e estável para cache.

Shortcode recomendado para a quarta coluna:

```text
[m360_tag_cloud surface="dark" limit="12" min_size="13" max_size="16" order="mixed" show_counts="false"]
```

Parâmetros:

- `limit`: quantidade máxima de tags;
- `min_size` e `max_size`: intervalo de tamanho em pixels;
- `order="mixed"`: alterna tags mais e menos usadas;
- `order="popular"`: mantém as mais usadas primeiro;
- `order="name"`: ordena alfabeticamente;
- `show_counts`: exibe ou oculta a quantidade de conteúdos.
