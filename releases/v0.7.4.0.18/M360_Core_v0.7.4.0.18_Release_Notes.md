# M360 - Core Editorial v0.7.4.0.18

## Entrega

Consolidação da evolução Mengão 360 → Portal Energia Limpa com correção da integração CMP → Google Consent Mode → GA4.

## Validação obrigatória

1. Primeira visita sem decisão: estado default conforme a política do portal.
2. Analytics negado: analytics_storage=denied.
3. Analytics concedido: analytics_storage=granted, sem reload.
4. Ajustar cookies: transições granted/denied persistem em reload e navegação.
5. DevTools/Tag Assistant: uma sequência de consentimento e ausência de page views duplicados.
6. GA4 Tempo Real: sessões autorizadas e eventos permitidos registrados.

## Rollback

Reinstalar o pacote anterior; não apagar opções, cookies ou tabelas.
