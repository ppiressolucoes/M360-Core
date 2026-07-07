# M360 Core Version Manifest

## Current version

```text
0.4.2.2
```

## Status

```text
Sprint 11 PT theme menu hotfix ready for workflow build
```

## Current sprint

```text
Sprint 11 - UI Components Foundation / Navigation Polish
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
- `0.4.1.3`: Home Editorial compatibility hotfix with scoped UI polish.
- `0.4.2.0`: M360 UI Components Foundation.
- `0.4.2.1`: Scoped Navigation UI Components Polish.
- `0.4.2.2`: PT-BR theme menu icon hotfix; removed legacy global theme selectors from foundation CSS.

## Package source

The installable WordPress ZIP must always be generated from:

```text
plugin/
```

## Deployment flow

```text
main -> GitHub Actions -> ZIP artifact -> WordPress
```
