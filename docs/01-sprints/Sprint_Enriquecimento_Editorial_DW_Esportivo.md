# Sprint — Enriquecimento Editorial com DW Esportivo

Status: **Análise técnica preliminar — aguardando validação do DW de produção**.
Data do levantamento: 04/09/2026. Implementação e homologação ainda não realizadas.

## Objetivo editorial

Responder: **“O que o Mengão 360 acrescenta ao fato noticiado?”**

Acrescentar dados objetivos, contexto esportivo e informação própria do M360 à notícia, preservando o produto jornalístico. A iniciativa integra a preparação editorial após a indicação de “Conteúdo de baixo valor” pelo AdSense; seu critério de sucesso é utilidade factual verificável, sem expansão artificial de texto.

O documento de abertura `M360_Sprint_Core_Enriquecimento_Editorial_DW_Esportivo_v2.docx` foi usado como referência de escopo. Este registro consolida o alinhamento aceito pelo responsável do projeto e explicita as hipóteses técnicas ainda não homologadas.

## Base aceita e rastreabilidade

| Item | Referência |
| --- | --- |
| Plugin em produção informado pelo responsável | M360 - Core Editorial, 0.7.4.0.20, autor M360 |
| Código conferido | [Commit 0190d99](https://github.com/ppiressolucoes/M360-Core/tree/0190d99cb5492f7356a5302b813570b3242249ae) |
| PR de origem | [#27](https://github.com/ppiressolucoes/M360-Core/pull/27), aberta e sem merge na consulta de 04/09/2026 |
| Branch de origem | `release/m360-core-editorial-v0.7.4.0.18`, cujo HEAD conferido contém a versão .20 |
| ZIP local comparado | `m360-core-v0.7.4.0.20.zip`, 107 arquivos |
| SHA-256 do ZIP | `8d662330b934a1c5a070b092299d215957bb357c88b6398f7f220bd42b4683be` |

Os 107 arquivos correspondem ao commit: 29 são idênticos byte a byte e 78 coincidem após normalização CRLF → LF. A comparação considera arquivos da raiz quando presentes e, nos demais caminhos, de `plugin/`; não há divergências nem arquivos-fonte faltantes nessa composição. Isso comprova correspondência de conteúdo, não um ZIP publicado por um workflow oficial.

A cópia `plugin/m360-core.php` desse commit permanece em 0.7.4.0.1; o arquivo principal .20 está na raiz. Não gerar a próxima implementação usando apenas `plugin/`. A `main` consultada e a última release publicada estão na 0.7.4.0.1. Esta sprint referencia a base aceita sem declarar sua PR incorporada ou alterar a versão global do repositório. A instalação WordPress não foi inspecionada diretamente.

## Escopo aceito

- Competições: Brasileirão Série A, Conmebol Libertadores, Premier League, La Liga, Bundesliga e Ligue 1; IDs dependem do catálogo real.
- Resolução determinística de time e competição quando houver correspondência válida.
- Classificação, próximos jogos e estatísticas básicas, com identidade, edição, escopo e atualização verificáveis.
- DW somente leitura, cache, consultas limitadas e fallback que não bloqueie publicação ou leitura.
- Compatibilidade WordPress, n8n, Semantic Relations e PT-BR/EN-US.
- Bloco complementar ao artigo, com fonte e referência temporal; sem reescrita genérica do texto ou exposição de SQL/tabelas no navegador.

## Arquitetura proposta

Em conformidade com o [ADR-0007](../00-platform/ADR-0007_M360_Core_Interface_Architecture.md), a lógica e a apresentação pertencem ao Core, independentes do tema. A base aceita fornece `M360_Module_Interface`, registro via `M360_Platform` e hook `m360_platform_register_modules`.

Proposta: módulo inicialmente desativado, resolvedor de entidades, interface de provedor factual, adaptador de leitura, serviço de validação/cache e apresentação. O transporte, a origem de classificação e a política temporal serão definidos após o diagnóstico. Nenhuma decisão estrutural nova é promovida a ADR aprovado nesta entrega.

Priorizar vínculo explícito validado e termos/aliases curados. Siglas ambíguas, menções incidentais, duas entidades plausíveis ou competição indefinida não justificam escolher um time por padrão. `catalog_ref_id` semântico não é ID esportivo comprovado.

A apresentação deve consumir cache válido, com atualização fora da requisição pública, cache negativo e controle de concorrência. Separar timestamps do registro DW, observação do fornecedor quando disponível, consulta e expiração. TTLs dependem da cadência real. Não persistir HTML factual no corpo do post nem atualizar sua data jornalística apenas pela renovação do cache.

Notícias antigas devem identificar informação atual como situação atual. A mudança de entidade/edição invalida o snapshot. No Core aceito, o injector semântico usa `the_content` em prioridade 29: a posição e a ordem do novo bloco precisam de homologação para prevenir duplicação e interferência nos links.

## Critérios de aceite da implementação futura

| Cenário | Resultado esperado |
| --- | --- |
| Identidade e escopo únicos, fatos válidos | Bloco com fonte, edição, fase/grupo aplicável e atualização |
| Ambiguidade ou competição fora do escopo | Fallback sem seleção arbitrária |
| Temporadas/fases incompatíveis ou classificação duplicada | Nenhuma combinação indevida |
| Informação parcial | Exibir somente seções válidas, sem converter ausência em zero |
| DW indisponível/timeout | Publicação e artigo continuam funcionando |
| Cache válido e concorrência | Nenhuma consulta repetitiva ao DW por visualização |
| Cache vencido, jogo iniciado ou notícia alterada | Não exibir informação vencida como atual/próxima |
| PT/EN e Semantic Relations | Identidade factual preservada, idioma correto, sem duplicação/interferência |
| Conteúdo malformado | Validação e escape; diagnóstico administrativo sem detalhes de infraestrutura públicos |
| Módulo desligado | Comportamento editorial anterior preservado |

## Entregas e dependências

- [x] Base Core conferida no GitHub e contra o pacote local.
- [x] [Mapeamento das fontes disponíveis](../02-architecture/M360_Enriquecimento_Editorial_DW_Mapeamento_v1.md), distinguindo evidências e propostas.
- [x] [Roteiro e consultas de diagnóstico somente leitura](../06-runbooks/M360_Enriquecimento_Editorial_DW_Diagnostico.md).
- [ ] Receber estrutura/views atuais, catálogo de IDs e amostras por escopo.
- [ ] Confirmar fuso de armazenamento, convenção de temporadas, cobertura e cadência do ETL.
- [ ] Identificar Node 7 e conexão/API com privilégio somente leitura.
- [ ] Confirmar ou construir mapeamento WordPress → entidades DW.
- [ ] Implementar adaptador e módulo; testar cache, resolução, falhas e validação.
- [ ] Homologar apresentação PT/EN e coexistência semântica.

Versão futura ainda não atribuída. Sem código PHP, migração de banco, execução SQL em produção ou release instalável nesta entrega documental.
