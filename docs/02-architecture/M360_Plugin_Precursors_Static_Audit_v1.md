# Auditoria Estática dos Plugins Precursores — v1

Status: concluída em 2026-07-17
Tipo: auditoria de código somente leitura
Decisão relacionada: `ADR-0008`
Baseline do Core: `v0.6.5.4`

## 1. Escopo recebido

Os fontes foram disponibilizados descompactados em `work/plugin-audit/`.

| Plugin | Pasta recebida | Versão declarada no código | Arquivos | Tamanho aproximado |
|---|---|---:|---:|---:|
| M360 Home Editorial | `m360-home-editorial-v0.1.0` | `0.1.2` | 4 | 32 KB |
| M360 Semantic Relations | `m360-semantic-relations-v0.3.0` | `0.9.0` | 16 | 165 KB |

As pastas não correspondem às versões declaradas pelos cabeçalhos dos plugins. Para esta auditoria, os cabeçalhos `0.1.2` e `0.9.0` são a referência funcional. Como não foram entregues ZIPs, não existe hash do pacote; foram calculados hashes dos arquivos principais:

- Home Editorial: `285D3AF81841769CC230AA021FE14BA16AC2DBDD198B4AC991E8EA6994AC14BD`;
- Semantic Relations: `2DF3754D27C641EC96149D7E0C039AD288D61392ECAA10520C52E6B3B41DBDC1`.

## 2. Resultado executivo

- a absorção dos dois plugins é tecnicamente viável;
- o Home Editorial possui baixa persistência e complexidade moderada de interface;
- o Semantic Relations possui alta complexidade de dados e operação;
- nenhum dos dois deve ser simplesmente copiado para dentro do Core;
- o Home Editorial deve ser generalizado primeiro;
- o Semantic Relations exige contratos de catálogo, idioma, tipos de conteúdo, algoritmo, storage, scheduler e renderer;
- o DW/futebol deve tornar-se um provider externo opcional, nunca uma dependência do núcleo;
- os plugins precursores devem permanecer ativos até migração homologada e reversível.

## 3. M360 Home Editorial 0.1.2

### Contratos públicos

- `[m360_news_ticker]`;
- `[m360_news_hero]`;
- `[m360_news_section]`.

### Persistência e cache

- opção `m360_home_editorial_cache_version`;
- transients `m360_home_*` com duração de 10 minutos;
- invalidação em `save_post_post`, `created_category` e `edited_category`;
- nenhuma tabela própria;
- nenhum cron;
- nenhum endpoint REST;
- nenhuma rotina de ativação, desativação ou uninstall.

### Dependências

- WordPress `WP_Query`, posts, categorias, tags e imagens destacadas;
- parâmetro `lang` compatível com a query do Polylang;
- composição operacional por shortcodes no Elementor;
- template JSON citado no README, mas ausente do material recebido.

### Componentes reutilizáveis

- ticker;
- hero rotativo;
- seções `grid`, `featured-list` e `compact`;
- deduplicação de posts durante a mesma renderização;
- fallback de conteúdo;
- cache por parâmetros;
- respeito a `prefers-reduced-motion`.

### Acoplamentos a remover

- defaults e mensagens fixas em inglês;
- `post_type = post`;
- taxonomias `category` e `post_tag` fixas;
- dependência operacional de slugs como `featured-en`;
- exemplos e documentação específicos de Flamengo e competições;
- fallback visual `M360`;
- formatação de datas fixa;
- dependência implícita do Elementor para composição da home.

### Pontos de hardening

- substituir os múltiplos `<h1>` dos slides por nível configurável e apenas um heading principal por página;
- internalizar textos no sistema de internacionalização;
- resolver idioma pelo contexto comum do Core;
- introduzir content source e taxonomy configuráveis;
- criar configuração administrativa de blocos e presets;
- preservar os três shortcodes durante a transição;
- registrar ativação, diagnóstico e invalidação de cache como contratos do módulo.

### Classificação

Portabilidade: alta após generalização.
Risco de migração: médio.
Ordem recomendada: primeiro plugin a ser absorvido.

## 4. M360 Semantic Relations 0.9.0

### Contratos públicos

- `[m360_related_posts]`;
- `[m360_semantic_topics]`;
- `[m360_internal_links]`;
- injeção automática em `the_content` na prioridade 30;
- ações `m360_sr_generate_relations` e `m360_sr_retry_post`;
- tela administrativa em `Configurações > M360 Semantic Relations`.

### Persistência

Tabelas:

- `{$wpdb->prefix}m360_semantic_runs`;
- `{$wpdb->prefix}m360_semantic_relations`.

Relações persistidas:

- `topic`;
- `internal_link`;
- `related_post`.

Estados observados:

- runs: `running`, `success`, `partial`, `failed`;
- relations: `candidate`, `active`, `pinned`, `superseded`;
- post meta: `ready`, `stale`, `pending`, `error`, `no_locale`, `invalid_post` e `not_publish`.

Post meta principais:

- `_m360_semantic_active_run`;
- `_m360_semantic_generated_at`;
- `_m360_semantic_state`;
- `_m360_semantic_source_hash`;
- `_m360_semantic_last_retry_attempt`;
- `_m360_semantic_last_retry_reason`;
- `_m360_gsc_candidate`, `_m360_gsc_priority` e `_m360_gsc_last_marked_at`.

O plugin mantém diversas opções `m360_sr_*` para idioma, layouts, auto append, links contextuais, auto-heal, geração síncrona, janela operacional e diagnóstico.

### Processamento

- geração em `save_post_post`, `wp_after_insert_post`, mudança de status e REST;
- geração síncrona ativada por padrão;
- retry por eventos únicos do WP-Cron;
- auto-heal síncrono na primeira visualização pública;
- snapshot novo é promovido em transação antes de substituir o snapshot saudável anterior;
- inserção inline após o segundo parágrafo;
- blocos relacionados e tópicos adicionados ao final do conteúdo;
- painel de cobertura, reprocessamento e candidatos ao Search Console.

### Dependências portáveis

- WordPress e `$wpdb`;
- Polylang, com fallback PT-BR;
- Yoast, por metadados de indexabilidade;
- categorias, tags e posts;
- imagens destacadas;
- WP-Cron;
- hooks de publicação via REST/n8n.

### Acoplamentos específicos do Mengão 360

- banco padrão `u164126954_dw_esportes`;
- tabela `m360_seo_links_catalog`;
- helper `conectar_dw_esportes_m360()`;
- filtro SQL fixo `tpo_conteudo = futebol`;
- constantes `M360_SR_DW_*`;
- catálogo e diagnóstico denominados DW;
- idiomas limitados a PT-BR e EN-US;
- `post_type = post`;
- taxonomias fixas `category` e `post_tag`;
- sinônimos fixos para jogos, Seleção Brasileira, Copa do Mundo e Brazilian Team;
- termos genéricos ajustados para futebol e mercado de transferências.

### Riscos operacionais

1. Auto-heal síncrono pode gerar escrita e processamento pesado durante a primeira visita pública.
2. Ativação paralela de plugin antigo e módulo novo pode duplicar links, blocos e snapshots.
3. O hook `the_content` precisa entrar no pipeline único do Core para preservar a ordem com Newsletter e outros componentes.
4. Eventos cron antigos precisam ser drenados ou cancelados no corte.
5. Tabelas acumulam runs e relações sem política explícita de retenção.
6. Upgrade força `enabled = 1`, `shadow_mode = 0` e geração síncrona, podendo substituir escolhas operacionais anteriores.
7. Schema usa a versão do plugin como versão de banco; a plataforma deverá separá-las.
8. Transações pressupõem tabelas com engine transacional.
9. Ausência de rotina de desativação/uninstall exige estratégia explícita de preservação e limpeza.
10. O renderer inline utiliza `<h1>` para uma notícia relacionada e deve ser corrigido para não competir com o título principal.
11. A documentação interna contém histórico até versões posteriores ao nome da pasta recebida, reforçando a necessidade de normalizar pacotes e releases.

### Segurança observada

- tela administrativa exige `manage_options`;
- ações administrativas usam nonces;
- saídas principais são escapadas;
- queries variáveis usam `prepare` ou APIs do WordPress;
- credenciais DW não estão nos fontes recebidos e são esperadas por constantes externas.

A auditoria estática não substitui teste de runtime, análise de dependências instaladas ou revisão dos valores existentes no banco.

### Classificação

Portabilidade: média, condicionada à extração dos providers e regras de domínio.
Risco de migração: alto.
Ordem recomendada: depois da fundação modular e do Home Editorial.

## 5. Arquitetura de absorção

### Editorial Layout & Home

Separar em:

- `EditorialModule`;
- `ContentQueryInterface`;
- `LanguageContextInterface`;
- `BlockRegistry`;
- `EditorialCache`;
- renderers Ticker, Hero e Section;
- configuração de headings, textos, tipos e taxonomias;
- presets por Site Profile.

### Content Discovery & SEO

Separar em:

- `CatalogProviderInterface`;
- `WordPressCatalogProvider`;
- `LegacyDwCatalogAdapter`, externo e opcional;
- `LocaleResolverInterface`;
- `ContentTypeRegistry`;
- `RelationAlgorithmInterface`;
- `RelationRepository`;
- `SnapshotPromoter`;
- `GenerationScheduler`;
- `ContentInjectionPipeline`;
- `DiscoveryRenderer`;
- `SeoDiagnostics`.

As listas de sinônimos e termos genéricos devem pertencer ao Site Profile, não ao algoritmo central.

## 6. Estratégia de migração

### Home Editorial

1. manter os shortcodes existentes;
2. criar renderers equivalentes no Core em shadow mode;
3. importar ou reconstruir o template da home;
4. comparar HTML, conteúdo, responsividade e idioma;
5. transferir os shortcodes ao Core;
6. desativar o plugin precursor mantendo rollback imediato.

### Semantic Relations

1. congelar schema e registrar contagens de tabelas/estados;
2. criar repositório compatível com as tabelas legadas;
3. extrair DW para provider opcional;
4. mover regras esportivas para o Site Profile Mengão 360;
5. executar o novo gerador em shadow mode sem injetar HTML;
6. comparar relações, ranks, cobertura e latência;
7. transferir cron e hooks, impedindo duplicidade;
8. transferir os shortcodes e o pipeline de conteúdo;
9. desativar o plugin precursor sem apagar dados;
10. manter rollback por pelo menos dois ciclos de homologação.

## 7. Informações operacionais ainda necessárias

Antes da codificação da absorção:

- confirmar no WordPress as versões exibidas e os nomes reais das pastas ativas;
- exportar o template JSON do Elementor mencionado pelo Home Editorial ou documentar a composição atual das homes;
- registrar shortcodes e parâmetros efetivamente usados em PT-BR e EN-US;
- registrar valores das opções `m360_sr_*`, sem credenciais;
- obter somente as contagens por status das duas tabelas semânticas;
- registrar cron pendente `m360_sr_*`;
- confirmar engine e charset das tabelas;
- registrar tempo de geração e incidência de auto-heal;
- confirmar se o catálogo DW é indispensável ou se o fallback WordPress atende parte da operação.

## 8. Conclusão

A `v0.7.0` pode avançar para desenho e codificação do kernel modular. A absorção funcional deve continuar dividida em `v0.7.1` e `v0.7.2`. O material recebido é suficiente para a arquitetura estática, mas o corte de produção depende das evidências operacionais listadas acima.
