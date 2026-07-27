# M360 Core v0.7.1 - Editorial Layout & Home

Status: instalado com sucesso; homologacao funcional em andamento.

Esta versao inicia a absorcao segura do M360 Home Editorial `0.1.2`. O modulo e opcional, fica desligado por padrao e inicia em shadow mode quando habilitado. O precursor permanece a referencia publica ate cutover autorizado.

## Seguranca de migracao

- nenhum transporte de conteudo, dados pessoais ou segredos;
- nenhuma tabela nova;
- nenhum registro automatico dos shortcodes legados com o precursor ativo;
- rollback por feature flag, sem remocao de dados;
- producao nao deve ser alterada sem pacote, roteiro, backup e autorizacao.

## Evidencia operacional

- atualizacao do M360 Core `0.7.0 -> 0.7.1` concluida em 2026-07-21;
- substituicao correta do plugin existente usando raiz `m360-core/`;
- modulo Editorial Layout & Home ainda sem cutover;
- precursores preservados durante a homologacao.
