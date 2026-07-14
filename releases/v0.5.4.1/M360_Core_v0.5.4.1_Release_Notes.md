# M360 Core v0.5.4.1 — Latest News Sidebar Mode

## Objetivo

Entregar uma variante compacta do componente de últimas notícias para uso em barras laterais, permitindo que o bloco seja exibido sem publicidade inserida automaticamente entre os itens.

## Shortcode de homologação

```text
[m360_latest_news layout="sidebar" show_ads="false"]
```

## Compatibilidade

- `[m360_latest_news]` continua usando `layout="list"`;
- `show_ads` permanece `true` por padrão;
- as opções existentes de limite, título, imagem, categoria, data e cache foram preservadas;
- valores de layout desconhecidos retornam com segurança ao layout `list`.

## Critérios de homologação

- validar a sidebar em PT-BR e EN-US;
- confirmar cards uniformes no desktop e no mobile;
- confirmar títulos das notícias com peso regular e boa legibilidade;
- confirmar que `show_ads="false"` não renderiza o slot `latest-inline`;
- confirmar que o shortcode legado preserva a entrega publicitária existente;
- verificar imagens, categorias, datas e links das notícias.
## Compatibilidade do pacote

O ZIP deve usar o identificador canônico `m360-core/m360-core.php` e caminhos internos com `/`, inclusive quando for gerado no Windows. O script `scripts/build-plugin-package.ps1` valida esse contrato antes da homologação.