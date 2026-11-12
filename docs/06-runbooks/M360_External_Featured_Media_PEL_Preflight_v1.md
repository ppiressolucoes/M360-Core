# M360 External Featured Media — preflight do Portal Energia Limpa

## Escopo

Validar a instalação independente da baseline `0.1.6` no staging do PEL, sem mudança de tema, M360 Core, conteúdo existente ou produção.

## Pré-requisitos

1. Confirmar WordPress `6.0+` e PHP `8.0+`.
2. Confirmar que não existe outro plugin usando a rota `m360/v1/external-featured-media`.
3. Instalar o ZIP canônico `m360-external-featured-media-wordpress-v0.1.6.zip` e ativá-lo.
4. Criar credencial n8n exclusiva com Application Password e capacidades `upload_files` e `edit_posts`.
5. Configurar no n8n as credenciais do Cloudinary do PEL; nenhuma credencial do Mengão 360 é reutilizada.

## Teste controlado

1. Enviar uma imagem de teste ao Cloudinary do PEL e calcular seu SHA-256.
2. Chamar `POST /m360/v1/external-featured-media`; repetir a chamada e confirmar `existing: true` e o mesmo `attachment_id`.
3. Associar o attachment a um post de teste via `/assign` com `alt`, título, descrição e legenda.
4. Confirmar `_thumbnail_id`, hash e `attachment_id` no post; confirmar que nenhum binário ou thumbnail foi criado em `uploads`.
5. Abrir o post em janela anônima e validar URL Cloudinary, largura máxima de 768 px, `alt` e legenda.
6. Se Polylang estiver ativo, criar a tradução EN-US, reutilizar o attachment e confirmar legenda/`alt` em inglês no frontend.

## Critérios de aceite

- rotas REST respondem autenticadas e não expõem segredos;
- uma repetição é idempotente;
- o PEL não acessa assets, banco, usuários ou credenciais do Mengão 360;
- tema, Elementor, M360 Core e posts sem mídia externa continuam sem regressão;
- o rollback consiste em desativar o workflow n8n e o plugin para novas publicações, sem apagar attachments ou assets.
