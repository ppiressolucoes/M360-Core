# Mengão 360 — Sprints Concluídas

Versão: v1.2

## Sprint — DW Esportivo Base

Status: concluída.

Entregas:
- API externa integrada.
- DW Esportivo estruturado.
- Tabelas de dimensões e fatos.
- Views frontend.
- Publicação via cache_widgets.

## Sprint — Publicador HTML [1]

Status: concluída.

Entregas:
- Node 8 estabilizado.
- Publicador multimodelo.
- WC26, CLI e BSA validados.
- pt-BR/en-US.
- Shortcode [m360_competicao].
- Responsividade e dark mode.

## Sprint — WC26

Status: concluída.

Modelo: grupos + mata-mata jogo único.  
Entregas: página publicada, widgets pt-BR/en-US, integração com Bolão WC.

## Sprint — CLI / Libertadores

Status: concluída.

Modelo: grupos + mata-mata ida/volta.  
Entregas: widgets principais, tradução de mata-mata, MATCH, SECOND LEG, Winner, Scheduled e Waiting.

## Sprint — BSA / Brasileirão Série A

Status: concluída.

Modelo: pontos corridos.  
Entregas: tabela de classificação, jogos por rodada, pt-BR/en-US e fallback pt-BR.

## Sprint — HTML [2] Artilharia e Estatísticas

Status: concluída.

Entregas:
- ETL [2] reativado/evoluído.
- HTML [2] reativado/evoluído.
- Loop multilíngue.
- Widgets extras publicados.
- Shortcode preparado para concatenar extras.

## Sprint — Bolão WC

Status: operacional / base madura.

Entregas:
- Palpites.
- Ranking.
- Ligas.
- Convite WhatsApp.
- Bloqueio por horário.
- Apuração.
- Dashboard/resumo.

## Sprint — Internacionalização PT-BR/EN-US

Status: concluída, publicada e validada em 28/06/2026.

Entregas:
- Home EN publicada em /en/.
- WordPress, Elementor e Polylang integrados.
- Workflow n8n de tradução PT → EN consolidado.
- Plugin M360 Home Editorial 0.1.2.
- Ticker, hero e blocos editoriais responsivos.
- Navegação PT-BR/EN-US.
- Pacote histórico com arquitetura, runbook, inventário, manifesto e checksums.

Pendências pós-sprint:
- hreflang e sitemap EN.
- Search Console e indexação.
- Core Web Vitals.
- Planejamento da futura camada ES.

## Sprint — SEO Semântico / Relações Internas PT-BR e EN-US

Status: concluída, publicada e validada.

Data de validação: 29/06/2026.

Objetivo:
- Migrar a camada de links internos, tópicos relacionados e bloco “Leia também / Read also” do fluxo n8n para o plugin WordPress, preservando conteúdo editorial limpo e relações semânticas controladas por snapshots.

Entregas:
- Plugin M360 Semantic Relations evoluído e validado até a versão v0.6.4.
- Catálogo semântico canônico consolidado no DW Esportivo: `m360_seo_links_catalog`.
- Snapshots operacionais no WordPress: `wp_m360_semantic_runs` e `wp_m360_semantic_relations`.
- Renderização dinâmica de links contextuais sem alteração direta do `post_content`.
- Renderização dinâmica dos blocos finais:
  - `LEIA TAMBÉM / READ ALSO`.
  - `TÓPICOS RELACIONADOS / RELATED TOPICS`.
- Layout de “Leia também / Read also” validado em cards e lista com miniatura, título em caixa alta, data e categoria.
- Separação operacional entre PT-BR e EN-US, evitando herança de links internos PT-BR na versão traduzida.
- Admin do plugin refinado com configuração por linha, item primeiro, controles no centro e ativação por idioma nas colunas finais.
- Estratégia aprovada para desativar gradualmente no n8n a injeção de links internos e blocos relacionados diretamente no HTML dos posts.
- Análise de impacto SEO documentada para Google Search, Search Console, clusters semânticos e saúde multilíngue.

Artefatos:
- `m360-seo-semantico-sprint-artifacts-v1.zip`
- `m360-semantic-relations-v0.6.4-upgrade.zip`

Decisão arquitetural:
- O n8n passa a entregar conteúdo editorial limpo.
- O plugin passa a ser responsável pela camada semântica, links contextuais, tópicos e posts relacionados.
- PT-BR e EN-US passam a operar com relações próprias por idioma, sem mistura de URLs, âncoras ou blocos herdados.

Pendências pós-sprint:
- Criar painel de observabilidade no admin do plugin.
- Exibir cobertura por idioma: posts com snapshot, posts sem snapshot e últimos erros.
- Adicionar botão de reprocessamento manual por post.
- Acompanhar Search Console em janelas de 7, 14, 28 e 90 dias.
- Auditar posts antigos com HTML legado antes de qualquer limpeza em massa.


## Sprint — Observabilidade e Reprocessamento SEO Semântico

Status: concluída, publicada e validada.

Data de validação: 29/06/2026.

Release estável: `M360 Semantic Relations v0.7.2`.

Objetivo:
- Garantir que posts PT-BR e traduções EN-US recebam snapshots semânticos, possam ser diagnosticados/reprocessados pelo admin e exibam corretamente as melhorias no front-end, inclusive em fluxos n8n/REST/Polylang e templates Elementor.

Problemas tratados:
- Traduções EN-US publicadas sem criação de runs em `wp_m360_semantic_runs`.
- Traduções EN-US sem relações em `wp_m360_semantic_relations`.
- Reprocessamento pendente dependente de WP-Cron.
- Casos com snapshot ativo no banco, mas sem exibição no ambiente externo por limitação na renderização `the_content`.

Entregas:
- Plugin evoluído de v0.7.0 até v0.7.2.
- Hooks adicionais para posts criados por automação: `wp_after_insert_post`, `transition_post_status` e `rest_after_insert_post`.
- Diagnóstico administrativo por Post ID.
- Exibição de idioma detectado, estado semântico, active run, últimos runs e relações por tipo/status.
- Botão `Reprocessar agora`.
- Botão `Limpar estado semântico`.
- Geração síncrona ativada por padrão para não depender de WP-Cron em correções manuais.
- Renderização mais resiliente no front-end, sem dependência rígida de `in_the_loop()` e `is_main_query()`.
- Chamada direta do renderer para `READ ALSO` e `RELATED TOPICS`, reduzindo falhas de contexto em Elementor/tema.
- Proteção contra duplicidade de blocos e links no conteúdo.
- Validação final de PT-BR e EN-US com runs, relations active, postmeta sincronizado e exibição externa.

Validação técnica:
- `wp_m360_semantic_runs` com status `success`.
- `wp_m360_semantic_relations` com `internal_link`, `related_post` e `topic` em status `active`.
- `wp_postmeta` com `_m360_semantic_active_run`, `_m360_semantic_state = ready`, `_m360_semantic_generated_at` e `_m360_semantic_source_hash`.
- Blocos semânticos refletidos no front-end após correção v0.7.2.

Artefatos:
- `m360-semantic-relations-v0.7.0-upgrade.zip`
- `m360-semantic-relations-v0.7.1-upgrade.zip`
- `m360-semantic-relations-v0.7.2-upgrade.zip`
- `M360_Sprint_Observabilidade_SEO_Semantico_v0.7.2.md`

Decisão operacional:
- A versão v0.7.2 passa a ser a base estável para operação da camada SEO semântica.
- Refinamentos visuais pontuais ficam para sprint posterior, sem alterar a lógica de processamento validada.

Pendências pós-sprint:
- Ajustes finos de CSS dos blocos finais.
- Tipografia dos títulos `READ ALSO` / `RELATED TOPICS`.
- Alinhamento de miniaturas no layout lista.
- Ajustes mobile e hover dos links.
- Padronização visual com News Portal/Elementor.
- Acompanhamento dos novos posts no Google Search Console.

## Síntese

A plataforma tem API, DW, WordPress/Elementor/News Portal, publicador principal, publicador de extras, competições validadas, internacionalização PT-BR/EN-US, base para produto comercial e camada de SEO semântico dinâmica, versionada, reversível, observável e reprocessável.
