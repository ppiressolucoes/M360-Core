# M360 Core Version Manifest

## Current version

```text
0.3.8.0
```

## Status

```text
Sprint 10.6 Author Engine ready for workflow build
```

## Current sprint

```text
Sprint 10.6 - Dynamic View Engine / Author Engine
```

## Next planned version

```text
0.3.9.0
```

## Target environment

- WordPress 6.8+
- PHP 8+
- Elementor
- Polylang
- Yoast SEO
- LiteSpeed Cache

## Versioning rule

- `0.3.4`: View Engine Foundation.
- `0.3.6.2`: Navigation responsive hotfix homologated.
- `0.3.7.4`: Search Engine dynamic view homologated.
- `0.3.8.0`: Author Engine dynamic view.
- `0.3.9.0`: Category Engine.
- `0.4.0.0`: Tag Engine.

## Package source

The installable WordPress ZIP must always be generated from:

```text
plugin/
```

## Deployment flow

```text
main -> GitHub Actions -> ZIP artifact -> WordPress
```
