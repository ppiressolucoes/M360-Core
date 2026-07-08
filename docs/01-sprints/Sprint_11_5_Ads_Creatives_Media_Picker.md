# Sprint 11.5 - M360 Ads Creatives Media Picker

Versao: 0.4.2.7
Status: pronto para workflow

## Objetivo

Melhorar o cadastro de criativos do M360 Ads Manager com seletor nativo de midia do WordPress e formatos comerciais pre-definidos.

## Escopo entregue

- Botao Selecionar imagem no formulario de criativo.
- Integracao com `wp.media`.
- Preenchimento automatico de URL da imagem.
- Preenchimento automatico de largura, altura, MIME, tamanho e alt text quando a midia fornece os dados.
- Preview visual da imagem selecionada.
- Campo Formato com presets:
  - 300x250
  - 728x90
  - 970x250
  - 1200x250
  - 320x100
  - 300x600
  - Responsivo
  - Personalizado
- Largura e altura continuam editaveis manualmente.
- CSS administrativo ajustado.

## Arquivos adicionados

- plugin/assets/js/m360-ads-admin.js

## Arquivos atualizados

- plugin/includes/ads/class-m360-ads-creatives-admin.php
- plugin/includes/class-m360-core.php
- plugin/assets/css/m360-ads-admin.css
- plugin/m360-core.php
- VERSION.md

## Observacao tecnica

Esta release usa a Biblioteca de Midia do WordPress para selecionar imagens. A separacao fisica automatica para `/uploads/m360-ads/` permanece preparada pelo diretorio dedicado, mas a movimentacao automatica de uploads fica para uma evolucao futura, evitando impacto na biblioteca global.

## Criterios de aceite

- Plugin atualiza sem erro fatal.
- Formulario Novo Criativo mostra campo Formato.
- Formulario mostra botao Selecionar imagem.
- Media picker do WordPress abre corretamente.
- Ao selecionar imagem, URL e metadados sao preenchidos.
- Preview aparece no formulario.
- Criativos cadastrados por URL continuam funcionando.
- Campanhas e slots existentes seguem funcionais.

## Proxima entrega

v0.4.3.0 - Taxonomy Intelligence Foundation.
