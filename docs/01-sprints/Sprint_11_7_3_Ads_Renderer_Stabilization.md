# Sprint 11.7.3 - M360 Ads Renderer Stabilization

Versao: 0.4.3.3
Status: pronto para workflow

## Objetivo

Corrigir a engrenagem especifica de renderizacao do piloto M360 Ads para garantir que cada slot entregue o criativo correto e que payloads HTML sejam renderizados como HTML, sem imprimir CSS como texto.

## Problemas validados

- `sidebar-square` estava exibindo o criativo horizontal do header.
- `sidebar-community` estava imprimindo CSS/HTML como texto.
- O renderer ainda dependia demais do primeiro criativo da campanha.

## Correcoes entregues

- Selecao de criativo por candidatos especificos de slot.
- `header-top` resolve para `m360-pilot-header-mega-bolao`.
- `content-bottom` resolve para `m360-pilot-content-whatsapp`.
- `sidebar-community` resolve para `m360-pilot-sidebar-whatsapp`.
- `sidebar-square` resolve para `m360-pilot-sidebar-mega-bolao`.
- Fallback por largura/altura preservado.
- Fallback 1:1 por proporcao quadrada adicionado.
- Payload HTML agora preserva tag `<style>`.
- Shortcodes dentro de HTML payloads passam por `do_shortcode()`.

## Arquivos alterados

- plugin/includes/ads/class-m360-ad-slot-component.php
- plugin/m360-core.php
- VERSION.md

## Criterios de aceite

- `header-top` continua exibindo banner 728x140.
- `sidebar-square` exibe criativo quadrado 1:1.
- `sidebar-community` renderiza HTML estilizado, sem imprimir CSS como texto.
- `content-bottom` renderiza HTML estilizado.
- Shortcodes existentes continuam funcionando.

## Proxima etapa

Validar os 4 slots em PT-BR e EN-US e remover definitivamente as pecas manuais legadas do tema/Elementor.
