# M360 Ads Archive Engine v1

Status: implementação funcional inicial
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Módulo: M360 Advertising
Versão de referência: M360 Core v0.4.4.4

## 1. Objetivo

Inserir slots publicitários automaticamente nas listagens controladas pelo M360 Core, sem alterar templates do tema News Portal ou estruturas do Elementor.

## 2. Contextos integrados

| Contexto | Slot | Posição padrão |
|---|---|---:|
| Search | `search-inline` | após o 3º resultado |
| Category | `category-inline` | após o 3º artigo |
| Tag | `tag-inline` | após o 3º artigo |
| Author | `author-inline` | após o 3º artigo |
| Latest News | `latest-inline` | após o 4º item |

O contexto `archive` possui API e slot `archive-inline`, mas a injeção automática em arquivos genéricos do tema ficará para o Theme Adapter/Universal Slot Renderer.

## 3. APIs

```php
echo m360_ads_render_archive('category');
$position = m360_ads_archive_position('category');
```

## 4. Filtros

Desativar um contexto:

```php
add_filter('m360_ads_archive_enabled', function (bool $enabled, string $context): bool {
    return $context === 'tag' ? false : $enabled;
}, 10, 2);
```

Alterar posição:

```php
add_filter('m360_ads_archive_position', function (int $position, string $context): int {
    return $context === 'search' ? 2 : $position;
}, 10, 2);
```

Alterar slot:

```php
add_filter('m360_ads_archive_slot', function (string $slot, string $context): string {
    return $context === 'author' ? 'author-inline' : $slot;
}, 10, 2);
```

## 5. Busca sem resultados

Quando uma busca não retorna posts, o engine renderiza o slot:

```text
search-empty
```

## 6. Compatibilidade

- PT-BR / EN-US;
- M360 Ad Slot Component;
- Inventory Library;
- placeholders;
- campanhas internas;
- futura integração AdSense/GAM;
- layouts grid e lista.

## 7. Validação

Validar em desktop e mobile:

- busca com mais de três resultados;
- busca sem resultados;
- categoria com mais de três artigos;
- tag com mais de três artigos;
- autor com mais de três artigos;
- Latest News com ao menos quatro itens;
- labels `PUBLICIDADE` e `ADVERTISEMENT`;
- placeholder quando não houver campanha vinculada.
