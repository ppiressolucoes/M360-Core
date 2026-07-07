# M360 Core Version Manifest

## Current version

```text
0.4.2.4-docs
```

## Status

```text
M360 Ads Database Design documented for review
```

## Current sprint

```text
Sprint 11.2 - M360 Ads Database Design
```

## Next planned version

```text
0.4.2.4
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
- `0.4.2.4-docs`: M360 Ads Database Design and SQL reference scripts.

## Documentation source

```text
docs/02-architecture/M360_Ads_Database_Design.md
```

## SQL reference scripts

```text
database/migrations/2026_07_08_m360_ads_schema.sql
database/seeds/2026_07_08_m360_ads_default_slots.sql
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
