# Sprint v0.6.0 — M360 Privacy & Consent Foundation

## Objetivo

Criar uma camada independente de privacidade e consentimento que aumente a confiança do usuário, prepare o portal para a candidatura ao AdSense e evite dependência direta de um fornecedor de CMP.

## Entregas

- painel `M360 Ads → Privacy & Consent`;
- categorias normalizadas: necessários, preferências, analytics, publicidade e mídia externa;
- adaptador com modos `external_cmp` e `local_foundation`;
- Google Consent Mode v2 com sinais negados por padrão;
- API PHP `m360_has_consent()`;
- API JavaScript `window.M360Consent` e evento `m360:consent:update`;
- ponte neutra `m360:cmp:consent` para adaptadores de CMP externa;
- interface local multilíngue para homologação, aceite, rejeição, gestão e revogação;
- bloqueio opcional de AdSense e Google Ad Manager pelo Ads Manager;
- diagnóstico administrativo e links institucionais PT-BR/EN-US.

## Limite de responsabilidade

A interface local não é apresentada como CMP certificada. Para veiculação no EEE, Reino Unido e Suíça, a operação deve usar uma CMP certificada pelo Google e integrada ao IAB TCF. A validação jurídica dos textos, bases legais, fornecedores e retenções continua obrigatória antes da produção.

## Critérios de aceite

1. instalação não altera o front-end enquanto a fundação estiver desativada;
2. Consent Mode v2 inicia antes das tags quando ativado;
3. decisões são persistidas em cookie primário e podem ser revistas;
4. PT-BR e EN-US apresentam textos e links corretos;
5. campanhas internas continuam independentes do consentimento publicitário;
6. AdSense/GAM podem ser bloqueados até consentimento, quando a política for ativada;
7. a troca de CMP não exige alteração dos consumidores da API M360.
