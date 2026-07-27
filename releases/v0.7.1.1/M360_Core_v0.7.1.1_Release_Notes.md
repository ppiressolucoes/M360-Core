# M360 Core v0.7.1.1 - Editorial Diagnostic Encoding Hotfix

Status: instalado e homologado em shadow mode em 2026-07-21.

## Correcao

- corrige o texto de saude do modulo Editorial Layout & Home exibido com mojibake;
- nao altera schema, configuracoes, shortcodes, queries, cache ou renderizacao;
- preserva o modulo em shadow mode e os plugins precursores ativos.

## Evidencia da 0.7.1

- Foundation e Editorial Layout & Home `healthy`;
- Home Editorial 0.1.2 e Semantic Relations 0.9.0 preservados;
- posts PT-BR e EN-US operacionais;
- Newsletter, Ads e Privacy & Consent operacionais;
- nenhum erro critico aparente no frontend ou no administrativo.

## Rollback

Reinstalar o pacote canonico 0.7.1. O hotfix modifica somente mensagens de diagnostico.

## Resultado

- M360 Core 0.7.1.1 atualizado com sucesso;
- modulo Editorial Layout & Home ativo e `healthy`;
- mensagem confirmada: `Shadow mode ativo, sem HTML publico.`;
- nenhum cutover de shortcode realizado.
