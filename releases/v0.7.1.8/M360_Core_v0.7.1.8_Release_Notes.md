# M360 Core v0.7.1.8 — Editorial Sections

## Escopo

- Registra o aceite visual do Newsroom v0.7.1.7 como baseline de homologação.
- Evolui os blocos públicos `m360_editorial_section` nos layouts `grid`, `featured-list` e `compact`.
- Preserva o plugin M360 Home Editorial e o ticker legado durante a migração progressiva.
- Não altera cabeçalho, rodapé, conteúdo, dados pessoais ou configurações do portal.

## Shortcodes para homologação EN-US

```text
[m360_editorial_section title="Latest News" lang="en" layout="grid" limit="8"]
[m360_editorial_section title="Brazilian Team" lang="en" category="brazilian-team" layout="featured-list" limit="5"]
[m360_editorial_section title="Flamengo" lang="en" category="flamengo-en" layout="grid" limit="4"]
[m360_editorial_section title="Transfers" lang="en" category="transfers" layout="featured-list" limit="5"]
[m360_editorial_section title="Lineups & Injuries" lang="en" category="lineups,injuries" layout="compact" limit="6"]
```

O ticker `[m360_news_ticker]` permanece sob ownership do precursor nesta versão.

## Rollback

1. Restaurar os cinco shortcodes legados `m360_news_section` na página EN-US.
2. Reinstalar o ZIP canônico v0.7.1.7, se necessário.
3. Manter M360 Home Editorial 0.1.2 ativo durante toda a homologação.
