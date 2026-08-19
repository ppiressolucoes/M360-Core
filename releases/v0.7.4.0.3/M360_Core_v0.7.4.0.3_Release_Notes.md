# M360 - Core Editorial v0.7.4.0.3 — PEL Discovery Canary Allowlist UI Hotfix

## Correção

- restaura no painel Content Discovery o campo de IDs autorizados do Renderer Canary;
- mantém a persistência isolada na opção operacional `m360_discovery_canary_posts`;
- exibe os IDs atualmente persistidos e aceita até 20 posts publicados;
- diferencia no shortcode de diagnóstico: post inválido, módulo inativo, modo fora de `shadow` e post ausente da allowlist;
- preserva snapshots, writer manual, backfill parado e o modo público já escolhido;
- não altera Ads, Newsletter, Consent, tema, Elementor, Polylang ou templates.

## Procedimento PEL

1. Atualizar o Core de `0.7.4.0.2` para `0.7.4.0.3`.
2. Em **M360 Platform > Content Discovery > Renderer público**, salvar `77976, 77991` em **IDs autorizados no canário**.
3. Manter renderer `Canary`, links contextuais `3`, writer `manual` e backfill parado.
4. Repetir o shortcode de diagnóstico e a validação pública dos posts.

## Rollback

Retornar o renderer a `Shortcode — rollback` e reinstalar o ZIP `0.7.4.0.2` se necessário. Nenhuma tabela ou snapshot deve ser removido.
