# M360 Core v0.7.2 — Contracts & Legacy Read

## Resultado

Esta versão inicia a absorção segura do M360 Semantic Relations 0.9.0. O Core passa a reconhecer os contratos de Content Discovery & SEO e a inspecionar o estado legado em modo somente leitura.

O precursor continua sendo o único responsável por gerar, gravar e renderizar relações semânticas.

## Entregas

- módulo registrado como `content-discovery-seo` e desativado por padrão;
- contrato portátil de provider de catálogo;
- contrato portátil de repositório de relações;
- adapter de leitura das tabelas `m360_semantic_runs` e `m360_semantic_relations`;
- proteção contra schema legado incompatível;
- diagnóstico agregado de runs, relações, estados, cron e flags não secretas;
- submenu `M360 Platform > Content Discovery` para preflight;
- documentação do ownership, riscos e sequência de cutover.

## Garantias desta entrega

- não cria nem altera tabelas;
- não escreve em opções ou postmeta do M360 Semantic Relations;
- não registra cron, shortcode ou filtro `the_content`;
- não gera HTML público;
- não transporta conteúdo, dados pessoais, URLs, credenciais ou argumentos de cron;
- não desativa o precursor automaticamente.

## Homologação recomendada

1. atualizar o M360 Core pelo pacote canônico;
2. confirmar o Core na versão `0.7.2` e o M360 Semantic Relations na versão `0.9.0` ativo;
3. abrir `M360 Platform > Content Discovery` sem ativar o módulo e revisar o preflight;
4. confirmar que as duas tabelas aparecem como existentes e compatíveis;
5. registrar os alertas sobre geração síncrona, auto-heal, engine e shadow mode legado;
6. ativar `Content Discovery & SEO` na lista modular somente após o preflight;
7. confirmar saúde `healthy` ou documentar os warnings encontrados;
8. validar front-end e administração em PT-BR e EN-US, esperando zero alteração visual.

## Critérios de aceite

- WordPress e área administrativa sem erro crítico, warning ou notice;
- precursor permanece ativo e funcional;
- módulo aparece desativado após a atualização;
- painel de preflight funciona sem modificar a base legada;
- zero mudança de HTML, shortcodes, publicação e tarefas agendadas;
- desligar o módulo não altera a operação do precursor.

## Rollback

1. desativar `Content Discovery & SEO` na Plataforma;
2. confirmar o M360 Semantic Relations 0.9.0 ativo;
3. se necessário, reinstalar o pacote canônico v0.7.1.15;
4. não apagar as tabelas `m360_semantic_*` nem postmeta legado.

Nenhuma ação de limpeza ou migração de dados faz parte deste rollback.

## Próximo gate

A v0.7.2 não autoriza storage próprio nem geração do Core. A implementação da v0.7.2.1 depende do preflight homologado e de autorização explícita para criar `m360_discovery_*` e executar geração apenas em shadow mode.
