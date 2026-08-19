# PEL — Content Discovery & SEO Canary

## Estado de entrada — 11/08/2026

- `[m360_breadcrumb]` validado com sucesso em PT-BR e EN-US;
- pesquisa estabilizada nos dois idiomas;
- prioridade operacional movida para Content Discovery & SEO;
- nenhuma autorização de renderer global, writer automático ou backfill.

## Objetivo

Homologar o renderer do M360 em um par de posts traduzidos PT-BR/EN-US sem
ativar writer, backfill ou saída global. Ads, Newsletter, Consent e takeover
de templates permanecem desligados.

## Pré-condições

- Core candidato `0.7.4.0.2` instalado no staging;
- módulo `Content Discovery & SEO` ativo e `healthy`;
- modo do módulo `shadow`;
- writer `manual` e backfill parado;
- renderer `shortcode` antes do início;
- limite de links contextuais igual a `3`;
- um post publicado PT-BR, sua tradução publicada EN-US e um post de controle;
- cada canário com categoria, tags e pelo menos quatro posts relacionados no
  mesmo idioma, para separar o “Leia mais” das três notícias finais.

## Amostra autorizada — 11/08/2026

| Papel | Locale | ID WordPress | Allowlist | Snapshot manual |
|---|---|---:|---|---|
| Canário de origem | PT-BR | `77976` | sim | sim |
| Tradução correspondente | EN-US | `77991` | sim | sim |
| Controle negativo | a confirmar no próprio post | `77939` | não | não |

Valor exato do campo Renderer Canary: `77976, 77991`.

## Gate 1 — snapshots isolados

1. Em **M360 Platform > Content Discovery**, preencher Renderer Canary apenas
   com os dois IDs autorizados, separados por vírgula.
2. Em **Gerar snapshot isolado**, executar `Agora` uma vez para cada ID.
3. Confirmar runs `success`, locale correto e relações ativas dos tipos:
   `internal_link`, `related_post` e `topic`.
4. Não iniciar backfill e não mudar o writer para `automatic`.

As relações de palavras-chave usam os nomes das categorias e tags nativas
atribuídas ao post. O renderer não consulta sinônimos livres nem transporta o
dicionário de outro portal.

## Gate 2 — renderer automático canário

1. Selecionar `Canary — somente IDs autorizados`.
2. Manter `Links contextuais = 3` e salvar.
3. Limpar somente os caches de página/objeto necessários ao staging.
4. Validar os dois canários em sessão anônima, desktop e mobile.
5. Abrir o post de controle e confirmar ausência total de componentes M360.

## Critérios de aceite por idioma

- no máximo três links contextuais, com destino único e no mesmo locale;
- âncoras somente em ocorrências já existentes no corpo do post;
- “LEIA TAMBÉM” ou “RELATED STORY” após o segundo parágrafo;
- três notícias relacionadas ao final, sem repetir a notícia intermediária;
- categorias e tags em “TÓPICOS RELACIONADOS” ou “RELATED TOPICS”;
- nenhum destino PT-BR em post EN-US e nenhum destino EN-US em post PT-BR;
- nenhuma duplicação pelo Elementor, tema ou outro plugin;
- nenhum impacto no header, footer, AdSense, MailPoet, SEO, cache ou Consent;
- nenhuma geração síncrona durante a visita pública.

## Evidências mínimas

- IDs e URLs dos dois canários e do controle;
- captura do painel com writer `manual`, modo `canary` e limite `3`;
- contagem das relações por post e locale;
- capturas desktop/mobile dos cinco componentes;
- HTML ou inspeção dos links comprovando locale e destinos;
- resultado do rollback.

## Execução registrada — 11/08/2026

- Core `0.7.4.0.2` confirmado;
- snapshots ativos: `1` em PT-BR e `1` em EN-US;
- PT-BR: `4 internal_link`, `6 related_post`, `4 topic`;
- EN-US: `2 internal_link`, `2 related_post`, `2 topic`;
- renderer salvo em `canary`, com limite de `3` links contextuais;
- writer `manual`, backfill `idle`, fila sem itens e zero falhas;
- posts públicos permaneceram inalterados após o acionamento.

Estado do gate: **dados shadow válidos, renderer público ainda não homologado**.
O PT-BR possui relações suficientes para o aceite completo. O EN-US precisa de
pelo menos quatro relações `related_post` para entregar uma notícia
intermediária e três notícias finais sem repetição.

Diagnóstico subsequente: o shortcode com `debug="1"` foi bloqueado antes da
leitura do snapshot. A auditoria da `0.7.4.0.2` confirmou que o handler de
persistência da allowlist existia, mas seu formulário não era exibido no
painel reorganizado. O hotfix `0.7.4.0.3` restaura o campo de IDs canários e
passa a informar a razão exata de cada bloqueio.

Após a persistência da allowlist, o post `77976` renderizou “Leia também”, três
notícias relacionadas e tópicos; o controle `77939` permaneceu inalterado.
Nenhuma âncora contextual foi criada porque os termos relacionados ativos não
ocorriam literalmente no corpo elegível. A candidata `0.7.4.0.4` adiciona um
adapter opcional e somente leitura para `WP_links_internos`, incluindo termos
do dicionário que realmente ocorram no conteúdo, sem inventar âncoras.

Na validação da `0.7.4.0.4`, os snapshots foram substituídos, mas as contagens
de `internal_link` permaneceram `4` em PT-BR e `2` em EN-US. Isso comprovou que
o adapter não resolveu a tabela física. A `0.7.4.0.5` passa a descobrir o nome
real preservando caixa e prefixo — necessário para `WP_links_internos` em
MySQL/Linux — e expõe tabela, linhas por locale e matches canários no painel.

A validação da `0.7.4.0.5` confirmou `Provider: não detectado`, com zero linhas
e zero correspondências. O dicionário PEL reside no schema externo
`u164126954_energia_limpa` e usa credenciais diferentes das credenciais do
WordPress. A `0.7.4.0.6` adiciona um provider PDO externo, estritamente de
leitura, configurado fora do pacote. Nenhuma credencial integra Site Profile,
opções do WordPress, diagnóstico, release ou ZIP.

Antes de gerar novos snapshots, configurar o provider conforme
`M360_PEL_External_Dictionary_Provider.md` e confirmar no painel:

- origem `external-pdo`;
- estado `connected`;
- tabela `WP_links_internos`;
- linhas elegíveis maiores que zero em `pt-BR` e `en-US`;
- correspondências literais nos posts canários.

Na primeira execução com o provider externo, o storage permaneceu no algoritmo
`portable-v3-keyword-dictionary`. A causa foi o identificador da `0.7.4.0.6`,
maior que a coluna `algorithm_version varchar(32)`. A `0.7.4.0.7` adota
`portable-v6-ext-dict`, sem migração de schema, e exige nova geração isolada
dos IDs `77976` e `77991`.

## Rollback imediato

1. Retornar o renderer a `Shortcode — rollback`.
2. Limpar os caches de página/objeto.
3. Confirmar que links e blocos automáticos desapareceram dos canários.
4. Preservar snapshots e tabelas para diagnóstico; não excluir dados.

O modo `Automatic — Core renderer global` não faz parte desta homologação e
exige autorização explícita de cutover.

## Homologação canária concluída — 11/08/2026

- provider externo detectado como `external-pdo`, estado `connected`, tabela
  `WP_links_internos`;
- hotfix `0.7.4.0.7` adotado com algoritmo `portable-v6-ext-dict`;
- PT-BR `77976` validado com links contextuais e componentes relacionados;
- EN-US `77991` validado com três links contextuais para `Economy`,
  `Renewable Energy` e `Power Generation`, todos no locale correto;
- `RELATED STORY`, `RELATED NEWS` e `RELATED TOPICS` validados em EN-US;
- controle negativo `77939` permaneceu intacto;
- limite público de até três links contextuais preservado;
- writer permaneceu `manual`, backfill `idle` e renderer `canary`.

Estado do gate: **homologado no escopo canário PT-BR/EN-US**. Este aceite não
autoriza renderer `automatic`, backfill, alteração de templates ou cutover
global, que continuam sujeitos a alinhamento e autorização explícita.
