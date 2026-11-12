# M360 External Featured Media v0.1.6 — baseline de produção

Data: 24/08/2026

Ambiente de evidência: Mengão 360

Classificação: baseline oficial do componente independente

## Marco

A versão `0.1.6` está instalada em produção no Mengão 360. Ela registra attachments virtuais Cloudinary, evita binários e thumbnails no `uploads`, normaliza a entrega em até 768 px e permite que traduções PT-BR/EN-US reutilizem a mesma mídia técnica.

## Evidências funcionais

- publicação n8n registra/vincula Featured Image e metadados editoriais;
- tradução EN-US reutiliza o mesmo `attachment_id` e não faz novo download ou upload;
- o frontend resolve legenda e texto alternativo do post EN-US;
- a Media Library conserva os metadados globais da primeira associação, sem sobrescrever o PT-BR.

## Artefato aprovado

- pacote: `artifacts/m360-external-featured-media-wordpress-v0.1.6.zip`;
- SHA-256: `9506EE33B31AA3E56D24AD8DB147CBB491DB65C12770AEA02846262798D7EF25`;
- estrutura obrigatória: `m360-external-featured-media/m360-external-featured-media.php`;
- código-fonte: `artifacts/m360-external-featured-media/`.

## Próximo ambiente

O Portal Energia Limpa deve instalar o mesmo ZIP em staging, executar o preflight e publicar apenas por workflow controlado após aceite. Não há migração de attachments, assets ou configurações do Mengão 360.
