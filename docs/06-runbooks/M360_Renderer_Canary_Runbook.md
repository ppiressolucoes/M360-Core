# M360 Renderer Canary Runbook

## Pré-requisitos

- v0.7.2.3 encerrada com Comparator `eligible`;
- módulo Content Discovery & SEO ativo, em `shadow` e storage saudável;
- M360 Semantic Relations 0.9.0 permanece ativo;
- posts de teste: 7804 (PT-BR) e 7807 (EN-US).

## Procedimento

1. Atualizar para v0.7.2.4.6.
2. No template/conteúdo de cada post, inserir somente os shortcodes necessários:
   - `[m360_discovery_canary type="read_more"]` no ponto intermediário;
   - `[m360_discovery_canary type="related_posts"]` ao final do conteúdo;
   - `[m360_discovery_canary type="topics"]` depois das notícias relacionadas.
3. Em templates compartilhados, não informar `post_id`; o Core usa o post público corrente. O atributo fica reservado ao preview do editor quando não há post corrente.
4. Validar em sessão anônima e em desktop/mobile:
   - os títulos aparecem em PT-BR ou EN-US conforme o post corrente;
   - links abrem somente destinos publicados no mesmo locale ou arquivos de termos do mesmo idioma;
   - `related_posts` exibe miniaturas quando disponíveis;
   - não há bloco duplicado nem mudança fora do post;
   - o Comparador continua `eligible`.
5. Registrar a evidência antes de incluir outro tipo de relação.

## Cutover automático do renderer

1. Remover do template os shortcodes manuais `m360_discovery_canary`.
2. Em **M360 Platform > Content Discovery > Renderer Canary**, manter três links contextuais e selecionar `Automatic — cutover do renderer`.
3. Salvar e limpar os caches do WordPress/host.
4. Validar pelo menos um post PT-BR e um EN-US:
   - no máximo três links contextuais, sempre no mesmo locale;
   - “LEIA TAMBÉM” / “RELATED STORY” depois do segundo parágrafo;
   - três notícias finais em linha, com miniatura, título, data, categoria e CTA;
   - tópicos como botões clicáveis;
   - nenhum bloco duplicado do precursor.
5. Confirmar que posts sem snapshot ativo permanecem sem bloco, sem geração síncrona durante a visita.

Esse cutover transfere apenas o renderer. O M360 Semantic Relations permanece ativo como writer até o Core receber e homologar os hooks assíncronos de publicação.

## Contrato dos links contextuais

No modo automático, o Core insere no máximo três links, cada um para um destino único e somente quando encontra no corpo uma ocorrência textual existente:

- nomes de categorias e tags atribuídas, vindos de relações `internal_link` ativas;
- palavras e termos que correspondam a esses nomes;
- frases e nomes próprios de duas a seis palavras derivados de títulos de posts `related_post` do mesmo locale.

Somente a primeira ocorrência elegível é ligada. Links já existentes, títulos, código, citações, formulários e componentes M360 são preservados. O Core não inventa âncoras, não executa reconhecimento genérico de entidades e não atravessa locales. O objetivo é melhorar a malha interna, a rastreabilidade e o contexto temático; resultados de indexação, descoberta ou posicionamento devem ser medidos posteriormente no Google Search Console e não são garantidos pelo renderer.

## Rollback imediato

Retornar o modo público a `Shortcode — homologação isolada`. Não desativar o precursor, não limpar tabelas e não executar eventos `m360_sr_*`.
