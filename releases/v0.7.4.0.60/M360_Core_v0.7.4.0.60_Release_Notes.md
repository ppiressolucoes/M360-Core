# M360 Core v0.7.4.0.60 — CARREGAR MAIS na página dedicada

## Correção

A versão 0.7.4.0.59 introduziu a navegação por cursor, mas a página dedicada ainda podia ser interpretada como bloco-resumo quando o shortcode conservava `pagination="false"`. Nesse cenário, o rodapé mostrava “Ver todas as notícias” e apontava para a própria página.

A versão 0.7.4.0.60 compara o endereço atual com a página dedicada resolvida para o idioma. Quando coincidem e o layout é `list`, o Core ativa automaticamente a navegação por cursor e apresenta **CARREGAR MAIS**. Um valor explícito `navigation="none"` continua sendo respeitado.

## Custo da navegação

- busca somente `limit + 1` registros;
- usa ordem estável `post_date DESC, ID DESC`;
- não calcula total de páginas;
- não usa `paged`, offset crescente, polling ou endpoint público adicional;
- mantém cache separado por idioma, opções, contexto e cursor.

## Compatibilidade

- posts e barra lateral continuam exibindo o link para a página dedicada;
- `pagination="true"` e `navigation="cursor"` continuam ativando cursor explicitamente;
- pt-BR usa “CARREGAR MAIS” e en-US usa “LOAD MORE”.

## Reversão

Reinstalar o ZIP 0.7.4.0.59. A atualização não altera tabelas, índices, conteúdo, filas ou eventos WP-Cron.
