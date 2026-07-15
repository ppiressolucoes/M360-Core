# M360 Core v0.6.0.1 — Consent Frontend Initialization Hotfix

## Correção

O HTML da interface própria M360 podia ser impresso depois do JavaScript de rodapé. Nesse cenário, a fundação permanecia configurada e o Consent Mode v2 era iniciado, mas o banner não era localizado nem exibido.

## Entregas

- inicialização após `DOMContentLoaded`;
- HTML da interface antecipado no `wp_footer`;
- rótulo administrativo esclarecido para homologação diretamente no portal;
- nenhuma alteração no bloqueio publicitário, que permanece opt-in.

## Validação

1. atualizar o plugin;
2. abrir uma nova janela anônima;
3. confirmar a exibição do banner PT-BR e EN-US;
4. testar aceitar, rejeitar, gerenciar e reabrir preferências;
5. manter o bloqueio Ads desativado durante a homologação inicial.
