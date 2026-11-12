# M360 External Featured Media — componente portátil

Versão do componente: `0.1.6`

Baseline: produção do Mengão 360 em 24/08/2026

Pacote canônico: `artifacts/m360-external-featured-media-wordpress-v0.1.6.zip`

## Finalidade

Registra imagens Cloudinary como attachments virtuais do WordPress. O binário permanece externo; o WordPress preserva a Featured Image pelo `_thumbnail_id`.

## Contrato portátil

- Requer WordPress `6.0+` e PHP `8.0+`.
- Não requer M360 Core, Elementor, Polylang, tema, tabela ou opção específica de portal.
- Requer URL HTTPS em `res.cloudinary.com` e workflow autenticado com `upload_files` e `edit_posts`.
- Uma imagem identificada por SHA-256 gera no máximo um attachment virtual por instalação.
- Metadados técnicos ficam no attachment; `alt`, título, descrição e legenda ficam no post que usa a imagem.
- PT-BR e EN-US compartilham o attachment, mas possuem metadados editoriais independentes.

## Superfícies públicas

| Função | Endpoint/filtro |
| --- | --- |
| Registrar ou reutilizar asset | `POST /wp-json/m360/v1/external-featured-media` |
| Associar Featured Image e metadados do post | `POST /wp-json/m360/v1/external-featured-media/assign` |
| URL de entrega | `wp_get_attachment_url`, `image_downsize` |
| Texto alternativo e legenda contextualizados | `wp_get_attachment_image_attributes`, `wp_get_attachment_caption` |

## Limites intencionais

- A Media Library mostra os campos globais do attachment, definidos na primeira associação. Ela não é uma tela de edição por idioma.
- O frontend resolve `alt` e legenda pelo post exibido.
- O perfil de entrega é único, proporcional e limitado a 768 px (`c_limit,w_768`).
- Assets Cloudinary, credenciais, conteúdo, taxonomias e usuários nunca são transportados entre portais.

## Evolução

Mudanças devem preservar a pasta `m360-external-featured-media/`, o arquivo principal `m360-external-featured-media.php`, os dois endpoints e os metadados existentes. O PEL começa em piloto controlado; toda alteração de contrato exige versão, evidência e rollback.
