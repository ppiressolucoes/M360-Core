# M360 Core v0.7.4.0.37

## Categorias e Arquivos separados

Os dois componentes podem ser posicionados individualmente no Elementor, permitindo inserir Newsletter, Ads ou outro conteúdo entre eles:

```text
[m360_categories]
[m360_archives]
```

Cada bloco possui fundo branco, título preto e linha inferior na cor primária do portal. Os títulos alternam automaticamente entre `Categorias`/`Categories` e `Arquivos`/`Archives` conforme o idioma atual.

Parâmetros comuns: `limit`, `title`, `show_counts`, `primary` e `secondary`.

```text
[m360_categories limit="8" show_counts="true" primary="#d71920" secondary="#b81218"]
[m360_archives limit="6" show_counts="false" primary="#d71920" secondary="#b81218"]
```

Os shortcodes combinados `[m360_categories_archives]` e `[m360_sidebar_categories_archives]` permanecem ativos para compatibilidade, mas os novos templates devem usar os blocos separados.

## Tipografia editorial configurável

Acesse **M360 Core > Editorial > Tipografia editorial** para definir:

- família tipográfica;
- tamanho dos títulos de seção;
- tamanho do título do destaque principal;
- tamanho dos títulos de destaque;
- tamanho dos títulos de cards e listas;
- tamanho dos resumos;
- tamanho de data e autor.

As configurações são aplicadas aos shortcodes editoriais nativos e às instâncias `[m360_editorial_widget]`, inclusive Newsroom. Os valores ficam no contrato portátil `m360_editorial_settings.typography` e usam pixels para não encolher quando o tema ativo define uma raiz tipográfica pequena.

## Homologação

1. Substitua o bloco combinado da sidebar por `[m360_categories]` e `[m360_archives]` em widgets separados.
2. Confirme fundo branco, título preto e sublinhado vermelho em desktop e mobile.
3. Ajuste a tipografia no painel Editorial e valide Newsroom, modelos #1 a #5, resumos, datas e autores.
4. Limpe os caches do Elementor, LiteSpeed e CDN antes da validação pública.
