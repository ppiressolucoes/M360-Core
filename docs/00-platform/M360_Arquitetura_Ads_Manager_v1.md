# M360 Arquitetura Ads Manager v1

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Módulo: M360 Advertising
Subsistema: M360 Ads Manager
Versão de referência: M360 Core v0.4.4.x

## 1. Visão geral

O M360 Ads Manager é o subsistema oficial de inventário publicitário do ecossistema Mengão 360.

Ele foi criado para substituir gradualmente banners, scripts e HTMLs espalhados pelo tema News Portal, Elementor, widgets e customizações manuais, centralizando a gestão de publicidade em uma camada própria do M360 Core.

Referência arquitetural obrigatória:

```text
docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md
```

## 2. Dependência arquitetural

O Ads Manager não deve renderizar diretamente elementos dependentes do tema.

Toda renderização publicitária deve passar por componentes do M360 Core:

```text
Inventory Library
      ↓
Ads Manager
      ↓
Ad Slot Component
      ↓
Context Renderer / Inline Engine / futuros renderers
      ↓
Front-end PT-BR / EN-US
```

O tema News Portal, o Elementor e widgets manuais podem chamar shortcodes ou APIs do M360 Core, mas não devem conter a lógica final da peça publicitária.

## 3. Estado atual

Marco homologado anterior:

```text
M360 Core v0.4.3.5 — Ads Slot Intent Fallback
```

Marco em evolução:

```text
M360 Core v0.4.4.x — AdSense Ready / Inventory Engine
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

## 4. Arquitetura conceitual

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
M360 Ad Slot Component
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

## 5. Modelo de dados

O M360 Ads Manager utiliza tabelas próprias do WordPress.

### 5.1 Slots

Tabela lógica:

```text
m360_ad_slots
```

Responsabilidade:

- registrar espaços de inventário;
- refletir a M360 Inventory Library;
- definir chave única do slot;
- indicar contexto de página;
- registrar idioma/dispositivo quando aplicável;
- definir tamanho esperado.

### 5.2 Campanhas

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

### 5.3 Criativos

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

### 5.4 Relação Slot → Campanha

Tabela lógica:

```text
m360_ad_slot_campaigns
```

Responsabilidade:

- vincular campanhas a slots;
- controlar prioridade;
- controlar peso;
- controlar ativação.

## 6. Inventory Library

A partir da Sprint v0.4.4.x, o inventário oficial não deve ser tratado apenas como cadastro manual no banco.

A fonte oficial dos slots é:

```text
docs/00-platform/M360_Inventory_Library_v1.md
```

O código-fonte correspondente é:

```text
plugin/includes/ads/class-m360-ads-inventory-library.php
```

O banco é sincronizado pelo Inventory Seeder durante instalação/upgrade.

## 7. Inventário piloto homologado

| Slot | Uso | Formato | PT-BR | EN-US | Status |
|---|---|---|---:|---:|---|
| `header-top` | Banner de cabeçalho | 728x140 | OK | OK | Homologado |
| `content-bottom` | Banner horizontal no final do conteúdo | HTML horizontal | OK | OK | Homologado |
| `sidebar-community` | CTA/HTML da comunidade na sidebar | 300x300 | OK | OK | Homologado |
| `sidebar-square` | Banner 1:1 na sidebar | 1:1 | OK | OK | Homologado |

## 8. Evolução v0.4.4.x

Entregas em evolução:

- M360 Ad Slot Component;
- labels `PUBLICIDADE` / `ADVERTISEMENT`;
- data attributes;
- placeholders;
- CSS centralizado;
- Inventory Seeder;
- Context Renderer;
- Inline Ads Engine;
- workflow de build completo do plugin.

Primeiro impacto visual:

```text
article-after-paragraph-2
```

O slot é inserido automaticamente após o segundo parágrafo de posts individuais pelo Inline Ads Engine.

## 9. Tipos de criativos suportados

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
- `house`: suportado;
- `affiliate`: suportado;
- `sponsor`: suportado;
- `adsense`: reservado para evolução AdSense Ready;
- `gam`: reservado para evolução Google Ad Manager.

## 10. Internacionalização

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

## 11. API pública

### 11.1 Shortcodes

```text
[m360_ad_slot id="header-top"]
[m360_ads_slot id="header-top"]
[m360_ad_context context="post"]
[m360_ads_context context="search" position="top"]
```

### 11.2 PHP API

```php
echo m360_ads_render_slot('header-top');
echo m360_ads_render_context('post');
echo m360_ads_render_position('category', 'top');
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
- API PHP;
- filtro `the_content` pelo Inline Ads Engine.

## 13. Segurança

O fluxo de markup confiável é restrito a administradores do M360 Ads.

Decisão atual:

```text
Usuários com manage_options podem salvar HTML, style e script em criativos publicitários.
```

Evolução recomendada:

- criar capability específica `manage_m360_ads`;
- criar papel operacional para equipe comercial;
- registrar auditoria de alterações;
- adicionar preview seguro;
- adicionar logs de renderização.

## 14. CSS e apresentação

O CSS central da camada publicitária vive em:

```text
plugin/assets/css/m360-ads.css
```

Classes principais:

```text
.m360-ad-slot
.m360-ad-slot__label
.m360-ad-slot__content
.m360-ad-slot__placeholder
.m360-inline-ad
.m360-ad__image
.m360-ad__html
.m360-ad__script
```

## 15. Roadmap da Plataforma Comercial M360

Após AdSense Ready:

```text
v0.5.0.0 — Ads Inventory Platform
v0.5.1.0 — Campaign Management
v0.5.2.0 — Commercial Dashboard & Analytics
v0.5.3.0 — Google AdSense Connector
v0.5.4.0 — Smart Delivery Engine
v0.5.5.0 — Contextual Ads Integration
```

## 16. Decisão arquitetural final

O M360 Ads Manager deixa de ser funcionalidade auxiliar e passa a ser subsistema da plataforma.

Ele deve obedecer ao ADR-0007 e utilizar o M360 Core como camada oficial de interface, evitando lógica publicitária presa ao tema, Elementor ou HTML manual.
