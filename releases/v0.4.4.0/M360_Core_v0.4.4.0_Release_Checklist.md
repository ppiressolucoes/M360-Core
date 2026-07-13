# M360 Core v0.4.4.x — Release Checklist

Status: preparação de release funcional
Sprint: v0.4.4.0 — M360 AdSense Ready
Projeto: Mengão 360 | DW Esportivo

## 1. Escopo da release

- M360 Ad Slot Component semântico.
- Labels automáticas PT-BR / EN-US.
- Data attributes padronizados.
- Comentários HTML de diagnóstico.
- Placeholders para slots vazios.
- CSS unificado do M360 Ads.
- Checklist administrativo AdSense Ready.
- M360 Inventory Library.
- Inventory Seeder oficial em `M360 Core v0.4.4.1`.
- Context Renderer em `M360 Core v0.4.4.2`.
- Inline Ads Engine em `M360 Core v0.4.4.3`.

## 2. Arquivos funcionais alterados

- `plugin/m360-core.php`
- `plugin/includes/class-m360-core.php`
- `plugin/includes/ads/class-m360-ads-inventory-library.php`
- `plugin/includes/ads/class-m360-ads-context-renderer.php`
- `plugin/includes/ads/class-m360-ads-inline-engine.php`
- `plugin/includes/ads/class-m360-ads-db.php`
- `plugin/includes/ads/class-m360-ad-slot-component.php`
- `plugin/includes/ads/class-m360-ads-admin.php`
- `plugin/assets/css/m360-ads.css`

## 3. Validação técnica

| Item | Status |
|---|---|
| Versão WordPress header `0.4.4.3` | Pendente validação final |
| Constante `M360_CORE_VERSION` `0.4.4.3` | Pendente validação final |
| Schema Ads `0.4.4.1` | Pendente validação final |
| Inventory Library carregada no runtime | Pendente validação final |
| Inventory Seeder executa no upgrade | Pendente validação final |
| Context Renderer carregado | Pendente validação final |
| Inline Ads Engine carregado | Pendente validação final |
| Shortcode `[m360_ad_slot]` preservado | Pendente validação final |
| API `m360_ads_render_slot()` preservada | Pendente validação final |
| Tela admin AdSense Ready disponível | Pendente validação final |

## 4. Validação do Inventory Seeder

| Item | Status |
|---|---|
| Cadastra slots oficiais sem duplicidade | Pendente |
| Atualiza metadados dos slots existentes | Pendente |
| Preserva `is_active` dos slots existentes | Pendente |
| Cria novos slots como ativos | Pendente |
| Mantém vínculos do inventário piloto | Pendente |
| Atualiza opção `m360_ads_inventory_library_version` | Pendente |
| Atualiza opção `m360_ads_inventory_seeded_at` | Pendente |

## 5. Validação do Inline Ads Engine

| Item | Status |
|---|---|
| Insere anúncio em post individual | Pendente |
| Insere após o 2º parágrafo | Pendente |
| Usa slot `article-after-paragraph-2` | Pendente |
| Não executa em admin/feed/AJAX/REST | Pendente |
| Não executa fora de posts individuais | Pendente |
| Renderiza placeholder quando não houver campanha | Pendente |
| Preserva layout mobile | Pendente |
| Pode ser desligado pelo filtro `m360_ads_inline_enabled` | Pendente |

## 6. Validação de slots piloto

| Slot | PT-BR | EN-US | Placeholder | Criativo ativo | Status |
|---|---|---|---|---|---|
| `header-top` | Pendente | Pendente | Pendente | Pendente | Pendente |
| `content-bottom` | Pendente | Pendente | Pendente | Pendente | Pendente |
| `sidebar-community` | Pendente | Pendente | Pendente | Pendente | Pendente |
| `sidebar-square` | Pendente | Pendente | Pendente | Pendente | Pendente |

## 7. Validação de workflow

- Executar workflow `Build M360 Ads Inline Engine`.
- Informar source ref `sprint/v0.4.4.0-adsense-ready`.
- Informar versão `0.4.4.3`.
- Confirmar artifact `m360-ads-inline-engine-v0.4.4.3.zip`.
- Para instalação no WordPress, gerar ZIP completo pelo workflow `Build M360 Core Plugin ZIP`.

## 8. Rollback

Versão anterior homologada:

```text
M360 Core v0.4.3.5
```

Ação de rollback:

1. reinstalar ZIP da versão `0.4.3.5`;
2. limpar cache;
3. validar os quatro slots homologados;
4. registrar ocorrência.

## 9. Critério de pronto para merge

A release estará pronta para merge quando:

- PR revisado;
- checklist funcional validado;
- documentação consolidada;
- workflow de ZIP disponível;
- nenhum erro fatal no admin;
- nenhum shortcode quebrado;
- Inventory Seeder validado;
- Inline Ads Engine validado em post real;
- rollback documentado.
