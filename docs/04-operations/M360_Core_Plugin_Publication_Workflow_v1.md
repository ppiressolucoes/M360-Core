# M360 Core Plugin Publication Workflow v1

Status: oficial em preparação
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Aplicação inicial: Sprint v0.4.4.0 — M360 AdSense Ready

## 1. Objetivo

Definir o fluxo operacional mínimo para empacotar, publicar e validar o plugin M360 Core no WordPress sem alterar produção diretamente a partir da branch de desenvolvimento.

## 2. Princípio operacional

Nenhuma versão do M360 Core deve ser aplicada em produção sem:

1. branch dedicada;
2. pull request;
3. documentação de sprint/release atualizada;
4. pacote ZIP completo gerado por workflow;
5. validação em WordPress;
6. plano de rollback.

## 3. Fluxo simplificado

```text
branch de sprint
  ↓
PR draft / revisão
  ↓
workflow na main: Build M360 Core Plugin ZIP
  ↓
source_ref = branch da sprint
  ↓
artifact m360-core-v{version}.zip
  ↓
instalação em WordPress de homologação
  ↓
validação visual
  ↓
merge em main
  ↓
release oficial
```

## 4. Workflow de build completo do plugin

Arquivo na `main`:

```text
.github/workflows/build-m360-core-plugin-zip.yml
```

Nome no GitHub Actions:

```text
Build M360 Core Plugin ZIP
```

Tipo:

```text
workflow_dispatch
```

Inputs:

```text
source_ref: sprint/v0.4.4.0-adsense-ready
version: 0.4.4.3
```

O workflow:

- faz checkout do `source_ref`;
- valida `plugin/m360-core.php`;
- valida o header `Version`;
- valida a constante `M360_CORE_VERSION`;
- executa `php -l` no entrypoint e nos arquivos PHP de `includes`;
- empacota apenas o conteúdo de `plugin/`;
- gera artifact instalável `m360-core-v{version}.zip`.

## 5. Workflows modulares

Workflows modulares servem para validar componentes, não para instalar no WordPress.

Exemplos:

```text
Build M360 Ads Inventory Library
Build M360 Ads Inline Engine
```

Esses artifacts são úteis para revisão técnica, mas não substituem o plugin completo.

## 6. Conteúdo do pacote instalável

O ZIP completo deve conter:

```text
m360-core/
  m360-core.php
  assets/
  includes/
  languages/
```

Ficam fora do ZIP:

- `.git`;
- `.github`;
- `docs`;
- `tests`;
- `releases`;
- arquivos ZIP anteriores.

## 7. Checklist antes do build

Antes de gerar o ZIP completo:

- confirmar versão em `plugin/m360-core.php`;
- confirmar documentação da sprint;
- confirmar release checklist;
- confirmar PR revisado;
- confirmar que shortcodes existentes continuam compatíveis;
- confirmar que o plugin não depende de arquivos fora de `plugin/`.

## 8. Checklist pós-instalação no WordPress

Após instalar/atualizar o plugin:

- verificar ativação do plugin;
- limpar cache do WordPress/CDN, se aplicável;
- abrir `M360 Ads → Dashboard`;
- abrir `M360 Ads → Inventário Piloto`;
- abrir `M360 Ads → AdSense Ready`;
- validar os slots piloto:
  - `header-top`;
  - `content-bottom`;
  - `sidebar-community`;
  - `sidebar-square`;
- validar o slot novo `article-after-paragraph-2`;
- abrir um post individual e conferir anúncio após o segundo parágrafo;
- validar shortcode `[m360_ad_slot id="header-top"]`;
- validar API PHP `m360_ads_render_slot('header-top')`;
- validar idioma PT-BR;
- validar idioma EN-US;
- validar slot vazio com placeholder.

## 9. Rollback

Rollback recomendado:

1. manter ZIP da versão anterior homologada;
2. desativar o plugin atual, se necessário;
3. reinstalar pacote anterior;
4. limpar cache;
5. validar os quatro slots homologados;
6. registrar ocorrência no histórico operacional.

## 10. Critério de publicação

A versão só deve ser considerada publicada quando:

- o ZIP completo foi gerado pelo workflow `Build M360 Core Plugin ZIP`;
- o plugin foi instalado com sucesso;
- o admin carregou sem erro fatal;
- os slots renderizaram no front-end;
- o Inline Ads Engine foi validado em post real;
- o checklist AdSense Ready ficou acessível;
- não houve regressão nos shortcodes nem na API PHP.
