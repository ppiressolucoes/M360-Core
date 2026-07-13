# M360 Commercial Platform v1.0 — Documento de Abertura

Status: planejamento oficial
Projeto: Mengão 360 | DW Esportivo
Produto central: M360 Core
Linha evolutiva: v0.5.x — Plataforma Comercial M360
Baseline de origem: M360 Core v0.4.4.5

## 1. Finalidade

Este documento abre oficialmente a próxima linha evolutiva do Mengão 360 após o encerramento da Sprint `v0.4.4.0 — M360 AdSense Ready`.

A Plataforma Comercial M360 transforma a infraestrutura publicitária já homologada em um subsistema comercial completo, responsável por decisão de campanha, prioridade, rotação, métricas, provedores, receita e operação comercial.

A baseline v0.4.4 não será reaberta. A nova fase deve evoluir sobre os contratos, APIs, componentes e decisões arquiteturais já consolidados.

## 2. Objetivo geral

Criar uma camada comercial própria do M360 Core que permita operar publicidade direta, house ads, patrocinadores, afiliados e provedores programáticos sem distribuir lógica comercial pelo tema, Elementor, widgets ou templates.

A Plataforma Comercial deve separar claramente:

- decisão comercial;
- seleção de campanha;
- seleção e rotação de criativo;
- integração com provider;
- renderização de slot;
- mensuração;
- auditoria;
- receita.

## 3. Princípios obrigatórios

Toda evolução da linha v0.5.x deve preservar:

- arquitetura modular;
- baixo acoplamento;
- compatibilidade retroativa;
- internacionalização PT-BR / EN-US;
- independência do News Portal;
- independência da lógica estrutural do Elementor;
- uma única saída de renderização pelo M360 Universal Slot Renderer;
- APIs públicas documentadas;
- migrations e rollback rastreáveis;
- builds reproduzíveis;
- homologação incremental;
- privacidade e minimização de dados na coleta de métricas.

## 4. Baseline herdada

A Plataforma Comercial parte dos seguintes componentes homologados:

- M360 Ads Manager;
- Inventory Library;
- Inventory Seeder;
- Ad Slot Component;
- Context Renderer;
- Inline Ads Engine;
- Archive Ads Engine;
- Universal Slot Renderer;
- Biblioteca de Criativos;
- cadastro de campanhas;
- placeholders multilíngues;
- etiquetas `PUBLICIDADE` e `ADVERTISEMENT`;
- APIs PHP e shortcodes compatíveis;
- renderização PT-BR / EN-US;
- integração com WordPress, Elementor e News Portal como camadas de compatibilidade.

API oficial de saída:

```php
echo m360_render_ad_slot('header-top');
```

Nenhum novo componente comercial poderá renderizar publicidade diretamente no front-end fora desse pipeline.

## 5. Arquitetura alvo

```text
Fontes comerciais / Operação
            ↓
M360 Commercial Platform
            │
            ├── Campaign Engine
            ├── Priority Engine
            ├── Rotation Engine
            ├── Segmentation Engine
            ├── Provider Engine
            ├── Metrics Engine
            ├── Revenue Engine
            └── Audit Layer
            ↓
Decisão normalizada de entrega
            ↓
M360 Universal Slot Renderer
            ↓
M360 Ad Slot Component
            ↓
Front-end PT-BR / EN-US
```

### 5.1 Separação de responsabilidades

A Plataforma Comercial decide **o que** deve ser entregue.

O Universal Slot Renderer decide **como** a solicitação normalizada percorre o pipeline de renderização.

O Ad Slot Component produz **a saída semântica e visual** do slot.

O tema, Elementor, widgets e templates apenas consomem a API oficial.

## 6. Componentes planejados

### 6.1 Campaign Engine

Responsável por:

- ciclo de vida de campanhas;
- datas de início e término;
- status operacional;
- idiomas e dispositivos;
- vínculos com slots;
- regras de elegibilidade;
- validação de consistência.

### 6.2 Priority Engine

Responsável por:

- prioridade comercial;
- precedência entre campanha direta, sponsor, affiliate, house e programática;
- regras de desempate;
- fallback previsível;
- proteção contra conflitos de inventário.

### 6.3 Rotation Engine

Responsável por:

- rotação entre criativos elegíveis;
- pesos;
- distribuição equilibrada;
- frequência;
- preparação para testes A/B;
- prevenção de repetição excessiva.

### 6.4 Segmentation Engine

Responsável por:

- contexto de página;
- slot;
- idioma;
- dispositivo;
- competição;
- categoria editorial;
- período;
- regras futuras de audiência, observando privacidade e consentimento.

### 6.5 Provider Engine

Providers previstos:

- `internal`;
- `house`;
- `sponsor`;
- `affiliate`;
- `adsense`;
- `google-ad-manager`.

O Provider Engine não deve substituir o Universal Slot Renderer. Ele deve fornecer payload normalizado ao pipeline existente.

### 6.6 Metrics Engine

Responsável por:

- impressões técnicas;
- cliques;
- CTR;
- eventos de entrega;
- falhas e fallback;
- agregação por slot, campanha, criativo, provider, idioma e dispositivo;
- retenção e anonimização definidas antes da coleta em produção.

### 6.7 Revenue Engine

Responsável por:

- modelos CPM, CPC, CPA, valor fixo e patrocínio;
- contratos e períodos;
- receita estimada e realizada;
- conciliação futura;
- relatórios comerciais.

### 6.8 Audit Layer

Responsável por:

- histórico de alterações;
- autoria administrativa;
- mudanças de status;
- decisões de entrega;
- erros de provider;
- trilha para diagnóstico e rollback.

## 7. Roadmap inicial da linha v0.5.x

| Versão | Entrega | Objetivo |
|---|---|---|
| `0.5.0` | Commercial Foundation / Campaign Engine | estabelecer domínio, contratos, migrations e regras de elegibilidade |
| `0.5.1` | Campaign Priority | implementar precedência e desempate comercial |
| `0.5.2` | Creative Rotation | implementar pesos e rotação controlada |
| `0.5.3` | Metrics Foundation | registrar impressões, cliques e eventos de entrega |
| `0.5.4` | Provider Integrations | preparar integrações oficiais de AdSense e Google Ad Manager |
| `0.5.5` | Commercial Dashboard | centralizar operação, auditoria e indicadores |
| `0.5.6` | Revenue Foundation | estruturar modelos comerciais e relatórios básicos |
| `0.6.x` | AdServer / Analytics / Automação | ampliar decisão, métricas e automação operacional |
| `1.x` | Marketplace Comercial M360 | habilitar ecossistema comercial ampliado |

A numeração poderá ser refinada em cada documento de sprint, sem alterar os princípios deste documento.

## 8. Primeira sprint recomendada

### v0.5.0 — Commercial Foundation / Campaign Engine

Objetivo:

Criar a fundação do domínio comercial sem alterar a saída visual homologada da v0.4.4.5.

Escopo inicial:

- modelo de domínio comercial;
- contratos entre Campaign Engine e Universal Slot Renderer;
- estados e transições de campanha;
- elegibilidade por período, idioma, dispositivo e slot;
- migrations versionadas;
- APIs internas;
- hooks de decisão;
- logs básicos;
- documentação e testes;
- compatibilidade integral com campanhas e criativos existentes.

Fora do escopo inicial:

- integração oficial com código Google AdSense;
- Google Ad Manager em produção;
- billing;
- marketplace;
- segmentação comportamental;
- cache comercial agressivo;
- coleta de dados pessoais.

## 9. Critérios de aceite da fundação

- nenhuma regressão visual em relação à v0.4.4.5;
- todos os slots existentes continuam operacionais;
- APIs e shortcodes históricos continuam compatíveis;
- PT-BR e EN-US permanecem homologados;
- decisão comercial separada da renderização;
- Universal Slot Renderer permanece como único ponto de saída;
- migrations possuem estratégia de rollback;
- regras de campanha são testáveis e documentadas;
- admin atual permanece utilizável;
- build completo do plugin é reproduzível;
- evidências de homologação são registradas no repositório.

## 10. Riscos e controles

| Risco | Controle obrigatório |
|---|---|
| duplicação de lógica entre Campaign Engine e renderer | contratos claros e responsabilidade única |
| quebra de campanhas existentes | camada de compatibilidade e testes de regressão |
| métricas infladas | deduplicação, definição formal de impressão e auditoria |
| impacto de performance | medição antes de cache e consultas indexadas |
| dependência de provider externo | adapters e fallback interno |
| coleta indevida de dados | privacidade por desenho e minimização |
| conflito de prioridade | regras determinísticas e logs de decisão |

## 11. Governança

Cada entrega da linha v0.5.x deverá atualizar, quando aplicável:

- este documento de abertura;
- Documento Mestre;
- ADR correspondente;
- documento da sprint;
- documentação do módulo;
- Release History;
- release notes;
- workflows de build;
- checklist de homologação.

Alterações estruturais que modifiquem a separação entre decisão comercial e renderização exigem ADR.

## 12. Documentos normativos relacionados

```text
docs/00-platform/M360_Platform_Architecture_v2.md
docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md
docs/00-platform/M360_Arquitetura_Ads_Manager_v1.md
docs/01-modules/M360_Universal_Slot_Renderer_v1.md
docs/02-sprints/Sprint_v0.4.4.0_M360_AdSense_Ready.md
docs/03-releases/M360_Release_History_v2.md
```

## 13. Decisão de abertura

A linha `v0.5.x — Plataforma Comercial M360` está oficialmente aberta para planejamento e decomposição técnica.

A primeira entrega deverá preservar a baseline `M360 Core v0.4.4.5` e iniciar pelo domínio de campanhas e contratos de decisão, sem integrar prematuramente providers externos ou métricas comerciais em produção.
