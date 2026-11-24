# Proposta — Integridade, originalidade e procedência editorial

Status: proposta para discussão e homologação; nenhum fluxo n8n ou plugin foi alterado. Mapeamento informado pelo usuário em 24/11/2026; nomes, versões, conexões e contratos devem ser conferidos nos exports reais. Não houve acesso ao n8n ou ao WordPress nesta análise.

## Objetivo

Reduzir riscos de cópia indevida, melhorar rastreabilidade, confiabilidade e valor editorial, preservando a política de rastreamento de cada portal. Similaridade é sinal de revisão, não prova de plágio; atribuição não substitui autorização. Nenhuma técnica garante indexação, citação ou ausência de reclamações. Direitos e políticas de uso devem ser definidos pela equipe responsável, com avaliação jurídica quando necessária.

## Inventário informado

| Etapa | Fluxo ou subfluxo informado | Responsabilidade |
|---|---|---|
| E-mail | EMAIL &#124; M360 Auto Posts Release e Pautas | Releases e pautas de agências |
| RSS | RSS [ 8 Oportunidades ] Leads Posts &#124; Feeds | Descoberta por RSS |
| Web | WEB Discovery &#124; Portal Solar - Notícias [ Core Editorial ] | Descoberta editorial |
| SERP | WEB Serp-Api &#124; M360 v0.2.0 [ Core Editorial ] | Pesquisa direcionada |
| Deduplicação | SUB &#124; Deduplicação e Semântica - M360 [ Core Editorial ] | Duplicidade interna |
| Extração | SUB &#124; Extração Metadados [ Core Editorial ] | Metadados e limpeza da fonte |
| Edição/publicação | SUB &#124; WEB Edição Detalhes M360 v0.3 [ Core Editorial ] | Edição, imagem e publicação |
| Redação | SUB1 - LLM Reescreve Conteudo v0.2 [ Core Editorial ] | Texto, HTML e SEO |
| Storage | SUB &#124; Storage Media External | Armazenamento e metadados da imagem |
| Imagem IA | SUB4 - Gerar Imagem Featured IA-API [Cross] | Imagens para matérias sem featured |

## Encaixe proposto

1. Após extração e deduplicação interna, registrar proveniência: fonte, URL canônica quando conhecida, data de acesso, autor e titular quando conhecidos, tipo de entrada, licença ou contrato e evidência da permissão. E-mail pode não ter URL; manter referência interna com acesso restrito. Ausência de licença não equivale a permissão.
2. Antes da geração, classificar política da fonte: próprio, licenciado/sindicado, release autorizado, terceiro, domínio público ou desconhecido. Para reutilização sem autorização comprovada, encaminhar à revisão e obter permissão ou produzir apuração independente. Preservar citações, créditos e termos contratuais. Manter fatos rastreáveis; não inventar informações.
3. A redação deve agregar apuração, contexto ou análise verificável e respeitar o uso autorizado. Registrar fontes por afirmação quando aplicável. Atribuições públicas, links e créditos devem refletir as condições da fonte.
4. Após gerar a versão final em cada idioma e antes de publicar, comparar trechos com fontes utilizadas e, quando autorizado, com corpus interno ou serviço contratado. Identificar cópia literal e paráfrases muito próximas, distinguindo citações identificadas, nomes, números, termos técnicos e texto licenciado. Não calibrar para contornar detectores.
5. Adicionar barreira imediatamente antes de cada publicação: aprovado, revisão ou bloqueado. Direitos desconhecidos, fonte inacessível, erro, indisponibilidade, evidência insuficiente ou conflito seguem para rascunho/revisão; falha nunca equivale a aprovação. Aprovação humana registra responsável, justificativa, versão e evidências. Alterar texto ou fontes invalida a decisão anterior. Avaliar versões traduzidas individualmente.
6. Verificar direitos das imagens separadamente: origem, licença, crédito e permissão de uso; para imagens geradas, registrar provedor e condições. Storage não comprova direitos.
7. Manter Yoast como proprietário dos metadados e sitemaps existentes. Verificar HTML, canonical, hreflang por idioma e dados estruturados coerentes, sem duplicar schemas. Indexação e citação são decisões dos mecanismos. As políticas para OAI-SearchBot e GPTBot não mudam automaticamente.

## Contrato proposto

Entrada: item_id, site_id, locale, content_version, source_records com tipo, origem, URL opcional, data, titular, direitos, evidência e condições, draft_html, dados de imagem e contexto editorial. Credenciais e textos completos protegidos não devem constar nos logs gerais.

Saída versionada: status (approved, review, blocked), reason_codes, source_ids, rights_status, similarity_findings com trechos mínimos e referências, claim_findings, image_rights_status, content_hash, policy_version, reviewer e reviewed_at quando aplicável. O hash vincula resultado e aprovação ao texto publicado. Falha, timeout, contrato inválido ou fonte inacessível geram review ou blocked conforme política.

Limitar tempo, tamanho, concorrência e retenção; implementar idempotência por item, idioma e hash, retries com backoff e circuito de proteção. Respeitar políticas de acesso, privacidade e licenças. Enviar material de terceiros a serviço externo somente com autorização e base adequada. Definir retenção, acesso e exclusão antes do piloto.

## Fases e aceite

1. Inventariar exports reais, pontos de publicação, contratos por fonte e versões traduzidas; definir responsáveis e políticas.
2. Executar em shadow mode sem alterar publicações; medir cobertura, falhas, latência e custo, e avaliar amostra humana para falsos positivos e negativos.
3. Iniciar piloto por portal e fonte, encaminhando casos para rascunho/revisão e impedindo publicação diante de falha ou direitos incertos.
4. Expandir após aceite editorial e operacional, com feature flag por portal/fonte, monitoramento e rollback para novos itens sem apagar evidências.
5. Homologar cópia literal, citação autorizada, paráfrase próxima, cobertura independente do mesmo fato, release licenciado e não licenciado, imagem com direitos desconhecidos, PT/EN, duplicidade interna, erro/timeout e alteração após aprovação. Exercitar cada ponto de publicação e confirmar que erro não publica automaticamente.

## Referências

- [Google: conteúdo útil e confiável](https://developers.google.com/search/docs/fundamentals/creating-helpful-content)
- [Google: recursos de IA na busca](https://developers.google.com/search/docs/fundamentals/ai-optimization-guide)
- [Google: URLs canônicas](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls)
