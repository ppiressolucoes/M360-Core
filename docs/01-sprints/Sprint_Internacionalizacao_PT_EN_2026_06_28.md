# Sprint de Internacionalização PT-BR/EN-US

## Identificação

- Projeto: Portal Mengão 360 / DW Esportivo
- Data de consolidação: 28/06/2026
- Estado: concluída, publicada e validada
- Produção: `https://mengao360.com/en/`
- Pacote canônico: `artefatos/mengao360-internacionalizacao-pt-en-sprint-2026-06-28.zip`
- SHA-256 do pacote: `4ca10fe5ebb9aa95e4193c53380df53cec8a201f3f0b64df7661922d2c03d8d3`

## Resultado

A camada EN-US passou a operar como vitrine internacional do portal. A Home PT-BR foi preservada no News Portal e a Home EN foi composta no Elementor, ligada pelo Polylang e alimentada pelo workflow `INTER M360 | Traduzir Post PT - EN`.

## Arquitetura consolidada

`Post PT-BR → n8n/IA → Post EN → Polylang → shortcodes editoriais → Elementor /en/ → cache WordPress`

- WordPress/News Portal: experiência principal PT-BR.
- n8n e IA: tradução e normalização editorial.
- Polylang: idioma e vínculos entre entidades.
- Elementor: composição visual da Home EN.
- M360 Home Editorial: consultas, marcação, CSS, cache, fallbacks e interação.

## Artefatos executáveis

### Plugin M360 Home Editorial

- Versão funcional: `0.1.2`
- Arquivo: `m360-home-editorial-update-v0.1.2.zip`
- SHA-256 validado: `f83d92d76d446ff9fd19ac3b635a0b797c9783a6fa198663ffde6c65f898da21`

### Modelo Elementor

- Versão: `0.1.0`
- Arquivo: `m360-home-en-editorial-v0.1.0.json`
- SHA-256 validado: `b7411ea6a8dd59fb66d5064fd11d28917e059aa37ba7fb66eebc1dce141fa206`
- Templates relacionados: Header EN `4123` e Footer EN `3765`.

## Evidências de aceite

- Home `/en/` publicada.
- Header, footer, conteúdo dinâmico, ticker e hero aprovados.
- Desktop e mobile aprovados.
- Categorias EN e vínculos Polylang aprovados.
- Navegação PT-BR/EN-US e retorno ao topo validados.

## Uso em evoluções e corretivas

1. Consultar relatório, arquitetura e inventário do pacote.
2. Confirmar versão e checksum do plugin em produção.
3. Validar IDs dos templates Elementor e slugs das categorias.
4. Preservar a separação entre a Home PT-BR e a composição EN.
5. Aplicar o runbook de publicação, limpeza de cache e rollback.
6. Registrar a mudança no histórico, decisões e changelog.

## Pendências pós-sprint

- Validar `hreflang` PT-BR/EN-US.
- Confirmar URLs EN no sitemap XML.
- Inspecionar e solicitar indexação no Google Search Console.
- Medir Core Web Vitals após estabilização do cache.
- Planejar reutilização da engine para ES.

## Próxima frente

Com a internacionalização consolidada, o projeto está preparado para abertura da Sprint C — Mega Bolão 360 MVP Comercial.
