# M360 Core v0.6.3 — Newsletter Configuration & Form Hardening

## Resultado

Retira a lista MailPoet do código fixo e fortalece o formulário público sem alterar os fluxos homologados de confirmação, sincronização e cancelamento.

## Entrega

- configuração em `M360 Ads > Newsletter`;
- lista MailPoet selecionável e validada pela API;
- migração automática da instalação atual para a lista ID `3`;
- versão e texto do consentimento configuráveis;
- tempo mínimo, limite por IP e janela configuráveis;
- honeypot sem persistência do conteúdo capturado;
- eventos `spam_rejected` com motivo técnico;
- botão bloqueado durante envio e prevenção de clique duplo;
- `aria-busy`, mensagem viva e foco após resposta;
- falha controlada quando MailPoet ou lista estão indisponíveis.

## Homologação

Use o checklist `Sprint_v0.6.3_Newsletter_Configuration_Form_Hardening.md`. A release não altera assinantes ou consentimentos existentes durante a atualização.
