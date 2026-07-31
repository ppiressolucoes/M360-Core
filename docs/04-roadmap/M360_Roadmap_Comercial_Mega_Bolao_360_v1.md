# Mega Bolão 360 — Roadmap Comercial

Versão: v1.0

## Visão

Mega Bolão 360 será a evolução comercial do Bolão WC operacional.

Missão:
Oferecer ao usuário uma ferramenta para criação de bolões por competições existentes no DW Esportivo.

## Base existente

- Bolão WC operacional.
- DW Esportivo maduro.
- API externa consolidada.
- WordPress + Elementor + News Portal.
- Ranking, ligas, convite WhatsApp e apuração.

## Proposta de valor

Crie seu bolão online em minutos. Escolha a competição, convide amigos e acompanhe ranking automático.

## Público-alvo

Torcedores, grupos de amigos, empresas, bares, restaurantes, comunidades esportivas, influenciadores e páginas de clubes.

## MVP

1. Cadastro básico.
2. Login.
3. Escolha da competição.
4. Criação de bolão.
5. Link/código de convite.
6. Participantes.
7. Palpites.
8. Ranking.
9. Compartilhamento WhatsApp.
10. Plano Free.
11. Plano Pago preparado.

## Competições iniciais

- Copa do Mundo FIFA 2026.
- Copa Libertadores 2026.
- Brasileirão Série A 2026.

## Planos

### Free

- Criar 1 bolão.
- Limite de participantes.
- Ranking geral.
- Regras padrão.
- Convite WhatsApp.
- Marca Mega Bolão 360.

### Pago / Avançado

- Múltiplos bolões.
- Mais participantes.
- Ranking por rodada/fase/liga.
- Personalização.
- Regras customizadas.
- Página pública.
- Exportação CSV.
- Suporte prioritário.

## Landing page

URL sugerida: /mega-bolao-360/

Estrutura:
1. Hero.
2. Como funciona.
3. Competições disponíveis.
4. Benefícios.
5. Planos.
6. FAQ.
7. CTA.

CTA principal: Criar meu bolão grátis.

## SEO

Palavras-chave:
- criar bolão online
- bolão futebol online
- bolão copa do mundo 2026
- bolão brasileirão
- bolão libertadores
- bolão entre amigos
- site para criar bolão

## Sprints comerciais

1. Landing page.
2. Criação de bolão pelo usuário.
3. Plano pago preparado.
4. Pagamento/assinatura.
5. Ranking avançado e compartilhamento.

## Atualização canônica — 28/07/2026

Frente atual: **Sprint Comercial C.1 — Mega Bolão 360 Multi-Competition Foundation**, sem número de release. A versão `v0.7.0` permanece reservada ao M360 Publisher Platform.

Arquitetura de produto: o M360 Core é o plugin editorial e o Mega Bolão 360 é
o plugin esportivo integrado ao DW/ETL de competições. Eles permanecem
independentes e paralelos, cada um com repositório, código, banco de domínio,
administração, versionamento, release e pacote ZIP próprios. Nenhum será
incorporado ao outro e não haverá dependência de runtime.

O primeiro incremento será administrado pelo portal. Autoatendimento, planos e monetização continuam neste roadmap, mas não fazem parte da fundação C.1.

Escopo da fundação:

1. recuperar e versionar o baseline real do plugin;
2. criar bolões administrados por competição e temporada elegíveis no DW;
3. isolar participantes, palpites, apuração e ranking por bolão;
4. suportar pontos corridos, grupos + ida/volta e mata-mata;
5. bloquear palpites no cliente e no servidor quando o confronto estiver indefinido, iniciado ou finalizado;
6. sincronizar de forma idempotente após o ETL;
7. preservar ligas, convites e ranking já validados;
8. manter PT-BR/EN-US, auditoria, homologação e rollback;
9. preservar intervenções urgentes por override temporário, auditado e conciliado.

Competições da fundação:

- Brasileirão Série A 2026;
- Copa Libertadores 2026;
- Copa do Brasil 2026.

A Copa do Mundo FIFA 2026 permanece como baseline operacional e cenário de regressão.

Situação do marco 0:

- repositório: `ppiressolucoes/m360-bolao`;
- commit de produção: `5c7e4e4`;
- tag: `baseline-production-0.1.0-assets-0.1.4`;
- PR documental: `m360-bolao#1`;
- issue de aceite e governança: `M360-Core#24`.

Situação do marco 1 — fundação implementada:

- commit funcional: `m360-bolao@c8f645c`;
- draft PR: `m360-bolao#1`;
- migração C.1 explícita, idempotente e desabilitada por padrão;
- catálogo de competições elegíveis lido do DW;
- administração e estados de bolão;
- isolamento de participantes, palpites, rankings e ligas;
- bloqueio de palpites no cliente e no AJAX;
- sincronização pós-ETL com hash, chave idempotente e lock;
- override temporário com fonte, validade, auditoria e reconciliação;
- escrita direta em `fato_jogos` removida do painel.

O marco 1 está pronto para homologação, não para produção. Permanecem pendentes
o lint PHP nativo, a migração em cópia do banco, a regressão da Copa, os três
modelos esportivos, o rollback e o ZIP próprio.

Fronteira de dados:

- DW Esportivo e ETL são a única fonte de verdade para jogos, times, horários, status e resultados oficiais;
- o WordPress não altera diretamente `fato_jogos`, dimensões ou resultados oficiais;
- atraso da API pode ser tratado por override temporário com autor, motivo, validade, trilha de auditoria e reconciliação automática;
- dumps, dados de produção e segredos não integram o repositório.

## Atualização canônica — 31/07/2026

Marco 2 — migração e abertura controlada do primeiro bolão multi-competição:

- migração C.1 aplicada em produção e novamente bloqueada;
- protótipo da Copa do Mundo preservado como `ENCERRADO`;
- Bolão Brasileirão Série A 2026 criado, aberto e publicado pelo fluxo administrado;
- gate do DW aprovado com 380 jogos, 170 futuros, confrontos definidos e horários presentes;
- gravação e recuperação de palpite validadas;
- visibilidade restrita `ADMIN` e publicação explícita `PUBLICO` validadas;
- experiência PT-BR e EN-US validada para usuários autenticados e visitantes;
- pacote corretivo v0.1.7 preparado para invalidar o cache público após mudanças de estado ou visibilidade.

O modelo de pontos corridos está homologado. Permanecem como próximos marcos:
sincronização/apuração pós-ETL em janela real, isolamento após apuração,
overrides temporários e os modelos Libertadores e Copa do Brasil.
