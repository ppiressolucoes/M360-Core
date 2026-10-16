# Homologação PEL — Home, busca e widgets (0.7.4.0.56)

1. Atualize em staging e confirme que a Home pt-BR continua no tema. Não ative a Home do Core antes de revisar os widgets e o menu de cada idioma.
2. Em **M360 Dashboard > Widgets editoriais**, edite `home-en-featured-1`. A lista de editorias deve incluir as categorias en-US e manter `Regulation` selecionada. Acrescente outra categoria, salve, reabra e confira a persistência; remova-a e salve novamente.
3. Em **Site Profile**, configure `pt-BR` e `en-US` separadamente: menu e IDs dos widgets na ordem editorial. Ative primeiro en-US em staging. O Core exige ao menos um widget válido do idioma para assumir a Home. Após homologação, ative pt-BR.
4. No cabeçalho Elementor, substitua o HTML/JavaScript manual da lupa por um widget Shortcode com `[m360_search_toggle]`. Não mantenha as duas lupas simultaneamente. O formulário independente é `[m360_search_form variant="header" show_intro="false"]`.
5. Pesquise um termo em `/en/` e em `/` em aba anônima. Confirme URL de destino do mesmo idioma, idioma dos resultados, paginação e ausência de itens do outro idioma. Verifique também Enter, clique no botão e navegação pelo teclado.
6. Faça rollback desativando a Home do Core no Site Profile e restaurando o bloco anterior do cabeçalho Elementor, se necessário. A configuração dos widgets e seus conteúdos não é apagada.

O pacote não substitui automaticamente o cabeçalho já publicado. A ação de edição no Elementor é deliberadamente separada da atualização do plugin.
