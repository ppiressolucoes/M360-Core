# Sprint v0.7.1 - Editorial Layout & Home

Status: homologacao funcional em shadow mode aprovada na versao 0.7.1.1.
Baseline de producao: `M360 Core v0.7.0`.

## Objetivo

Absorver progressivamente as capacidades do M360 Home Editorial `0.1.2`, preservando o precursor, a saida atual e o rollback. A desacoplagem ampla sera planejada somente apos a estabilizacao do M360.

## Entrega

- modulo opcional `editorial-layout-home`, dependente da Foundation;
- shortcodes `m360_editorial_ticker`, `m360_editorial_hero` e `m360_editorial_section`;
- compatibilidade condicional com os tres shortcodes legados;
- post type, taxonomias, heading e cache configuraveis;
- shadow mode sem HTML publico;
- evento `m360_editorial_shadow_sample` para comparacao;
- filtros `m360_editorial_query_args` e `m360_editorial_query_language`.

## Contrato de convivencia

O modulo nasce desativado. Ao ser habilitado, inicia em `shadow`. Os nomes legados continuam pertencendo ao precursor. O Core somente os assume no modo `public`, com ownership `core` e quando o precursor nao os registrou.

## Aceite

- nenhum HTML no modo `shadow`;
- nenhum conflito com o precursor ativo;
- equivalencia de IDs, ordem e idioma nas amostras;
- nenhum `<h1>` adicional;
- cache invalidado para o post type e taxonomias configurados;
- validacao PT-BR/EN-US, desktop e mobile;
- Core `v0.7.0` e Home Editorial `0.1.2` disponiveis para rollback.

## Rollback

Alterar o modo para `off` ou desativar o modulo. Apos cutover, devolver o ownership ao precursor e reativa-lo. Nao remover opcoes nem caches durante a estabilizacao.

## Aceleracao 0.7.1.2

A absorcao publica passa a usar shortcodes novos, sem tomar os nomes do precursor. O modo `hybrid` registra os componentes do Core e preserva os contratos legados. O componente `m360_editorial_newsroom` entrega uma noticia principal e quatro cards laterais, incluindo a categoria Internacional como fonte configuravel.

## Aceite visual do Newsroom

- versao 0.7.1.7 aprovada em 2026-07-21;
- destaque com carrossel manual e automatico;
- controles visiveis e funcionais;
- quatro cards com editoria e titulo sobre imagem;
- degrade e contraste aprovados;
- largura maxima de 1200px;
- Home EN-US normalizada, sem regressao de cabecalho ou rodape.

## Rota v0.7.1.9 — Editorial Widgets

A migracao dos blocos lineares foi redirecionada para instancias configuraveis na M360 Platform. Cada instancia possui ID estavel, titulo, idioma, uma ou varias editorias e um preset visual. A Home referencia somente `[m360_editorial_widget id="..."]`, permitindo trocar layout ou editorias sem alterar o shortcode publicado.

Presets iniciais:

1. destaque 100% e tres cards;
2. destaque 50% e seis noticias em lista;
3. dois destaques, cada um com duas noticias complementares;
4. tres destaques em retrato, cada um com duas noticias complementares;
5. carrossel de nove noticias em retrato, com controles manuais e autoplay opcional.

O recurso nao cria conteudo, nao altera cabecalho ou rodape, nao desativa o precursor e nao substitui shortcodes existentes automaticamente.

## Ajustes de homologacao v0.7.1.10

- configurador movido para o submenu `M360 Platform > Widgets editoriais`;
- formulario de cadastro retraido e formulario de edicao aberto somente sob demanda;
- instancias apresentadas em lista CRUD com editar e excluir;
- seletor de editorias convertido em dropdown multiplo;
- modelo #5 convertido de paginas de tres para trilho continuo com nove cards, contador e movimento de um card por vez;
- viewport do modelo #5 exibe tres cards no desktop, dois no tablet e um no mobile;
- assets e reinicializacao adicionados ao preview do widget Shortcode no Elementor.

## Refinamento v0.7.1.11

- titulos editoriais em caixa alta, maior peso e separador de secao;
- opcao `VIEW ALL` com resolucao automatica do arquivo para uma editoria ou URL manual;
- quantidade total e tamanho do resumo configuraveis por instancia;
- contraste branco forcado no titulo sobre imagem do modelo #1;
- titulo, data e autor reforcados nos cards principais e complementares;
- modelo #5 com ate 12 itens e seis visiveis no desktop, tres no tablet, dois em telas intermediarias e um no mobile;
- contador do carrossel convertido em badge de alto contraste;
- submenu principal renomeado para `Profile Info`;
- Newsroom listado como componente nativo e disponivel como preset de instancia, sem alterar o shortcode homologado.

## Ajuste fino v0.7.1.12 e gate do precursor

- `VIEW ALL`, data e autor ampliados para leitura proporcional nos layouts desktop e mobile;
- item nativo do Newsroom removido da lista apos o cadastro da instancia personalizada;
- preset Newsroom permanece disponivel no formulario;
- desativacao do M360 Home Editorial nao autorizada nesta entrega.

Bloqueios do cutover:

1. o ticker `[m360_news_ticker]` ainda pertence ao precursor e nao recebeu aceite equivalente no Core para EN-US;
2. falta inventario final de referencias `[m360_news_*]` na Home EN-US e nos dados correspondentes do Elementor;
3. o modulo permanece em convivencia `hybrid`, com ownership legado `precursor`;
4. falta ensaio documentado de desativacao e rollback com o ZIP do Home Editorial 0.1.2.

O M360 Semantic Relations 0.9.0 permanece fora deste cutover e nao deve ser desativado antes da frente v0.7.2.

## Ticker e candidato de cutover v0.7.1.13

- ticker editorial proprio do Core, sem dependencia do tema News Portal;
- cutover restrito a Home EN-US, unico ambiente que utiliza o M360 Home Editorial 0.1.2;
- Home PT-BR e tema News Portal explicitamente fora do escopo;
- rotulos e controles configuraveis, com homologacao operacional em EN-US;
- categoria, manchete, navegacao manual, autoplay, pausa por hover/foco e movimento reduzido;
- layout responsivo e namespaced, limitado a 1200px;
- ticker nao exclui suas noticias das consultas dos widgets subsequentes;
- preview reinicializado no widget Shortcode do Elementor;
- fallbacks legados registrados somente quando o nome do shortcode estiver livre.

Durante a convivencia, `[m360_news_ticker]` continua resolvido pelo precursor na Home EN-US. A homologacao isolada usa `[m360_editorial_ticker lang="en" label="Latest News"]`. Quando o Home Editorial for desativado, o Core assume o nome legado sem sobrescrever um handler existente. Nenhuma alteracao deve ser aplicada a Home PT-BR.

## Hotfix de homologacao v0.7.1.14

- ticker reinicializado quando o Elementor injeta ou atualiza o widget Shortcode;
- MutationObserver restrito a novos elementos, com guardas contra inicializacao duplicada;
- ticker inicializado antes dos demais carrosseis para isolar falhas;
- assets carregados tambem no hook frontend do Elementor;
- setas substituidas por SVG independente da fonte do portal;
- controles escuros e contrastantes por padrao;
- tipografia de sistema com tamanhos absolutos para impedir reducao pelo tema;
- autoplay preservado no frontend e no preview, com pausa apenas por hover, foco, aba oculta ou movimento reduzido.

## Hotfix de homologacao v0.7.1.15

- causa do autoplay inativo confirmada no DOM publicado: `data-autoplay="true"`, inicializador ativo e `prefers-reduced-motion: reduce` no navegador;
- `autoplay="true"` passa a ser efetivo por padrao, inclusive quando o sistema operacional solicita movimento reduzido;
- controle visivel de pausa/retomada adicionado ao ticker para o visitante interromper o movimento;
- politica acessivel opcional preservada com `reduced_motion="respect"`, que inicia o ticker pausado quando a preferencia de movimento reduzido estiver ativa;
- escopo permanece restrito ao ticker da Home EN-US; Home PT-BR e tema News Portal nao foram alterados.

Uso de cutover:

```text
[m360_editorial_ticker lang="en" label="Latest News" limit="8" interval="4500" autoplay="true"]
```

Uso com preferencia de movimento reduzido respeitada no carregamento:

```text
[m360_editorial_ticker lang="en" label="Latest News" limit="8" interval="4500" autoplay="true" reduced_motion="respect"]
```

## Evidencia de instalacao

- atualizacao `v0.7.0 -> v0.7.1` concluida no WordPress em 2026-07-21;
- pacote com raiz canonica `m360-core/` substituiu a instalacao existente;
- instalacao paralela nao ocorreu com o pacote canonico;
- aceite funcional em shadow mode aprovado; cutover publico permanece fora desta etapa.
- Home Editorial 0.1.2 e Semantic Relations 0.9.0 ativos e preservados;
- posts PT-BR e EN-US, Newsletter, Ads e Privacy & Consent sem regressao aparente;
- modulo Editorial Layout & Home ativo, healthy e sem conflito aparente;
- nenhum erro critico de frontend ou administrativo observado;
- mojibake isolado no texto de diagnostico corrigido no hotfix 0.7.1.1.
- hotfix 0.7.1.1 instalado com sucesso em 2026-07-21;
- saude final: `healthy - Shadow mode ativo, sem HTML publico.`
