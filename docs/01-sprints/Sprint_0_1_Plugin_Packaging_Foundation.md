# Sprint 0.1 — Plugin Packaging Foundation

Status: concluída em base GitHub
Versão operacional: 0.3.4-rc1

## Objetivo

Transformar o artifact gerado pelo GitHub Actions em um pacote WordPress instalável, criando a raiz real do plugin dentro de `plugin/`.

## Entregas

- `plugin/m360-core.php`
- `plugin/includes/class-m360-core.php`
- `plugin/assets/css/m360-core.css`
- `plugin/uninstall.php`

## Escopo seguro

Esta entrega não altera visualmente o portal.

Shortcodes adicionados apenas para diagnóstico administrativo:

```text
[m360_core_status]
[m360_view view="latest_news"]
```

Ambos retornam conteúdo somente para usuários com permissão `manage_options`.

## Decisões

1. A raiz instalável passa a ser `plugin/`.
2. O plugin passa a ter bootstrap WordPress real.
3. A versão operacional da fase passa a ser `0.3.4-rc1`.
4. O uninstall não apaga dados por segurança.
5. A próxima sprint funcional deve migrar View Engine e módulos reais.

## Critérios de aceite

- [x] ZIP contém `m360-core.php`.
- [x] Plugin pode ser reconhecido pelo WordPress.
- [x] Ativação registra a versão em option.
- [x] Não há alteração visual pública.
- [x] Base pronta para View Engine.

## Próximo passo

Gerar novo artifact no GitHub Actions e validar instalação como plugin WordPress.
