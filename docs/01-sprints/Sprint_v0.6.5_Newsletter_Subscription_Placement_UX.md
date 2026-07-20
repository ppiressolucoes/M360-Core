# Sprint v0.6.5 — Newsletter Subscription Placement & UX

Status: homologada e consolidada no baseline estável em 2026-07-16
Baseline: `M360 Core v0.6.4`

## Critérios de aceite

- [x] variantes card, compact, inline e footer renderizam corretamente;
- [x] shortcode aceita título, descrição e CTA personalizados;
- [x] inserção automática permanece desativada após atualização;
- [x] ativação insere uma única ocorrência no final do artigo;
- [x] formulário não aparece em feed, REST, admin ou requisições AJAX;
- [x] home, sidebar e rodapé funcionam via shortcode no Elementor;
- [x] origem correta é persistida no consentimento;
- [x] painel apresenta contagem por origem;
- [x] cookie oculta o formulário em nova navegação após inscrição;
- [x] layout é responsivo e mantém foco/aria-busy da v0.6.3;
- [x] Double Opt-In e Delivery Readiness continuam funcionais.

## Evidências de homologação

- formulário posicionado depois de notícias e tópicos relacionados;
- localização integral validada em PT-BR e EN-US;
- origem `article_end` registrada e contabilizada;
- configurações e prontidão de entrega preservadas após atualização;
- diagnóstico operacional válido e prontidão de entrega `7/7`;
- rodapé de cancelamento validado com ícone informativo e contraste reforçado.

## Ordem de implantação

1. final dos artigos;
2. home após o destaque;
3. sidebar próxima de Últimas Notícias;
4. rodapé global.

## Fora do escopo

- popup, modal ou bloqueio de conteúdo;
- inserção no meio de parágrafos;
- testes A/B;
- campanha e automação editorial.
