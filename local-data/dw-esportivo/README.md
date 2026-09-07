# Dados locais do DW Esportivo

Esta pasta recebe artefatos de banco usados exclusivamente no desenvolvimento local do Enriquecimento Editorial. Dumps, pacotes e arquivos extraídos são ignorados pelo Git.

O destino padrão do exportador é:

```text
local-data/dw-esportivo/incoming/<data-hora>/
```

Não adicionar credenciais, arquivos `.sql`, `.zip`, logs do cliente ou dados de produção ao repositório. Depois de receber um pacote, execute o verificador descrito em [`scripts/dw-editorial/README.md`](../../scripts/dw-editorial/README.md).
