# M360 Core v0.5.4.2 — Latest News List UX

## Objetivo

Evoluir o layout de página do componente Últimas Notícias com hierarquia editorial clara e navegação paginada opcional, preservando o modo sidebar homologado.

## Shortcode de homologação

```text
[m360_latest_news layout="list" pagination="true"]
```

Exemplo com oito itens por página e sem publicidade interna:

```text
[m360_latest_news layout="list" limit="8" pagination="true" show_ads="false"]
```

## Comportamento

- primeiro post com imagem, espaçamento e título ampliados;
- somente o primeiro título utiliza negrito;
- demais títulos usam peso regular;
- paginação PT-BR/EN-US no rodapé;
- parâmetro de navegação isolado: `m360_news_page`;
- paginação disponível somente para `layout="list"`;
- `pagination="false"` permanece como padrão para preservar integrações existentes.

## Critérios de homologação

- validar destaque do primeiro post em desktop e mobile;
- confirmar peso regular nos demais títulos;
- validar paginação anterior, próxima e páginas numeradas;
- confirmar manutenção do idioma durante a paginação;
- confirmar publicidade e recolhimento conforme `show_ads`;
- confirmar que o modo sidebar permanece inalterado.