# M360 Core — Navigation Library Sprint 9.1 Foundation Log

Status: validada em ambiente real
Projeto: Mengão 360 | DW Esportivo
Módulo oficial: M360 Core
Release relacionada: Release 2.0 — M360 Navigation Library
Plugin oficial: M360 Core
Versão validada: v0.2.4

## 1. Objetivo

Registrar as evoluções executadas na Sprint 9.1 — Foundation da M360 Navigation Library, consolidando o início prático do M360 Core como camada própria de interface e navegação do portal.

A sprint teve como objetivo validar componentes independentes do tema News Portal e do Nav Menu do Elementor, preservando integração com WordPress, Elementor e Polylang.

## 2. Contexto

O problema original identificado na internacionalização PT-BR / EN-US foi a existência de dois motores diferentes de navegação:

- PT-BR: renderização dependente do tema News Portal.
- EN-US: renderização dependente do Elementor Theme Builder / Nav Menu.

Essa diferença gerava inconsistências de HTML, CSS, JavaScript, dropdowns, responsividade e comportamento entre idiomas.

## 3. Evolução do plugin M360 Core

### v0.1.0 — MVP inicial de Main Navigation

- shortcode `[m360_main_navigation]`;
- renderização inicial do menu principal;
- validação do menu EN-US como caso de uso controlado;
- redução da dependência do Nav Menu do Elementor.

### v0.1.1 — Correção de contraste e resolução do menu EN-US

- texto branco sobre barra vermelha;
- melhoria na resolução automática do menu;
- suporte ao menu Primary Menu English;
- dropdown funcional.

### v0.2.0 — Consolidação como plugin oficial M360 Core

- mudança de escopo de MVP para plugin oficial M360 Core;
- inclusão do componente Section Navigation;
- shortcode `[m360_section_navigation]`;
- navegação lateral institucional para páginas informativas;
- suporte a PT-BR, EN-US e ES-ES planejado.

### v0.2.1 — Correção de compatibilidade da Main Navigation

- correção da resolução automática do menu principal;
- preservação do shortcode `[m360_main_navigation]`;
- fallback por location, label, nome, slug e idioma;
- restauração da navegação principal em PT-BR e EN-US.

### v0.2.2 — Refinamento do Section Navigation

- maior área clicável;
- melhor espaçamento vertical;
- hover mais profissional;
- item ativo com destaque lateral e linha inferior;
- consistência visual em desktop, tablet e mobile.

### v0.2.3 — Introdução do M360 Breadcrumb

- shortcode `[m360_breadcrumb]`;
- barra de localização visual abaixo do menu principal;
- trilha de navegação para posts e páginas;
- validação em PT-BR e EN-US.

### v0.2.4 — Breadcrumb inteligente, Schema e SEO

- breadcrumb mais compacto;
- separador moderno `›`;
- ícone Home em SVG;
- destaque visual do item atual;
- comportamento inteligente usando hierarquia real do menu WordPress;
- HTML semântico com `nav`, `ol`, `aria-label` e `aria-current`;
- Schema.org BreadcrumbList em JSON-LD;
- integração visual validada em PT-BR e EN-US.

## 4. Componentes validados

### Main Navigation

Status: validado.

Função:

- renderizar o menu principal do portal com HTML/CSS próprios;
- suportar Polylang e menus por idioma;
- reduzir dependência de tema e Elementor.

### Section Navigation

Status: validado.

Função:

- renderizar navegação contextual lateral;
- apoiar páginas institucionais e informativas;
- melhorar UX e critérios de navegação para AdSense.

### Breadcrumb

Status: validado.

Função:

- indicar a localização atual do usuário;
- reforçar hierarquia editorial;
- gerar marcação semântica e Schema.org;
- apoiar SEO e rastreabilidade de navegação.

## 5. Shortcodes oficiais

```text
[m360_main_navigation]
[m360_section_navigation]
[m360_breadcrumb]
```

## 6. Decisões técnicas consolidadas

1. M360 Core passa a ser o plugin oficial da camada de interface.
2. Navigation Library é o primeiro domínio funcional do M360 Core.
3. Main Navigation, Section Navigation e Breadcrumb são componentes oficiais do Core.
4. A navegação será evoluída de forma incremental, sem substituição brusca de todo o tema.
5. O WordPress permanece como fonte dos menus; o M360 Core passa a controlar a renderização.
6. O Polylang é a autoridade de idioma.
7. Componentes devem ser reutilizáveis em Elementor e WordPress.
8. Schema.org e ARIA passam a fazer parte dos critérios de qualidade dos componentes de navegação.

## 7. Próximos passos planejados

### Sprint 9.2 — M360 Navigation Intelligence

Criar uma camada comum de contexto para os componentes de navegação.

### Sprint 10 — M360 Dynamic Views

Evoluir o M360 Core de biblioteca de componentes para camada de renderização de páginas dinâmicas.

## 8. Status final

```text
Sprint 9.1 — Foundation
Status: concluída e validada em ambiente real
Plugin: M360 Core
Versão estável da fase: v0.2.4
Componentes validados: Main Navigation, Section Navigation, Breadcrumb
```
