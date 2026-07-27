# M360 Core v0.7.2.4.6 — Semantic Renderer Visual Polish

## Objetivo

Concluir o refinamento visual do renderer automático antes da v0.7.2.5 — Scheduler, Writer & Backfill Cutover.

## Alterações

- título “LEIA TAMBÉM” / “RELATED STORY” maior e mais legível;
- tags e categorias relacionadas apresentadas como botões com borda, contraste, hover e foco visível;
- documentação explícita do contrato dos três links contextuais;
- incremento de versão para invalidar o cache do asset CSS.

## Contrato preservado

- no máximo três links para destinos únicos;
- somente ocorrências já presentes no corpo;
- termos atribuídos e frases significativas derivadas de posts relacionados;
- mesmo locale do post de origem;
- nenhuma geração de snapshots em visita pública;
- nenhuma mudança no writer, scheduler, tabelas legadas ou modo público armazenado.

## Atualização e rollback

A atualização preserva o modo `automatic` ou `shortcode` já salvo. Para rollback imediato, reinstalar a v0.7.2.4.5; não remover tabelas do Core nem do precursor.
