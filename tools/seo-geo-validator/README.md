# CORE SEO/GEO Validator — protótipo diagnóstico local

Este núcleo portátil recebe observações de um artigo e devolve um JSON de achados. É uma prova de contrato: não é workflow n8n importável, não consulta redes nem WordPress, não executa LLM, não grava logs, não publica e não altera conteúdo. Não incluído no ZIP do plugin. Os dados são fornecidos por um coletor confiável externo, que ainda precisa ser construído e homologado.

```js
const { validate } = require('./validator.cjs');
const report = validate({
  site_id: 'pel', locale: 'pt-BR', url: 'https://example.org/noticia/',
  observations: { http_status: 200, breadcrumb_list_count: 1, h1_count: 1, main_count: 1, og_image_type: 'image/jpeg' }
});
```

Retorna `contract_version`, `mode`, identidade do portal e URL, `classification`, `publication_decision`, motivo e `findings` com `code`, `outcome` (`pass`, `review`, `unknown`) e `observed`. Ausência é `unknown`, não aprovação. Dados malformados geram erro explícito. A classificação é sempre `REVIEW_REQUIRED` e a decisão de publicação é sempre `null`; `APPROVED` e `REJECTED` são apenas opções propostas para futura política editorial, não implementadas.

As regras atuais são sinais provisórios para revisão de **artigos públicos**: HTTP diferente de 200, quantidade de BreadcrumbList diferente de 1, número de H1 diferente de 1, ausência de main e tipo OG fora da lista reconhecida. Elas não provam erro em todo contexto nem são critérios impeditivos. `main_count` não prova a estrutura semântica; o tipo declarado não prova o formato efetivo da imagem. Em seguida serão necessários contexto por tipo de página, fontes, versão e horário das evidências, validação de URLs e limites por portal, canonical, robots, sitemaps, conteúdo, indexação e compatibilidade de idiomas. Consultas em rede futuras exigem allowlist, bloqueio de redes privadas e redirecionamentos inseguros, limites de tempo/tamanho/taxa e execução fora de requisições públicas.

Executar testes: `node tests/seo-geo-validator.cjs`. Nenhum workflow de produção usa este módulo.
