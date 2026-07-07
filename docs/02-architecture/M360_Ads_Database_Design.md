# M360 Ads Database Design

Versao documental: 0.4.2.4-docs
Modulo futuro: M360 Ads Slots Foundation
Status: desenho tecnico para revisao

## 1. Objetivo

Definir a estrutura de banco de dados do futuro modulo M360 Ads, responsavel por parametrizar, cadastrar e relacionar espacos de publicidade do portal Mengao 360.

Esta entrega e documental e nao altera o runtime do plugin.

## 2. Principios

- Manter o modulo isolado do tema WordPress.
- Evitar dependencia direta do M360 Home Editorial, M360 Semantic Relations ou Bolao.
- Permitir uso por shortcode, PHP API e futuros paineis administrativos.
- Suportar PT-BR e EN-US.
- Suportar desktop, tablet, mobile e todos.
- Permitir campanhas internas, banners, HTML, AdSense, GAM, afiliados e patrocinadores.
- Preparar a base para estatisticas futuras sem criar carga excessiva nesta primeira fase.

## 3. Entidades principais

### Slots

Espacos publicitarios disponiveis no portal.

Exemplos:

- header-banner
- article-top
- article-inline-1
- sidebar-top
- home-middle
- footer-banner
- latest-news-inline

### Campanhas

Anuncios ou campanhas que podem ocupar um ou mais slots.

Exemplos:

- Campanha de patrocinador.
- Banner do Mega Bolao.
- Bloco AdSense.
- House ad para comunidade WhatsApp.

### Relacionamentos

Ligacao entre slots e campanhas, com prioridade e status.

## 4. Tabela wp_m360_ad_slots

Armazena o inventario de espacos publicitarios.

| Campo | Tipo | Obrigatorio | Descricao |
|---|---:|:---:|---|
| id | BIGINT UNSIGNED | sim | Identificador interno. |
| slot_key | VARCHAR(120) | sim | Chave unica do slot. Ex: article-top. |
| name | VARCHAR(180) | sim | Nome amigavel. |
| description | TEXT | nao | Descricao do uso do slot. |
| position | VARCHAR(80) | nao | Posicao editorial. Ex: header, sidebar, inline. |
| page_context | VARCHAR(80) | sim | Contexto: global, home, post, category, tag, author, search, bolao. |
| language | VARCHAR(10) | sim | pt-br, en-us ou all. |
| device | VARCHAR(20) | sim | desktop, tablet, mobile ou all. |
| max_width | INT UNSIGNED | nao | Largura maxima esperada. |
| max_height | INT UNSIGNED | nao | Altura maxima esperada. |
| is_active | TINYINT(1) | sim | 1 ativo, 0 inativo. |
| created_at | DATETIME | sim | Data de criacao. |
| updated_at | DATETIME | sim | Data de atualizacao. |

### Indices

- PRIMARY KEY id
- UNIQUE KEY slot_key
- KEY page_context
- KEY language
- KEY device
- KEY is_active

## 5. Tabela wp_m360_ad_campaigns

Armazena as campanhas/anuncios.

| Campo | Tipo | Obrigatorio | Descricao |
|---|---:|:---:|---|
| id | BIGINT UNSIGNED | sim | Identificador interno. |
| title | VARCHAR(220) | sim | Titulo da campanha. |
| advertiser | VARCHAR(180) | nao | Anunciante ou patrocinador. |
| campaign_type | VARCHAR(40) | sim | image, html, adsense, gam, house, affiliate, sponsor. |
| image_url | TEXT | nao | URL da imagem do banner. |
| target_url | TEXT | nao | URL de destino. |
| html_code | LONGTEXT | nao | HTML livre seguro. |
| script_code | LONGTEXT | nao | Codigo de AdSense, GAM ou script autorizado. |
| alt_text | VARCHAR(220) | nao | Texto alternativo para imagem. |
| language | VARCHAR(10) | sim | pt-br, en-us ou all. |
| device | VARCHAR(20) | sim | desktop, tablet, mobile ou all. |
| start_at | DATETIME | nao | Inicio da campanha. |
| end_at | DATETIME | nao | Fim da campanha. |
| priority | INT | sim | Prioridade padrao. Maior vence. |
| status | VARCHAR(30) | sim | draft, active, paused, expired, archived. |
| created_at | DATETIME | sim | Data de criacao. |
| updated_at | DATETIME | sim | Data de atualizacao. |

### Indices

- PRIMARY KEY id
- KEY campaign_type
- KEY language
- KEY device
- KEY status
- KEY priority
- KEY start_at
- KEY end_at

## 6. Tabela wp_m360_ad_slot_campaigns

Relaciona campanhas com slots.

| Campo | Tipo | Obrigatorio | Descricao |
|---|---:|:---:|---|
| id | BIGINT UNSIGNED | sim | Identificador interno. |
| slot_id | BIGINT UNSIGNED | sim | ID do slot. |
| campaign_id | BIGINT UNSIGNED | sim | ID da campanha. |
| priority | INT | sim | Prioridade especifica no slot. |
| weight | INT | sim | Peso para rotacao futura. |
| is_active | TINYINT(1) | sim | 1 ativo, 0 inativo. |
| created_at | DATETIME | sim | Data de criacao. |
| updated_at | DATETIME | sim | Data de atualizacao. |

### Indices

- PRIMARY KEY id
- UNIQUE KEY slot_campaign (slot_id, campaign_id)
- KEY slot_id
- KEY campaign_id
- KEY priority
- KEY is_active

## 7. Tabela futura wp_m360_ad_events

Esta tabela deve ficar para uma sprint posterior, pois eventos podem gerar alto volume.

| Campo | Tipo | Descricao |
|---|---:|---|
| id | BIGINT UNSIGNED | Identificador interno. |
| slot_id | BIGINT UNSIGNED | ID do slot. |
| campaign_id | BIGINT UNSIGNED | ID da campanha. |
| event_type | VARCHAR(30) | impression ou click. |
| page_url | TEXT | URL onde ocorreu o evento. |
| post_id | BIGINT UNSIGNED | Post relacionado, quando houver. |
| language | VARCHAR(10) | Idioma. |
| device | VARCHAR(20) | Dispositivo. |
| created_at | DATETIME | Data do evento. |

## 8. Regras de selecao de campanha

Para renderizar um slot, o futuro motor deve:

1. Localizar o slot ativo por slot_key.
2. Filtrar por idioma atual: idioma exato ou all.
3. Filtrar por dispositivo: dispositivo exato ou all.
4. Filtrar campanhas ativas.
5. Validar janela de exibicao start_at/end_at.
6. Ordenar por prioridade do relacionamento.
7. Usar prioridade da campanha como desempate.
8. Renderizar a primeira campanha elegivel.
9. Se nao houver campanha elegivel, renderizar vazio ou fallback configurado.

## 9. Regras de idioma

Valores iniciais aceitos:

- pt-br
- en-us
- all

A integracao com Polylang deve mapear o idioma atual para esses valores.

## 10. Regras de dispositivo

Valores iniciais aceitos:

- desktop
- tablet
- mobile
- all

A deteccao precisa ser simples na primeira versao. Regras avancadas devem ser delegadas a versoes futuras.

## 11. Regras de seguranca

- HTML e scripts devem ser tratados com permissao restrita a usuarios administradores.
- O painel futuro deve usar nonce, capability manage_options e sanitizacao de campos.
- URLs devem passar por esc_url_raw na gravacao e esc_url na renderizacao.
- HTML deve usar allowlist quando possivel.
- Scripts de terceiros devem ter controle separado.

## 12. Estrategia de ativacao

Na implementacao futura, o plugin deve criar ou atualizar as tabelas via dbDelta durante a ativacao ou rotina de upgrade.

A versao do schema deve ser armazenada em option:

```text
m360_ads_db_version
```

Valor inicial sugerido:

```text
0.4.2.4
```

## 13. Criterios de aceite da etapa docs

- Documento tecnico criado.
- Script SQL de criacao criado.
- Script SQL de seed inicial criado.
- Estrutura revisavel antes da codificacao.
- Nenhuma alteracao no runtime do plugin.

## 14. Proxima etapa

Apos aprovacao deste desenho:

```text
v0.4.2.4 - M360 Ads Slots Foundation
```

com dbDelta, renderizador, shortcode e API PHP.
