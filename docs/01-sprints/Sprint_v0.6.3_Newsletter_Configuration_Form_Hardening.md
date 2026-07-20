# Sprint v0.6.3 — Newsletter Configuration & Form Hardening

Status: homologada e incorporada ao baseline estável em 2026-07-16
Baseline: `M360 Core v0.6.2`
Dependência: MailPoet ativo e ao menos uma lista disponível

## Objetivo

Transformar a integração hoje configurada no código em uma operação administrável e fortalecer o formulário público contra abuso e falhas de interação, preservando os fluxos homologados de Double Opt-In, sincronização e cancelamento.

## Escopo funcional

### Configuração

- adicionar seção de configurações em `M360 Ads > Newsletter`;
- listar as listas retornadas pela API do MailPoet;
- salvar o ID selecionado em uma opção WordPress sem autoload;
- migrar automaticamente a instalação atual para a lista ID `3` quando ainda não houver opção;
- impedir salvamento de lista inexistente;
- permitir configurar versão e texto curto do consentimento;
- exibir a configuração efetiva no diagnóstico.

### Formulário

- manter o shortcode `[m360_newsletter_form]` e sua compatibilidade;
- adicionar honeypot invisível e tempo mínimo de preenchimento;
- impedir múltiplos envios enquanto a requisição estiver em andamento;
- indicar carregamento com `aria-busy`;
- mover foco para a mensagem após a resposta;
- diferenciar mensagens de validação, limite, MailPoet indisponível e sucesso;
- não revelar se um endereço específico já pertence à lista.

### Segurança e operação

- manter rate limit por e-mail e IP com valores filtráveis;
- registrar rejeições antispam na auditoria sem armazenar o conteúdo do honeypot;
- falhar de forma segura quando MailPoet ou a lista configurada estiver indisponível;
- preservar o cancelamento exclusivamente pelos links assinados do MailPoet.

## Persistência prevista

Opção `m360_newsletter_settings`:

```text
list_id
consent_version
consent_text_pt
minimum_form_seconds
ip_limit
ip_window_minutes
```

Nenhuma credencial do MailPoet será armazenada pelo M360 Core.

## Critérios de aceite

- [x] administrador consegue selecionar e salvar uma lista MailPoet válida;
- [x] instalação v0.6.2 migra para a lista ID 3 sem interromper inscrições;
- [x] troca de lista não exige alteração de código ou novo pacote;
- [x] formulário legítimo continua produzindo Double Opt-In;
- [x] honeypot e envio rápido demais são rejeitados e auditados;
- [x] botão não permite duplo envio;
- [x] retorno do formulário é acessível por teclado e leitor de tela;
- [x] indisponibilidade do MailPoet/lista não cria falso sucesso;
- [x] texto e versão de consentimento usados no formulário são os mesmos persistidos;
- [x] painel apresenta lista configurada, formulário e proteção como saudáveis;
- [x] sincronização e cancelamento da v0.6.2 permanecem funcionais.

## Evidências de homologação

- lista MailPoet `#3 — M360 Newsletter` selecionada e localizada no diagnóstico;
- consentimento `v0.6.1` configurado;
- proteção validada com tempo mínimo de 2 segundos, limite por IP e janela configurável;
- Double Opt-In, confirmação, sincronização e auditoria preservados;
- configurações administrativas preservadas nas atualizações posteriores.

## Casos mínimos de homologação

1. atualizar da v0.6.2 e confirmar migração automática para lista 3;
2. salvar novamente a lista pelo painel;
3. submeter um endereço novo e concluir o Double Opt-In;
4. tentar clique duplo e confirmar uma única solicitação;
5. preencher o honeypot via inspeção e confirmar rejeição genérica;
6. enviar antes do tempo mínimo e confirmar rejeição;
7. desativar temporariamente o MailPoet e confirmar erro controlado;
8. restaurar o MailPoet e validar diagnóstico saudável;
9. confirmar consentimento persistido com texto e versão configurados.

## Fora do escopo

- CAPTCHA de fornecedor externo;
- campanhas, segmentação e automações;
- painel público de preferências;
- analytics de abertura/clique;
- múltiplos provedores simultâneos.
