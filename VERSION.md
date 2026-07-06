# M360 Core Version Manifest

## Current version

```text
0.3.4
```

## Status

```text
Foundation delivered in GitHub
```

## Current sprint

```text
Sprint 10.4 - M360 View Engine Foundation
```

## Next planned version

```text
0.3.5
```

## Target environment

- WordPress 6.8+
- PHP 8+
- Elementor
- Polylang
- Yoast SEO

## Versioning rule

- `0.3.2`: stable baseline migrated to GitHub.
- `0.3.4-rc1`: installable plugin packaging foundation.
- `0.3.4`: View Engine Foundation.
- `0.3.5`: Dynamic View Migration.

## Package source

The installable WordPress ZIP must always be generated from:

```text
plugin/
```

## Deployment flow

```text
main -> GitHub Actions -> ZIP artifact -> WordPress
```
