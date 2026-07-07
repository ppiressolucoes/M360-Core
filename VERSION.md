# M360 Core Version Manifest

## Current version

```text
0.4.2.5
```

## Status

```text
Sprint 11.3 Ads Manager ready for workflow build
```

## Current sprint

```text
Sprint 11.3 - M360 Ads Manager
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

- `0.3.7.4`: Search Engine dynamic view homologated.
- `0.3.8.2`: Author Engine homologated.
- `0.3.9.0`: Category Engine homologated.
- `0.4.0.0`: Tag Engine homologated.
- `0.4.2.0`: M360 UI Components Foundation.
- `0.4.2.1`: Scoped Navigation UI Components Polish.
- `0.4.2.2`: PT-BR theme menu hotfix.
- `0.4.2.3`: M360 Latest News Component.
- `0.4.2.4`: M360 Ads Slots Foundation with dbDelta, seed, shortcode and PHP API.
- `0.4.2.5`: M360 Ads Manager admin panel.

## Ads DB schema version

```text
0.4.2.4
```

## Ads Manager admin menu

```text
M360 Ads
- Dashboard
- Slots
- Campanhas
- Nova Campanha
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
