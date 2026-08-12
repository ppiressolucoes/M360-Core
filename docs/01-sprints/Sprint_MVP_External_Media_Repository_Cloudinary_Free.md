# Sprint MVP — Repositório Externo de Featured Images

## Decisão e objetivo

Criar um fluxo de mídia externa para as **imagens destacadas de novos posts** dos portais Mengão 360 e Energia Limpa. O arquivo binário será armazenado uma única vez no Cloudinary Free e os posts PT-BR e EN-US compartilharão essa mesma identidade física, sem novo upload nem geração de derivados em `wp-content/uploads` durante a tradução.

O motivador é a pressão de inodes na Hostinger: no levantamento de 12/08/2026, o ambiente utilizava 501.372 de 600.000 inodes (84%). O MVP é preventivo: não migra o acervo existente e não remove arquivos já publicados.

**Decisão técnica:** não usar uma URL solta como se fosse uma Featured Image nativa. O WordPress guarda a imagem destacada em `_thumbnail_id`, que aponta para um attachment. O adaptador do MVP deve criar um único *attachment virtual compartilhado* para cada hash de imagem, sem arquivo correspondente no uploads, e entregar a URL Cloudinary por filtros próprios. Os metadados editoriais ficam no post, não no attachment.

## Marco evolutivo aprovado — piloto isolado Mengão 360

Em 12/08/2026 foi aprovado iniciar a solução por um workflow n8n **isolado**, exclusivo do Mengão 360. Ele produzirá no máximo **duas postagens PT-BR por dia** e suas respectivas traduções EN-US, sem alterar os workflows editoriais já homologados.

O piloto será ativado por feature flag e terá identificação técnica própria nos posts e logs. Somente as publicações criadas por ele usam a camada de mídia externa; posts existentes e demais automações continuam usando o comportamento vigente.

O avanço para a implementação depende da disponibilização das credenciais Cloudinary em armazenamento seguro do n8n. Enquanto isso, ficam autorizados o desenho técnico, o mapeamento dos nodes atuais e a preparação do adaptador em homologação. As credenciais não devem ser registradas neste repositório.

## Escopo

Incluído:

- Featured Images de novos posts PT-BR e EN-US.
- Workflow n8n de publicação e workflow `INTER M360 | Traduzir Post PT - EN`.
- Cloudinary Free, credenciais seguras no n8n e entrega por CDN.
- Deduplicação por SHA-256.
- Uma referência virtual WordPress por mídia física.
- Alt, título, descrição e legenda por post/idioma.
- Compatibilidade com News Portal, Elementor, Open Graph e os componentes M360 que usam Featured Image.
- POC, medição de inodes e rollback.

Fora do escopo:

- Migração ou remoção do acervo histórico.
- Imagens no corpo dos artigos, vídeos, biblioteca de mídia completa, backup/DR.
- Cloudflare R2, Cloudflare Images, Google Drive e contratação de plano pago.

## Arquitetura-alvo

```text
arquivo editorial
  -> n8n: SHA-256 e lookup
  -> Cloudinary: um upload por hash
  -> registro de mídia externa + attachment virtual WordPress
  -> post PT-BR e post EN-US: mesmo _thumbnail_id
  -> adaptador WordPress: URL/CDN Cloudinary + metadados por post
```

O `asset_id` do Cloudinary é imutável e deve ser preservado para rastreabilidade; o `public_id` é o identificador de delivery. A chave de deduplicação da aplicação é `sha256` do binário. O nome original jamais é chave de unicidade.

## Contrato de dados

### Registro de mídia externa

Persistir em uma tabela própria ou, provisoriamente, em um custom post type técnico; escolher um dos dois antes da implementação. Campos mínimos:

| Campo | Regra |
| --- | --- |
| `sha256` | único; 64 caracteres hexadecimais; identidade física |
| `cloudinary_asset_id` | resposta do upload; imutável |
| `cloudinary_public_id` | identificador de delivery |
| `cloudinary_secure_url` | URL HTTPS original devolvida pelo Cloudinary |
| `mime_type`, `width`, `height`, `bytes` | telemetria e validação |
| `wp_attachment_id` | attachment virtual compartilhado |
| `created_at`, `last_verified_at` | auditoria |

O attachment virtual deve conter somente dados técnicos necessários à compatibilidade. Não pode apontar `'_wp_attached_file'` para um arquivo inexistente nem solicitar geração de thumbnails WordPress. A associação é feita em `_thumbnail_id` para que o core, o Elementor e os componentes existentes continuem a reconhecer uma Featured Image.

### Metadados editoriais por post

Armazenar no próprio post, com prefixo único (por exemplo `'_m360_external_featured_*'`):

- `media_sha256` e `attachment_id`;
- `alt`, `title`, `description` e `caption` no idioma do post;
- `delivery_url` somente como cache/reconciliação, não como fonte de identidade.

Isso é obrigatório porque um único attachment compartilhado não pode ter simultaneamente textos PT-BR e EN-US diferentes em seus campos globais.

## Alteração mínima no WordPress

Implementar um pequeno adaptador, preferencialmente como plugin independente do M360 Core, sem alterar o core do WordPress nem o tema de terceiros. Ele deve:

1. localizar posts com `_m360_external_featured_media_sha256`;
2. resolver o registro da mídia e gerar a URL de entrega Cloudinary adequada ao tamanho pedido;
3. filtrar os pontos de resolução de imagem usados pelo WordPress (`wp_get_attachment_image_src`, `wp_get_attachment_image_attributes` e `post_thumbnail_url`) para devolver URL, dimensões e `alt` do post atual;
4. garantir que `has_post_thumbnail()`, `get_the_post_thumbnail_url()` e `the_post_thumbnail()` continuem funcionais;
5. fornecer integração explícita para Open Graph/SEO usada no portal, se o plugin de SEO não consumir os filtros acima;
6. não interceptar attachments normais: a regra aplica-se apenas ao attachment virtual marcado como mídia externa M360.

Antes de codificar, testar a cadeia real de renderização no site de homologação: tema/News Portal, Elementor, widgets M360, plugin de SEO e prévia social. Os componentes M360 existentes consultam tamanhos como `medium`, `medium_large` e `large`; o adaptador deve convertê-los para transformações Cloudinary, preservando a proporção e evitando que o WordPress crie arquivos locais.

## Workflow n8n

### Publicação PT-BR

1. Receber o binário da Featured Image antes da chamada que cria/atualiza o post.
2. Validar tipo, tamanho e capacidade de leitura; calcular SHA-256 do binário original.
3. Consultar o endpoint WordPress do registro por `sha256`.
4. Se existir registro saudável, reutilizar `wp_attachment_id`, `asset_id` e `public_id`.
5. Se não existir, enviar o binário ao endpoint de upload do Cloudinary, receber a resposta e criar o registro + attachment virtual de forma idempotente.
6. Criar/atualizar o post PT-BR com o `wp_attachment_id` em `_thumbnail_id` e os quatro metadados editoriais no próprio post.

### Tradução EN-US (a cada 15 minutos)

1. Recuperar do post PT-BR o `sha256` e o `wp_attachment_id` já resolvidos.
2. Criar/atualizar o post EN-US com o **mesmo** `_thumbnail_id`; não chamar endpoint de upload de mídia.
3. Persistir somente os quatro metadados editoriais traduzidos no post EN-US.
4. Vincular o post no Polylang e registrar no log o ID PT, ID EN, hash e attachment compartilhado.

Os dois caminhos precisam suportar reexecução: uma repetição do workflow não pode criar outro asset Cloudinary, outro attachment virtual ou outro vínculo de tradução.

## Segurança e operação

- Usar upload autenticado no servidor n8n. `API_SECRET` só pode existir nas credenciais/variáveis seguras do n8n; nunca em export de workflow, código, logs ou payload para WordPress.
- Usar `secure_url` HTTPS. Restringir formatos e tamanho aceitos antes do upload.
- Usar `public_id` determinístico derivado do SHA-256 (por exemplo `m360/featured/<sha256>`), com proteção de concorrência: a criação do registro deve ter unicidade no hash e tratar resposta de conflito como reutilização.
- Registrar eventos técnicos sem expor URL assinada, segredo ou conteúdo binário.
- Manter a imagem original no Cloudinary durante o MVP; não habilitar exclusão automática enquanto o rollback estiver aberto.

## POC e evidências de aceite

Executar em homologação com um post real de teste:

1. Registrar contador de inodes e lista de arquivos relevantes em `uploads` antes do teste.
2. Publicar PT-BR pelo novo fluxo; comprovar um único asset Cloudinary, um registro e um attachment virtual sem arquivo em uploads.
3. Executar a tradução manual para EN-US; comprovar o mesmo hash, `asset_id`, `public_id` e `_thumbnail_id` em ambos os posts.
4. Confirmar que alt, título, descrição e legenda diferem por idioma, sem modificar o registro técnico compartilhado.
5. Validar frontend desktop/mobile, widgets M360, Elementor, WordPress admin, URL de imagem e Open Graph/social preview.
6. Rodar o agendamento de 15 minutos e uma repetição do job; comprovar idempotência e ausência de upload/arquivo adicional.
7. Comparar inodes após cada etapa. O delta esperado para a Featured Image e seus thumbnails na Hostinger é zero.
8. Guardar IDs, hash, resposta sanitizada do Cloudinary e capturas de validação no registro da POC.

## Critérios de aceitação

- Uma mesma imagem binária produz exatamente um registro técnico, um asset Cloudinary e um attachment virtual.
- PT-BR e EN-US compartilham a mesma referência física e não criam segundo upload nem arquivos locais equivalentes.
- O WordPress continua a reconhecer a Featured Image por `_thumbnail_id`.
- As URLs dos tamanhos solicitados pelos componentes são entregues pelo Cloudinary, sem thumbnails gerados no Hostinger.
- Os quatro metadados editoriais são independentes por post/idioma.
- Frontend, Elementor e Open Graph exibem a imagem externa corretamente.
- O job recorrente e seu retry são idempotentes.
- Credenciais não aparecem em código, export, logs ou API WordPress.
- O rollback restaura a etapa anterior de atribuição de Featured Image para novos posts, sem apagar posts nem assets do Cloudinary.

## Plano de implementação e rollback

1. Inventariar os workflows n8n e identificar o node exato que hoje envia a Featured Image para a Media Library na tradução EN-US.
2. Inspecionar o tema/plugins de produção para mapear filtros e o provedor efetivo de Open Graph.
3. Implementar endpoint/registro, attachment virtual e adaptador em homologação.
4. Importar uma cópia dos workflows como nova versão, executar POC e validar as evidências acima.
5. Habilitar por feature flag somente para novos posts; monitorar consumo do Cloudinary e inodes durante o piloto.

Rollback: desabilitar a feature flag/versão nova do workflow e restaurar a atribuição nativa de mídia somente para publicações posteriores. Não excluir assets, registros ou attachments virtuais durante a janela de observação. Caso a imagem externa não resolva no frontend, o post permanece publicado e pode receber uma Featured Image nativa manualmente até a correção.

## Pendências que bloqueiam alteração em produção

- Export/credenciais de leitura dos dois workflows n8n, sem segredos.
- Identificação do tema ativo, versão do Elementor e plugin responsável por SEO/Open Graph.
- Ambiente de homologação com acesso REST administrativo e cópia segura do fluxo.
- Confirmação dos limites vigentes de armazenamento, bandwidth e transformações da conta Cloudinary Free antes do piloto.

## Referências técnicas

- [Cloudinary Upload API](https://cloudinary.com/documentation/image_upload_api_reference): upload autenticado, `public_id` e retorno de asset.
- [Cloudinary asset identifiers](https://cloudinary.com/documentation/upload_parameters): `asset_id` imutável e `public_id` para delivery.
- [WordPress: `get_post_thumbnail_id`](https://developer.wordpress.org/reference/functions/get_post_thumbnail_id/): Featured Image é resolvida por `_thumbnail_id`.
- [WordPress: `get_the_post_thumbnail_url`](https://developer.wordpress.org/reference/functions/get_the_post_thumbnail_url/): URL é obtida a partir do attachment, razão para o adaptador.
