# Sprint 11.6 - M360 Ads Creative Preview UX

Versao: 0.4.2.8
Status: pronto para workflow

## Objetivo

Refinar a experiencia de cadastro de criativos no M360 Ads Manager com preview grande, painel de metadados e validacao visual de formato.

## Escopo entregue

- Preview ampliado do criativo selecionado.
- Painel Dados do criativo.
- Exibicao de arquivo, formato, peso, tipo e origem.
- Atualizacao dos metadados apos selecao via Media Library.
- Deteccao automatica do preset quando o tamanho da imagem corresponde a um formato conhecido.
- Aviso de incompatibilidade quando o formato selecionado nao corresponde a largura/altura atuais.
- CSS administrativo refinado.
- JS administrativo refinado.

## Arquivos atualizados

- plugin/includes/ads/class-m360-ads-creatives-admin.php
- plugin/assets/js/m360-ads-admin.js
- plugin/assets/css/m360-ads-admin.css
- plugin/m360-core.php
- VERSION.md

## Criterios de aceite

- Plugin atualiza sem erro fatal.
- Formulario Novo Criativo mostra preview ampliado.
- Painel Dados do criativo aparece abaixo da imagem.
- Selecionar imagem atualiza URL, largura, altura, MIME, filesize e alt text.
- Preset e detectado automaticamente quando aplicavel.
- Aviso de incompatibilidade aparece quando formato e dimensoes divergem.
- Criativos cadastrados por URL continuam funcionando.

## Proxima entrega

v0.4.3.0 - Taxonomy Intelligence Foundation.
