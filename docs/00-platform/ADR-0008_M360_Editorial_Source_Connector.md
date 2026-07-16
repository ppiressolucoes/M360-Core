# ADR-0008 — M360 Editorial Source Connector

Status: aceito
Data: 2026-07-15
Projeto: Mengão 360 | DW Esportivo

## Contexto

Os fluxos editoriais do n8n precisam descobrir publicações recentes e coletar conteúdo bruto de fontes externas. Algumas fontes bloqueiam IPs de datacenter, aplicam WAF, expõem formatos diferentes ou exigem cabeçalhos e cache específicos. Implementar essas exceções diretamente em cada workflow aumenta o acoplamento, replica regras de segurança e torna a manutenção dependente de mudanças em múltiplos fluxos.

## Decisão

A Plataforma M360 adotará uma camada intermediária denominada **M360 Editorial Source Connector (ESC)**.

O ESC será um componente do M360 Core com endpoint REST versionado, configuração das fontes em banco de dados e duas operações iniciais:

- `discover`: interpreta o mecanismo cadastrado da fonte e retorna uma lista normalizada de publicações;
- `fetch`: valida uma URL pertencente à fonte e devolve seu conteúdo bruto.

A frase normativa do componente é:

> Conector único interpreta a configuração da fonte e fornece lista normalizada ou conteúdo bruto.

A lógica editorial, a deduplicação definitiva, a extração semântica, a IA e a publicação permanecem fora do ESC.

## Alternativas consideradas

### Acesso direto exclusivo pelo n8n

Rejeitado como solução única porque fontes podem bloquear o IP do n8n e porque regras específicas seriam duplicadas nos workflows.

### Um plugin diferente para cada fonte

Rejeitado porque aumentaria o custo de publicação, versionamento e manutenção.

### Proxy livre que recebe qualquer URL

Rejeitado por risco de SSRF, abuso e acesso a recursos internos.

### Conector único com cadastro em banco e allowlist

Aceito por equilibrar flexibilidade operacional, segurança, baixo acoplamento e evolução sem novas versões para cada fonte.

## Consequências positivas

- contrato único para o n8n;
- inclusão e ajuste de fontes orientados a dados;
- cache e segurança centralizados;
- menor dependência do IP do orquestrador;
- separação entre aquisição e processamento editorial;
- telemetria comum para todas as fontes.

## Consequências e custos

- o WordPress passa a realizar requisições externas controladas;
- o componente exige proteção rigorosa contra SSRF;
- o banco passa a armazenar configuração operacional sensível;
- o cache e os logs precisam de política de retenção;
- fontes com navegador obrigatório continuarão fora do escopo inicial.

## Restrições normativas

- o ESC não pode se tornar proxy aberto;
- nenhuma fonte pode desativar proteções fixas do motor;
- protocolos, hosts e redirecionamentos devem ser validados;
- segredos não podem ser versionados;
- a inclusão de novos tipos estruturais exige revisão arquitetural;
- novas fontes do mesmo tipo devem ser preferencialmente cadastradas sem alteração de código.

## Piloto

A fonte de homologação inicial será o NETFLA, usando o RSS:

```text
https://netfla.com.br/noticias/rss/
```

O caminho feliz deve comprovar descoberta das três notícias mais recentes e coleta do HTML bruto de uma delas.

## Relação com a arquitetura M360

O ESC inaugura formalmente a camada **Editorial Acquisition Layer**, posicionada entre as fontes externas e a camada de processamento editorial composta por n8n, DW Esportivo e IA.
