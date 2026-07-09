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
4. pacote ZIP gerado por workflow;
5. validação em WordPress;
6. plano de rollback.

## 3. Branches

Fluxo recomendado:

```text
main
  ↓
sprint/v0.4.4.0-adsense-ready
  ↓
Pull Request
  ↓
merge em main
  ↓
workflow Build M360 Core Plugin ZIP
  ↓
artifact ZIP
  ↓
instalação manual/assistida no WordPress
```

## 4. Workflow de build

Arquivo:

```text
.github/workflows/build-plugin-zip.yml
```

Tipo:

```text
workflow_dispatch
```

Parâmetro obrigatório:

```text
version
```

Exemplo:

```text
0.4.4.0
```

O workflow valida se a versão informada existe em:

```text
plugin/m360-core.php
```

Valida os dois pontos:

- cabeçalho WordPress `Version`;
- constante `M360_CORE_VERSION`.

## 5. Conteúdo do pacote

O ZIP gerado deve conter apenas o plugin instalável:

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

## 6. Nome do artifact

Padrão:

```text
m360-core-v{version}.zip
```

Exemplo:

```text
m360-core-v0.4.4.0.zip
```

## 7. Checklist antes do build

Antes de gerar o ZIP:

- confirmar versão em `plugin/m360-core.php`;
- confirmar documentação da sprint;
- confirmar release history;
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
- validar os slots:
  - `header-top`;
  - `content-bottom`;
  - `sidebar-community`;
  - `sidebar-square`;
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

- o ZIP foi gerado pelo workflow;
- o plugin foi instalado com sucesso;
- o admin carregou sem erro fatal;
- os slots renderizaram no front-end;
- o checklist AdSense Ready ficou acessível;
- não houve regressão nos shortcodes nem na API PHP.
