# M360 Core v0.7.1.13 — Editorial Ticker Cutover Candidate

## Entrega

O ticker foi absorvido como componente portatil do M360 Core. Ele nao depende do tema News Portal, Elementor ou classes CSS externas.

Recursos:

- configuracao de idioma portatil, com cutover desta entrega restrito a EN-US;
- rotulo configuravel;
- filtro por uma ou varias categorias;
- categoria da manchete;
- controles anterior e proxima;
- autoplay e intervalo configuraveis;
- pausa por hover e foco;
- respeito a `prefers-reduced-motion`;
- responsividade desktop e mobile;
- preview no widget Shortcode do Elementor.

## Homologacao EN-US

```text
[m360_editorial_ticker lang="en" label="Latest News" limit="8" interval="4500" autoplay="true"]
```

## Ownership legado

Enquanto o M360 Home Editorial 0.1.2 estiver ativo, `[m360_news_ticker]` continua pertencendo ao precursor na Home EN-US. O Core registra o fallback legado apenas quando o nome estiver livre, sem sobrescrever handlers existentes.

## Fora do escopo

A Home PT-BR utiliza o tema News Portal independentemente do M360 Core. Esta entrega nao altera seu tema, composicao, shortcodes ou renderizacao. O plugin precursor esta em uso apenas no EN-US.

Depois do aceite do novo ticker, o ensaio de cutover deve confirmar:

1. renderizacao do shortcode novo em EN-US;
2. funcionamento manual e automatico;
3. preview no Elementor;
4. desativacao controlada do Home Editorial;
5. resolucao de `[m360_news_ticker]` pelo fallback do Core;
6. reativacao imediata do precursor em caso de regressao.

## Rollback

Reativar o M360 Home Editorial 0.1.2 e restaurar o ZIP canonico v0.7.1.12 se necessario. Nenhuma opcao ou dado do precursor e removido.
