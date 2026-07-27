# M360 Core v0.7.2.4.5 — Semantic Renderer Cutover Candidate

## Entrega

- modo público reversível: `shortcode` ou `automatic`;
- até três links contextuais para termos e frases de posts relacionados;
- “LEIA TAMBÉM” / “RELATED STORY” depois do segundo parágrafo;
- bloco final com três notícias, miniaturas, títulos, data, categoria e CTA;
- tópicos relacionados como botões;
- validação fail-closed de locale em todos os destinos;
- marcadores de coexistência que impedem duplicidade visual com o precursor;
- zero geração ou auto-heal em requisição pública.

## Ativação controlada

1. Remover os shortcodes canários manuais do template.
2. Selecionar `Automatic — cutover do renderer` no painel Content Discovery.
3. Manter no máximo três links contextuais.
4. Limpar caches e homologar PT-BR/EN-US.

## Limite do cutover

Esta versão substitui somente o renderer. O M360 Semantic Relations 0.9.0 deve permanecer ativo como writer até o Scheduler Cutover do Core.

## Rollback

Retornar o modo público a `Shortcode — homologação isolada`. Nenhuma tabela, opção ou rotina do precursor é alterada.
