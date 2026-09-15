# M360 Core v0.7.4.0.36

## Escopo da entrega

Esta versão consolida dois componentes portáteis do Core Editorial: navegação de categorias/arquivos para sidebars e links de redes sociais para headers, footers e blocos laterais.

O ticker de competições esportivas **não faz parte desta entrega nem do M360 Core**. O shortcode `[m360_sports_competitions_ticker]` continua pertencendo ao pacote **M360 Plus Editorial**. O Core não o registra, não o empacota e não o injeta na Home.

## Categorias e arquivos na sidebar

Use o bloco em qualquer sidebar do Elementor ou template M360:

```text
[m360_categories_archives]
```

O alias `[m360_sidebar_categories_archives]` também é aceito. Os títulos padrão são automaticamente `Categorias`/`Categories` e `Arquivos`/`Archives`. Exemplo configurado para o Mengão 360:

```text
[m360_categories_archives categories_limit="8" archives_limit="6" show_counts="true" title_categories="Categorias" title_archives="Arquivos" primary="#d71920" secondary="#b81218"]
```

## Redes sociais

Use o componente em um widget Shortcode, no Theme Builder do Elementor ou em templates M360:

```text
[m360_social_links facebook="https://facebook.com/exemplo" instagram="https://instagram.com/exemplo" youtube="https://youtube.com/@exemplo" whatsapp="https://wa.me/5500000000000"]
```

Os aliases `[m360_social_networks]` e as variantes `variant="boxed|plain|pill"` são suportados. Também é possível informar `x`, `tiktok` e `linkedin`, usar `target="_self"` e personalizar `primary`/`secondary`. Para configuração central por portal, grave um array na opção `m360_social_links` ou use o filtro PHP `m360_social_links`.

## Homologação

1. Limpe os caches do Elementor, LiteSpeed e CDN.
2. Insira `[m360_categories_archives]` em uma sidebar de categoria e valide títulos, contagens e cores em PT-BR/en-US.
3. Insira `[m360_social_links]` no Header e Footer do Mengão 360 e confirme links, foco por teclado e abertura em nova aba.
4. Valide que o ticker esportivo continua sendo fornecido pelo pacote Plus Editorial quando esse pacote estiver ativo.
