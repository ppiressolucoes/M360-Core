# M360 Privacy & Consent Architecture v1

## Decisão arquitetural

O M360 Core é o proprietário do contrato de consentimento, mas não da certificação regulatória da CMP. O fornecedor escolhido coleta e mantém os sinais exigidos; o `M360 Consent Adapter` traduz esses sinais para uma interface estável usada pelo portal.

## Categorias canônicas

| Categoria | Finalidade | Padrão |
|---|---|---|
| `necessary` | segurança e funcionamento essencial | concedido |
| `preferences` | preferências e personalização funcional | negado |
| `analytics` | medição de audiência | negado |
| `advertising` | publicidade, dados e personalização de anúncios | negado |
| `external_media` | embeds e mídia de terceiros | negado |

## Contratos públicos

### PHP

```php
m360_has_consent('analytics');
M360_Consent_Manager::can_deliver_provider('adsense');
```

### JavaScript

```javascript
window.M360Consent.has('advertising');
window.M360Consent.update({ advertising: true });
window.addEventListener('m360:consent:update', handler);
```

Uma CMP externa pode alimentar o contrato sem dependência direta do fornecedor:

```javascript
window.dispatchEvent(new CustomEvent('m360:cmp:consent', {
  detail: { categories: { analytics: true, advertising: false } }
}));
```

### Filtros WordPress

- `m360_consent_state`;
- `m360_consent_provider_requires_advertising`;
- `m360_consent_can_deliver_provider`.

## Google Consent Mode v2

Quando habilitado, o M360 declara inicialmente como `denied`:

- `ad_storage`;
- `ad_user_data`;
- `ad_personalization`;
- `analytics_storage`;
- `functionality_storage`;
- `personalization_storage`.

`security_storage` permanece `granted`. Após a escolha do usuário, o adaptador envia `gtag('consent', 'update', ...)` com o estado normalizado.

## Integração com publicidade

O bloqueio é opt-in. Quando habilitado, apenas providers `adsense` e `google-ad-manager` dependem de `advertising`. Campanhas internas, house ads, afiliados e patrocinadores permanecem sob suas regras próprias e podem ser ampliados por filtros.

## Modos

- `external_cmp`: produção recomendada; a CMP certificada é responsável pela interface e pelos sinais TCF;
- `local_foundation`: homologação funcional da central M360; não substitui certificação Google/IAB.

## Privacidade por padrão

- plugin inicia desativado;
- Consent Mode v2 usa negação padrão;
- bloqueio do Ads Manager inicia em modo de observação;
- nenhuma alegação de conformidade automática;
- políticas e fornecedores exigem validação jurídica e operacional.
