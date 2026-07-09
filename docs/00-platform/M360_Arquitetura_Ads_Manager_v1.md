# M360 Arquitetura Ads Manager v1

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Módulo: M360 Advertising
Subsistema: M360 Ads Manager
Versão de referência: M360 Core v0.4.3.5

## 1. Visão geral

O M360 Ads Manager é o subsistema oficial de inventário publicitário do ecossistema Mengão 360.

Ele foi criado para substituir gradualmente banners, scripts e HTMLs espalhados pelo tema News Portal, Elementor, widgets e customizações manuais, centralizando a gestão de publicidade em uma camada própria do M360 Core.

Objetivo principal:

```text
Transformar espaços publicitários manuais em slots governados por campanhas, criativos, idioma, dispositivo e renderer.
```

## 2. Estado atual

Marco homologado:

```text
M360 Core v0.4.3.5 — Ads Slot Intent Fallback
```

Status:

```text
Piloto funcional homologado em PT-BR e EN-US.
```

O piloto já valida:

- slots;
- campanhas;
- criativos;
- imagens;
- HTML;
- scripts/markup confiável;
- idioma PT-BR;
- idioma EN-US;
- links de redirecionamento;
- renderização via shortcode;
- renderização via API PHP;
- fallback por idioma;
- fallback por intenção do slot;
- uso em tema/Elementor/widgets.

## 3. Arquitetura conceitual

Fluxo principal:

```text
Slot
  ↓
Campanha vinculada
  ↓
Criativo elegível
  ↓
Filtro por idioma
  ↓
Filtro por dispositivo
  ↓
Filtro por formato/intenção
  ↓
Renderer
  ↓
Front-end
```

O tema e o Elementor deixam de conhecer o conteúdo publicitário específico. Eles passam apenas a chamar um slot.

Exemplo:

```text
[m360_ad_slot id="sidebar-community"]
```

ou:

```php
echo m360_ads_render_slot('sidebar-community');
```

## 4. Modelo de dados

O M360 Ads Manager utiliza tabelas próprias do WordPress.

### 4.1 Slots

Tabela lógica:

```text
m360_ad_slots
```

Responsabilidade:

- registrar espaços de inventário;
- definir chave única do slot;
- indicar contexto de página;
- registrar idioma/dispositivo quando aplicável;
- definir tamanho esperado.

Campos conceituais:

```text
id
slot_key
name
description
position
page_context
language
device
max_width
max_height
is_active
created_at
updated_at
```

### 4.2 Campanhas

Tabela lógica:

```text
m360_ad_campaigns
```

Responsabilidade:

- representar campanha comercial, institucional ou house ad;
- controlar anunciante;
- controlar tipo;
- controlar idioma;
- controlar dispositivo;
- controlar prioridade;
- controlar status.

Campos conceituais:

```text
id
title
advertiser
campaign_type
image_url
target_url
html_code
script_code
alt_text
language
device
start_at
end_at
priority
status
created_at
updated_at
```

### 4.3 Criativos

Tabela lógica:

```text
m360_ad_creatives
```

Responsabilidade:

- armazenar peças publicitárias específicas;
- separar variações por idioma;
- separar variações por formato;
- armazenar imagem, HTML ou script;
- integrar com Media Library.

Campos conceituais:

```text
id
campaign_id
title
slug
creative_type
image_url
target_url
html_code
script_code
alt_text
language
device
width
height
mime
filesize
checksum
status
created_at
updated_at
```

### 4.4 Relação Slot → Campanha

Tabela lógica:

```text
m360_ad_slot_campaigns
```

Responsabilidade:

- vincular campanhas a slots;
- controlar prioridade;
- controlar peso;
- controlar ativação.

Campos conceituais:

```text
id
slot_id
campaign_id
priority
weight
is_active
created_at
updated_at
```

## 5. Inventário piloto homologado

| Slot | Uso | Formato | PT-BR | EN-US | Status |
|---|---|---|---:|---:|---|
| `header-top` | Banner de cabeçalho | 728x140 | OK | OK | Homologado |
| `content-bottom` | Banner horizontal no final do conteúdo | HTML horizontal | OK | OK | Homologado |
| `sidebar-community` | CTA/HTML da comunidade na sidebar | 300x300 | OK | OK | Homologado |
| `sidebar-square` | Banner 1:1 na sidebar | 1:1 | OK | OK | Homologado |

## 6. Tipos de criativos suportados

Tipos funcionais no piloto:

```text
image
html
script
house
affiliate
sponsor
adsense
gam
```

Status atual:

- `image`: homologado;
- `html`: homologado;
- `script`: persistência e renderer preparados para administradores;
- `house`: suportado como tipo conceitual;
- `affiliate`: suportado como tipo conceitual;
- `sponsor`: suportado como tipo conceitual;
- `adsense`: reservado para evolução AdSense Ready;
- `gam`: reservado para evolução Google Ad Manager.

## 7. Internacionalização

Idiomas suportados:

```text
pt-br
en-us
all
```

Regra atual:

- criativo com idioma exato tem prioridade sobre `all`;
- PT-BR e EN-US podem possuir criativos próprios para o mesmo slot;
- `all` deve ser usado somente para peças neutras ou intencionalmente globais.

Exemplo recomendado:

```text
slot: sidebar-community
criativo PT-BR: language=pt-br
criativo EN-US: language=en-us
```

## 8. Dispositivos

Dispositivos suportados:

```text
desktop
mobile
all
```

Regra atual:

- dispositivo exato tem prioridade sobre `all`;
- fallback para `all` ocorre somente quando não há criativo específico.

## 9. Algoritmo de seleção do renderer

O renderer executa, em ordem:

1. Localiza o slot ativo por `slot_key`.
2. Localiza campanha ativa vinculada ao slot.
3. Aplica filtro de idioma.
4. Aplica filtro de dispositivo.
5. Procura criativo por slug conhecido do slot.
6. Procura criativo por intenção do slot:
   - `content-bottom`: prioriza criativo horizontal;
   - `sidebar-community`: prioriza criativo quadrado;
   - `sidebar-square`: prioriza criativo quadrado;
   - `header-top`: prioriza criativo 728x140.
7. Procura por largura/altura.
8. Usa fallback genérico da campanha somente por último.
9. Renderiza conforme tipo.

## 10. Estratégia de fallback

Fallbacks homologados:

- por idioma: `en-us`/`pt-br` antes de `all`;
- por dispositivo: dispositivo exato antes de `all`;
- por formato: horizontal vs. 1:1;
- por campanha: somente se as estratégias de slot falharem.

O fallback entre idiomas não deve ser usado como comportamento editorial padrão. Quando o conteúdo textual muda, deve existir criativo específico por idioma.

## 11. API pública

### 11.1 Shortcode

```text
[m360_ad_slot id="header-top"]
[m360_ad_slot id="content-bottom"]
[m360_ad_slot id="sidebar-community"]
[m360_ad_slot id="sidebar-square"]
```

Alias:

```text
[m360_ads_slot id="header-top"]
```

### 11.2 PHP API

```php
echo m360_ads_render_slot('header-top');
echo m360_ads_render_slot('content-bottom');
echo m360_ads_render_slot('sidebar-community');
echo m360_ads_render_slot('sidebar-square');
```

Função base:

```php
m360_ad_slot(string $slot_key, array $args = []): string
```

## 12. Integração com WordPress

Integrações homologadas:

- admin menu `M360 Ads`;
- dashboard;
- slots;
- campanhas;
- criativos;
- Media Library;
- widgets de texto/custom HTML;
- Elementor/tema via shortcode;
- API PHP para futura integração com componentes M360.

## 13. Segurança

O fluxo de markup confiável foi liberado para administradores do M360 Ads.

Decisão atual:

```text
Usuários com manage_options podem salvar HTML, style e script em criativos publicitários.
```

Justificativa:

- criativos publicitários frequentemente exigem HTML/CSS/JS;
- o painel é administrativo;
- a operação é restrita ao escopo de inventário controlado.

Evolução recomendada:

- criar capability específica `manage_m360_ads`;
- criar papel operacional para equipe comercial;
- registrar auditoria de alterações;
- adicionar preview seguro;
- adicionar logs de renderização.

## 14. CSS e apresentação

No piloto, parte do CSS ainda pode viver dentro do próprio criativo HTML.

A próxima sprint deverá consolidar:

```text
.m360-ad-slot
.m360-ad-label
.m360-ad-content
.m360-ad-placeholder
.m360-ad-image
.m360-ad-html
.m360-ad-click
```

Objetivo:

Padronizar todos os espaços para AdSense Ready, com etiquetas, IDs únicos, data attributes e placeholders.

## 15. Sprint seguinte — M360 AdSense Ready

Sprint recomendada:

```text
v0.4.4.0 — M360 AdSense Ready
```

Escopo previsto:

- etiqueta `PUBLICIDADE` para PT-BR;
- etiqueta `ADVERTISEMENT` para EN-US;
- IDs únicos por slot;
- comentários HTML de diagnóstico;
- data attributes (`data-slot`, `data-format`, `data-provider`, `data-language`);
- placeholders quando não houver campanha;
- CSS unificado;
- checklist operacional AdSense Ready;
- preparação para ads.txt, sellers.json e políticas institucionais;
- estrutura semanticamente limpa para crawler e avaliador humano.

## 16. Roadmap da Plataforma Comercial M360

Após AdSense Ready:

```text
v0.5.0.0 — Ads Inventory Platform
v0.5.1.0 — Campaign Management
v0.5.2.0 — Commercial Dashboard & Analytics
v0.5.3.0 — Google AdSense Connector
v0.5.4.0 — Smart Delivery Engine
v0.5.5.0 — Contextual Ads Integration
```

Evoluções previstas:

- clientes/anunciantes;
- contratos;
- agendamento;
- rotação;
- peso;
- prioridade;
- impressões;
- cliques;
- CTR;
- relatórios;
- AdSense;
- Google Ad Manager;
- campanhas contextuais por taxonomia;
- integração com Taxonomy Intelligence;
- integração com Mega Bolão 360.

## 17. Critérios de aceite do piloto

O piloto é considerado homologado porque:

- os quatro slots renderizam em produção;
- PT-BR e EN-US foram validados;
- os criativos corretos são selecionados por slot;
- os formatos não se misturam após v0.4.3.5;
- HTML, CSS e scripts são persistidos corretamente;
- links funcionam;
- shortcodes funcionam no contexto do tema/Elementor;
- a API PHP está disponível;
- o inventário pode ser operado pelo painel.

## 18. Decisão arquitetural final

O M360 Ads Manager deixa de ser apenas uma funcionalidade auxiliar do M360 Core e passa a ser documentado como subsistema da plataforma.

Ele será a base técnica para a futura Plataforma Comercial M360 e para a preparação do Mengão 360 à monetização via Google AdSense, campanhas diretas, house ads, afiliados e patrocinadores.
