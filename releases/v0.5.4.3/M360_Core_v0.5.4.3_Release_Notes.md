# M360 Core v0.5.4.3 — Current Post Exclusion

## Objetivo

Impedir que o artigo atualmente aberto seja repetido no componente de últimas notícias exibido na mesma página.

## Comportamento

- em uma página individual de post, o ID consultado é excluído automaticamente;
- a regra vale para os layouts `list`, `compact` e `sidebar`;
- o componente busca a próxima notícia elegível para preservar o limite configurado;
- a contagem e o total de páginas desconsideram o artigo aberto;
- fora de uma página individual, a consulta permanece inalterada.

## Compatibilidade

O comportamento é automático e não exige alteração nos shortcodes existentes. Para um caso excepcional que precise incluir o post atual:

```text
[m360_latest_news exclude_current="false"]
```
