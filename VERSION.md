# M360 Core Version Manifest

## Current version

```text
0.4.2.6
```

## Status

```text
Sprint 11.4 Ads Creatives Library ready for workflow build
```

## Current sprint

```text
Sprint 11.4 - M360 Ads Creatives Library
```

## Next planned version

```text
0.4.3.0
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

## Ads DB schema version

```text
0.4.2.6
```

## Ads Manager admin menu

```text
M360 Ads
- Dashboard
- Slots
- Campanhas
- Criativos
- Nova Campanha
- Novo Criativo
```

## Dedicated upload directory

```text
wp-content/uploads/m360-ads/
```

## Ads shortcode

```text
[m360_ad_slot id="article-top"]
```

## Ads PHP API

```php
m360_ad_slot('article-top');
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
