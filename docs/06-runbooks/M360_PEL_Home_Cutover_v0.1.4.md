# PEL — Cutover da Home para o M360 Core

## Escopo

Runbook para o Portal Energia Limpa assumir a Home pt-BR e en-US com os blocos editoriais do M360 Core, mantendo o Cream Magazine e o Elementor disponíveis para rollback.

## Baselines

- M360 Core: `0.7.4.0.62`;
- M360 PEL Controlled Deployment: `0.1.4`;
- idiomas: `pt-BR` e `en-US`;
- paleta PEL: primária `#ff3d00`, secundária `#fc893c`;
- menu en-US: `EL360 Menu Principal en-US`.

## Sequência segura

1. instalar o Core e o Controlled Deployment em staging;
2. aplicar o perfil PEL com a opção de takeover desmarcada;
3. validar Site Profile, paleta, Polylang e o shortcode `[m360_main_navigation menu_pt="EL360 Menu Principal en-US"]` em uma página de teste;
4. revisar os widgets `pel-home-pt-newsroom` e `pel-home-en-newsroom` no cadastro editorial;
5. repetir a aplicação do perfil marcando **Assumir a Home pt-BR/en-US pelo Core nesta aplicação**;
6. validar `/` e `/en/`, responsividade, navegação, busca, widgets e cache;
7. registrar aceite antes de qualquer cutover público.

## Comportamento do cutover

O checkbox grava `home.locales` no Site Profile, cadastra um Newsroom mínimo por idioma e muda `m360_editorial_settings.mode` para `public`. Sem o checkbox, o modo permanece `hybrid` e o tema continua controlando a Home.

## Rollback

Desmarcar a Home no Site Profile ou reinstalar o perfil sem takeover, retornar `m360_editorial_settings.mode` para `hybrid` e limpar caches. O tema e os templates do Elementor não são apagados nem sobrescritos.
