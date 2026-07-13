# M360 Core v0.5.2.2 — Multilingual Date Archive

## Objetivo

Assumir no M360 Core as páginas abertas pelo link de data do `[m360_post_info]`, eliminando o título misto produzido pelo arquivo padrão do tema.

## Entregas

- controlador próprio para arquivos diários, mensais e anuais;
- títulos naturais, como `13 de julho de 2026` e `July 13, 2026`;
- breadcrumb hierárquico com ano, mês e período atual;
- descrição, total de artigos, estado vazio e paginação traduzidos;
- grid responsivo de artigos;
- integração com o slot publicitário `archive-inline`.

## Homologação

- abrir uma data a partir de um post PT-BR;
- abrir a data equivalente a partir de um post EN-US;
- validar título, breadcrumb, cards e paginação;
- confirmar que cada ambiente lista apenas seus artigos correspondentes;
- validar desktop e mobile.
