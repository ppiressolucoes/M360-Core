# Sprint v0.7.2 — Content Discovery & SEO

Status: v0.7.2 implementada como candidata de homologação — contratos e leitura legada somente leitura

Precursor: M360 Semantic Relations `0.9.0`

Decisões relacionadas: `ADR-0008` e `ADR-0009`

## 1. Objetivo

Absorver progressivamente as capacidades genéricas do M360 Semantic Relations no M360 Core, eliminar o ownership paralelo do plugin precursor e preservar dados, SEO, renderização, rollback e operação PT-BR/EN-US.

A absorção seguirá:

```text
inventário runtime
→ contratos
→ adapter legado somente leitura
→ storage próprio do Core
→ gerador em shadow mode
→ comparação
→ renderer canário
→ transferência de scheduler e hooks
→ cutover
→ estabilização
→ desativação do precursor
```

Não haverá big bang, escrita concorrente ou alteração direta de `post_content`.

## 2. Referência funcional e materiais do workspace

### Referência canônica da absorção

| Item | Valor |
|---|---|
| Fonte | `work/plugin-audit/m360-semantic-relations-v0.3.0/` |
| Versão declarada | `0.9.0` |
| Arquivos | 16 |
| Tamanho aproximado | 164.589 bytes |
| SHA-256 do arquivo principal | `2DF3754D27C641EC96149D7E0C039AD288D61392ECAA10520C52E6B3B41DBDC1` |
| Algoritmo declarado | `det-v8-quality` |

O nome da pasta recebida não representa a versão funcional. O cabeçalho `0.9.0` é a referência.

### Material não canônico

Existe um protótipo em `work/m360-semantic-relations/`, declarado como `0.1.0`, com SHA-256 principal `19B35B4CEC3B9DCE1219BE201C3C71769A7C207590F675CE13A47A53EBEA5855`.

Esse protótipo não deve ser instalado nem usado como base de schema. Ele tenta criar as mesmas tabelas `m360_semantic_runs` e `m360_semantic_relations`, mas usa colunas, estados, índices e postmeta incompatíveis com o precursor `0.9.0`. Executar seu `dbDelta()` sobre produção criaria risco direto de alteração estrutural.

## 3. Inventário do precursor 0.9.0

### Contratos públicos

- `[m360_related_posts]`;
- `[m360_semantic_topics]`;
- `[m360_internal_links]`;
- filtro `the_content` na prioridade 30;
- ações `m360_sr_generate_relations` e `m360_sr_retry_post`;
- tela `Configurações > M360 Semantic Relations`;
- ações administrativas de diagnóstico, reprocessamento, limpeza e prioridade GSC.

### Gatilhos

- `save_post_post`, prioridade 20;
- `wp_after_insert_post`, prioridade 999;
- `transition_post_status`, prioridade 999;
- `rest_after_insert_post`, prioridade 999;
- WP-Cron single event para geração e retry;
- auto-heal síncrono durante a primeira visualização pública quando o snapshot está ausente ou stale.

### Persistência legada

Tabelas:

- `{$wpdb->prefix}m360_semantic_runs`;
- `{$wpdb->prefix}m360_semantic_relations`.

Postmeta:

- `_m360_semantic_active_run`;
- `_m360_semantic_generated_at`;
- `_m360_semantic_state`;
- `_m360_semantic_source_hash`;
- `_m360_semantic_last_retry_attempt`;
- `_m360_semantic_last_retry_reason`;
- `_m360_gsc_candidate`;
- `_m360_gsc_priority`;
- `_m360_gsc_last_marked_at`.

Tipos de relação:

- `topic`;
- `internal_link`;
- `related_post`.

Estados principais:

- runs: `running`, `success`, `partial`, `failed`;
- relations: `candidate`, `active`, `pinned`, `superseded`;
- post: `ready`, `stale`, `pending`, `error`, `no_locale`, `invalid_post`, `not_publish`.

### Configurações

Foram encontrados aproximadamente 30 contratos `m360_sr_*`, incluindo:

- master flag, shadow mode, geração síncrona e auto-heal;
- layouts, auto append, inline related e links contextuais por idioma;
- limites de links por idioma;
- feature flags para related posts, topics e internal links;
- freeze de promoções, janela de pendências e reprocessamento sem filtro;
- versão de banco e último erro do provider DW.

Os valores atuais precisam ser inventariados no WordPress sem exportar credenciais.

### Dependências e acoplamentos

Portáteis:

- WordPress, `$wpdb`, posts, termos e imagens;
- Polylang;
- Yoast SEO;
- WP-Cron;
- publicação WordPress, REST e automações;
- snapshots determinísticos e renderização sem alterar conteúdo persistido.

Específicos do Mengão 360:

- helper `conectar_dw_esportes_m360()`;
- constantes `M360_SR_DW_*`;
- banco padrão `u164126954_dw_esportes`;
- tabela `m360_seo_links_catalog`;
- filtro fixo `tpo_conteudo = futebol`;
- idiomas limitados a PT-BR/EN-US;
- `post_type = post`, `category` e `post_tag` fixos;
- stopwords, sinônimos e âncoras voltados a futebol, Seleção, Copa do Mundo e transferências.

### Falhas contratuais que não serão copiadas

1. `m360_sr_shadow_mode` é salvo e exibido no admin, mas não é consultado pelo gerador, hooks ou renderer do fonte `0.9.0`; portanto, não constitui shadow mode efetivo.
2. `maybe_upgrade()` força `enabled=1`, `shadow_mode=0`, geração síncrona, auto-heal e descongelamento de promoções.
3. o auto-heal realiza escrita e cálculo durante uma visita pública.
4. a geração síncrona é padrão e pode alongar publicação, REST ou front-end.
5. o renderer inline usa `<h1>` e compete semanticamente com o título do artigo.
6. schema e versão do plugin são o mesmo contrato.
7. não existe rotina de desativação ou uninstall operacional.
8. não existe política explícita de retenção de runs e relações antigas.
9. o provider DW e regras esportivas estão misturados ao algoritmo.
10. o estado global de idioma assume fallback PT-BR.

## 4. Limites da absorção

### Entram no módulo portátil

- geração determinística explicável;
- snapshots atômicos e rollback;
- related posts, related topics e internal links;
- links contextuais no HTML em memória;
- adapters para idioma e indexabilidade;
- storage, scheduler assíncrono, renderer e diagnóstico;
- shortcodes compatíveis;
- feature flags e configuração por Site Profile;
- métricas de cobertura, qualidade, erros e latência.

### Não entram no módulo portátil

- credenciais, banco ou helper DW;
- filtro de vertical `futebol`;
- regras, sinônimos ou listas esportivas;
- Search Console credentials ou dados privados;
- n8n, ETLs, APIs esportivas ou Bolão;
- conteúdo, relações ou postmeta exportados de produção.

Se o catálogo DW for indispensável para manter qualidade, ele será atendido por um provider opcional do ambiente Mengão 360, fora do algoritmo e fora do Site Profile exportável. A necessidade deverá ser demonstrada pela comparação em shadow mode.

## 5. Módulo e contratos do Core

Identidade proposta:

| Campo | Valor |
|---|---|
| ID | `content-discovery-seo` |
| Label | `Content Discovery & SEO` |
| Dependência | `publisher-foundation` |
| Schema inicial | `1` |
| Capacidade administrativa | `manage_options` |
| Estado inicial | `off` |

Contratos:

- `CatalogProviderInterface` — fornece candidatos sem conhecer o algoritmo;
- `WordPressCatalogProvider` — posts e taxonomias configuráveis;
- `LegacyDwCatalogAdapter` — provider opcional, nunca dependência do módulo;
- `LocaleResolverInterface` — resolve e valida locale sem fallback cruzado;
- `IndexabilityProviderInterface` — canonical, publicação e noindex;
- `ContentTypeRegistry` — post types e taxonomias do Site Profile;
- `RelationAlgorithmInterface` — score versionado e explicável;
- `RelationRepositoryInterface` — storage próprio do Core;
- `LegacySnapshotReader` — leitura somente das tabelas `m360_semantic_*`;
- `SnapshotPromoter` — promoção atômica sem substituir snapshot saudável por vazio;
- `GenerationScheduler` — debounce, fila e retry fora do request público;
- `ContentInjectionPipeline` — ordem e deduplicação no `the_content`;
- `DiscoveryRenderer` — HTML namespaced e headings configuráveis;
- `SemanticComparator` — compara precursor e Core sem publicar o Core;
- `SeoDiagnostics` — saúde, cobertura, latência, divergências e ownership.

## 6. Estratégia de dados

### Regra principal

O Core não escreverá nas tabelas legadas enquanto o precursor estiver ativo.

### Storage proposto do Core

- `{$wpdb->prefix}m360_discovery_runs`;
- `{$wpdb->prefix}m360_discovery_relations`;
- schema versionado independentemente de `M360_CORE_VERSION`;
- postmeta próprio, por exemplo `_m360_discovery_active_run_{locale}`;
- `algorithm_version`, `provider_id`, `source_hash`, `reason_codes` e métricas preservados;
- engine transacional obrigatória para promoção.

### Convivência

- `LegacySnapshotReader` lê snapshots ativos do `0.9.0` sem alterar status ou postmeta;
- o gerador do Core grava somente nas tabelas `m360_discovery_*`;
- o Core permanece sem HTML público em shadow mode;
- o precursor continua como único writer e renderer público até o gate de cutover;
- o fallback legado pode servir conteúdo durante a janela de transição, mas nunca promover ou modificar dados legados.

### Retenção

Antes de implementar limpeza:

- preservar todos os snapshots legados;
- definir quantidade mínima de snapshots saudáveis por post/locale;
- introduzir dry-run de retenção;
- excluir apenas dados próprios do Core após aceite e backup;
- nenhuma limpeza legada faz parte da `v0.7.2` inicial.

## 7. Configuração portátil

Configurações propostas:

- `mode`: `off`, `shadow`, `hybrid`, `public`;
- `post_types`;
- `taxonomies`;
- `supported_locales`;
- `catalog_provider`;
- `algorithm_profile`;
- `generation_strategy`: `async` ou `manual`;
- `debounce_seconds` e política de retry;
- `related_posts`, `topics`, `internal_links` e `contextual_links` por locale;
- limites e layouts por componente;
- `legacy_read_fallback`;
- `retention_policy`;
- heading level e textos localizáveis.

Não serão importados para o Site Profile:

- senhas, hosts, usuários ou nomes privados de banco;
- relações, runs, postmeta ou conteúdo;
- dados do Search Console;
- filtros fixos de futebol.

## 8. Pipeline de conteúdo

Ordem atual relevante do Core:

| Prioridade | Componente |
|---:|---|
| 18 | Ads inline |
| 30 | Discovery & SEO, após o cutover |
| 999 | Newsletter no final do artigo |

Regras:

- operar somente no conteúdo singular e no post consultado;
- nunca persistir alterações em `post_content`;
- proteger `a`, `script`, `style`, `code`, `pre`, `textarea`, botões e headings;
- impedir auto-link, link duplicado, locale cruzado e destino inválido;
- inserir apenas um bloco inline;
- usar marcadores namespaced para idempotência;
- retornar conteúdo original em erro;
- não gerar snapshot durante o front-end;
- corrigir o heading inline para nível configurável, nunca `<h1>` por padrão.

## 9. Compatibilidade de shortcodes

Durante shadow mode, o Core usará contratos próprios para homologação:

- `[m360_discovery_related_posts]`;
- `[m360_discovery_topics]`;
- `[m360_discovery_internal_links]`.

Os aliases legados serão preservados no cutover:

- `[m360_related_posts]`;
- `[m360_semantic_topics]`;
- `[m360_internal_links]`.

O Core só registrará aliases legados quando o handler não estiver registrado pelo precursor. Parâmetros `limit`, `layout`, `post_id`, `lang`, `taxonomy` e `types` manterão compatibilidade.

## 10. Sequência versionada

### v0.7.2 — Contracts & Legacy Read

- registrar o módulo desligado;
- declarar settings, schema e diagnóstico;
- implementar interfaces e `LegacySnapshotReader` somente leitura;
- adicionar inventário e preflight no admin;
- nenhuma tabela nova sem aprovação do preflight;
- nenhum HTML, hook de escrita, cron ou shortcode legado.

### v0.7.2.1 — Portable Storage & Shadow Generator

- criar tabelas `m360_discovery_*`;
- implementar provider WordPress e algoritmo determinístico versionado;
- gerar somente por ação manual/assíncrona em shadow;
- impedir geração síncrona em visita pública;
- preservar integralmente tabelas e postmeta legados.

### v0.7.2.2 — Comparator & Diagnostics

- comparar coverage, destinos, ranks, locale, latência e erros;
- mostrar divergências explicáveis por post e por locale;
- medir dependência real do catálogo DW;
- bloquear promoção pública enquanto houver vazamento de idioma ou destinos inválidos.

### v0.7.2.3 — Renderer Canary

- renderers namespaced e acessíveis;
- shortcodes próprios do Core;
- canário em posts explícitos, primeiro PT-BR e depois EN-US;
- sem auto append global;
- comparação visual, estrutural e SEO.

### Plano original v0.7.2.4 — Scheduler & Injection Cutover Candidate

- transferir hooks de publicação e REST ao Core;
- ativar geração assíncrona e retry do Core;
- integrar prioridade 30 ao pipeline;
- preparar drenagem de eventos `m360_sr_*`;
- impedir dois writers e dois renderers.

Esse plano foi reordenado durante a homologação: as entregas reais e seus contratos estão consolidados nas seções 21 a 24.

### Gate posterior — Precursor Cutover & Stability

- congelar baseline e realizar backup;
- ativar modo público do Core;
- desativar o precursor sem excluir dados;
- validar aliases legados, cron, HTML e novos posts;
- manter rollback por no mínimo dois ciclos de homologação;
- remover o precursor somente em frente posterior.

## 11. Preflight de produção — somente leitura

Antes da codificação do writer do Core, registrar:

1. versão e slug reais do plugin ativo;
2. valores das opções `m360_sr_*`, sem credenciais;
3. engine, charset, collation, índices e contagens das duas tabelas;
4. runs por idioma e status;
5. relations por idioma, tipo e status;
6. cobertura de posts publicados por locale;
7. estados de postmeta;
8. eventos cron `m360_sr_*` pendentes;
9. latência de geração e incidência de auto-heal;
10. dependência do catálogo DW e taxa de fallback WordPress;
11. shortcodes efetivamente usados em páginas/templates;
12. flags de auto append, inline e links contextuais por idioma.

Somente contagens, estados e configurações não secretas devem sair do WordPress. Não transportar conteúdo, URLs privadas, dados pessoais, credenciais ou dumps de banco.

## 12. Homologação

### Gate A — contrato e storage

- schema próprio criado sem alterar `m360_semantic_*`;
- módulo pode ser ativado/desativado isoladamente;
- health check identifica precursor, engine e ownership;
- shadow mode efetivo comprovado: zero HTML público e zero hook de escrita legado.

### Gate B — shadow PT-BR

- amostra de até 100 posts publicados PT-BR;
- zero relação EN-US;
- zero self-link, duplicidade, destino não publicado ou URL inválida;
- coverage do Core não inferior à baseline do precursor além da tolerância aprovada;
- divergências de rank e destino explicáveis;
- nenhuma escrita em request público;
- snapshot saudável anterior preservado em falha.

### Gate C — shadow EN-US

- repetir os critérios em até 100 posts EN-US;
- zero fallback automático PT-BR;
- canonical, idioma e traduções coerentes;
- nenhuma âncora ou URL herdada do PT-BR.

### Gate D — canário visual

- related posts, topics, internal links e links contextuais validados;
- headings, HTML, acessibilidade, mobile e CSS aprovados;
- Ads e Newsletter mantêm ordem e ausência de duplicidade;
- Elementor e tema não impedem o renderer;
- widget vazio não deixa markup residual.

### Gate E — cutover

- Core é o único scheduler, writer e renderer;
- eventos legados inventariados e drenados;
- aliases legados resolvidos pelo Core;
- publicação WordPress, REST, n8n e Polylang criam snapshots;
- sem erro crítico, warning ou notice;
- rollback administrativo executável em até cinco minutos;
- aceite explícito antes de desativar o precursor.

## 13. Riscos e mitigação

| Risco | Mitigação |
|---|---|
| dois writers nas mesmas tabelas | storage separado e ownership exclusivo |
| dois filtros `the_content` | Core sem HTML até o canário; cutover transacional |
| falso shadow mode do precursor | não usar `m360_sr_shadow_mode` como controle de segurança |
| upgrade reativar flags legadas | não atualizar o precursor durante o cutover; registrar opções |
| cron antigo executar após rollback | inventariar, drenar e documentar restauração |
| auto-heal escrever no front | Core proíbe geração em request público |
| perda de snapshot saudável | promoção atômica e fallback legado somente leitura |
| dependência oculta do DW | medir em shadow; provider externo somente se necessário |
| locale cruzado | resolver locale por contrato e falhar fechado |
| regressão SEO/HTML | canário, comparação e headings configuráveis |
| conflito com Ads/Newsletter | pipeline com prioridades 18/30/999 e marcadores idempotentes |
| protótipo 0.1.0 alterar schema legado | proibir instalação e manter fora do build |

## 14. Rollback

### Antes do cutover

- desligar o módulo do Core;
- remover apenas eventos e dados shadow próprios do Core quando aprovado;
- precursor permanece ativo e proprietário da saída pública.

### Depois do cutover

1. colocar o Core em `off` ou `shadow`;
2. impedir aliases e injeção pública do Core;
3. reativar o precursor `0.9.0` sem executar upgrade;
4. restaurar opções não secretas registradas no preflight;
5. revalidar cron, snapshot ativo e HTML em PT-BR/EN-US;
6. preservar tabelas `m360_discovery_*` para diagnóstico;
7. não apagar tabelas `m360_semantic_*` nem postmeta legado.

## 15. Gate para iniciar codificação

A codificação de `v0.7.2` poderá iniciar após:

- revisão deste plano;
- coleta do preflight somente leitura;
- confirmação do provider necessário;
- definição da baseline de comparação;
- autorização explícita para criar o módulo e, em fase posterior, suas tabelas próprias.

Nenhuma etapa desta preparação autoriza alteração de produção ou desativação do M360 Semantic Relations.

## 16. Implementação entregue na v0.7.2

- módulo `content-discovery-seo`, dependente apenas de `publisher-foundation` e desativado por padrão;
- contratos `M360_Catalog_Provider_Interface` e `M360_Relation_Repository_Interface`;
- adapter `M360_Legacy_Semantic_Adapter`, restrito a consultas nas tabelas e opções legadas;
- validação defensiva das colunas esperadas antes de qualquer leitura operacional;
- painel `M360 Platform > Content Discovery` com preflight agregado de tabelas, flags, cron e estados;
- zero tabela nova, writer, scheduler, shortcode, filtro `the_content` ou HTML público;
- M360 Semantic Relations 0.9.0 preservado como único writer e renderer.

O gate foi aprovado após a homologação saudável da v0.7.2 e resultou na implementação descrita a seguir.

## 17. Implementação entregue na v0.7.2.1

- tabelas exclusivas `m360_discovery_runs` e `m360_discovery_relations`, com engine InnoDB e schema independente;
- provider portátil baseado apenas em posts, post types e taxonomias públicas do WordPress;
- resolução estrita de locale via Polylang/Site Profile, sem fallback cruzado;
- algoritmo determinístico `portable-v1`, com razões e decomposição de score persistidas;
- relações shadow dos tipos `topic`, `related_post` e `internal_link`;
- promoção transacional que preserva o snapshot ativo anterior quando o novo resultado é vazio ou falha;
- execução manual imediata ou assíncrona, sempre iniciada por administrador;
- nenhum hook automático de publicação nesta etapa;
- zero shortcode, renderer, filtro `the_content` ou HTML público do Core;
- tabelas, postmeta, opções, cron e renderer do precursor permanecem intocados.

O gate de homologação exige pelo menos um post PT-BR e um EN-US, storage próprio InnoDB, runs ativos, ausência de locale cruzado e nenhuma alteração visual no front-end.

## 18. Homologação da v0.7.2.1

A geração manual foi aprovada em 22/07/2026 com os seguintes resultados:

- modo `shadow`;
- saúde `healthy`, com storage próprio saudável e zero HTML público;
- schema próprio `1`;
- tabelas `m360_discovery_runs` e `m360_discovery_relations` disponíveis em InnoDB;
- um run `active` para `pt-BR` e um run `active` para `en-US`;
- em cada locale: quatro `internal_link`, seis `related_post` e quatro `topic` ativos;
- front-end sem impacto visual;
- M360 Semantic Relations 0.9.0 preservado como renderer público exclusivo.

Não foi observado vazamento de locale nas contagens agregadas. O gate de geração manual e storage está aprovado.

### Fechamento assíncrono

A execução por `m360_discovery_generate_shadow` foi homologada em 22/07/2026. Para PT-BR e EN-US, o painel mostrou um run `active` e um `superseded`; para cada tipo, as relações novas permaneceram `active` e as anteriores foram marcadas `superseded` na proporção 4 `internal_link`, 6 `related_post` e 4 `topic` por locale. O front-end permaneceu inalterado.

O painel legado apresentou relações `candidate` e um evento pendente `m360_sr_retry_post`. Como pertencem ao precursor, eles não devem ser removidos nem executados manualmente nesta etapa. A v0.7.2.2 deve compará-los com o storage do Core e definir a drenagem apenas no cutover autorizado.

## 19. Implementação entregue na v0.7.2.2

- `M360_Semantic_Comparator` em modo somente leitura, acionado por post e locale no painel Content Discovery;
- comparação de relações ativas dos tipos `topic`, `internal_link` e `related_post`;
- métricas de cobertura compartilhada, destinos exclusivos, diferenças de rank, latência e runs com falha;
- validação de destino post publicado, termo existente e locale estrito para destinos post;
- status `eligible`, `review` ou `blocked`, sem escrever diagnóstico em tabelas ou opções;
- `blocked` obrigatório para destino inválido, locale cruzado, storage ruim ou snapshot Core ausente;
- `review` para precursor sem snapshot ativo ou cobertura compartilhada inferior a 50%;
- nenhum renderer, shortcode, hook público, geração, cron ou mudança no precursor.

### Homologação esperada

Executar comparações em uma amostra de posts PT-BR e EN-US já processados no shadow. Para cada resultado, registrar totais Core/precursor, coverage, destinos exclusivos, rank differences, latência e falhas. A promoção para renderer canário permanece bloqueada enquanto existir `invalid_targets` ou `cross_locale_targets` acima de zero e exige autorização explícita mesmo em resultados `eligible`.

### Hotfix v0.7.2.2.1 — normalização de taxonomias

Na primeira execução com os posts `7804` (PT-BR) e `7807` (EN-US), o Comparator apontou seis destinos inválidos e zero locale cruzado em ambos os casos. A inspeção mostrou que eram taxonomias válidas do precursor (`category` e `post_tag`) comparadas contra o tipo portátil `term` do Core. O hotfix normaliza esses tipos antes da validação.

Após a correção, uma divergência de tipo sem destinos compartilhados passa a gerar `review`: o precursor fornece `internal_link` baseado em termos, enquanto o Core atual fornece posts. A divergência é real e deverá orientar a evolução do algoritmo; não permite renderer canário, mas não deve bloquear o diagnóstico como se fosse destino inválido.

## 20. v0.7.2.3 — Contrato portátil de `internal_link`

Esta etapa substitui o Renderer Canary previsto inicialmente. O contrato portátil determina que uma relação `internal_link` tenha `target_type = term` e `target_id` de uma taxonomia configurada e existente. O destino representa uma página de arquivo de termo que poderá ser resolvida por renderer próprio apenas numa etapa posterior.

O gerador shadow não usa mais posts como `internal_link`. Ele usa, no máximo, quatro termos atribuídos ao post de origem, ordenados deterministicamente por taxonomia e ID, com as razões `assigned_term` e `portable_internal_term`. `related_post` continua sendo o único tipo que aponta para posts.

Não há shortcode, `the_content`, renderer, hook de escrita, alteração em tabelas legadas ou execução em visita pública. A homologação requer gerar snapshots shadow e repetir o Comparator nos posts 7804 (PT-BR) e 7807 (EN-US). O renderer canary continua condicionado a evidência de paridade e aprovação separada.

## 21. v0.7.2.4.4 — Locale-safe Renderer Canary

A validação da v0.7.2.4.3 revelou títulos fixos em inglês e possibilidade de um template compartilhado consultar uma origem de outro idioma por `post_id`. O hotfix torna o post público corrente autoritativo, usa `post_id` apenas como fallback de preview e valida o locale de cada destino antes da saída.

O contrato editorial canário passa a oferecer três composições explícitas:

- `read_more`: uma chamada intermediária localizada, baseada em `related_post`;
- `related_posts`: até seis notícias relacionadas com miniaturas;
- `topics`: termos relacionados apresentados depois das notícias.

Também permanece disponível `internal_links` para arquivos dos termos do contrato portátil. Não há inserção automática em `the_content`; o precursor continua responsável por seus próprios hooks até autorização de cutover.

## 22. v0.7.2.4.5 — Semantic Renderer Cutover Candidate

A v0.7.2.4.5 introduz um controle operacional não portátil com os modos `shortcode` e `automatic`. O padrão após atualização permanece `shortcode`. A ativação de `automatic` transfere ao Core somente a composição pública, mantendo o precursor como writer durante a homologação.

No modo automático, o filtro do Core executa na prioridade 29 e:

- cria no máximo três links contextuais para termos atribuídos ou frases específicas encontradas em títulos de posts relacionados;
- não altera títulos, links existentes, código, citações, formulários ou componentes M360;
- insere “LEIA TAMBÉM” / “RELATED STORY” depois do segundo parágrafo;
- anexa três notícias relacionadas com miniatura, título, data, categoria e CTA;
- anexa tags e categorias relacionadas como botões clicáveis;
- rejeita destinos com locale ausente ou diferente do post de origem;
- não gera, repara ou promove snapshots durante visitas públicas.

Marcadores transitórios `m360-sr-*` são emitidos apenas como adapter de coexistência para impedir que o precursor duplique a renderização quando ambos os plugins estão ativos. Eles não representam dependência de storage ou regra específica no domínio portátil.

O aceite desta versão autoriza o cutover do renderer, mas não a desativação do precursor. Antes disso, o Core ainda precisa assumir e homologar hooks de publicação, REST/Polylang, fila assíncrona e retry.

## 23. v0.7.2.4.6 — Semantic Renderer Visual Polish

Último refinamento do renderer antes do gate de Scheduler, Writer & Backfill:

- amplia e reforça o título “LEIA TAMBÉM” / “RELATED STORY”;
- apresenta tags e categorias de “TÓPICOS RELACIONADOS” como botões de alto contraste, com estados hover e foco visível;
- preserva o modo público salvo, os snapshots ativos e o precursor como writer;
- mantém o limite de três links contextuais para destinos únicos;
- documenta que as âncoras são ocorrências já existentes no texto: nomes de termos atribuídos ou frases significativas de duas a seis palavras derivadas de títulos relacionados;
- mantém locale estrito, primeira ocorrência apenas e exclusão de links existentes, títulos, código, citações, formulários e componentes M360.

O renderer não faz NER/IA genérica nem cria âncoras artificiais. Ele fortalece a malha de links internos e o contexto rastreável, mas ganhos de descoberta e posicionamento devem ser avaliados por métricas do Google Search Console após o cutover.

## 24. v0.7.2.5 — Scheduler, Writer & Backfill Cutover

A v0.7.2.5 transfere ao Core a capacidade de manter seus próprios snapshots após publicação e atualização, sem importar registros do storage legado.

### Writer assíncrono

- hooks tardios de publicação, atualização e alteração de taxonomias apenas enfileiram o post;
- processamento exclusivo por WP-Cron, sem geração durante visita pública;
- deduplicação de eventos disparados por WordPress, REST, Elementor e Polylang;
- lock por post e retry progressivo de 60, 300 e 900 segundos;
- locale obrigatório e sem fallback entre PT-BR e EN-US;
- hash de origem idempotente: conteúdo e termos inalterados não criam novo run;
- promoção transacional preserva o snapshot ativo anterior em qualquer falha;
- despublicação aposenta runs e relações ativas do Core.

### Backfill

O backfill consulta posts publicados por ID crescente e processa dez itens por lote. Cada lote agenda o seguinte com intervalo mínimo, permitindo interrupção pelo painel. Posts já cobertos com o mesmo hash são contabilizados como `unchanged`; não são regravados.

O backfill regenera relações com o algoritmo portátil vigente. Ele não copia `wp_m360_semantic_runs`, `wp_m360_semantic_relations`, postmeta ou opções do precursor.

### Controles e diagnóstico

O painel passa a exibir:

- modo do writer `manual` ou `automatic`;
- posts publicados, cobertos e ausentes;
- percentual de cobertura;
- estado e contadores do backfill;
- contagem recente da fila por estado.

O padrão da atualização é `automatic`, autorizado para esta etapa. `manual` interrompe o backfill e impede novos processamentos automáticos, sem remover snapshots ativos.

### Gate de desativação do precursor

O M360 Semantic Relations deve permanecer ativo até que:

1. uma nova publicação PT-BR e outra EN-US atinjam `active` automaticamente;
2. uma atualização produza novo snapshot e marque o anterior `superseded`;
3. o renderer público use o Core sem duplicidade;
4. o backfill termine como `completed`;
5. cobertura ausente seja zero ou tenha exceções registradas;
6. fila `queued/running/failed` esteja drenada;
7. rollback para writer `manual` e renderer `shortcode` tenha sido conferido.

## 25. v0.7.2.5.1 — Discovery Operations Console

Hotfix administrativo sem mudança no algoritmo, storage ou ownership:

- resumo de saúde, cobertura, backfill e ownership no topo;
- writer e backfill apresentados como operação principal;
- barra de progresso e contadores essenciais;
- ações contextuais que evitam reinício acidental durante backfill ativo;
- renderer público em painel próprio;
- Comparator e geração manual recolhidos em diagnóstico;
- tabelas, flags e cron legados movidos para área avançada;
- layout responsivo para desktop e telas menores;
- rearme automático do próximo lote quando o backfill está `running` e o evento WP-Cron não existe, preservando a continuidade após atualização do plugin.

Fila, cursor, snapshots e configurações continuam nas mesmas opções e tabelas. O hotfix não reinicia nem interrompe o backfill.

## 26. Homologação final do cutover — 24/07/2026

Todos os gates de transferência foram concluídos no WordPress:

| Indicador | Resultado |
|---|---:|
| Saúde | `healthy` |
| Ownership | `automatic` |
| Backfill | `completed` |
| Posts publicados | 2.278 |
| Posts cobertos | 2.278 |
| Posts ausentes | 0 |
| Cobertura | 100% |
| Gerados no ciclo | 2.256 |
| Inalterados no ciclo | 17 |

A diferença entre o total coberto e a soma `gerados + inalterados` corresponde a cinco posts que já possuíam cobertura válida antes do ciclo final.

O `M360 Semantic Relations 0.9.0` foi desativado após aceite explícito. Não houve impacto visual ou administrativo observado, e novas publicações continuam recebendo snapshots e renderização automática pelo Core.

### Ownership consolidado

- writer, scheduler, retry e backfill: M360 Core;
- storage ativo e snapshots: tabelas próprias do M360 Core;
- links contextuais, “Leia também”, notícias e tópicos relacionados: M360 Core;
- resolução estrita de locale PT-BR/EN-US: M360 Core;
- plugin precursor: desativado e sem responsabilidade operacional.

### Retenção e rollback

- manter o plugin precursor instalado e desativado durante a janela de observação;
- preservar `wp_m360_semantic_runs`, `wp_m360_semantic_relations`, opções, postmeta e eventos legados;
- não migrar nem mesclar registros legados no storage próprio do Core;
- não excluir dados sem frente posterior, inventário, backup e autorização explícita;
- em regressão crítica, interromper o writer do Core, retornar o renderer ao modo seguro e somente então avaliar a reativação do precursor.

Status da sprint: **concluída e homologada**.
