# M360 Core v0.7.1.14 — Editorial Ticker Preview & Playback Hotfix

## Correcoes

- renderizacao dinamica no preview do widget Shortcode do Elementor;
- reinicializacao quando o Elementor substitui o conteudo do widget;
- autoplay ativado apos insercao dinamica;
- setas em SVG, sem dependencia dos glifos da fonte ativa;
- controles escuros com contraste permanente;
- rotulo, categoria e manchete ampliados;
- tipografia isolada da fonte condensada do portal;
- ticker inicializado independentemente do Newsroom e dos Editorial Widgets.

## Shortcode de homologacao EN-US

```text
[m360_editorial_ticker lang="en" label="Latest News" limit="8" interval="4500" autoplay="true"]
```

O autoplay pausa quando o cursor permanece sobre o ticker, quando um controle recebe foco, quando a aba fica oculta ou quando o sistema solicita movimento reduzido.

## Escopo

Somente a Home EN-US participa desta homologacao. A Home PT-BR e o tema News Portal permanecem independentes e inalterados.

## Rollback

Reinstalar o ZIP canonico v0.7.1.13. O M360 Home Editorial 0.1.2 permanece ativo ate o aceite final deste hotfix.
