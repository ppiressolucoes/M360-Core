# M360 - Core Editorial v0.7.4.0.24

## Objetivo

Atualização manual e controlada da Home Editorial PT-BR do Mengão 360, preservando os Header/Footer Elementor durante a homologação.

## Entregas

- template próprio da Home no M360 Core;
- composição PT-BR com ticker, Newsroom, seções, últimas notícias e slots M360 Ads;
- preservação do modo `hybrid`/`off` para rollback;
- inclusão do controlador e template nativos no pacote instalável;
- contrato dos shortcodes editoriais preservado.
- correção do controle play/pause do ticker, separando pausa manual de pausa temporária por hover, foco e visibilidade da aba.

## Procedimento

1. Fazer backup do plugin atual e das opções `m360_editorial_settings` e `m360_platform_module_states`.
2. Instalar o ZIP sem desativar o M360 Core existente.
3. Confirmar os templates Elementor `M360 Header Modelo pt-BR` e `M360 Footer Modelo pt-BR`.
4. Validar a Home PT-BR em página/rota de homologação.
5. Alterar o módulo Editorial Layout & Home para `mode=public` somente após aceite.
6. Limpar cache WordPress/CDN e validar ticker, Newsroom, Ads, links e responsividade.

## Rollback

Retornar o módulo para `hybrid` ou `off`, limpar cache e restaurar o ZIP anterior. Não remover opções nem dados editoriais.
