# Handoff — Start do M360 Core no staging do Portal Energia Limpa

## Objetivo da nova tarefa

Preparar e conduzir o primeiro embarque controlado do M360 Core no staging do Portal Energia Limpa — PEL, comprovando a portabilidade da mesma release homologada no Mengão 360.

## Estado de origem

- repositório oficial: `ppiressolucoes/M360-Core`;
- linha vigente: `v0.7.x — M360 Publisher Platform`;
- pacote homologado no Mengão 360: `M360 Core v0.7.4.0.1`;
- SHA-256: `DD614F644E78A047999EF0B93C184AC9E7C1826B99CB915614FA83B94C2BCFE8`;
- Mengão 360: `legacy-compatible`, origem `preserved-existing-profile`;
- módulos Foundation, Editorial e Discovery & SEO: `healthy`;
- Home Editorial e Semantic Relations absorvidos pelo Core e precursores desativados;
- Content Discovery & SEO no Mengão: writer e renderer automáticos, cobertura homologada em 100%.

## Contexto do PEL

- WordPress existente, maduro e ativo;
- tema atual com baixa escalabilidade, mas deve ser preservado no início;
- Polylang já instalado;
- MailPoet já em execução;
- monetização aprovada pelo Google AdSense;
- prioridades iniciais: internacionalização e Content Discovery & SEO;
- Ads, Newsletter, Consent e takeover de templates não fazem parte do primeiro cutover.

## Regras obrigatórias

- staging primeiro;
- nenhuma alteração em produção sem pacote, roteiro, evidências e autorização explícita;
- instalar exatamente o mesmo ZIP homologado, sem variante PEL;
- não transportar conteúdo, usuários, assinantes, dados pessoais, campanhas, credenciais, segredos ou snapshots do Mengão;
- preservar tema, Elementor quando existente, Polylang, MailPoet, AdSense, plugin SEO, cache e CMP;
- adotar `inventário → contratos → adapters → shadow → homologação → cutover`;
- qualquer capacidade pública começa desligada e possui rollback próprio.

## Primeira entrega esperada

1. confirmar staging, backup e acesso operacional;
2. inventariar WordPress, PHP, tema, plugins, Polylang, MailPoet, AdSense, SEO, cache e CMP;
3. instalar a v0.7.4.0.1 sem ativar capacidades públicas;
4. confirmar:
   - política `portable-safe`;
   - origem `fresh-installation-no-evidence`;
   - todas as capacidades públicas desmarcadas;
   - nenhum takeover de template;
   - nenhum seed de Ads;
   - nenhum endpoint ou cron de Newsletter criado;
   - nenhuma interface pública de Consent;
5. criar proposta do Site Profile do PEL sem importar dados operacionais;
6. apresentar gaps, riscos, critérios de aceite e rollback;
7. somente iniciar internacionalização ou Discovery shadow após novo alinhamento.

## Texto pronto para abertura da nova tarefa

```text
M360 Core — Start controlado no staging do Portal Energia Limpa

Vamos iniciar uma nova frente para homologar o mesmo pacote M360 Core v0.7.4.0.1 no staging do Portal Energia Limpa — PEL.

Estado consolidado:
- repositório oficial: ppiressolucoes/M360-Core;
- pacote homologado no Mengão 360: v0.7.4.0.1;
- SHA-256: DD614F644E78A047999EF0B93C184AC9E7C1826B99CB915614FA83B94C2BCFE8;
- no Mengão, política legacy-compatible, origem preserved-existing-profile e módulos Foundation, Editorial e Discovery & SEO healthy;
- Home Editorial e Semantic Relations já foram absorvidos, com os precursores desativados;
- o PEL possui WordPress ativo, Polylang, MailPoet e monetização aprovada pelo Google AdSense;
- o tema e toda a infraestrutura existente do PEL devem ser preservados.

Objetivo desta primeira etapa:
1. inventariar o staging e seus providers;
2. confirmar backup e rollback;
3. instalar exatamente o ZIP homologado;
4. validar bootstrap portable-safe, origem fresh-installation-no-evidence e capacidades públicas desligadas;
5. confirmar zero impacto em tema, templates, AdSense, MailPoet, SEO, cache e Consent;
6. propor o Site Profile do PEL;
7. preparar a sequência de Internacionalização e Content Discovery & SEO em shadow.

Restrições:
- não alterar produção;
- não transportar conteúdo, dados pessoais, campanhas, credenciais, segredos ou snapshots do Mengão;
- não ativar Ads, Newsletter, Consent ou takeover de templates;
- não iniciar cutover sem apresentar o alinhamento e receber autorização explícita.

Use como referências ADR-0008, ADR-0010, o Portable Deployment Runbook, as release notes v0.7.4.0.1 e o registro de homologação de 27/07/2026.
```
