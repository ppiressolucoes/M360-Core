# Exportação local do DW Esportivo

Este pacote gera os insumos necessários ao mapeamento do DW sem Docker e sem gravar a senha na linha de comando. É necessário ter apenas `mariadb-dump` ou `mysqldump` disponível na máquina que alcança o banco.

## Destino local

O exportador grava em `local-data/dw-esportivo/incoming/<data-hora>/` e cria um ZIP ao lado da pasta. O conteúdo é ignorado pelo Git.

O pacote contém:

- `dw-esportivo-schema.sql`: estrutura completa do schema, sem dados e com identidades `DEFINER` neutralizadas;
- `dw-esportivo-core-data.sql`: dados das cinco tabelas centrais;
- `manifest.json`: versão do cliente, escopo, tamanhos e hashes SHA-256;
- `<pacote>.zip` e `<pacote>.zip.sha256`: arquivos para transporte e conferência.

As tabelas de dados padrão são `dim_times`, `dim_competicoes`, `fato_classificacao`, `fato_jogos` e `dim_competicao_fase_jogo`.

## Pré-requisitos

1. Cliente MariaDB ou MySQL instalado. Um servidor local não é necessário para produzir o dump.
2. Conectividade direta com o DW ou túnel autorizado.
3. Usuário dedicado com `SELECT` e visibilidade de metadados/views.
4. PowerShell 5.1 ou superior.

Confirme o cliente com `mariadb-dump --version` ou `mysqldump --version`.

## Execução recomendada

Na raiz deste checkout:

```powershell
.\scripts\dw-editorial\Export-DwEditorialDump.ps1 `
  -HostName 'HOST_DO_DW' `
  -Port 3306 `
  -Database 'NOME_DO_SCHEMA' `
  -UserName 'USUARIO_SOMENTE_LEITURA' `
  -RequireTls
```

A senha será solicitada de forma interativa e não será adicionada aos argumentos do processo. Não informar senha no comando, em arquivo versionado ou no chat.

Se o executável não estiver no `PATH`, acrescente:

```powershell
-DumpExecutable 'C:\Program Files\MariaDB 11.4\bin\mariadb-dump.exe'
```

Para gerar inicialmente apenas a estrutura, acrescente `-SchemaOnly`. Quando a conexão ocorrer por túnel SSH local e o servidor não oferecer TLS no trecho local, omita `-RequireTls` somente se o túnel já proteger o transporte.

Depois que o primeiro schema confirmar os nomes reais, o conjunto pode ser alterado com:

```powershell
-Tables @('dim_times', 'dim_competicoes', 'fato_jogos')
```

## Verificação

```powershell
.\scripts\dw-editorial\Test-DwEditorialDump.ps1 `
  -PackagePath '.\local-data\dw-esportivo\incoming\PACOTE.zip'
```

O verificador confere manifesto, tamanhos, hashes e procura comandos de usuários/permissões e tabelas sensíveis do WordPress. Ele não importa nem executa o SQL.

## Comportamento operacional

- O exportador usa `--single-transaction`, `--quick` e `--skip-lock-tables`. A consistência depende de as tabelas relevantes usarem InnoDB.
- Rotinas, eventos e triggers não são exportados porque não integram o contrato de leitura editorial.
- A estrutura do schema é completa para preservar tabelas, views, índices e relacionamentos.
- Os dados ficam limitados às cinco tabelas centrais. Outras tabelas serão incluídas após a leitura do primeiro schema.
- O ZIP deve permanecer no diretório local ignorado pelo Git. Para transferi-lo, use um canal privado aprovado e confira o `.sha256`.
