# Enriquecimento Editorial DW — fundação em desenvolvimento

Data: 06/09/2026. Base: Core 0.7.4.0.20, commit `0190d99cb5492f7356a5302b813570b3242249ae`.
Estado: primeiro incremento de código, sem conexão ao DW e sem apresentação pública. Não é pacote de produção.

A [PR #29](https://github.com/ppiressolucoes/M360-Core/pull/29) registra a análise técnica e os diagnósticos. Este incremento parte da base aceita da PR #27, ainda sem merge na consulta de 06/09/2026.

## Implementado

- Módulo `editorial-dw-enrichment` registrado pela extensão da plataforma e desativado por padrão. Ativá-lo não publica blocos: o diagnóstico informa que provedor/apresentação ainda não estão disponíveis.
- Resolvedor de escopos explicitamente vinculados: time, competição, edição, fase, grupo e idioma. O catálogo de IDs reais deve ser fornecido pelo chamador; nenhum ID de produção foi inventado.
- Preservação de BIGINT como string decimal; rejeição de ambiguidades e escopos fora da lista das seis competições.
- Interface de provedor somente leitura; nenhum adaptador concreto, credencial ou endpoint foi adicionado.
- Contrato inicial restrito a classificação e estatísticas básicas do mesmo registro. Valida identidade, timestamp com offset, idade, campos inteiros e consistência dos números. Mantém valores ausentes como nulos e admite pontos negativos por dedução.
- Serviço com leitura exclusiva do cache e atualização explícita separada. Chave inclui contexto, idioma, namespace/versionamento do provedor e política temporal. Cache negativo e revalidação de idade/identidade na leitura.
- Erros do provedor resultam em estado `unavailable`, sem reproduzir mensagens internas.

O único ajuste no runtime existente é carregar `includes/enrichment/bootstrap.php`. Não há hook em `the_content`, `save_post`, REST ou n8n; não há SQL, migração, alteração de notícias ou uso da conexão de escrita do Bolão.

## Uso e contrato de integração futura

`M360_Enrichment_Context::resolve($bindings, $allowed, $locale)` recebe vínculos editoriais já curados. `$allowed` mapeia a chave interna da competição para seu ID DW em string. O resolver não transforma menções no texto em protagonista nem supõe que `catalog_ref_id` seja um ID esportivo.

Cada vínculo deve conter `team_id`, `competition_id`, `competition_key`, `season`, `phase`, `group`. `phase` e `group` exigem presença explícita e admitem null. O idioma aceito é pt-BR/en-US. Mais de um escopo distinto resulta em `ambiguous`.

O provedor retorna `context` e `standings`. A seção traz `dw_updated_at` em ISO 8601 `YYYY-MM-DDTHH:MM:SS+HH:MM` (offset explícito), mais `position`, `points`, `played`, `won`, `drawn`, `lost`, `goals_for`, `goals_against`, `goal_difference`. IDs e edição devem coincidir integralmente com a consulta. Números devem ser inteiros PHP ou null: o adaptador deve validar/canonicalizar os tipos oriundos do driver, sem coerção permissiva.

O serviço exige TTL positivo, TTL negativo e idade máxima, sem valores de produção predefinidos. `read()` nunca chama `fetch()`. `refresh()` é ponto explícito para integração futura com worker; ainda não possui agendador, trava distribuída, backoff ou deduplicação de refresh concorrente. Não ligar `refresh()` à requisição pública. Alteração de provedor, origem ou semântica exige novo `cache_namespace()`.

O contrato desta fundação é interno e parcial: agenda, proveniência pública, timestamps de observação do fornecedor e de coleta por seção ainda não foram implementados. `dw_updated_at` significa atualização do registro DW, não observação do fornecedor. O limite técnico de idade/TTL aceito pelo serviço é sete dias; a política factual real ainda precisa de homologação.

## Testes locais

Com PHP CLI disponível, executar a partir da raiz:

```sh
php tests/enrichment-foundation.php
```

Resultado em PHP 8.5.10 CLI: **42 verificações aprovadas**, com fixtures sintéticas e funções WordPress substituídas em memória. Cobrem composição da base e carregamento único do bootstrap, registro/desativação, BIGINT, ambiguidades, IDs fora do catálogo, timestamps inválidos/futuros/antigos, equivalência de offset, nulos, inconsistências, expiração e isolamento do cache, ausência de consultas durante leitura, cache negativo e proteção contra exposição de erros.

Lint aprovado nos seis arquivos do módulo, no bootstrap principal alterado e no teste. Isso não substitui testes em WordPress/PHP de produção, cache persistente, consultas SQL ou homologação visual.

## Próximas dependências

1. Resultados do diagnóstico atual do DW, fuso, temporadas e catálogo real.
2. Escolha da classificação carregada/calculada, cadência e transporte com permissão efetiva somente leitura.
3. Vínculos WordPress/n8n reais e política de escolha do protagonista.
4. Adaptador, agenda, proveniência, atualização assíncrona com controle de concorrência e invalidação ao editar notícia/mapeamentos.
5. Apresentação PT/EN, fallback e coexistência com Semantic Relations, com teste de integração.

Nenhuma nova versão instalável foi atribuída. A composição de arquivos da base (raiz + `plugin/` para os caminhos legados) continua necessária; não empacotar apenas `plugin/`. A reversão deste incremento consiste em reverter seu commit, sem rollback de dados.
