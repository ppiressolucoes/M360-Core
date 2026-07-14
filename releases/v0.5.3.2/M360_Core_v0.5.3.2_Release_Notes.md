# M360 Core v0.5.3.2 — Elementor Search Column Width Hotfix

## Causa

No template PT-BR, o Elementor mantinha o widget do shortcode com largura automática dentro da coluna, fazendo o Search Hero encolher e permanecer alinhado à direita. O componente interno já possuía `width: 100%`, mas estava limitado pela largura do widget hospedeiro.

## Correção

- identifica o widget Elementor que contém `[m360_search_form]`;
- aplica uma classe exclusiva ao widget e ao seu contêiner;
- força apenas esse widget a ocupar integralmente a coluna disponível;
- preserva o layout dos demais elementos e o comportamento mobile já homologado.

## Homologação

- limpar o LiteSpeed Cache após instalar;
- validar o cabeçalho PT-BR em desktop;
- confirmar ocupação integral da coluna 70%;
- repetir rapidamente EN-US e mobile para controle de regressão.
