# M360 Core v0.7.2.3 — Internal Link Contract & Shadow Parity

## Contrato

- `internal_link.target_type` é obrigatoriamente `term`.
- O termo deve existir em taxonomia configurada no Site Profile e estar atribuído ao post de origem.
- Há no máximo quatro destinos, ordenados por taxonomia e ID.
- `related_post` permanece o único tipo que aponta para `post`.

## Segurança operacional

- Geração apenas no storage próprio e em modo `shadow`.
- Sem shortcode, renderer, `the_content`, REST público ou geração em visita.
- Sem escrita, migração ou cancelamento de dados/eventos do precursor.
- M360 Semantic Relations 0.9.0 continua writer e renderer exclusivo.

## Homologação solicitada

1. Atualizar o pacote e confirmar `M360 Core 0.7.2.3`.
2. Gerar em shadow os posts `7804` (PT-BR) e `7807` (EN-US).
3. Rodar o Comparator nos mesmos posts/locales.
4. Confirmar `internal_link` com destinos `term`, sem tipo sem destino compartilhado e sem efeito visual.
5. Não ativar renderer nem desativar o precursor nesta release.

## Rollback

Desativar o módulo Content Discovery & SEO ou reinstalar a versão anterior. Nenhuma tabela, configuração ou renderer legado é alterado.
