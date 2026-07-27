# ADR-0010 — Portable Safe Bootstrap

Status: aceita para implementação.
Data: 24/07/2026.
Complementa: `ADR-0008` e `ADR-0009`.

## Contexto

O M360 Core concluiu no Mengão 360 a absorção de Home Editorial e Semantic Relations, atingindo cobertura semântica integral e ownership automático. Essa maturidade autoriza iniciar a frente de desacoplagem, mas não autoriza transportar para outro portal os defaults, campanhas, templates ou regras operacionais do ambiente precursor.

O Portal Energia Limpa será o primeiro ambiente a validar a portabilidade. Ele já possui tema, Polylang, MailPoet e monetização aprovada pelo Google AdSense. O M360 deve entrar como camada evolutiva, priorizando internacionalização, produtividade editorial e Content Discovery & SEO sem assumir imediatamente o tema, a monetização, a Newsletter ou a CMP existentes.

## Decisão

Toda instalação passa a ter uma política explícita de implantação:

- `legacy-compatible`: preserva o comportamento homologado de uma instalação M360 existente;
- `portable-safe`: padrão de toda instalação nova, sem ownership público automático.

No modo `portable-safe`, as capacidades abaixo são independentes e desativadas por padrão:

- takeover de templates de busca, autor, categoria, tag e data;
- runtime e shortcodes de Ads;
- inserção automática de Ads no conteúdo;
- endpoint, cron, formulário e renderização da Newsletter;
- sinais e interface pública de Consent.

Editorial e Content Discovery continuam controlados pelo registro modular e permanecem desativados por padrão em instalações novas.

## Compatibilidade

- upgrades de instalações que já possuem `m360_core_version` recebem automaticamente `legacy-compatible`;
- nenhuma configuração homologada do Mengão 360 é zerada;
- campanhas e criativos existentes permanecem no banco;
- novas instalações criam somente schemas e inventário genérico, sem campanhas ou criativos esportivos;
- Site Profile schema 2 inclui a política e os gates portáteis;
- importações de Site Profile schema 1 continuam aceitas e preservam a política local.

## Limites

- `portable-safe` não habilita automaticamente componentes no front-end;
- o perfil não exporta conteúdo, pessoas, consentimentos, assinantes, campanhas, criativos, credenciais ou segredos;
- a interface local de Consent não substitui uma CMP certificada em produção;
- Content Discovery melhora malha interna e descoberta, mas não substitui sozinho sitemap, canonical, metadados ou schema de um plugin de SEO técnico;
- a monetização AdSense existente no portal de destino deve permanecer intocada durante o piloto.

## Gate de produção

Uma capacidade só pode ser habilitada após inventário, shadow/preview, evidência de locale, teste de conflito, plano de rollback e autorização explícita por ambiente.
