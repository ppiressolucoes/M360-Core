# PEL — homologação da busca 0.7.4.0.57

Diagnóstico público em 16/10/2026: o formulário en-US da versão 0.7.4.0.56 enviava para `/en/news-and-sustainability/?s=federal&lang=en`, que devolvia a capa pt-BR. A rota `/en/?s=federal&lang=en` entregava resultados de busca. A causa é o uso do URL da página inicial traduzida como action do formulário.

1. Atualize o Core em staging e abra `/en/` em aba anônima. Inspecione o formulário da lupa: `action` deve terminar em `/en/` e conter um campo oculto `lang=en`.
2. Busque `federal` por Enter e pelo botão. Confirme a URL `/en/?s=federal&lang=en`, título de resultados e apenas posts en-US. Repita uma busca em pt-BR e teste a paginação.
3. Em Site Profile, defina as cores primária e secundária específicas do PEL e salve. Confirme que o botão de pesquisa, a lupa e seus estados de interação usam essas cores. No Mengão 360, mantenha a paleta vermelha própria.
4. Se o portal usar domínios de idioma ou uma estrutura de URL diferente, valide o destino antes da publicação. O filtro `m360_search_form_action_url` permite ajustar o action sem alterar o Core.

O hotfix não modifica o shortcode já inserido no cabeçalho. Não há migração de dados pessoais ou credenciais.
