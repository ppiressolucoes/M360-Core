# M360 Core v0.6.5.1 — Placement Order & Settings Preservation Hotfix

## Correções

- impede que o salvamento das configurações do formulário remova remetente, responsável editorial, DKIM, DMARC e teste de envio;
- executa a inserção automática na prioridade `999` de `the_content`, permitindo que notícias e tópicos relacionados adicionados por filtros anteriores apareçam primeiro.

## Impacto operacional

As configurações já removidas no site não podem ser reconstruídas pelo pacote. Após a atualização, informe novamente os dados de Delivery Readiness uma única vez; os salvamentos seguintes serão preservados.

## Homologação

1. Atualizar para v0.6.5.1.
2. Reconfigurar Delivery Readiness e confirmar `7/7`.
3. Salvar novamente a configuração do formulário.
4. Confirmar que Delivery Readiness permanece `7/7`.
5. Abrir um artigo e verificar o formulário após os blocos relacionados.
