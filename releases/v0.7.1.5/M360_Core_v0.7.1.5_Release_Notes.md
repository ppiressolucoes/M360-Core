# M360 Core v0.7.1.5 - Newsroom Carousel Fallback

Status: candidato a homologacao.

## Correcoes

- limita o Newsroom a 1200px no desktop e centraliza o componente;
- preserva margem lateral de 1rem em viewports menores;
- completa o carrossel com noticias recentes quando `featured-en` possui menos itens que `featured_limit`;
- mantem a materia marcada como destaque na primeira posicao;
- garante controles e autoplay quando houver pelo menos dois slides.

## Causa

A tag `featured-en` retornou somente uma materia. A versao anterior nao aplicava o fallback do precursor, portanto apenas um slide era renderizado e os controles eram omitidos.

## Validacao

- largura maxima de 1200px;
- cinco slides com `featured_limit="5"` quando houver conteudo no idioma;
- botoes anterior e proximo visiveis;
- autoplay no intervalo configurado;
- quatro cards laterais preservados.
