# M360 Core — Fluxo de Implantação via GitHub

Status: oficial  
Sprint: Sprint 0 — GitHub Build Foundation

## 1. Objetivo

Criar um fluxo simples, prático e rastreável para desenvolvimento e implantação do plugin M360 Core.

O GitHub passa a ser a fonte única de verdade do projeto, e o ZIP instalável do WordPress passa a ser gerado por GitHub Actions.

## 2. Fluxo oficial

```text
Sprint
  ↓
Branch
  ↓
Código + documentação
  ↓
Pull Request
  ↓
Merge na main
  ↓
GitHub Actions
  ↓
ZIP do plugin
  ↓
Instalação no WordPress
```

## 3. Estrutura do pacote

O diretório `plugin/` é a raiz do pacote instalável.

```text
plugin/
├── m360-core.php
├── assets/
├── core/
├── navigation/
├── router/
├── views/
├── schema/
├── seo/
└── languages/
```

O workflow deve compactar o conteúdo de `plugin/` dentro de uma pasta nomeada por versão.

## 4. Versionamento

A versão humana e operacional deve ser registrada em:

```text
VERSION.md
```

O changelog deve ser atualizado em:

```text
CHANGELOG.md
```

## 5. Convenção de branches

```text
feature/<nome-da-entrega>
fix/<nome-da-correcao>
infra/<nome-da-infra>
docs/<nome-da-documentacao>
release/<versao>
```

Exemplos:

```text
feature/view-engine-v0.3.4
feature/radar-news-v0.3.5
infra/build-foundation
```

## 6. Como gerar o ZIP

### Opção A — automática

Ao fazer merge na `main`, o GitHub Actions executa o workflow e gera um artifact ZIP.

### Opção B — manual

No GitHub:

```text
Actions → Build M360 Core Plugin ZIP → Run workflow
```

Depois baixar o artifact gerado.

## 7. Como instalar no WordPress

1. Acessar o painel WordPress.
2. Ir em Plugins → Adicionar novo.
3. Enviar plugin.
4. Selecionar o ZIP gerado pelo GitHub Actions.
5. Instalar.
6. Substituir versão atual.
7. Ativar se necessário.
8. Limpar cache.
9. Validar checklist da release.

## 8. Checklist pós-instalação

- [ ] Plugin ativo.
- [ ] Versão correta.
- [ ] Main Navigation funcionando.
- [ ] Section Navigation funcionando.
- [ ] Breadcrumb funcionando.
- [ ] Author Hub funcionando.
- [ ] Search Results funcionando.
- [ ] PT-BR validado.
- [ ] EN-US validado.
- [ ] Desktop validado.
- [ ] Mobile validado.
- [ ] Sem regressão visual global.

## 9. Regra de segurança

Nenhuma release deve alterar header, footer ou layout global sem registro explícito no changelog e checklist de validação.

## 10. Próxima release operacional

```text
v0.3.4-rc1 — M360 View Engine
```
