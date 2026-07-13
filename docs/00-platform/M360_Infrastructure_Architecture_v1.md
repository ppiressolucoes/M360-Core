# M360 Infrastructure Architecture v1

Status: oficial em preparação
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Função: referência da infraestrutura técnica e da posição do M360 Core na plataforma.

## 1. Objetivo

Este documento descreve a infraestrutura de execução da Plataforma Mengão 360 e consolida a posição do M360 Core como camada de interface e aplicação, conforme definido no ADR-0007.

## 2. Referência arquitetural

Documento normativo relacionado:

```text
docs/00-platform/ADR-0007_M360_Core_Interface_Architecture.md
```

Documento mestre relacionado:

```text
docs/00-platform/M360_Platform_Architecture_v2.md
```

## 3. Camadas da plataforma

```text
GitHub / Releases / Workflows
        ↓
WordPress Hosting
        ↓
WordPress Core
        ↓
Polylang / Yoast / Plugins de suporte
        ↓
M360 Core
        ↓
Componentes próprios PT-BR / EN-US
        ↓
Tema News Portal / Elementor como compatibilidade
        ↓
Front-end Mengão 360
```

## 4. Papel do M360 Core

O M360 Core é responsável por abstrair a interface da plataforma em componentes próprios, reduzindo dependência direta do tema e do Elementor.

Ele fornece:

- componentes de navegação;
- cabeçalhos e rodapés multilíngues;
- busca;
- views dinâmicas;
- publicidade;
- inventário comercial;
- shortcodes;
- APIs PHP;
- integração operacional com WordPress.

## 5. Tema e Elementor

O tema News Portal e o Elementor permanecem como camadas de compatibilidade e composição.

Eles podem:

- compor páginas;
- hospedar shortcodes;
- renderizar containers;
- manter compatibilidade visual temporária.

Eles não devem:

- concentrar regra de negócio;
- definir lógica de internacionalização;
- manter menus, rodapés ou anúncios como fonte primária;
- substituir componentes oficiais do M360 Core.

## 6. Infraestrutura operacional

Componentes oficiais:

- WordPress;
- Polylang;
- Elementor;
- News Portal;
- Yoast SEO;
- n8n;
- MariaDB;
- DW Esportivo;
- cache_widgets;
- WP-Cron / CRON;
- REST API;
- GitHub;
- GitHub Actions;
- build ZIP;
- releases;
- rollback documentado.

## 7. Workflow de publicação

A publicação de plugin deve seguir:

```text
branch de sprint
  ↓
PR
  ↓
Build M360 Core Plugin ZIP
  ↓
Artifact instalável
  ↓
WordPress de homologação
  ↓
Validação
  ↓
Merge / Release
```

Workflow oficial:

```text
.github/workflows/build-m360-core-plugin-zip.yml
```

Documento operacional:

```text
docs/04-operations/M360_Core_Plugin_Publication_Workflow_v1.md
```

## 8. Governança de infraestrutura

Toda nova sprint que introduzir componente de interface, workflow, release ou integração deve verificar:

1. aderência ao ADR-0007;
2. impacto no M360 Core;
3. impacto no WordPress;
4. impacto no tema/Elementor;
5. compatibilidade PT-BR / EN-US;
6. existência de rollback;
7. documentação atualizada.

## 9. Diretriz de manutenção

A infraestrutura deve favorecer evolução incremental sem refatoração ampla.

Toda implementação deve evitar dependência estrutural de:

- template específico do tema;
- widget manual;
- HTML solto;
- layout duplicado por idioma;
- lógica presa ao Elementor.

## 10. Próximas frentes

- Theme Independence Program;
- Universal Component Library;
- M360 Layout Engine;
- Universal Slot Renderer;
- Archive Ads Engine;
- Plataforma Comercial M360;
- Observabilidade de builds e releases.
