# M360 Core v0.7.1.10 — Editorial Widgets UX & Carousel

## Correcoes da homologacao

- submenu exclusivo `M360 Platform > Widgets editoriais`;
- cadastro em acordeao retraido;
- lista compacta de instancias com shortcode, edicao e exclusao;
- editorias em dropdown multiplo com checkboxes;
- formulario de edicao aberto somente ao selecionar `Editar`;
- modelo #5 com trilho continuo de ate nove noticias em retrato;
- navegacao de um card por vez, contador de posicao, autoplay e controles manuais;
- tres cards visiveis no desktop, dois no tablet e um no mobile;
- reinicializacao do carrossel no preview do widget Shortcode do Elementor.

## Modelo #5

O modelo continua consultando nove noticias. A versao anterior dividia o resultado em paginas de tres, fazendo o componente parecer um grid do modelo #4. Agora todos os posts permanecem no mesmo trilho e os controles percorrem os nove itens.

## Preview no Elementor

Insira o shortcode em um widget `Shortcode` do Elementor. O Core carrega CSS e JavaScript no modo Preview e reinicializa o componente quando o Elementor renderiza novamente o widget.

```text
[m360_editorial_widget id="latest-news-home-en"]
```

## Rollback

Reinstalar o pacote canonico v0.7.1.9. As instancias usam o mesmo schema de configuracao e permanecem compativeis. Nenhuma pagina ou shortcode e alterado automaticamente.
