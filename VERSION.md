# M360 Core Version Manifest

## Current version

```text
0.3.5
```

## Status

```text
Urgent production shortcode recovery
```

## Current sprint

```text
Sprint 10.5 - Navigation Shortcode Recovery
```

## Next planned version

```text
0.3.6
```

## Target environment

- WordPress 6.8+
- PHP 8+
- Elementor
- Polylang
- Yoast SEO

## Versioning rule

- `0.3.2`: stable baseline migrated to GitHub.
- `0.3.4`: View Engine Foundation.
- `0.3.4.1`: plugin upgrade pipeline fix.
- `0.3.5`: restore production navigation shortcodes.
- `0.3.6`: Dynamic View Migration.

## Package source

The installable WordPress ZIP must always be generated from:

```text
plugin/
```

## Deployment flow

```text
main -> GitHub Actions -> ZIP artifact -> WordPress
```
