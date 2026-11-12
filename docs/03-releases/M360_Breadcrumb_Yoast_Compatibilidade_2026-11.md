# Breadcrumb do Core e Yoast — consolidação

O shortcode `[m360_breadcrumb]` mantém a navegação visual; quando o Yoast está carregado, o JSON-LD `BreadcrumbList` do Core fica desativado por padrão para evitar duplicação. Sem Yoast, o Schema do Core continua ativo. A detecção usa `WPSEO_VERSION` ou classes Yoast já carregadas; ela **não comprova** que o Schema ou a peça Breadcrumb do Yoast estejam ativos.

Em instalações que tenham confirmado a ausência dessa peça no HTML, é possível restaurar o Schema do Core:

```php
add_filter('m360_breadcrumb_schema_enabled', '__return_true');
```

O atributo `[m360_breadcrumb schema="false"]` prevalece sobre o filtro. Não ativar ambos provedores de BreadcrumbList na mesma página.

## Evidências e limites

- O ZIP 0.7.4.0.87 de produção, SHA-256 `69fcc93dca6b17b3c1262cc7a1f8928c63027040065cf971749f07be55fd95e9`, serviu de base à candidata SEO 0.88, SHA-256 `ff524f1423837a98634d57eda6544009ccfbb1a8e8a87a44083f25bce23f3340`. Diferiam apenas o arquivo principal (versão) e a classe de navegação.
- O usuário informou a instalação da candidata SEO 0.88 no PEL e ausência de erros aparentes. Na consulta pública posterior ao artigo piloto, o HTML respondeu 200 e apresentou um BreadcrumbList, um gráfico Yoast e um elemento de navegação do Core, sem script de Schema do Core. É uma verificação pontual; não comprova todos os templates ou idiomas.
- Testes sintéticos PT/EN em processos separados cobrem Yoast detectado, ausência de Yoast, filtro de reativação e `schema=false`, além de links e acessibilidade. Eles não substituem uma homologação WordPress/Yoast real para todos os cenários.
- O GitHub já contém uma pré-release diferente com versão 0.88. Esta correção foi transplantada para a base consolidada e identificada no código como 0.89 em desenvolvimento. A 0.89 não corresponde ao ZIP SEO 0.88 instalado no PEL e inclui outras mudanças da consolidação anterior que continuam pendentes de homologação. Não declarar a 0.89 implantada no PEL.

## Regressão e rollback

Antes de instalar qualquer versão consolidada, homologar em staging PT/EN, com e sem Yoast, Schema do Yoast ativo e desativado, filtro e `schema=false`. Verificar o HTML anônimo após limpar os caches. Reter o ZIP instalado e backup das configurações; em caso de regressão, reinstalar a versão anterior e limpar os caches.
