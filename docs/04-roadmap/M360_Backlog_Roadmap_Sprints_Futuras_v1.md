# Mengão 360 — Backlog Priorizado e Sprints Futuras

Versão: v1.0

Atualização canônica: 2026-07-16

Baseline de referência: `M360 Core v0.6.5.4`

Newsletter: objetivo inicial concluído; evoluções avançadas permanecem adiadas.

## P0 — Alta prioridade

1. Obter e auditar os pacotes-fonte `M360 Home Editorial` e `M360 Semantic Relations`.
2. Implementar `v0.7.0 — M360 Publisher Platform Foundation`.
3. Incorporar SEO Technical Readiness aos contratos do futuro módulo `Content Discovery & SEO`.
4. Manter versionamento, documentação e rollback dos artefatos estáveis.

## P1 — Crescimento e operação

5. `v0.7.1 — Editorial Layout & Home`.
6. `v0.7.2 — Content Discovery & SEO`.
7. `v0.7.3 — Portable Newsletter, Ads & Consent`.
8. Piloto progressivo no Portal Energia Limpa — PEL.
9. CMP certificada para monetização e regiões sujeitas ao IAB TCF.

## P2 — Evolutivas acumulativas

10. Produto comercial Mega Bolão 360 e sua landing page.
11. Painel Operacional DW.
12. SEO programático e expansão de competições.
13. Auditoria avançada de i18n e dados.

## P3 — Evolução técnica sem bloqueio de negócio

14. Refatoração da engine HTML.
15. Templates HTML em banco ou subworkflows.
16. Automações editoriais adicionais.

## Linha estratégica — M360 Publisher Platform

- decisão normativa: `ADR-0008`;
- estratégia: `M360 Publisher Platform — Estratégia de Evolução v1`;
- baseline de origem: `v0.6.5.4`;
- Home Editorial será absorvido como `Editorial Layout & Home`;
- Semantic Relations será absorvido como `Content Discovery & SEO`;
- PEL será a segunda implementação e prova de portabilidade;
- Bolão, DW Esportivo, ETLs e regras esportivas permanecem externos;
- nenhum plugin precursor será desativado antes de migração homologada e reversível.

## Frente concluída — Newsletter M360

- baseline consolidada: `v0.6.5.4`;
- captação, consentimento, Double Opt-In, cancelamento, sincronização e auditoria homologados;
- prontidão de entrega `7/7`;
- componentes PT-BR/EN-US e posicionamento validados;
- MailPoet configurado para envio diário das seis notícias mais recentes;
- analytics avançado, preferências e múltiplos provedores adiados para uma futura reabertura do módulo.

## Sprint futura A — Versionamento e Operação Estável

- Exportar workflows HTML [1] e HTML [2].
- Salvar nodes canônicos.
- Salvar snippet WordPress.
- Criar checklist de publicação por competição.

## Sprint B — Internacionalização PT-BR/EN-US

Status: concluída, publicada e validada em 28/06/2026.

Referência: pacote `mengao360-internacionalizacao-pt-en-sprint-2026-06-28.zip`.

Pendências remanescentes:
- Validação de hreflang e sitemap EN.
- Search Console, indexação e Core Web Vitals.
- ES permanece como evolução futura.

## Sprint futura C — Mega Bolão 360 MVP Comercial

- Cadastro.
- Criação de bolão pelo usuário.
- Escolha de competição.
- Plano Free.
- Plano Pago preparado.
- Convite WhatsApp.
- Ranking automático.

## Sprint futura D — Landing Page Mega Bolão 360

- Página /mega-bolao-360/.
- Copy de vendas.
- Planos.
- CTA.
- FAQ.
- SEO.

## Sprint futura E — Monetização

- WooCommerce ou solução equivalente.
- Assinatura ou compra por competição.
- Controle de permissões por plano.

## Sprint futura F — SEO Programático

Páginas candidatas:
- /artilharia-brasileirao-serie-a-2026/
- /classificacao-brasileirao-serie-a-2026/
- /jogos-brasileirao-serie-a-2026/
- /artilharia-libertadores-2026/
- /jogos-copa-do-mundo-2026/

## Sprint futura G — Novas Competições

Ordem recomendada:
1. Brasileirão Série B.
2. Copa do Brasil.
3. Mundial de Clubes.
4. Campeonato Carioca.
5. Champions League.
6. Premier League.

## Sprint futura H — Painel Operacional DW

Indicadores:
- Última carga ETL.
- Último cache HTML.
- Widgets por competição.
- Caches desatualizados.
- Jogos pendentes.
- Falhas de apuração.

## Sprint futura I — Refatoração HTML Evolutiva

Diretrizes:
- Não bloquear operação atual.
- Separar componentes.
- Avaliar templates em banco.
- Avaliar subworkflows.
