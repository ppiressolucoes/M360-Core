# M360 Core v0.7.1.12 — Editorial Readability & Cutover Gate

## Ajustes visuais

- link `VIEW ALL` ampliado e apresentado como controle com borda;
- data e autor ampliados, com maior peso e contraste;
- metadados dos cards compactos ajustados para preservar encaixe;
- item nativo do Newsroom removido da lista CRUD apos o cadastro da instancia personalizada;
- preset Newsroom preservado no dropdown de modelos.

## Decisao sobre o precursor

O M360 Home Editorial 0.1.2 deve permanecer ativo. O ticker legado continua fora do aceite de absorcao, o ownership dos shortcodes legados permanece no precursor e ainda falta o inventario final de referencias na Home EN-US e nos dados correspondentes do Elementor. A Home PT-BR e o tema News Portal nao pertencem a este cutover.

O M360 Semantic Relations 0.9.0 tambem permanece ativo. Sua absorcao pertence a v0.7.2 — Content Discovery & SEO.

## Gate para desativar o Home Editorial

1. substituir ou absorver o ticker legado em EN-US;
2. confirmar zero referencias inesperadas a `[m360_news_ticker]`, `[m360_news_hero]` e `[m360_news_section]` na Home EN-US e no Elementor;
3. homologar desktop e mobile em EN-US;
4. preparar o ZIP 0.1.2 e o roteiro de reativacao;
5. executar uma janela controlada de desativacao, sem excluir opcoes ou dados.

## Rollback

Reinstalar o ZIP canonico v0.7.1.11 para o ajuste visual. Em qualquer ensaio futuro de cutover, reativar imediatamente o M360 Home Editorial 0.1.2 se um shortcode, idioma ou layout perder renderizacao.
