# M360 Core — PEL Controlled Deployment v0.1.3

Este complemento é instalado **depois** do ZIP candidato `m360-core-v0.7.4.0.10.zip`. Ele aplica somente o perfil controlado do PEL.

## Sequência

1. Verificar o SHA do ZIP candidato: `80F311F7B8295E7B5FA9AE31A7E585CFAC2C34100E148840A92FE9751A483CB5`.
2. Instalar e ativar o M360 Core v0.7.4.0.10.
3. Verificar `portable-safe` e `fresh-installation-no-evidence` em M360 > Plataforma.
4. Instalar este complemento e, em M360 Dashboard > PEL — Implantação, selecionar **Aplicar perfil PEL controlado**.
5. Homologar cada shortcode em uma página/template Elementor de teste antes de qualquer publicação.

## Gates do perfil

- Preserva Elementor e Cream Magazine; não assume templates públicos.
- Mantém Ads, Newsletter e Consent Runtime desligados.
- Editorial usa somente shortcodes inseridos de forma explícita.
- Discovery gera snapshots manualmente, sem fallback externo, writer automático ou injeção no conteúdo.
- Site Profile usa primária `#ff3d00` e secundária `#fc893c`.
- Não altera conteúdo, usuários, campanhas, listas, credenciais, segredos ou dados de MailPoet/AdSense/CMP.

## Homologação por funcionalidade

| Capacidade | Instrumento | Gate inicial |
| --- | --- | --- |
| Pesquisa | `[m360_pel_search_form]` + `[m360_pel_search_results]` | Criar páginas Elementor `/resultados-da-pesquisa/` e `/en/search-results/` |
| Navegação | `[m360_breadcrumb]`, `[m360_main_navigation]` | Inserção explícita no Elementor |
| Últimas notícias | `[m360_latest_news pagination="true"]` | Página de teste pt-BR/en-US |
| Metadados de post | `[m360_post_info]` | Template Single en-US de teste |
| Widgets | `[m360_editorial_widget id="..."]` | Widget por idioma/categoria |
| Discovery | snapshots + canário + shortcodes renderer | Sem injeção automática |
| Consent, Newsletter, Ads | diagnóstico de provider | Sem runtime público |

Qualquer ativação de Ads, Newsletter, Consent, injeção automática de Discovery ou takeover de templates exige uma autorização específica e um registro de homologação.

## Página de resultados sem takeover

Crie uma página em cada idioma no Polylang e coloque nela o shortcode
`[m360_pel_search_results]`. O formulário padrão
`[m360_pel_search_form]` envia para essas URLs e a consulta somente posts
publicados do idioma atual. Nenhum filtro `template_include` é habilitado.

Para evitar título duplicado, oculte o título da página no Elementor e mantenha
o título semântico gerado pelo shortcode. Como alternativa, use
`[m360_pel_search_results show_title="false"]`.

A v0.1.3 substitui formulários renderizados por `get_search_form()` pelo
formulário PEL após a aplicação do perfil. Formulários HTML personalizados devem
usar `name="m360q"` e enviar para `/resultados-da-pesquisa/` ou
`/en/search-results/` conforme o idioma.
