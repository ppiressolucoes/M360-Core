# M360 - Core Editorial v0.7.4.0.31

- fixa automaticamente a barra `.m360-header-topbar` durante a rolagem;
- elimina a necessidade do script HTML de sticky header nos modelos pt-BR e en-US;
- adiciona o seletor inline `[m360_language_navigation]`, com flag e código do idioma ativo;
- conserva o seletor flutuante `[m360_language_switcher]` como componente separado;
- preserva a lupa nativa `[m360_search_toggle]` entregue na versão anterior.

## Migração do header

1. Mantenha a classe CSS `m360-header-topbar` no contêiner vermelho do Elementor.
2. Remova somente o código HTML/JavaScript usado para fixar a barra.
3. Para os controles à direita, use `[m360_language_navigation]` e `[m360_search_toggle]` em widgets de shortcode separados.
