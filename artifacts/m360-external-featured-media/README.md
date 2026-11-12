# M360 External Featured Media — piloto

Plugin WordPress independente para a POC de Featured Images externas no Mengão 360. Ele não grava o binário da imagem no `uploads`: cria um attachment técnico com a URL Cloudinary e expõe endpoints REST para o workflow n8n.

Versão `0.1.6`: os campos editoriais (`alt`, título, descrição e legenda) são gravados no post e são a fonte de verdade por idioma. Como o attachment virtual pode ser compartilhado entre PT-BR e EN-US, seus campos nativos globais são preenchidos somente na primeira associação, como fallback de compatibilidade para widgets legados; associações posteriores não podem sobrescrevê-los. Para a legenda exibida no frontend, o plugin intercepta `wp_get_attachment_caption` e entrega a legenda do post atualmente renderizado.

## Perfil único de exibição do MVP

Toda Featured Image externa é entregue pelo Cloudinary com largura máxima de **768 px** (`c_limit,w_768`), preservando a proporção original. Pedidos do tema ou de componentes por `thumbnail`, `medium`, `large`, `full` ou dimensões customizadas são deliberadamente normalizados para esse perfil único.

O asset original e seus metadados técnicos continuam registrados no attachment virtual, mas não há geração de thumbnails nem gravação de binários no `uploads` do WordPress. A regra vale tanto para a resolução de imagem (`image_downsize`) quanto para a URL direta do attachment.

## Instalação em homologação

1. Compactar a pasta `m360-external-featured-media` em ZIP e instalar como plugin.
2. Ativar o plugin.
3. Usar um usuário WordPress dedicado com Application Password e as capacidades `upload_files` e `edit_posts` no n8n.
4. Não ativar em produção antes da POC completa.

## Endpoints

### Registrar ou reutilizar mídia

`POST /wp-json/m360/v1/external-featured-media`

Body JSON:

```json
{
  "sha256": "<64-hex>",
  "asset_id": "<cloudinary-asset-id>",
  "public_id": "m360/featured/<hash>",
  "secure_url": "https://res.cloudinary.com/...",
  "width": 1200,
  "height": 675,
  "mime_type": "image/jpeg",
  "original_filename": "imagem.jpg"
}
```

Retorna `attachment_id` e `existing`. Uma repetição com o mesmo hash devolve o attachment já criado.

### Associar ao post PT-BR

`POST /wp-json/m360/v1/external-featured-media/assign`

Body JSON:

```json
{
  "post_id": 123,
  "attachment_id": 456,
  "alt": "...",
  "title": "...",
  "description": "...",
  "caption": "..."
}
```

O endpoint atualiza `_thumbnail_id` e guarda os quatro campos editoriais no post. O attachment é compartilhável para futura tradução EN-US, sem duplicar o arquivo.
