# Ecossistema de Plugins M360 v1

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Função: registrar os plugins precursores e seus limites de responsabilidade em relação ao M360 Core.

## 1. Visão

Antes da consolidação do M360 Core como camada oficial de interface, três plugins especializados estabeleceram capacidades essenciais de produto, internacionalização editorial e SEO. Eles permanecem módulos independentes do ecossistema e não devem ter suas regras absorvidas indiscriminadamente pelo Core.

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

Papel precursor: o isolamento das Homes por idioma demonstrou a necessidade de uma camada própria de interface. O M360 Core consolida esse princípio nos componentes globais reutilizáveis, preservando o Home Editorial como responsável pela composição editorial da página inicial.

## 4. M360 Semantic Relations

Camada de relações semânticas internas e apoio operacional a SEO.

Responsabilidades:

- melhorar descoberta, rastreamento e indexação de páginas;
- apoiar operações do Google Search Console;
- manter relações internas entre conteúdos, tópicos e páginas relacionadas;
- renderizar no front-end snapshots semânticos persistidos;
- não executar IA durante a renderização pública.

Limite: o M360 Core fornece a camada visual e os pontos de integração; o Semantic Relations permanece responsável pela geração, persistência, diagnóstico e entrega dos dados semânticos.

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

Todos os módulos
            ↓
        M360 Core
            ↓
Interface global, componentes e experiência multilíngue
```

## 6. Regra de evolução

- cada plugin mantém seu domínio de negócio e ciclo de versão;
- integrações devem ocorrer por APIs, shortcodes, hooks ou contratos documentados;
- o M360 Core não deve duplicar regras de Bolão, composição editorial ou semântica;
- alterações no Core devem verificar regressões nos três plugins precursores;
- novos recursos da Plataforma Comercial devem preservar esses limites.
