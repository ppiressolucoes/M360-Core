# M360 Core v0.7.1.9 — Editorial Widgets

## Resultado

A M360 Platform passa a configurar widgets editoriais reutilizaveis por instancia. O shortcode publicado referencia um ID estavel; layout, titulo, idioma e editorias permanecem administraveis no Core.

## Modelos

1. destaque 100% + 3 cards;
2. destaque 50% + 6 noticias em lista;
3. 2 destaques + 2 noticias complementares por coluna;
4. 3 destaques em retrato + 2 noticias complementares por coluna;
5. Latest News com 9 cards em retrato, agrupados em carrossel com controles e autoplay opcional.

Todos os modelos exibem imagem, titulo, data e autor. Destaques dos modelos 2, 3 e 4 tambem exibem resumo.

## Configuracao

1. Atualize o M360 Core.
2. Acesse `M360 Platform > Widgets editoriais`.
3. Informe ID, titulo, modelo, idioma e uma ou mais editorias.
4. Salve e copie o shortcode apresentado, por exemplo:

```text
[m360_editorial_widget id="brasileirao-home"]
```

Tambem e possivel testar sem persistir uma instancia:

```text
[m360_editorial_widget layout="5" title="Latest News" lang="en" category="international,transfers"]
```

## Seguranca de migracao

- nenhuma pagina e reescrita automaticamente;
- M360 Home Editorial 0.1.2 permanece ativo;
- Newsroom v0.7.1.7 permanece inalterado;
- ticker e shortcodes legados permanecem sob ownership do precursor;
- a configuracao armazena somente IDs tecnicos e slugs editoriais, sem conteudo, dados pessoais ou segredos.

## Homologacao recomendada

Criar e validar uma instancia de cada modelo em pagina nao indexada. Conferir desktop, tablet e mobile, PT-BR e EN-US, navegacao manual do modelo 5, autoplay, movimento reduzido e ausencia de regressao no cabecalho e rodape.

## Rollback

Remover os novos shortcodes da pagina e restaurar os blocos anteriores. Se necessario, reinstalar o ZIP canonico v0.7.1.8. As configuracoes salvas permanecem inertes sem o shortcode.
