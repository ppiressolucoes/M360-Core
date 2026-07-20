# Ecossistema de Plugins M360 v1

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Função: registrar os plugins precursores e seus limites de responsabilidade em relação ao M360 Core.

## 1. Visão

Antes da consolidação do M360 Core como camada oficial de interface, três plugins especializados estabeleceram capacidades essenciais de produto, internacionalização editorial e SEO.

A partir do ADR-0008, o Home Editorial e o Semantic Relations passam a ser precursores de módulos portáveis da M360 Publisher Platform. Eles continuarão independentes durante a transição e somente serão desativados depois de auditoria, migração, homologação e validação de rollback. O Bolão permanece um produto independente.

## 2. Mengão 360 — Bolão

Módulo de bolão esportivo do Portal Mengão 360.

Responsabilidades:

- gestão de bolões, palpites, rankings e regras de participação;
- carga e atualização de dados esportivos por API externa;
- bloqueios e apuração conforme o estado das partidas;
- integração com o DW Esportivo e tabelas auxiliares.

Limite: o M360 Core pode fornecer componentes de interface e integração, mas não é a fonte das regras de negócio, dos palpites ou da apuração do Bolão.

## 3. M360 Home Editorial

Camada independente de composição das páginas principais do portal.

Responsabilidades:

- layouts de Home independentes do tema News Portal;
- blocos editoriais configuráveis;
- Homes internacionais com modelos próprios por idioma;
- composição localizada de cabeçalho, conteúdo e rodapé;
- suporte à experiência editorial PT-BR / EN-US.

Papel precursor: o isolamento das Homes por idioma demonstrou a necessidade de uma camada própria de interface. Sua capacidade será generalizada e absorvida gradualmente como `M360 Editorial Layout & Home`, sem referências obrigatórias ao tema, ao Mengão 360 ou ao nicho esportivo.

## 4. M360 Semantic Relations

Camada de relações semânticas internas e apoio operacional a SEO.

Responsabilidades:

- melhorar descoberta, rastreamento e indexação de páginas;
- apoiar operações do Google Search Console;
- manter relações internas entre conteúdos, tópicos e páginas relacionadas;
- renderizar no front-end snapshots semânticos persistidos;
- não executar IA durante a renderização pública.

Evolução planejada: suas capacidades serão generalizadas e absorvidas como `M360 Content Discovery & SEO`. O plugin precursor permanece responsável pela operação atual até que relações, snapshots, diagnósticos e renderização estejam migrados e homologados no módulo interno.

## 5. Relação entre os módulos

```text
API externa / DW Esportivo
            ↓
    Mengão 360 — Bolão
            ↓
Produto de palpites e comunidade

WordPress / Polylang / Conteúdo editorial
            ↓
    M360 Home Editorial
            ↓
Homes configuráveis PT-BR / EN-US

Conteúdo / relações internas / snapshots
            ↓
    M360 Semantic Relations
            ↓
SEO, descoberta e Search Console

Home Editorial + Semantic Relations
            ↓ auditoria e migração reversível
     M360 Publisher Platform
            ↓
Interface, SEO, internacionalização, Newsletter, Ads e produtividade

Bolão / DW Esportivo
            ↓ contratos e APIs
     permanecem externos ao núcleo portável
```

## 6. Regra de evolução

- o Bolão mantém domínio de negócio e ciclo de versão próprios;
- integrações devem ocorrer por APIs, shortcodes, hooks ou contratos documentados;
- Home Editorial e Semantic Relations não serão copiados indiscriminadamente: serão generalizados como módulos internos;
- os plugins precursores permanecem ativos até a homologação de seus substitutos;
- alterações devem verificar regressões, duplicidade de cron, shortcodes, schema, assets e HTML;
- nenhuma regra do DW Esportivo, Bolão ou futebol entra no núcleo portável;
- o Portal Energia Limpa — PEL será a segunda implementação e prova de portabilidade;
- a referência normativa da transição é o `ADR-0008`.
