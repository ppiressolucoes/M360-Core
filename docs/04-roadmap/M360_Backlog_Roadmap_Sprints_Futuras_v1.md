# Mengão 360 — Backlog Priorizado e Sprints Futuras

Versão: v1.0

Atualização canônica: 2026-07-28

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

## Atualização canônica — Sprint Comercial C.1

Status: em execução, sem número de release. Esta seção substitui, para execução corrente, a antiga “Sprint futura C — Mega Bolão 360 MVP Comercial”; autoatendimento e monetização permanecem no backlog posterior.

Separação obrigatória: Mega Bolão 360 e M360 Core são plugins independentes.
O Core atende o domínio editorial; o Bolão atende o domínio esportivo e o
DW/ETL de competições. Não haverá incorporação de código, dependência de runtime,
pacote ZIP compartilhado ou migrations cruzadas.

Marco 0 — discovery e baseline: concluído em 28/07/2026.

- baseline real do plugin publicado em `ppiressolucoes/m360-bolao`, commit `5c7e4e4`;
- tag imutável `baseline-production-0.1.0-assets-0.1.4`;
- documentação de descoberta e fronteiras arquiteturais no PR `m360-bolao#1`;
- schema-only do DW inventariado sem versionar dump, dados ou segredos;
- divergência registrada: cabeçalho do plugin `0.1.0`, constante/assets `0.1.4`;
- intervenção manual preservada como requisito operacional, a ser implementada por override temporário, auditado e conciliado.

Marco 1 — implementação da fundação: concluído em branch em 28/07/2026.

- commit `m360-bolao@c8f645c` no draft PR `m360-bolao#1`;
- migração controlada e backfill do baseline;
- catálogo DW somente leitura e administração de bolões;
- engine comum para os três modelos esportivos;
- isolamento por `bolao_competicao_id`;
- guard de palpites no cliente e no servidor;
- sincronização pós-ETL idempotente;
- overrides temporários auditáveis, sem escrita em `fato_jogos`.

Próximos marcos:

1. lint PHP e migração em cópia do banco de produção;
2. regressão completa do bolão da Copa;
3. homologação assistida em Brasileirão Série A, Copa Libertadores e Copa do Brasil;
4. ensaio de rollback;
5. ZIP independente de homologação.

Fora do escopo desta fundação:

- criação de bolão pelo usuário;
- pagamentos, assinaturas e planos;
- novas modalidades;
- edição direta de fatos esportivos no WordPress.

### Marco 2 — produção controlada (31/07/2026)

Concluído:

- migração C.1 e regressão do bolão encerrado da Copa;
- criação, abertura restrita e publicação do Bolão Brasileirão Série A 2026;
- gate de elegibilidade do DW para pontos corridos;
- palpite autenticado, acesso anônimo e PT-BR/EN-US;
- correção preventiva de invalidação do cache LiteSpeed na v0.1.7.

Próxima prioridade:

1. sincronização e apuração pós-ETL idempotente em janela real;
2. validar isolamento de participantes e rankings após apuração;
3. homologar override temporário e reconciliação;
4. homologar Libertadores e Copa do Brasil;
5. consolidar rollback e release independente do Mega Bolão 360.


## Sprint Comercial C.2 — Mega Bolão 360 Product Hub

Status: em implementação a partir de 03/08/2026.

Esta sprint promove e substitui a antiga “Sprint futura D — Landing Page Mega
Bolão 360” como próxima prioridade comercial da frente esportiva.

Backlog priorizado:

1. shortcode dinâmico do Product Hub;
2. catálogo DW de competições monitoradas;
3. cards de bolões abertos, encerrados, bloqueados e futuros;
4. resolução de páginas PT-BR/EN-US e CTAs;
5. hero, como funciona, contrato de dados e FAQ;
6. responsividade, acessibilidade e isolamento CSS;
7. SEO e dados estruturados;
8. pré-homologação controlada no WordPress;
9. validação anônima e autenticada;
10. rollback e pacote canônico independente.

Dependências: C.1 homologada, DW/ETL operacional e bolões administrados pelo
portal. O M360 Core editorial não é dependência de runtime.

### Catálogo internacional após C.2

Preparar, por prioridade comercial e disponibilidade real no DW, UEFA Champions
League, Bundesliga, Eredivisie, Primera División/La Liga, Ligue 1, Championship,
Primeira Liga, European Championship, Serie A italiana e Premier League.

Cada entrada depende de dimensão/temporada, modelo esportivo suportado, carga em
`fato_jogos`, traduções PT-BR/EN-US, gate de abertura e publicação administrada.

### Pós-homologação visual do Product Hub

Validar navegação móvel, SEO/schema.org, métricas de interação dos CTAs e ordenação comercial configurável dos cards.


## Sprint Comercial C.3 — Fundação em execução

1. shortcodes componíveis e retrocompatibilidade;
2. edição da ordem pelo Elementor/Gutenberg;
3. cards Free, Jogador e Dirigente sem cobrança;
4. URLs de CTA configuráveis;
5. próximo gate: catálogo administrativo, limites numéricos e entitlement no servidor.
