# M360 Core Plugin Publication Workflow v1.1

Status: oficial
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Baseline atual: `v0.7.4.0.42`

## 1. Objetivo

Definir o fluxo para empacotar, homologar e publicar o M360 Core sem alterar produção diretamente a partir de uma branch de desenvolvimento.

## 2. Fluxo oficial

```text
branch de desenvolvimento
  ↓
revisão e documentação
  ↓
build do ZIP instalável
  ↓
instalação em WordPress de homologação
  ↓
validação PT-BR/EN-US e desktop/mobile
  ↓
commit e tag imutável
  ↓
Pull Request para main
  ↓
merge e GitHub Release com ZIP/checksum
```

## 3. Build local reproduzível

Na raiz do repositório:

```powershell
.\scripts\build-plugin-package.ps1 -Version 0.7.4.0.42
```

Saída esperada:

```text
outputs/m360-core-v0.7.4.0.42/m360-core-v0.7.4.0.42.zip
```

O workflow `.github/workflows/build-m360-core-plugin-zip.yml` oferece o build equivalente no GitHub Actions.

## 4. Conteúdo do pacote

O ZIP contém somente o plugin instalável:

```text
m360-core/
  m360-core.php
  assets/
  includes/
  languages/
  templates/
  views/
```

Documentação, testes, histórico Git, releases e pacotes anteriores permanecem fora do ZIP.

## 5. Checklist técnico

- versão do header e `M360_CORE_VERSION` coincidentes;
- `VERSION.md`, `CHANGELOG.md`, índice e release notes atualizados;
- `php -l` executado quando o runtime PHP estiver disponível;
- ZIP aberto e conteúdo obrigatório inspecionado;
- nenhum componente de outro pacote incorporado por engano;
- checksum SHA-256 registrado.

## 6. Checklist de homologação

- plugin ativa sem erro fatal;
- painel `M360 Core` e submenus carregam;
- Home e conteúdo individual carregam em PT-BR e EN-US;
- widgets editoriais preservam categoria, idioma, título, resumo e link de arquivo;
- breadcrumb, pesquisa, idioma, preloader, header fixo e retorno ao topo funcionam;
- Categorias, Arquivos, Tags, menus do Footer e redes sociais renderizam;
- desktop e mobile não apresentam regressão visual;
- cache do WordPress/CDN é limpo e o CSS do Elementor é regenerado quando necessário.

## 7. Rollback

1. preservar o ZIP da versão homologada anterior;
2. reinstalar o pacote anterior;
3. limpar cache e regenerar CSS;
4. validar páginas críticas nos dois idiomas;
5. registrar a ocorrência e criar nova versão para a correção.

## 8. Critério de publicação

A versão é oficial quando o pacote homologado, a documentação, o commit, a tag, a `main` e a GitHub Release apontam para a mesma baseline funcional.
