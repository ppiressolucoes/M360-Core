# Energia Limpa — análise de pressão de recursos e contenção do Core

Data da análise: 21/10/2026. Fonte operacional: histórico do atendimento Hostinger fornecido para esta revisão.

## Evidências observadas

- CPU do domínio `energialimpa.live` permaneceu entre 90% e 100%, com limite atingido.
- Memória chegou repetidamente ao limite de 3 GB; PHP Workers ficaram baixos (2–3 de 60) e I/O/IOPS não apresentaram saturação.
- O tráfego destacou `guest.vary.php` do LiteSpeed (144–194 requisições/hora), página inicial, `admin-ajax.php` (44–47/hora) e feeds.
- A conta também hospeda outros domínios. As métricas filtradas apontam pressão no Energia Limpa, mas não fornecem atribuição por processo, consulta ou plugin.
- A carga persistiu após desativação de Clarity, Wordfence e MailPoet; portanto, nenhum desses foi comprovado como causa principal.

## Avaliação do M360 Core 0.7.4.0.57

O Core não faz chamadas para `guest.vary.php` e não registra um endpoint próprio em `admin-ajax.php`. Logo, esses números não demonstram que o Core seja a origem direta.

Há influência potencial em dois pontos:

1. **Content Discovery & SEO:** quando o writer automático está liberado, hooks de publicação e termos enfileiram geração de snapshots. Cada job procura candidatos, lê taxonomias e grava relações/snapshots. Sob muitos salvamentos, cron concorrente ou cache frio, esse trabalho pode ampliar a pressão no banco e em PHP.
2. **Ads:** uma página com vários slots podia repetir leituras de slot, campanha e criativo no mesmo request. O custo não explica isoladamente 3 GB, mas é evitável em páginas sem cache ou durante reconstruções.

O plugin M360 Core Media Storage External não está neste repositório; esta alteração não modifica sua execução. Ele deve ser auditado separadamente quanto a sincronização, polling, processamento de imagens e cron.

## Contenção aplicada em v0.7.4.0.58

- Atualização do circuito de proteção para garantir writer manual, backfill parado e limpeza de eventos assíncronos do Discovery após a atualização.
- Manutenção do renderer público como leitura de snapshots promovidos, sem geração durante visitas.
- Cache apenas em memória da requisição para a resolução de anúncios; não há transiente, cron ou escrita adicional.

## Protocolo de medição

1. Instalar somente o pacote v0.7.4.0.58 e aguardar 30–60 minutos, sem alternar outros plugins.
2. Manter LiteSpeed Crawler, Guest Mode e Guest Optimization desligados. Executar no máximo um purge após a instalação.
3. Registrar CPU, memória, `guest.vary.php`, `admin-ajax.php`, 503 e tempo de resposta antes/depois.
4. Se não houver redução material, manter o Core protegido e isolar o M360 Media Storage External em uma janela equivalente.
5. Solicitar à Hostinger os processos PHP/cron e as consultas lentas no intervalo do incidente. Esse dado é necessário para atribuição conclusiva.