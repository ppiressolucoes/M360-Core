# M360 Core v0.7.4.0.15 — Locale-aware Editorial Categories

## Widgets editoriais

- O campo de idioma é um dropdown baseado nos idiomas ativos do Polylang.
- As listas de **categorias dos destaques** e **editorias dos cards** exibem
  somente os termos do idioma selecionado.
- A mudança de idioma exige salvar e reabrir a instância para recarregar a
  lista de termos do locale correto.

## Limite de escopo

- Não muda o shortcode ou o conteúdo de nenhuma página automaticamente.
- Para refletir as opções do Dashboard, a página precisa usar o shortcode da
  instância: `[m360_editorial_widget id="..."]`.
