# M360 Core v0.7.4.0.96 — Informações de leitura

O componente `[m360_post_info]` exibe a quantidade de palavras e o tempo estimado de leitura. O usuário informou que o pacote foi aplicado e homologado no PEL e no Mengão 360.

## Uso

- `[m360_post_info date_format="d/m/Y"]` apresenta a data no formato 19/11/2026; sem o atributo, usa o formato geral do WordPress.
- `words="false"` e `reading_time="false"` ocultam os respectivos itens.
- `wpm="220"` ajusta a velocidade estimada; o padrão é 200 palavras por minuto, limitado entre 80 e 1000.
- Rótulos são exibidos em português ou inglês conforme o idioma corrente.

A contagem usa o conteúdo armazenado do post, sem executar shortcodes. O cálculo é dispensado quando os dois itens estão ocultos e reutilizado na mesma requisição para o mesmo conteúdo.

## Artefato homologado

- Arquivo: `m360-core-v0.7.4.0.96.zip`
- SHA-256: `735EF5848562C5286AF0466D1CD073D5EAE9B40A20B1160B29564F672D4A560D`
- A homologação visual e a aplicação nos dois ambientes foram informadas pelo usuário. Lint PHP e medição de desempenho não foram executados neste workspace.
