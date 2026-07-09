# Sprint 11.7.5 - M360 Ads Slot Intent Fallback

Versao: 0.4.3.5
Status: pronto para workflow

## Objetivo

Corrigir o fallback do renderer para evitar troca indevida entre criativos horizontais e quadrados quando existem variacoes PT-BR e EN-US dentro da mesma campanha.

## Problema validado

- `sidebar-community` passou a renderizar corretamente em EN-US apos criacao de criativo dedicado.
- `content-bottom` em EN-US ainda podia cair no criativo 1:1, pois o renderer usava fallback generico da campanha quando o slug/tamanho nao batia exatamente.

## Correcoes entregues

- Preferencia por idioma exato antes de `all`.
- Preferencia por dispositivo exato antes de `all`.
- `content-bottom` agora usa estrategia wide: largura maior que altura e largura >= 700.
- `sidebar-community` agora usa estrategia square: largura igual a altura.
- `sidebar-square` agora usa estrategia square.
- Slugs manuais PT-BR/EN-US adicionados aos candidatos conhecidos.
- Fallback generico da campanha somente depois de falharem as estrategias do slot.

## Arquivos alterados

- plugin/includes/ads/class-m360-ad-slot-component.php
- plugin/m360-core.php
- VERSION.md

## Criterios de aceite

- `content-bottom` PT-BR carrega criativo horizontal.
- `content-bottom` EN-US carrega criativo horizontal.
- `sidebar-community` PT-BR carrega criativo 300x300.
- `sidebar-community` EN-US carrega criativo 300x300.
- `sidebar-square` carrega imagem 1:1 Mega Bolao.
- Nenhum slot horizontal deve cair em criativo quadrado.

## Observacao

Esta release estabiliza o piloto de inventario para diferentes idiomas sem exigir novos relacionamentos de slot-criativo no banco. A fase v0.5.x devera formalizar este relacionamento em tabela propria.
