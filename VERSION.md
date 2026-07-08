# M360 Core Version Manifest

## Current version

```text
0.4.3.3
```

## Status

```text
M360 Ads Renderer Stabilization ready for workflow build
```

## Current sprint

```text
Sprint 11.7.3 - M360 Ads Renderer Stabilization
```

## Next planned version

```text
0.5.0.0
```

## Target environment

- WordPress 6.8+
- PHP 8+
- Elementor
- Polylang
- Yoast SEO
- LiteSpeed Cache
- Mengão 360 Bolão
- M360 Home Editorial
- M360 Semantic Relations

## Versioning rule

- `0.4.2.3`: M360 Latest News Component.
- `0.4.2.4`: M360 Ads Slots Foundation with dbDelta, seed, shortcode and PHP API.
- `0.4.2.5`: M360 Ads Manager admin panel.
- `0.4.2.6`: M360 Ads Creatives Library with dedicated creatives table and admin screens.
- `0.4.2.7`: M360 Ads Creatives Media Picker and size presets.
- `0.4.2.8`: M360 Ads Creative Preview UX and media metadata panel.
- `0.4.3.0`: M360 Ads Pilot Production Inventory.
- `0.4.3.1`: Hotfix for widget shortcode support and slot-aware creative selection.
- `0.4.3.2`: Stabilization for creative edit form, media picker assets and header banner alignment.
- `0.4.3.3`: Renderer stabilization for slot-specific creatives and HTML payloads.

## Ads DB schema version

```text
0.4.3.0
```

## Production pilot slots

```text
header-top
content-bottom
sidebar-community
sidebar-square
```

## Theme/API integration

```php
echo m360_ads_render_slot('header-top');
echo m360_ads_render_slot('content-bottom');
echo m360_ads_render_slot('sidebar-community');
echo m360_ads_render_slot('sidebar-square');
```

## Ads shortcodes

```text
[m360_ad_slot id="header-top"]
[m360_ad_slot id="content-bottom"]
[m360_ad_slot id="sidebar-community"]
[m360_ad_slot id="sidebar-square"]
```

## Renderer fixes

```text
Slot-specific creative candidates
sidebar-square resolves to m360-pilot-sidebar-mega-bolao
sidebar-community resolves to m360-pilot-sidebar-whatsapp
HTML payloads preserve style tags instead of printing CSS as text
Shortcodes inside HTML payloads are executed
Square fallback by aspect ratio
```

## Ads Manager admin menu

```text
M360 Ads
- Dashboard
- Inventário Piloto
- Slots
- Campanhas
- Criativos
- Nova Campanha
- Novo Criativo
```

## Package source

The installable WordPress ZIP must always be generated from:

```text
plugin/
```

## Deployment flow

```text
main -> GitHub Actions -> ZIP artifact -> WordPress
```
