# M360 Core v0.7.2.4.2 — Renderer Canary Persistence Hotfix

## Correção

- separa a allowlist do canário da configuração geral do módulo;
- persiste os IDs na opção operacional `m360_discovery_canary_posts`;
- confirma a leitura de retorno antes de informar sucesso;
- exibe a confirmação do canário como aviso positivo;
- preserva snapshots, storage legado, shortcodes e gates de segurança.

## Aceite

Após salvar `7804, 7807`, o aviso superior deve mostrar os dois IDs e “Saída pública do Core” deve indicar dois posts. Só então o shortcode poderá produzir HTML.
