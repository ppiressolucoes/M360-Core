# M360 Core v0.7.4.0.17 — Editorial Autoplay Policy

## Correção

Unifica o autoplay dos componentes editoriais. Newsroom, cards laterais e
Latest News agora seguem a mesma regra do ticker: quando a instância estiver
com **avanço automático** marcado, o carrossel avança no intervalo definido.

- pausa enquanto a aba estiver oculta;
- pausa em hover/foco e retoma ao sair;
- os botões anterior/próximo continuam disponíveis;
- não depende mais da preferência global de redução de movimento do navegador.
