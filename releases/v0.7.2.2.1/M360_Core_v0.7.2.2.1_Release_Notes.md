# M360 Core v0.7.2.2.1 — Comparator Normalization Hotfix

## Correção

A primeira validação do Comparator & Diagnostics em `7804` (PT-BR) e `7807` (EN-US) registrou seis destinos inválidos em cada post, sem locale cruzado. A causa foi identificada: o precursor grava taxonomias com os tipos `category` e `post_tag`, enquanto o Core usa o tipo portátil `term`.

Esta versão normaliza `category` e `post_tag` para `term` antes da comparação e da validação de destino. Assim, taxonomias válidas do precursor deixam de ser classificadas como inválidas.

## Novo comportamento de gate

- `blocked`: somente destino realmente inválido, locale cruzado, storage não saudável ou snapshot Core ausente;
- `review`: cobertura insuficiente, precursor sem snapshot ou tipo de relação sem destinos compartilhados;
- `eligible`: sem bloqueios técnicos e sem divergência estrutural por tipo.

O diagnóstico agora mostra **Tipos sem destino compartilhado**. Para os posts homologados, espera-se `review`, não `blocked`, porque o precursor usa termos em `internal_link` e o Core atual usa posts nesse tipo. Essa é uma divergência funcional real a ser tratada pela próxima frente, não um problema de integridade ou locale.

## Garantias preservadas

- zero escrita no precursor;
- zero alteração nos snapshots existentes;
- zero renderer, shortcode, cron ou HTML público;
- M360 Semantic Relations 0.9.0 permanece writer e renderer exclusivos.

## Homologação do hotfix

1. atualizar o Core para `0.7.2.2.1`;
2. repetir a comparação de `7804` em `pt-BR` e `7807` em `en-US`;
3. confirmar `Destinos inválidos / locale cruzado = 0 / 0`;
4. confirmar resultado `review` por divergência de contrato em `internal_link`;
5. confirmar que não houve impacto visual.
