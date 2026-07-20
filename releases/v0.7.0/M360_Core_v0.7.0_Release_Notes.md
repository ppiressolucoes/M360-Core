# M360 Core v0.7.0 — Publisher Platform Foundation

Status: homologada em produção em 20/07/2026.

## Objetivo

Introduzir a fundação modular da M360 Publisher Platform sem absorver, alterar ou desativar os plugins Home Editorial e Semantic Relations.

## Entregas

- contrato `M360_Module_Interface`;
- registro de módulos com dependências, ciclo de vida, permissões, configurações e assets declarados;
- schema versionado por módulo;
- boot ordenado e diagnóstico de falhas;
- feature flags para módulos não obrigatórios;
- Site Profile portável;
- importação e exportação JSON com schema e campos obrigatórios validados;
- painel `M360 Platform`;
- diagnóstico dos plugins precursores e isolamento seguro de falhas de dependência;
- módulo obrigatório `Publisher Platform Foundation`.

## Site Profile

Campos exportáveis:

- chave do portal;
- nome do portal;
- vertical;
- idioma padrão;
- idiomas suportados;
- versão do schema.

Não são exportados conteúdo, dados pessoais, listas, campanhas, credenciais ou segredos.

## Compatibilidade

- preserva todos os componentes da baseline `v0.6.5.4`;
- Home Editorial e Semantic Relations permanecem plugins independentes;
- não incorpora DW Esportivo, Bolão, ETLs ou regras esportivas;
- novos módulos podem registrar-se no hook `m360_platform_register_modules`.

## Roteiro de homologação executado

1. atualizar da `v0.6.5.4` para `v0.7.0`;
2. confirmar que o portal e os componentes existentes permanecem operacionais;
3. abrir `M360 Platform` e confirmar módulo Foundation como saudável;
4. confirmar detecção dos dois plugins precursores ativos;
5. salvar Site Profile com `pt-BR` e `en-US`;
6. exportar o JSON;
7. importar o mesmo JSON e confirmar validação;
8. validar Newsletter, Ads, Privacy, busca, navegação e páginas dinâmicas;
9. confirmar ausência de duplicidade de shortcodes, conteúdo ou cron.

## Rollback

Reinstalar o pacote homologado `v0.6.5.4`. As novas opções `m360_site_profile`, `m360_platform_module_states`, `m360_platform_module_schemas` e timestamps podem permanecer no banco, pois a versão anterior não as lê. Nenhuma tabela nova é criada e nenhum dado dos plugins precursores é modificado.

## Resultado da homologação

- atualização `v0.6.5.4 → v0.7.0` concluída no WordPress de produção;
- Foundation `healthy` em WordPress `7.0.2` e PHP `8.3.30`;
- Site Profile salvo, exportado e reimportado com sucesso;
- Home Editorial e Semantic Relations ativos e preservados;
- Newsletter, MailPoet, cron, Ads e Consent sem regressões;
- páginas e componentes PT-BR/EN-US funcionais;
- ausência de erro crítico ou erro de console atribuído ao M360.
