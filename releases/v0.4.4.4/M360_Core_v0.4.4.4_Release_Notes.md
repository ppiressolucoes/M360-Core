# M360 Core v0.4.4.4 — Release Notes

Status: pronta para build e homologação
Entrega: M360 Archive Ads Engine
Projeto: Mengão 360 | DW Esportivo

## Entregas

- Novo `M360_Ads_Archive_Engine`.
- Inserção automática em Search, Category, Tag, Author e Latest News.
- Slot `search-empty` em buscas sem resultados.
- APIs `m360_ads_render_archive()` e `m360_ads_archive_position()`.
- Filtros de habilitação, posição e slot por contexto.
- CSS responsivo para `.m360-archive-ad`.
- Compatibilidade PT-BR / EN-US.
- Sem alterações em templates do tema ou Elementor.

## Posições padrão

| Contexto | Slot | Posição |
|---|---|---:|
| Search | `search-inline` | 3 |
| Category | `category-inline` | 3 |
| Tag | `tag-inline` | 3 |
| Author | `author-inline` | 3 |
| Latest News | `latest-inline` | 4 |

## Build de publicação

Workflow:

```text
Build M360 Core Plugin ZIP
```

Inputs:

```text
source_ref: sprint/v0.4.4.0-adsense-ready
version: 0.4.4.4
```

Artifact esperado:

```text
m360-core-v0.4.4.4.zip
```

## Homologação

- validar plugin/admin sem erro fatal;
- validar os cinco contextos integrados;
- validar PT-BR e EN-US;
- validar placeholder sem campanha;
- validar desktop e mobile;
- confirmar preservação dos quatro slots já homologados;
- confirmar Inline Ads Engine da v0.4.4.3.

## Rollback

Reinstalar o último ZIP homologado (`v0.4.4.3` ou `v0.4.3.5`, conforme política operacional), limpar caches e validar os slots piloto.
