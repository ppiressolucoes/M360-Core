# M360 Core v0.7.1.11 — Editorial Widgets Content Controls

## Entrega

- cabecalhos de widgets mais evidentes, em caixa alta;
- link opcional `VIEW ALL` para arquivo da categoria ou URL informada;
- quantidade de noticias configuravel por instancia;
- tamanho do resumo configuravel entre zero e 80 palavras;
- contraste branco reforcado no titulo sobre imagem do modelo #1;
- titulo, data e autor com maior tamanho, peso e contraste;
- modelo #5 com ate 12 noticias e seis cards visiveis no desktop;
- contador do carrossel em badge de alto contraste;
- menu `M360 Platform > Profile Info` organizado sem rotulo duplicado;
- `Top Header Section` incluido no catalogo como componente nativo e preset Newsroom.

## View All

Quando uma unica editoria esta selecionada, o Core resolve automaticamente o arquivo da categoria. Para multiplas editorias, informe a URL completa no configurador.

## Quantidade e resumo

Instancias existentes recebem os valores padrao do seu preset sem exigir recadastro. No modelo #5, a consulta e limitada a 12 noticias. Nos modelos 1 a 4, os itens adicionais sao distribuidos automaticamente nas grades ou colunas.

## Newsroom

A lista CRUD mostra `[m360_editorial_newsroom]` como componente nativo. A acao `Criar instancia` abre o formulario com o preset EN-US homologado, mas nao altera nem substitui o shortcode atual da Home.

## Rollback

Reinstalar o ZIP canonico v0.7.1.10. As configuracoes anteriores permanecem compativeis; os novos campos sao ignorados pela versao anterior.
