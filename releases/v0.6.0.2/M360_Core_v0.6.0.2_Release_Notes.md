# M360 Core v0.6.0.2 — Consent UI Contrast & Layering Hotfix

## Correção

Regras globais do tema sobrescreviam a cor e a aparência dos botões secundários da interface própria M360. Os controles existiam e funcionavam, mas seus rótulos ficavam invisíveis. No mobile, o seletor flutuante de idioma também podia permanecer acima do modal.

## Entregas

- isolamento visual dos botões de consentimento;
- contraste explícito para ações primárias e secundárias;
- rótulos visíveis em PT-BR e EN-US;
- camada de consentimento acima dos componentes flutuantes do portal;
- controles responsivos em largura total no mobile.

## Validação

1. atualizar o plugin para v0.6.0.2;
2. abrir uma nova janela anônima em PT-BR e EN-US;
3. confirmar os rótulos Rejeitar opcionais, Gerenciar escolhas e Aceitar todos;
4. abrir as preferências e confirmar Cancelar e Salvar escolhas;
5. confirmar que o seletor de idioma permanece abaixo da camada de consentimento;
6. manter o bloqueio Ads desativado durante esta homologação.
