# PEL — Cutover prospectivo de Content Discovery & SEO

## Autorização

Autorizado cutover público apenas para novos posts. Backfill, reprocessamento
do acervo e renderer global irrestrito permanecem sem autorização.

## Preflight

- Core `0.7.4.0.8` ativo;
- provider `external-pdo / connected`;
- canários `77976` e `77991` homologados;
- controle `77939` intacto;
- fila sem itens `queued`, `running` ou `failed`;
- backfill `idle` ou `stopped`.

## Ativação

1. No renderer, selecionar `Prospective — canários e novos posts`.
2. Manter `Links contextuais = 3` e salvar.
3. No writer, selecionar `Prospective — somente novos posts` e salvar.
4. Confirmar que o botão de iniciar backfill está indisponível.
5. Não selecionar `Automatic` em nenhum dos dois controles.

## Smoke test

1. Publicar um novo post PT-BR ou EN-US com categoria, tags e termos elegíveis.
2. Aguardar a fila e o WP-Cron concluírem o snapshot.
3. Confirmar run ativo `portable-v6-ext-dict`.
4. Validar até três links, história intermediária, notícias e tópicos.
5. Abrir um post histórico fora da allowlist e confirmar ausência de mudança.

## Recuperação da fila

Se o painel mostrar itens `queued` sem o hook `m360_discovery_process_post` no
WP Crontrol, atualizar para `0.7.4.0.9`. Ao carregar o site, o Core reageenda
automaticamente apenas itens cuja origem seja `post_published`. A tabela da
fila deve exibir `cron_recovered`; itens incompatíveis são marcados `ignored`.
O hook é um evento único e desaparece após a execução bem-sucedida.

## Runner dedicado quando WP-Cron estiver indisponível

Se `DISABLE_WP_CRON=true` e o Action Scheduler tiver backlog que impeça a
execução dos eventos nativos, usar o runner CLI da `0.7.4.0.10` na Cron do
servidor, a cada dois ou cinco minutos. No tipo **PHP** do hPanel, informar o
caminho relativo abaixo:

```text
domains/energialimpa.live/public_html/wp-content/plugins/m360-core/m360-discovery-cron.php
```

O hPanel acrescenta `/usr/bin/php /home/u164126954/` automaticamente. O caminho
`m360-core` deve corresponder à pasta real do plugin ativo. O runner processa
no máximo cinco entradas prospectivas por vez e sua saída registra
`considered`, `processed` e `skipped`.

Manter a tarefa existente de `wp-cron.php` para os demais serviços do
WordPress. O runner M360 é uma **segunda tarefa**, limitada à fila editorial
prospectiva.

## Rollback

1. Writer `manual`.
2. Renderer `canary` para preservar apenas os dois canários, ou `shortcode`
   para remover toda injeção automática.
3. Limpar cache de página/objeto.
4. Não remover runs, relações ou tabela externa.
