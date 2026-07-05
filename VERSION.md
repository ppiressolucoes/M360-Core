# M360 Core — Version Manifest

## Versão atual

```text
0.3.2
```

## Status

```text
Stable baseline
```

## Sprint atual

```text
Sprint 0 — GitHub Build Foundation
```

## Próxima versão planejada

```text
0.3.4-rc1
```

## Ambiente alvo

- WordPress 6.8+
- PHP 8+
- Elementor
- Polylang
- Yoast SEO

## Regra de versionamento

- `0.3.2`: baseline estável migrada para GitHub.
- `0.3.4-rc1`: primeira release candidate do View Engine.
- `0.3.4`: View Engine estável.

## Fonte do pacote

O ZIP instalável do WordPress deve ser gerado sempre a partir do diretório:

```text
plugin/
```

## Fluxo de implantação

```text
main → GitHub Actions → ZIP artifact → WordPress
```
