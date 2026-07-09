# M360 Core v0.4.4.0 — Release Checklist

Status: preparação de release
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
- Workflow manual de build ZIP.
- Documentação operacional de publicação.

## 2. Arquivos funcionais alterados

- `plugin/m360-core.php`
- `plugin/includes/ads/class-m360-ad-slot-component.php`
- `plugin/includes/ads/class-m360-ads-admin.php`
- `plugin/assets/css/m360-ads.css`

## 3. Arquivos documentais adicionados/atualizados

- `docs/02-sprints/Sprint_v0.4.4.0_M360_AdSense_Ready.md`
- `docs/03-releases/M360_Release_History_v2.md`
- `docs/01-modules/M360_Advertising_Plugin_Guide_v1.md`
- `docs/04-operations/M360_Core_Plugin_Publication_Workflow_v1.md`
- `.github/workflows/build-plugin-zip.yml`

## 4. Validação técnica

| Item | Status |
|---|---|
| Versão WordPress header `0.4.4.0` | Pendente validação final |
| Constante `M360_CORE_VERSION` `0.4.4.0` | Pendente validação final |
| Shortcode `[m360_ad_slot]` preservado | Pendente validação final |
| Shortcode `[m360_ads_slot]` preservado | Pendente validação final |
| API `m360_ads_render_slot()` preservada | Pendente validação final |
| API `m360_ad_slot()` preservada | Pendente validação final |
| CSS `m360-core-ads` enfileirado | Pendente validação final |
| Tela admin AdSense Ready disponível | Pendente validação final |

## 5. Validação de slots

| Slot | PT-BR | EN-US | Placeholder | Criativo ativo | Status |
|---|---|---|---|---|---|
| `header-top` | Pendente | Pendente | Pendente | Pendente | Pendente |
| `content-bottom` | Pendente | Pendente | Pendente | Pendente | Pendente |
| `sidebar-community` | Pendente | Pendente | Pendente | Pendente | Pendente |
| `sidebar-square` | Pendente | Pendente | Pendente | Pendente | Pendente |

## 6. Validação visual

- Label acima do criativo.
- Nenhum incentivo a clique.
- Placeholder discreto.
- Slot vazio preserva layout.
- Imagens responsivas.
- HTML administrado preservado.
- Scripts administrados preservados apenas quando permitidos.

## 7. Validação de workflow

- Executar workflow `Build M360 Core Plugin ZIP`.
- Informar versão `0.4.4.0`.
- Confirmar artifact `m360-core-v0.4.4.0.zip`.
- Baixar ZIP.
- Instalar em ambiente WordPress de validação.

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
- rollback documentado.
