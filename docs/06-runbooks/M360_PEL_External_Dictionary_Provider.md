# PEL — Provider externo do dicionário de links

## Finalidade

Conectar o M360 Core do WordPress PEL ao dicionário somente leitura
`u164126954_energia_limpa.WP_links_internos`, mantido em banco paralelo.

As credenciais são locais do ambiente. Elas não pertencem ao Site Profile e
não podem ser incluídas no repositório, pacote ZIP, banco de opções, exportação
ou evidência de homologação.

## Configuração recomendada

Adicionar as constantes abaixo ao `wp-config.php`, antes da linha final que
interrompe a edição. Substituir somente os placeholders locais:

```php
define('M360_DISCOVERY_DB_HOST', 'localhost');
define('M360_DISCOVERY_DB_PORT', 3306);
define('M360_DISCOVERY_DB_NAME', 'u164126954_energia_limpa');
define('M360_DISCOVERY_DB_USER', 'USUARIO_SOMENTE_LEITURA');
define('M360_DISCOVERY_DB_PASSWORD', 'SEGREDO_LOCAL');
define('M360_DISCOVERY_DB_TABLE', 'WP_links_internos');
```

O usuário MySQL deve possuir apenas `SELECT` sobre a tabela. O Core executa
`SELECT` preparado e não contém comandos de escrita para esse provider.

## Ponte compatível por PHP

Quando o ambiente gerencia segredos fora do `wp-config.php`, pode fornecer um
objeto `PDO` pela função abaixo ou pelo filtro de mesmo nome:

```php
function m360_discovery_dictionary_pdo(): ?PDO {
    // Resolver as credenciais no cofre/configuração local do ambiente.
    // Nunca incluir valores reais no repositório ou no pacote do plugin.
    return $pdo_local_somente_leitura ?? null;
}
```

Não reutilizar `conectar_dw_esportes_m360()` no PEL: o nome e o schema são do
ambiente esportivo. A ponte do PEL deve usar o contrato neutro acima.

## Gate de validação

1. Manter writer `manual`, backfill `idle` e renderer `canary`.
2. Reabrir **M360 Dashboard > Content Discovery & SEO**.
3. Confirmar `external-pdo`, `connected` e `WP_links_internos`.
4. Confirmar linhas elegíveis em PT-BR e EN-US.
5. Confirmar correspondências nos IDs `77976` e `77991`.
6. Somente então gerar novamente os dois snapshots isolados.
7. Validar até três âncoras no conteúdo e confirmar `77939` intacto.

## Rollback

Remover as constantes ou desativar a ponte PHP local. O provider volta ao
estado não configurado sem excluir snapshots nem alterar o dicionário externo.
