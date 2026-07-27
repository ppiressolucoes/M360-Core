# Runbook — Preflight do M360 Semantic Relations 0.9.0

Status: pronto para execução somente leitura

Objetivo: coletar a baseline necessária para `M360 Core v0.7.2 — Content Discovery & SEO` sem alterar produção.

## 1. Regras de segurança

- não atualizar, desativar ou reinstalar o precursor durante o preflight;
- não executar o protótipo `work/m360-semantic-relations/` versão `0.1.0`;
- não executar `dbDelta`, reprocessamento, backfill, limpeza ou promoção;
- não alterar opções `m360_sr_*`;
- não exportar conteúdo, URLs privadas, dados pessoais, credenciais ou dumps;
- coletar somente versões, nomes, estados, contagens, engines, índices e métricas agregadas;
- substituir `wp_` nas consultas pelo prefixo real da instalação;
- guardar a evidência fora de diretórios públicos do WordPress.

## 2. Identificação do plugin

Com WP-CLI:

```bash
wp plugin list --fields=name,status,version,update --format=table | grep -i "m360-semantic"
wp plugin path m360-semantic-relations
```

Registrar:

- slug e caminho físico;
- arquivo principal;
- versão exibida no admin;
- status ativo;
- existência de atualização pendente.

Aceite esperado: versão funcional `0.9.0`. Se o slug for legado, apenas registrar; não renomear a pasta.

## 3. Opções — nomes e valores não secretos

Listar somente nomes:

```bash
wp option list --search='m360_sr_%' --field=option_name | sort
```

Registrar os valores destas opções:

```bash
wp option get m360_sr_db_version
wp option get m360_sr_enabled
wp option get m360_sr_shadow_mode
wp option get m360_sr_sync_generation
wp option get m360_sr_auto_heal_on_view
wp option get m360_sr_freeze_promotions
wp option get m360_sr_pending_window_days
wp option get m360_sr_unfiltered_reprocess
wp option get m360_sr_enable_related_posts
wp option get m360_sr_enable_topics
wp option get m360_sr_enable_internal_links
wp option get m360_sr_auto_append_ptbr
wp option get m360_sr_auto_append_topics_ptbr
wp option get m360_sr_auto_append_related_ptbr
wp option get m360_sr_auto_inline_related_ptbr
wp option get m360_sr_auto_contextual_terms_ptbr
wp option get m360_sr_auto_contextual_posts_ptbr
wp option get m360_sr_related_layout_ptbr
wp option get m360_sr_auto_append_topics_enus
wp option get m360_sr_auto_append_related_enus
wp option get m360_sr_auto_inline_related_enus
wp option get m360_sr_auto_contextual_terms_enus
wp option get m360_sr_auto_contextual_posts_enus
wp option get m360_sr_related_layout_enus
```

Não exportar `m360_sr_last_dw_error`: a mensagem pode revelar host, banco ou detalhes de conexão.

Observação crítica: o fonte `0.9.0` não consulta `m360_sr_shadow_mode` fora do admin/upgrade. O valor dessa opção não comprova ausência de renderização.

## 4. Tabelas e engine

```sql
SHOW TABLE STATUS
WHERE Name IN ('wp_m360_semantic_runs', 'wp_m360_semantic_relations');

SHOW INDEX FROM wp_m360_semantic_runs;
SHOW INDEX FROM wp_m360_semantic_relations;
```

Registrar somente:

- existência;
- engine;
- collation;
- quantidade aproximada de linhas;
- nomes de índices;
- tamanho agregado.

Gate: as duas tabelas precisam usar engine transacional antes de qualquer promoção atômica.

## 5. Runs

```sql
SELECT language, status, COUNT(*) AS total
FROM wp_m360_semantic_runs
GROUP BY language, status
ORDER BY language, status;

SELECT language, trigger_source, COUNT(*) AS total
FROM wp_m360_semantic_runs
GROUP BY language, trigger_source
ORDER BY language, total DESC;

SELECT language,
       COUNT(*) AS runs,
       ROUND(AVG(duration_ms), 2) AS avg_ms,
       MAX(duration_ms) AS max_ms
FROM wp_m360_semantic_runs
WHERE created_at >= NOW() - INTERVAL 30 DAY
GROUP BY language;

SELECT language, error_code, COUNT(*) AS total
FROM wp_m360_semantic_runs
WHERE status IN ('partial', 'failed')
GROUP BY language, error_code
ORDER BY total DESC;
```

Não selecionar `error_message`, `metadata`, título, conteúdo ou URL.

## 6. Relações

```sql
SELECT language, relation_kind, status, COUNT(*) AS total
FROM wp_m360_semantic_relations
GROUP BY language, relation_kind, status
ORDER BY language, relation_kind, status;

SELECT language, relation_kind,
       COUNT(DISTINCT source_post_id) AS posts_cobertos,
       ROUND(AVG(score), 5) AS score_medio
FROM wp_m360_semantic_relations
WHERE status IN ('active', 'pinned')
GROUP BY language, relation_kind
ORDER BY language, relation_kind;

SELECT language, relation_kind, source_post_id, target_type, target_id, COUNT(*) AS total
FROM wp_m360_semantic_relations
WHERE status IN ('active', 'pinned')
GROUP BY language, relation_kind, source_post_id, target_type, target_id
HAVING COUNT(*) > 1
ORDER BY total DESC
LIMIT 20;

SELECT COUNT(*) AS self_links
FROM wp_m360_semantic_relations
WHERE target_type = 'post'
  AND target_id = source_post_id
  AND status IN ('active', 'pinned');
```

A consulta de duplicidades retorna apenas IDs técnicos e contagem. Se a política local impedir saída de IDs, registrar somente o número de grupos duplicados.

## 7. Estados de postmeta

```sql
SELECT meta_value AS semantic_state, COUNT(*) AS total
FROM wp_postmeta
WHERE meta_key = '_m360_semantic_state'
GROUP BY meta_value
ORDER BY total DESC;

SELECT COUNT(DISTINCT post_id) AS posts_com_active_run
FROM wp_postmeta
WHERE meta_key = '_m360_semantic_active_run'
  AND CAST(meta_value AS UNSIGNED) > 0;
```

Não exportar `post_id` individual, título ou conteúdo neste passo.

## 8. Cron

Contagem por hook, sem argumentos:

```bash
wp cron event list --fields=hook --format=csv \
  | grep '^m360_sr_' \
  | sort \
  | uniq -c
```

Registrar separadamente:

- `m360_sr_generate_relations`;
- `m360_sr_retry_post`;
- outros hooks `m360_sr_*`, se existirem.

Não executar nem excluir eventos durante o preflight.

## 9. Dependência do catálogo DW

O precursor `0.9.0` consulta o catálogo DW para `topic` e `internal_link`. `related_post` é calculado no WordPress.

Coletar apenas:

- provider disponível: sim/não;
- tipo de conexão: helper PDO, helper wpdb, constants ou cross-database;
- quantidade agregada de candidatos ativos por locale e target type;
- quantidade de runs com erro de catálogo, agrupada por `error_code`.

Não registrar nome de usuário, senha, host, nome privado de banco, mensagem completa de erro ou linhas do catálogo.

Se houver autorização para consulta agregada no catálogo:

```sql
SELECT language_locale, target_type, COUNT(*) AS total
FROM m360_seo_links_catalog
WHERE tpo_conteudo = 'futebol'
  AND is_active = 1
GROUP BY language_locale, target_type
ORDER BY language_locale, target_type;
```

## 10. Uso público

Inventariar sem editar:

- páginas, templates ou widgets que usam os três shortcodes legados;
- auto append, inline related e links contextuais efetivamente habilitados;
- layouts `cards` ou `list` por locale;
- presença dos marcadores CSS `m360-sr-*` em uma amostra aprovada;
- existência de HTML semântico persistido no `post_content` vindo de fluxos antigos.

Não copiar conteúdo ou URLs para a documentação. Registrar somente contagens e tipos de uso.

## 11. Resultado esperado

Entregar uma ficha com:

```text
Plugin/version/slug:
WordPress/PHP:
Tabelas/engine/collation:
Runs por locale/status:
Relations por locale/kind/status:
Posts cobertos por locale/kind:
Estados postmeta:
Cron por hook:
Geração síncrona:
Auto-heal:
Auto append/inline/contextual por locale:
Provider DW disponível:
Catálogo agregado por locale/type:
Shortcodes em uso:
Erros agregados:
```

## 12. Critério para encerrar o preflight

- nenhuma alteração realizada;
- nenhum segredo ou conteúdo transportado;
- schema e engine conhecidos;
- ownership público identificado;
- baseline por locale registrada;
- cron e geração síncrona conhecidos;
- dependência DW quantificada;
- dados suficientes para autorizar a implementação read-only de `v0.7.2`.
