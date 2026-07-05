# DW Esportivo — Índice Histórico e Ambiente de Manutenção

## Identidade

- Projeto: Mengão 360 / DW Esportivo
- Ambiente iniciado em: 27/06/2026
- Thread principal: DW Esportivo — Histórico, Evoluções e Manutenção
- Objetivo: centralizar contexto, artefatos, decisões, corretivas e evoluções.

## Documentos principais

- docs/2026-06-27-transicao-mata-mata.md
- HISTORICO.md
- DECISOES.md
- ARTEFATOS.md

## Convenção de registro

Cada intervenção deve conter data e ambiente, sintoma, diagnóstico, decisão, artefatos, validação, rollback e pendências.

## Estado atual

- DW Esportivo operacional.
- Publicadores HTML [1] e [2] operacionais.
- Sprint de Internacionalização PT-BR/EN-US concluída, publicada e validada.
- Home EN operacional em `/en/`, com Elementor, Polylang e automação editorial n8n.
- Plugin M360 Home Editorial consolidado na versão 0.1.2.
- WC2026 em transição para LAST_32.
- UPSERT protegido contra times nulos/9999.
- Bolão bloqueia palpites em confrontos incompletos.
- Horário e estádio exibidos no mata-mata.
- Próxima frente de produto: Mega Bolão 360 MVP Comercial.


# Índice de Artefatos

## Entregas de 28/06/2026

- dw-esportivo-historico/artefatos/mengao360-internacionalizacao-pt-en-sprint-2026-06-28.zip
- dw-esportivo-historico/docs/2026-06-28-sprint-internacionalizacao-pt-en.md

## Entregas de 27/06/2026

- outputs/UPSERT_fato_jogos_protegido_n8n.sql
- outputs/HTML_1_Gerar_HTML_Competicao_horario_estadio.js
- outputs/mengao360-bolao-confronto-bloqueado.zip
- outputs/mengao360-bolao-confronto-bloqueado/includes/class-bolao-db.php
- outputs/mengao360-bolao-confronto-bloqueado/includes/class-bolao-ajax.php
- outputs/mengao360-bolao-confronto-bloqueado/templates/bolao-home.php
- outputs/DW_Esportivo_Runbook_Transicao_Mata_Mata_2026-06-27.md
- outputs/DW_Esportivo_Runbook_Transicao_Mata_Mata_2026-06-27.docx

## Fontes

- Documentos do Google Drive.
- Chat compartilhado “Chat1 — Bolão 360 | WC2026”.
- Plugin WordPress fornecido em ZIP.
- JSONs da API e nodes n8n.
- Evidências de produção desktop/mobile.

## Repositórios

- ppiressolucoes/workflow_n8n_bkp — acesso ainda indisponível.
- ppiressolucoes/m360-bolao — inicialmente vazio.
