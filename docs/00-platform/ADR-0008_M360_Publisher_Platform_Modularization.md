# ADR-0008 — M360 Core como Publisher Platform Modular

Status: aceita para planejamento
Data: 2026-07-17
Baseline de origem: `M360 Core v0.6.5.4`

## Contexto

O M360 Core consolidou no Portal Mengão 360 componentes próprios de interface, internacionalização, publicidade, privacidade e Newsletter. Os plugins precursores `M360 Home Editorial` e `M360 Semantic Relations` mantêm capacidades complementares de composição editorial e descoberta de conteúdo.

O Portal Energia Limpa — PEL já existe, é maduro e permanece em operação. O objetivo futuro não é migrar seu conteúdo ou substituí-lo de forma abrupta, mas utilizar o M360 como camada progressiva de evolução, produtividade e independência do tema e do Elementor.

## Decisão

O M360 Core evoluirá para uma Publisher Platform modular, distribuída como um único produto instalável e composta por módulos internos ativáveis.

- `M360 Home Editorial` será generalizado e absorvido como `M360 Editorial Layout & Home`.
- `M360 Semantic Relations` será generalizado e absorvido como `M360 Content Discovery & SEO`.
- Newsletter, Ads, Privacy, Internationalization e componentes de interface serão capacidades portáveis.
- o plugin `Mengão 360 — Bolão`, o DW Esportivo, ETLs, APIs e regras esportivas permanecerão fora do núcleo exportável.
- cada portal conservará conteúdo, identidade, taxonomias, integrações e configurações próprias.

## Princípio de distribuição

Um único ZIP não significa um código monolítico. Cada módulo deverá declarar ciclo de vida, dependências, permissões, schema, configurações, assets, diagnóstico e possibilidade de desativação.

```text
M360 Core
├── Kernel e contratos
├── UI Components
├── Editorial Layout & Home
├── Content Discovery & SEO
├── Internationalization
├── Newsletter
├── Advertising
├── Privacy & Consent
├── Diagnostics
└── Site Profile
```

## Limite do núcleo portável

Entram no núcleo:

- SEO técnico e descoberta;
- internacionalização;
- Newsletter desacoplada de provedor;
- publicidade e inventário;
- privacidade e consentimento;
- navegação, busca, views, layouts e componentes independentes;
- auditoria, diagnóstico e configuração por portal.

Não entram:

- DW Esportivo;
- Bolão, palpites, jogos, competições, rankings e apuração;
- ETLs e APIs esportivas;
- identidade, conteúdo e taxonomias específicas do Mengão 360;
- dados ou configurações privadas de uma instalação.

## Migração dos plugins precursores

Os plugins atuais continuarão ativos até que cada módulo equivalente seja auditado, implementado, migrado e homologado. A absorção seguirá uma estratégia reversível:

1. inventário técnico;
2. definição de contratos;
3. implementação paralela;
4. importação controlada de configurações e dados;
5. comparação de saída e diagnóstico;
6. homologação PT-BR/EN-US;
7. desativação do plugin antigo com rollback disponível;
8. remoção somente após estabilização.

## Portal Energia Limpa — PEL

O PEL será a segunda implementação real da plataforma. A adoção ocorrerá componente a componente, preservando o portal existente e permitindo convivência temporária com tema e Elementor.

O perfil PEL configurará identidade, idiomas, taxonomias, regras de relações, home, Newsletter, Ads e consentimento sem importar regras esportivas.

## Consequências

Benefícios:

- reutilização das capacidades homologadas;
- redução progressiva da dependência de tema e page builder;
- produtividade editorial;
- consistência entre portais;
- evolução independente do nicho;
- segunda implementação como prova de portabilidade.

Riscos controlados:

- aumento da responsabilidade do pacote;
- conflitos durante coexistência;
- duplicidade de cron, shortcodes, schema ou renderização;
- migração de opções e snapshots;
- necessidade de carregamento condicional e rollback por módulo.

## Linha de entrega

- `v0.7.0 — Publisher Platform Foundation`;
- `v0.7.1 — Editorial Layout & Home`;
- `v0.7.2 — Content Discovery & SEO`;
- `v0.7.3 — Portable Newsletter, Ads & Consent`;
- piloto progressivo no Portal Energia Limpa.

Este ADR complementa o ADR-0007 e prevalece sobre a regra anterior de independência permanente dos plugins Home Editorial e Semantic Relations. O Bolão permanece independente.
