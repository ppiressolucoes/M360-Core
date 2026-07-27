# M360 Core — Portable Deployment Runbook

Versão de referência: `v0.7.4.0.1`.

## 1. Pré-condições

- staging isolado do portal de destino;
- backup de banco e arquivos;
- inventário de tema, cache, SEO, Polylang, MailPoet, CMP e AdSense;
- pacote canônico e checksum registrados;
- responsável e janela de rollback definidos.

## 2. Instalação segura

Após instalar e ativar:

1. abrir `M360 Dashboard > Privacidade & Plataforma`;
2. confirmar política `portable-safe`;
3. confirmar origem `fresh-installation-no-evidence` e ausência de evidências históricas;
4. confirmar todos os gates públicos desmarcados;
5. confirmar Editorial e Discovery inativos;
6. validar que busca, autor, categorias, tags e arquivos continuam usando o tema;
7. verificar ausência de cron `m360_newsletter_*`;
8. verificar que não existem campanhas M360 criadas pela instalação.

Se qualquer saída pública aparecer, desativar o plugin e restaurar o backup antes de prosseguir.

Em upgrade de uma instalação M360 anterior sem Runtime Profile, confirmar:

1. política `legacy-compatible`;
2. origem `historical-installation-evidence`;
3. lista de opções ou tabelas históricas no diagnóstico;
4. capacidades públicas anteriormente homologadas habilitadas.

Se já existir Runtime Profile, o hotfix deve preservá-lo sem alteração.

## 3. Site Profile

Configurar somente identidade e contratos portáteis:

- chave e nome do portal;
- vertical;
- locale padrão;
- locales suportados;
- política de implantação e gates.

Não importar conteúdo, assinantes, consentimentos, campanhas, criativos, tokens ou credenciais.

## 4. Discovery

1. ativar o módulo;
2. manter renderer em shortcode e writer conforme roteiro de shadow;
3. executar amostra por idioma;
4. iniciar backfill controlado;
5. validar cobertura, falhas, locale cruzado e latência;
6. liberar canário;
7. autorizar `automatic` somente após homologação.

## 5. Editorial

- ativar inicialmente em preview/hybrid;
- cadastrar widgets com taxonomias do portal;
- não assumir slugs como `destaque` ou `internacional` sem confirmação;
- inserir componentes somente em páginas selecionadas;
- validar desktop, mobile, cache e Polylang.

## 6. Serviços existentes

- AdSense: não alterar slots, scripts ou ownership na primeira onda;
- MailPoet: selecionar explicitamente uma lista antes de habilitar Newsletter;
- CMP: manter o fornecedor atual; não ativar a interface local em produção;
- SEO técnico: preservar sitemap, canonical, meta e schema do plugin atual.

## 7. Rollback

1. desmarcar o gate afetado;
2. desativar o módulo correspondente;
3. limpar somente caches relacionados;
4. confirmar restauração do tema e integrações existentes;
5. desativar o Core se o impacto não cessar;
6. preservar tabelas para diagnóstico; nenhuma exclusão automática é autorizada.
