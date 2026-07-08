# Sprint 11.7 - M360 Ads Pilot Production Inventory

Versao: 0.4.3.0
Status: pronto para workflow

## Objetivo

Entregar um piloto funcional do M360 Ads Manager usando os quatro espacos publicitarios ja existentes no portal, substituindo insercoes manuais por slots renderizados pelo plugin.

## Inventario piloto

| Slot | Uso atual | Tipo | Formato |
|---|---|---|---|
| header-top | Mega Bolao no cabecalho | Imagem | 728x140 |
| content-bottom | Comunidade WhatsApp antes do rodape | HTML | Responsivo |
| sidebar-community | Comunidade WhatsApp na sidebar | HTML | 300x300 |
| sidebar-square | Mega Bolao na sidebar | HTML/House Ad | 1:1 |

## Entregas

- Schema Ads atualizado para 0.4.3.0.
- Seed dos quatro slots de producao.
- Seed das campanhas piloto Mega Bolao e WhatsApp.
- Seed de criativos piloto vinculados as campanhas.
- Vinculo automatico campanha -> slot.
- Nova tela administrativa: M360 Ads > Inventario Piloto.
- API PHP alias `m360_ads_render_slot()`.
- Shortcode alias `[m360_ads_slot]`.
- Estilos front-end para house ads responsivos.
- Estilos admin para cards de inventario e preview.

## Como integrar no tema

Substituir insercoes manuais por:

```php
echo m360_ads_render_slot('header-top');
echo m360_ads_render_slot('content-bottom');
echo m360_ads_render_slot('sidebar-community');
echo m360_ads_render_slot('sidebar-square');
```

Ou por shortcodes:

```text
[m360_ad_slot id="header-top"]
[m360_ad_slot id="content-bottom"]
[m360_ad_slot id="sidebar-community"]
[m360_ad_slot id="sidebar-square"]
```

## Observacoes

- O link dos CTAs piloto foi mantido como `#` para ser ajustado no painel.
- O banner horizontal usa o arquivo existente:

```text
/wp-content/uploads/2026/06/BANNER-HORIZONTAL-MEGA-BOLAO-360-728X140.jpg
```

- Os HTMLs piloto foram criados como house ads editaveis nos criativos.

## Criterios de aceite

- Plugin atualiza sem erro fatal.
- Menu Inventario Piloto aparece no admin.
- Quatro slots aparecem ocupados.
- Campanhas piloto sao criadas.
- Criativos piloto sao criados.
- Shortcodes renderizam conteudo.
- API PHP renderiza conteudo.
- Insercoes manuais podem ser substituidas sem perder o layout.

## Proxima fase

Linha 5 - Plataforma Comercial M360

- v0.5.0.0 - Ads Inventory Platform
- v0.5.1.0 - Campaign Management
- v0.5.2.0 - Commercial Dashboard & Analytics
- v0.5.3.0 - Google AdSense Connector
