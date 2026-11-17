# Sprint — CORE SEO/GEO: diagnóstico e absorção

## Objetivo e limites

Preparar uma camada reutilizável de diagnóstico para PEL, com futura avaliação no Mengão 360 e outros portais. O Yoast continua responsável por canonical, metadados, sitemaps e Schema; o protótipo não injeta outro JSON-LD. O futuro subfluxo `SUBFLOW | CORE SEO-GEO VALIDATOR` é independente e começa sem bloquear publicação. Não promete indexação, recomendação ou citação.

Nenhuma chamada ao n8n, instalação de plugin, alteração de robots, cache, banco ou publicação ocorreu nesta etapa. Não há conector n8n disponível nesta sessão. O protótipo local em `tools/seo-geo-validator/` é uma função pura, não um workflow importável nem integração com o Core de produção.

## Baselines e evidência

- O usuário informou instalação no PEL da candidata SEO 0.88 baseada no ZIP 0.87. O ZIP 0.87 recebido tem SHA-256 `69fcc93dca6b17b3c1262cc7a1f8928c63027040065cf971749f07be55fd95e9`; o pacote SEO 0.88 registrado tem SHA-256 `ff524f1423837a98634d57eda6544009ccfbb1a8e8a87a44083f25bce23f3340`. Não se comprovou o hash dos arquivos efetivamente instalados no servidor. Antes de novo pacote de produção, obter o artefato efetivamente instalado ou equivalência verificável, comparar arquivos e preservar rollback.
- Existe outra pré-release 0.88 de conteúdo distinto; a main 0.89 é uma base consolidada em desenvolvimento e não deve ser presumida como conteúdo instalado no PEL.
- O usuário aprovou visualmente os breadcrumbs PT/EN e o layout editorial #4. Uma consulta pontual anterior confirmou um BreadcrumbList do Yoast e nenhum script de breadcrumb do Core em cada artigo piloto.
- Releitura pública em 12/11/2026 09:36 BRT, artigo piloto PT: HTTP 200, resposta com cache HIT, canonical próprio, robots `index, follow`, um `BreadcrumbList`, um breadcrumb visual do Core, zero scripts breadcrumb do Core, dois H1 (artigo e CTA de arrendamento), zero elementos `<main>` e `og:image:type=image/undefined`. Resultado observado na resposta recebida; não indica autoria do problema nem prova que conteúdo armazenado ou todos os caminhos estejam iguais. A presença ou ausência de um `<article>` envolvendo especificamente o conteúdo não foi reconfirmada.
- No mesmo horário, a URL normal do artigo EN continuava com cache HIT e o texto `Início` no BreadcrumbList. A consulta anterior com URL de verificação/cache MISS exibiu `Home`. O usuário informou atualização `wordpress-seo` / `breadcrumbs-home` no Polylang e solicitou limpeza; falta reconferir a URL normal após sua conclusão.
- `robots.txt` respondeu HTTP 200 com grupo específico OAI-SearchBot e sitemap Yoast. Não havia grupo GPTBot; a política de treinamento precisa de decisão explícita, sem inferir consentimento. Um user-agent simulado não autentica rastreador oficial. Não se conferiu neste ciclo a totalidade do sitemap ou as 24.065 URLs do levantamento anterior.
- MIME efetivo, alt da imagem destacada no HTML, sitemap de imagens, semântica do resumo e dos contêineres e fontes editoriais ainda exigem reauditoria direcionada e comparação com os dados de mídia; não atribuir o defeito a plugin ou workflow sem evidências.

## Contrato e implantação incremental

1. Definir versão e origem da evidência para cada observação: portal, idioma, URL, data, status e forma de coleta. Isolar configurações de portal e nunca transportar credenciais ou conteúdo privado entre ambientes.
2. Um coletor futuro gera observações determinísticas e auditáveis; o núcleo diagnóstico devolve achados `pass`, `review` ou `unknown`. Falha de rede, informação ausente ou parser indisponível permanece desconhecida. Critérios e severidades ainda exigem homologação editorial; nenhum resultado bloqueia publicação.
3. Uma etapa semântica opcional poderá avaliar atribuições e fontes sem declarar precisão factual como comprovada por um LLM. Reter referências verificáveis e registrar versão de modelo e revisão humana. Aplicar minimização de dados e limites de retenção em logs, sem segredos.
4. Definir contrato JSON versionado, exemplos para PT/EN, procedência, erro operacional, identidade de execução, idempotência e políticas por portal. Adicionar, depois de aprovado, classificações `APPROVED`, `REVIEW_REQUIRED` e `REJECTED` e seus limites explícitos.
5. Preparar workflow n8n isolado, sem ligação aos workflows de produção, sem polling e com limite de carga. Testar artigos reais anonimizados, cobertura, falsos positivos e custo; habilitar integração somente após homologação e aprovação editorial.
6. Medir rastreamento em logs verificados, referências identificáveis no GA4 e dados de Search Console, sem supor que todos os acessos ChatGPT contenham UTM ou que rastreamento implique indexação.

Referências: [Política de rastreadores OpenAI](https://platform.openai.com/docs/bots), [documentação do Schema Yoast](https://developer.yoast.com/features/schema/api/) e [robots.txt Google](https://developers.google.com/crawling/docs/robots-txt/robots-txt-spec).

## Rollback

O protótipo não participa da publicação. Reverter sua introdução no repositório remove somente documentação, testes e ferramenta local. Qualquer implantação futura precisa de um plano próprio, backup de configuração e reversão por portal.
