# M360 - Core Editorial v0.7.4.0.20

## Correção

A versão anterior restaurava visualmente a escolha no navegador, mas o consent default de uma página servida por cache ainda podia refletir o visitante que originou o HTML. Esta versão resolve o cookie antes da configuração Google e emite o estado efetivo antes de qualquer configuração GA4.

## Política

- Primeira visita: analytics_storage concedido.
- Ads, personalização, Preferências e mídia externa: negados.
- Ajustar cookies pode revogar Analytics imediatamente e essa decisão persiste.

## Validação

1. Limpar cache/CDN depois da atualização.
2. Primeira visita: Tag Assistant deve mostrar analytics_storage concedido no Padrão na página.
3. Revogar Analytics, navegar: Padrão/Estado atual devem permanecer negados.
4. Confirmar page_view em Hits enviados e GA4 Tempo Real.
