# Roadmap — M360 Core

## Baseline canônico — julho de 2026

- Arquitetura vigente: `M360 Platform Architecture v2.2`.
- Baseline oficial homologada: `v0.7.0 — Publisher Platform Foundation`.
- Próxima linha estratégica: `v0.7.x — M360 Publisher Platform`.
- Etapa atual: `v0.7.0 — Publisher Platform Foundation` homologada em produção; próxima entrega `v0.7.1 — Editorial Layout & Home`.
- SEO Technical Readiness passa a compor a baseline e os contratos do módulo `Content Discovery & SEO`.

As seções históricas abaixo preservam a evolução original. Para novas implementações, prevalece a baseline `v0.7.0`.

## v0.7.x — M360 Publisher Platform

Objetivo: transformar as capacidades homologadas do M360 em uma camada editorial modular, independente de nicho e aplicável progressivamente ao Portal Energia Limpa — PEL.

Sequência:

1. auditoria dos plugins precursores;
2. `v0.7.0 — Publisher Platform Foundation`;
3. `v0.7.1 — Editorial Layout & Home`;
4. `v0.7.2 — Content Discovery & SEO`;
5. `v0.7.3 — Portable Newsletter, Ads & Consent`;
6. piloto progressivo no PEL.

O Bolão, DW Esportivo, ETLs e regras esportivas permanecem fora do núcleo portável.

## v0.6.3 — Newsletter Configuration & Form Hardening

Objetivo: retirar configurações operacionais do código e fortalecer o formulário público sem alterar o fluxo MailPoet homologado.

Escopo planejado:

- seleção administrativa da lista MailPoet, eliminando o ID `3` fixo no adaptador;
- validação da lista antes de salvar e fallback seguro quando indisponível;
- honeypot, tempo mínimo de preenchimento e rate limit configurável;
- mensagens claras para sucesso, duplicidade, indisponibilidade e excesso de tentativas;
- estado de carregamento, bloqueio de duplo envio e foco acessível no retorno;
- configuração do texto e da versão de consentimento;
- diagnóstico do formulário no painel Newsletter;
- preservação da auditoria, Double Opt-In e cancelamento assinado já homologados.

## v0.5.x — Plataforma Comercial e Experiência M360

### Concluído

1. `v0.5.0 — Ads Manager Slot Management UX`: filtros, agrupamentos, estados e salvamento único.
2. `v0.5.1 — AdSense Approval Readiness`: auditoria de cobertura e recolhimento de slots vazios.

### Próximas entregas

3. `v0.5.2 — Multilingual Post Navigation`: Post Info individual entregue; evolução dos Breadcrumbs permanece como próximo incremento de navegação.
4. `v0.5.3 — M360 Search Experience`: formulário robusto, acessível, multilíngue e reutilizável nas variantes hero, header e compact.
5. `v0.5.4 — Header Search & Ad Orchestration`: campanha elegível, AdSense ou busca como fallback útil do cabeçalho.
6. `v0.5.4.1 — Latest News Sidebar Mode`: variante compacta, uniforme e com publicidade interna configurável.
7. `v0.5.4.2 — Latest News List UX`: destaque editorial e paginação opcional no layout de página.
8. `v0.5.4.3 — Current Post Exclusion`: remoção automática do artigo em leitura das listas do componente.
9. `v0.5.5 — Breadcrumb Navigation UX`: navegação hierárquica multilíngue, elegante, acessível e com schema.

Toda evolução deve preservar PT-BR, EN-US, SEO, acessibilidade, responsividade, APIs e shortcodes homologados.

## v0.6.0 — M360 Privacy & Consent Foundation

Objetivo: implantar uma arquitetura híbrida de privacidade que utilize uma CMP certificada como motor de consentimento e mantenha no M360 Core uma camada independente de integração.

Escopo básico planejado:

- prova de conceito e seleção de CMP certificada pelo Google, com suporte a PT-BR e EN-US;
- categorias M360 normalizadas: necessários, preferências, analytics, publicidade e mídia externa;
- `M360 Consent Adapter` desacoplado do fornecedor escolhido;
- integração com Google Consent Mode v2 e M360 Ads Manager;
- central de preferências com aceite, rejeição, gestão e revogação;
- inventário inicial de cookies, scripts e fornecedores;
- diagnóstico administrativo de prontidão e carregamento condicionado;
- validação jurídica dos textos, bases legais e prazos de retenção antes da publicação.

Primeiro incremento implementado:

- painel administrativo, categorias e contrato independente;
- modos CMP externa e fundação local de homologação;
- Consent Mode v2 e integração opcional com Ads Manager;
- central PT-BR/EN-US com aceite, rejeição, personalização e revogação;
- APIs e filtros para adaptadores certificados futuros.

Ficam fora do primeiro incremento: CMP própria, geolocalização própria, banco duplicado de provas de consentimento e automação jurídica avançada.

## Visão

O M360 Core é o framework de interface do Projeto Mengão 360. Sua missão é reduzir progressivamente a dependência do tema News Portal e do Theme Builder do Elementor, consolidando componentes próprios, multilíngues e reutilizáveis.

## Release 2.x — Navigation + Dynamic Views

### Concluído

- Main Navigation.
- Section Navigation.
- Breadcrumb inteligente.
- Author Hub.
- Search Engine funcional.
- Layout Foundation.

### Em transição

- Search ainda possui dependência residual do Theme Builder para header/footer/layout.
- Author Hub estabilizado via rota canônica M360.

## Release 3.x — View Engine e Radar News

### v0.3.3 — Radar News Engine + View Engine Consolidation

Objetivo:

- Criar o M360 View Engine.
- Entregar Radar News / Latest News independente do News Portal.
- Reutilizar Grid, Cards, Paginação, Breadcrumb e Schema.

Shortcodes previstos:

```text
[m360_latest_news]
[m360_view view="latest_news"]
```

Critérios de aceite:

- `/ultimas-noticias/` funcional.
- `/en/latest-news/` funcional.
- PT-BR e EN-US consistentes.
- Modo claro/escuro sem conflito com News Portal.
- Schema `CollectionPage`, `ItemList` e `BreadcrumbList`.

## Release 3.4 — Category Engine

Objetivo:

- Migrar arquivos de categoria para M360 Core.
- Padronizar cards, paginação e breadcrumb.
- Preparar PT-BR / EN-US / ES-ES.

## Release 3.5 — Tag Engine

Objetivo:

- Migrar arquivos de tags para M360 Core.
- Usar o mesmo View Engine.

## Release 3.6 — Competition Hub

Objetivo:

- Criar navegação e landing dinâmica por competição.
- Integrar com DW Esportivo e Competition Registry.

## Release 4.x — Layout Engine

Objetivo:

- Unificar header, footer, container, sidebar e template routing.
- Reduzir dependência do tema a uma camada de compatibilidade.

Componentes previstos:

- Header Manager.
- Footer Manager.
- Sidebar Manager.
- Template Router.
- Layout Slots.
- Container Manager.

## Release 5.x — Platform UI

Objetivo:

- Consolidar o M360 Core como framework visual completo do portal.
- Preparar suporte ampliado a ES-ES.
- Integrar componentes com M360 Community e Mega Bolão 360.
