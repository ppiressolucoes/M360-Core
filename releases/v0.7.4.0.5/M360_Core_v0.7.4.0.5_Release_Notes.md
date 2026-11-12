# M360 - Core Editorial v0.7.4.0.5 — Discovery Dictionary Table Resolution & Diagnostics

## Causa

A `0.7.4.0.4` regenerou corretamente os snapshots, mas o total de relações internas permaneceu inalterado. O adapter procurava somente `${$wpdb->prefix}links_internos`; no PEL, o nome físico informado é `WP_links_internos`. Em MySQL/Linux, a diferença de caixa pode impedir a resolução.

## Correção

- inspeciona as tabelas existentes e seleciona o nome físico real por comparação sem distinção de caixa;
- suporta prefixo WordPress configurado, `WP_links_internos` e `wp_links_internos`;
- mantém todas as consultas em modo somente leitura;
- adiciona diagnóstico com provider, tabela detectada, linhas elegíveis por locale e termos correspondentes nos canários;
- algoritmo `portable-v4-dictionary-table-resolution`.

## Homologação PEL

1. Atualizar o Core para `0.7.4.0.5`.
2. Abrir **M360 Dashboard > Discovery & SEO > Diagnósticos e execução manual**.
3. Confirmar provider `detectado`, tabela `WP_links_internos`, linhas PT/EN maiores que zero e correspondências para `77976`/`77991`.
4. Gerar novamente os snapshots dos dois canários.
5. Limpar cache e validar até três elementos com classe `m360-discovery-context-link`.
6. Confirmar novamente que `77939` permanece intacto.

## Rollback

Retornar o renderer a `Shortcode — rollback` e reinstalar `0.7.4.0.4`. Nenhuma tabela local ou snapshot deve ser excluído.
