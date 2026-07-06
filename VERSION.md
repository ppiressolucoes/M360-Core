# M360 Core — Version Manifest

## Versão atual

```text
0.3.4-rc1
```

## Status

```text
Release Candidate
```

## Sprint atual

```text
Sprint 0.1 — Plugin Packaging Foundation
```

## Próxima versão planejada

```text
0.3.4
```

## Ambiente alvo

- WordPress 6.8+
- PHP 8+
- Elementor
- Polylang
- Yoast SEO

## Regra de versionamento

- `0.3.2`: baseline estável migrada para GitHub.
- `0.3.4-rc1`: plugin packaging foundation instalável.
- `0.3.4`: View Engine foundation.

## Fonte do pacote

O ZIP instalável do WordPress deve ser gerado sempre a partir do diretório:

```text
plugin/
```

## Fluxo de implantação

```text
main → GitHub Actions → ZIP artifact → WordPress
```
