# M360 Core 0.7.4.0.88 — breadcrumb SEO (pacote aplicado no PEL)

Pacote pontual baseado na versão 0.7.4.0.87 de produção. Mantém o breadcrumb visual M360 e, quando o Yoast está carregado, desativa por padrão o BreadcrumbList adicional do Core. Sem Yoast o Schema do Core permanece; o filtro `m360_breadcrumb_schema_enabled` permite reativá-lo e o atributo `schema="false"` prevalece.

SHA-256 do ZIP: `ff524f1423837a98634d57eda6544009ccfbb1a8e8a87a44083f25bce23f3340`. Baseline 0.87: `69fcc93dca6b17b3c1262cc7a1f8928c63027040065cf971749f07be55fd95e9`. A comparação dos 127 arquivos da baseline e do pacote apontou somente o arquivo principal de versão e a classe de navegação.

O usuário informou a atualização do PEL e aprovou visualmente o breadcrumb em PT e EN. A inspeção pública confirmou um BreadcrumbList Yoast por artigo piloto e nenhum BreadcrumbList adicional do Core. Após a tradução no Polylang, uma consulta em inglês sem cache apresentou `Home`; a URL normal ainda apresentou `Início` com cache HIT na última verificação. A limpeza solicitada precisa ser confirmada na URL normal.

Os testes locais sintéticos passaram para detecção do Yoast, ausência, filtro e `schema=false`, em PT/EN; esses cenários não foram todos homologados numa instalação WordPress real. São necessários testes adicionais antes de generalizar a homologação para outros portais.

Há uma pré-release diferente já publicada com tag `v0.7.4.0.88` e outro ZIP. Esta release documental usa uma tag específica para não substituir aquele artefato nem confundir sua procedência. A main consolidada está identificada como 0.7.4.0.89 em desenvolvimento; este pacote pontual 0.88 não contém automaticamente as demais alterações dessa base.

Rollback: reinstalar o pacote 0.87 preservado e limpar caches após backup das configurações, se houver regressão.
