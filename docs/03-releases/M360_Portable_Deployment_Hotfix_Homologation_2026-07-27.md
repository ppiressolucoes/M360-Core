# M360 Core v0.7.4.0.1 — Homologação no Mengão 360

Data: 27/07/2026  
Ambiente: Mengão 360 | DW Esportivo  
Natureza: upgrade em produção da v0.7.4.0 para v0.7.4.0.1

## Objetivo

Validar que o hotfix preserva o Runtime Profile já configurado no ambiente precursor e não reclassifica um portal em operação.

## Evidências registradas

- versão carregada: `0.7.4.0.1`;
- política de implantação: `legacy-compatible`;
- origem da política: `preserved-existing-profile`;
- capacidades públicas previamente homologadas preservadas;
- `publisher-foundation`: `healthy`;
- `editorial-layout-home`: `healthy`;
- `content-discovery-seo`: `healthy`.

O diagnóstico confirma que o perfil salvo teve precedência sobre a classificação automática. Nenhum módulo, schema, conteúdo, snapshot, fila ou provider precisou ser migrado pelo hotfix.

## Resultado

**Aceito.** A v0.7.4.0.1 passa a ser a release operacional homologada no WordPress do Mengão 360.

O gate de compatibilidade do ambiente precursor está concluído. A próxima etapa autorizada é preparar o staging do Portal Energia Limpa para uma instalação nova em `portable-safe`.

## Limites

- esta homologação não autoriza instalação no PEL em produção;
- o PEL não deve receber conteúdo, pessoas, campanhas, credenciais, segredos ou storages do Mengão 360;
- cada capacidade pública do PEL exige preflight, ativação progressiva, aceite e rollback próprios;
- o mesmo ZIP canônico deve ser utilizado nos ambientes, sem reconstrução específica por portal.

## Pacote homologado

- arquivo: `m360-core-v0.7.4.0.1.zip`;
- SHA-256: `DD614F644E78A047999EF0B93C184AC9E7C1826B99CB915614FA83B94C2BCFE8`;
- commit de implementação: `a2693dc03704047922f80c29923069ad3796c5e7`.
