# Consolidação editorial — candidata Core 0.7.4.0.88 e PEL 0.1.27

Consolida os históricos dos branches `codex/pel-home-cutover` e `codex/pel-controlled-home-cutover`. A base ativa do Core é a 0.7.4.0.87; o plugin de implantação PEL permanece separado. Histórico de versões, pacotes de mídia externa e documentos locais de preflight são preservados.

O layout editorial #3 conserva a apresentação homologada `mini-below`, e o #4 passa a usá-la também. No PEL, a migração dos cinco tipos de páginas dinâmicas passa a ocorrer uma única vez, preservando o modo e as demais capacidades do Site Profile. A primeira execução habilita essas cinco capacidades em um perfil PEL já aplicado; configurações posteriores não são regravadas pela migração.

Os conflitos em arquivos do plugin Core foram resolvidos pela versão 0.87, mais recente. Alterações antigas exclusivas do Core no branch PEL, incluindo variações de branding e Discovery, permanecem no histórico Git, mas não são declaradas incorporadas ao runtime; documentos antigos de Discovery são registros históricos e exigem conferência antes do uso.

## Pacotes

- Core: `m360-core-v0.7.4.0.88.zip`, SHA-256 `B713FE3ED725985C2DFC2444E2061AEABC960C7E525B38408FEF6C4431638FF7`.
- PEL: `m360-pel-controlled-deployment-v0.1.27.zip`, SHA-256 `91252997E235591B32322C8AAB01FA043925C9E31AC4C1FEED95A6B643F53315`.

## Validação e reversão

Passaram verificação de sintaxe dos arquivos PHP e testes isolados de cursor, enriquecimento e migração PEL. WordPress, Elementor, integração real e aparência dos dois sites ainda necessitam homologação; esta é uma pré-release, não um aceite para produção. Não executa filas WP-Cron. Antes de instalar, faça backup e retenha os pacotes e configurações anteriores; em caso de regressão, reinstale as versões anteriores e restaure as configurações previamente registradas.
