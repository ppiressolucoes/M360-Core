# M360 Publisher Platform — Estratégia de Evolução v1

Status: plano consolidado
Data: 2026-07-17
Ambientes precursores: Portal Mengão 360 e Portal Energia Limpa — PEL

## Visão

Transformar o M360 Core em uma ferramenta modular de produtividade e alavancagem para portais editoriais WordPress, preservando a operação de cada portal e reduzindo progressivamente dependências de tema e Elementor.

O Mengão 360 é o ambiente precursor das capacidades. O PEL será a segunda implementação, validando que SEO, internacionalização, Newsletter, Ads, privacidade e componentes independentes funcionam sem regras esportivas.

## Objetivos

- concentrar capacidades editoriais reutilizáveis em um produto instalável;
- absorver de forma segura o Home Editorial e o Semantic Relations;
- permitir ativação independente dos módulos;
- configurar identidade e comportamento por portal;
- manter dados, conteúdo e domínio de negócio em cada instalação;
- fornecer transição progressiva e reversível;
- provar a portabilidade no PEL.

## Capacidades da plataforma

### Editorial Layout & Home

- blocos configuráveis;
- composição por idioma;
- templates próprios;
- header, conteúdo e footer independentes;
- presets e condições de visibilidade;
- integração transitória com Elementor.

### Content Discovery & SEO

- relações internas configuráveis;
- conteúdos e tópicos relacionados;
- snapshots persistidos;
- páginas órfãs;
- links contextuais;
- canonical, hreflang, sitemap e dados estruturados;
- diagnóstico e operação com Search Console.

### Internationalization

- contexto único de idioma;
- componentes traduzíveis;
- menus, header e footer por idioma;
- prevenção de fallback cruzado;
- relação entre traduções;
- integração com Polylang.

### Newsletter

- formulário próprio e consentimento independente;
- Double Opt-In;
- adaptadores de provedor;
- sincronização, auditoria e proteção;
- componentes e mensagens localizáveis.

### Advertising

- inventário de slots;
- campanhas, criativos e fallback;
- inserção em conteúdos e arquivos;
- segmentação por contexto, idioma e dispositivo;
- integração futura com provedores.

### Privacy & Consent

- categorias normalizadas;
- Consent Mode v2;
- contrato desacoplado de CMP;
- bloqueio coordenado de Ads e Analytics;
- central de preferências.

## Perfis por portal

O `Site Profile` conterá somente configurações portáveis: identidade, idiomas, recursos ativados, taxonomias, regras editoriais, posições, integrações e textos. Conteúdo, dados pessoais, campanhas e segredos não serão exportados por padrão.

## Plano de execução

### Fase 0 — Auditoria e contratos

- obter os pacotes-fonte do Home Editorial e Semantic Relations;
- mapear hooks, shortcodes, cron, opções, tabelas, assets e dependências;
- documentar contratos e plano de rollback;
- estabelecer baseline visual, funcional e de SEO.

### Fase 1 — v0.7.0 Publisher Platform Foundation

- kernel modular;
- registro, ativação e dependências;
- schema e migrações por módulo;
- Site Profile;
- diagnósticos;
- carregamento condicional;
- exportação segura de configurações;
- compatibilidade integral com `v0.6.5.4`.

### Fase 2 — v0.7.1 Editorial Layout & Home

- generalizar o Home Editorial;
- migrar configurações do Mengão 360;
- homologar PT-BR/EN-US, desktop e mobile;
- preservar retorno ao plugin precursor.

### Fase 3 — v0.7.2 Content Discovery & SEO

- generalizar o Semantic Relations;
- migrar relações e snapshots;
- impedir duplicidade de HTML e schema;
- validar descoberta, indexabilidade e Search Console;
- preservar retorno ao plugin precursor.

### Fase 4 — v0.7.3 Portabilidade

- remover padrões específicos do Mengão 360 dos módulos portáveis;
- consolidar assistente de configuração por portal;
- validar Newsletter, Ads e Consent com perfil independente;
- preparar instalação paralela no PEL.

### Fase 5 — Piloto PEL

- inventário técnico do portal;
- instalação sem substituição imediata;
- criação do perfil PEL;
- ativação gradual dos componentes;
- medição de SEO, desempenho e produtividade;
- redução progressiva da dependência de tema e Elementor.

## Critérios de sucesso

- nenhum módulo portável depende de futebol, DW Esportivo ou Bolão;
- módulos podem ser ativados individualmente;
- configurações de um portal não vazam para outro;
- plugins precursores podem ser restaurados durante a transição;
- Mengão 360 preserva a experiência homologada;
- PEL utiliza ao menos SEO, internacionalização e um componente independente;
- nenhuma adoção exige migração total ou indisponibilidade prolongada.
