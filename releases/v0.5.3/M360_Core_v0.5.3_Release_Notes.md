# M360 Core v0.5.3 — M360 Search Experience

## Objetivo

Entregar uma experiência de busca própria, visível, acessível e multilíngue, integrada ao Search Engine existente do M360 Core.

## Shortcode

```text
[m360_search_form variant="hero"]
```

Variantes disponíveis:

- `hero`: apresentação ampla para áreas estratégicas e homologação no cabeçalho;
- `header`: formato horizontal para cabeçalhos e barras superiores;
- `compact`: formato reduzido para resultados, widgets e áreas laterais.

## Recursos

- ação direcionada automaticamente para `/` ou `/en/`;
- placeholder, botão, título, descrição e mensagens em PT-BR/EN-US;
- termo atual preservado na página de resultados;
- label acessível e semântica `role="search"`;
- foco visível e operação completa por teclado;
- bloqueio nativo e complementar de consultas vazias;
- integração automática da variante compact nos resultados;
- variante hero exibida quando não há resultados.

## Personalização

```text
[m360_search_form variant="hero" title="Encontre sua notícia" description="Pesquise todo o portal."]
```

## Homologação

- inserir a variante hero em uma área de teste do cabeçalho;
- pesquisar em PT-BR e confirmar resultados em português;
- pesquisar em EN-US e confirmar a permanência em `/en/`;
- validar Enter, botão, foco por teclado e mensagem de termo vazio;
- validar desktop e mobile;
- confirmar que o banner publicitário atual não foi removido.
