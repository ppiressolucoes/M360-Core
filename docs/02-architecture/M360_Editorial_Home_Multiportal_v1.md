# M360 Editorial Home — inventário e contrato multiportal v1

Status: proposta implementável para homologação; não altera a Home de produção.

## Decisão inicial

`v0.7.4.0.20` permanece como baseline de compatibilidade do Mengão 360. A `v0.7.4.0.23` não deve ser promovida integralmente: ela contém correções do piloto do Portal Energia Limpa que não fazem parte do contrato da Home (busca do tema, categoria editorial principal e dicionário externo de Discovery). Essas mudanças devem entrar em incrementos separados, com testes próprios para cada portal.

A implementação en-US usada como referência é o precursor `M360 Home Editorial 0.1.2`. Seus contratos visuais e funcionais foram absorvidos no módulo `editorial-layout-home`, que já oferece `m360_editorial_ticker`, `m360_editorial_hero`, `m360_editorial_section`, `m360_editorial_newsroom` e instâncias persistidas de `m360_editorial_widget`. O precursor continua como fallback durante a homologação.

## Inventário das versões

| Área | Mengão `0.7.4.0.20` | PEL `0.7.4.0.23` | Decisão |
|---|---|---|---|
| Consent Mode/cache | bootstrap e estado efetivo antes do GA4 | mesmo contrato, com ajustes de UI | manter da baseline; validar PEL separadamente |
| Editorial Home | módulo opcional, modos `off/shadow/hybrid/public`, cache versionado | mesmo módulo com ajustes de Newsroom e categorias | adotar contrato comum; UX do PEL fica atrás de aceite |
| Site Profile | schema 2, idioma/runtime | schema 3 com `branding.primary_color` e `secondary_color` | evoluir schema por migração compatível |
| Busca | fluxo M360 existente | redirecionamento `m360q` → `?s=` e template nativo | não acoplar à Home; portar como capacidade isolada |
| Categoria principal | sem resolução adicional | meta M360 → Yoast → Rank Math → filtro → fallback | reutilizar somente nas listagens após teste de regressão |
| Discovery | base compartilhada | dicionário externo, cron e provider adicionais | manter como extensão PEL até contrato multiportal |
| Arquivos no pacote | 104 | 107 | os três exclusivos do PEL não entram automaticamente |

## Contrato de configuração por portal

O código da capa é compartilhado. O perfil do portal fornece identidade, idioma e fontes editoriais:

```json
{
  "schema_version": 1,
  "site_key": "mengao360",
  "site_name": "Mengão 360",
  "default_locale": "pt-BR",
  "supported_locales": ["pt-BR", "en-US"],
  "branding": {"primary_color": "#d71920", "secondary_color": "#b81218"},
  "home": {
    "mode": "shadow",
    "template": "newsroom",
    "featured_source": {"type": "tag", "values": ["featured-en"]},
    "sections": [{"id": "flamengo", "source": {"type": "category", "values": ["flamengo-en"]}, "layout": "grid", "limit": 4}],
    "latest": {"enabled": true, "limit": 8},
    "ads": {"slots": ["home-top", "home-middle", "home-bottom"]}
  }
}
```

`site_key`, `supported_locales`, taxonomias, fontes e URLs são dados do ambiente. O contrato não aceita IDs de posts, IDs Elementor, slugs de um único portal ou decisões de campanha. A publicidade é somente uma referência a slots do M360 Ads; o componente editorial não escolhe criativos.

## Arquitetura de renderização

1. O módulo resolve o perfil e o idioma atual.
2. Cada fonte produz argumentos de `WP_Query` através de um provider filtrável.
3. A consulta usa `post_type`, taxonomias e idioma do perfil, `no_found_rows` e exclusão de itens já renderizados.
4. O resultado de IDs é armazenado em transient com a versão editorial do portal; o HTML continua sem JavaScript para conteúdo e links.
5. A view `newsroom` compõe destaque, cards laterais, metadados semânticos e controles opcionais.
6. Slots de Ads são renderizados pela fachada do M360 Ads nos pontos declarados pelo perfil.

O cache deve ser invalidado quando um post publicável, categoria, tag ou configuração da Home mudar. A invalidação incrementa a versão editorial; não exige apagar transients individualmente e funciona com cache de objeto e CDN após purge da rota.

## Dependências atuais da referência en-US

- WordPress `post` e taxonomia `category`.
- Polylang para filtro de idioma, quando presente.
- Elementor apenas como invólucro da página de homologação e para o precursor; não é requisito do renderer Core.
- Tema News Portal para header/footer e estilos legados durante a convivência.
- M360 Ads para slots comerciais.
- `M360 Home Editorial 0.1.2` somente enquanto o shortcode legado ainda possuir ownership.

Dependências do tema que devem desaparecer do caminho editorial: IDs de templates Elementor, regras de layout do widget do News Portal, categorias hard-coded e textos `en-US` embutidos em shortcode.

## Rollout do Mengão 360

1. **Shadow:** ativar o módulo e comparar IDs, ordem, idioma, títulos, links, contagem, categoria e origem dos itens com a Home en-US atual. Nenhum HTML público do Core.
2. **Homologação isolada:** publicar uma página não indexada com `[m360_editorial_newsroom]` ou `[m360_editorial_widget id="mengao-home-en"]`, preservando a Home de produção.
3. **Hybrid:** manter o precursor nos nomes `m360_news_*`, publicar a composição Core por shortcode próprio e validar PT-BR, EN-US, desktop, tablet, mobile, acessibilidade, SEO e Ads.
4. **Canário:** direcionar somente uma rota de teste ou grupo interno para a Home Core; medir erros, cache hit, tempo de resposta, CLS e origem dos conteúdos.
5. **Cutover reversível:** alterar o ownership para `core` somente após aceite. Rollback = retornar para `hybrid`/`off` e reativar o precursor, sem remover opções ou caches.

Na entrega definitiva, o módulo deve ficar com `mode=public`. Esse estado registra o filtro de front page do Core e usa `templates/home.php`; os modos `hybrid` e `off` continuam disponíveis para rollback operacional.

## Critérios de aceite da primeira capa

- nenhum `<h1>` duplicado pelo tema;
- links e conteúdo funcionam sem JavaScript;
- consultas não repetem o mesmo post entre destaque e seções sem fallback explícito;
- PT-BR e EN-US não misturam categorias, menus ou rótulos;
- imagem sem thumbnail não quebra a composição e recebe fallback acessível;
- slots Ads aparecem apenas quando elegíveis pelo Ads;
- alteração de post, taxonomia ou configuração muda a versão de cache;
- diagnóstico informa modo, perfil, idioma, widgets, cache e precursor ativo;
- Home atual continua restaurável em uma única mudança de configuração.

## Próximos incrementos

- migrar o bloco `branding` para o Site Profile com schema compatível (`2 → 3`), sem portar correções PEL não relacionadas;
- criar uma tela de prévia/diagnóstico da Home que exponha provider, IDs, cache e fallback;
- adicionar teste de contrato para dois perfis mínimos: Mengão (`pt-BR/en-US`) e PEL (`pt-BR`);
- após o aceite do Mengão, cadastrar o perfil Boa Trilha sem copiar código ou shortcodes específicos.
