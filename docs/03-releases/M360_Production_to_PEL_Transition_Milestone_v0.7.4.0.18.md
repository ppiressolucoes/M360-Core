# Marco de transição Mengão 360 → Portal Energia Limpa — v0.7.4.0.18

## Origem validada

- Backup de produção do Mengão 360: M360 Core, versão 0.7.4.0.1, por Mengão 360 | DW Esportivo.
- SHA-256 do backup recebido: 751CE83F9D1D652D1A7F85A9C6D00A7C5A48AFE2D6DD1B0FBFB5871C64FA90FF.
- Pacote posterior do Portal Energia Limpa: M360 - Core Editorial, versão 0.7.4.0.17, por M360.
- A comparação encontrou 85 arquivos idênticos, 19 arquivos evoluídos e 3 novos arquivos no PEL; nenhum arquivo exclusivo da produção ficou fora da base PEL. Isso confirma a linha evolutiva direta.

## Consolidação

A v0.7.4.0.18 é a base editorial comum: incorpora as evoluções estruturais do PEL e o hotfix de Consent Mode/GA4, mantendo dados, configurações e perfil de runtime específicos de cada WordPress fora do pacote.

A identidade distribuída passa a ser:

- Plugin: M360 - Core Editorial
- Descrição: Sistema editorial e de navegação para WordPress com independência de tema M360 Core.
- Versão: 0.7.4.0.18
- Autor: M360

## Hotfix de Consent Mode

- aplica consent default antes da inicialização das tags Google;
- restaura uma decisão persistida válida antes de produzir estado inconsistente;
- encaminha a escolha do CMP para consent update imediatamente, sem reload;
- mantém Analytics, Publicidade, Preferências e Mídia externa independentes;
- evita eventos perdidos na janela entre a renderização inicial e o runtime do CMP;
- reduz atualizações duplicadas e page views duplicados;
- explicita ao visitante o que cada categoria permite ou bloqueia;
- habilita diagnóstico somente sob WP_DEBUG, administrador e parâmetro m360_consent_debug=1.

## Limites e rollout

Esta entrega não força Analytics, não substitui uma CMP certificada e não envia dados pessoais aos logs. Validar em staging: primeira visita, aceite, recusa, ajuste posterior, reload, navegação e Tag Assistant. Em seguida, acompanhar GA4 Tempo Real e a relação GSC Organic Clicks → GA4 Organic Search Sessions por página e período.

## Rollback

Reinstalar o pacote imediatamente anterior do ambiente. Opções, cookies e tabelas não são removidos por esta entrega.
