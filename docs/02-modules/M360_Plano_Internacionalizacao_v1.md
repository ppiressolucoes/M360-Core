# Mengão 360 — Plano de Internacionalização

Versão: v1.0

Status: PT-BR / EN-US operacional com interface global controlada pelo M360 Core

## Objetivo

Expandir a internacionalização além dos widgets dinâmicos, cobrindo site, tema, menus, páginas, botões, CTAs, footer, landing pages, SEO e produto Mega Bolão 360.

## Idiomas

- pt-BR: padrão.
- en-US: ativo.
- es-ES: planejado.

## Camadas

### i18n interno

Usado para widgets, bolão, rankings, ligas, artilharia, estatísticas e mensagens dinâmicas.

Base:
- m360_i18n_publico
- m360_idiomas_publicacao
- m360_get_lang()

### i18n WordPress

Usado para menus, header, footer, páginas Elementor, botões estáticos, páginas comerciais e SEO.

### Interface global M360

O uso compartilhado de templates do News Portal e do Elementor produzia mistura de idiomas: páginas EN-US podiam exibir footer ou menus PT-BR. A solução operacional adotada foi manter modelos independentes de topo e rodapé para cada idioma e resolver todos os elementos globais pelo contexto linguístico do M360 Core.

Regras:

- header, navegação e footer devem usar o mesmo idioma da página;
- PT-BR e EN-US possuem modelos globais independentes;
- não deve existir fallback automático de menu ou footer PT-BR em páginas EN-US;
- News Portal e Elementor são camadas de composição, não fontes da lógica multilíngue;
- novos componentes globais devem nascer no M360 Core.

## Estratégia de URL

Curto prazo:
- ?lang=pt-BR
- ?lang=en-US
- ?lang=es-ES

Médio prazo:
- /en/
- /es/

## Seletor de idioma

Formato: PT | EN | ES

Comportamento:
- Sem parâmetro: pt-BR.
- EN: versão em inglês da mesma página.
- ES: versão em espanhol da mesma página.

## Páginas prioritárias

1. Home.
2. Brasileirão Série A.
3. Copa Libertadores.
4. Copa do Mundo 2026.
5. Bolão Copa 2026.
6. Mega Bolão 360.
7. Login/cadastro.
8. Termos/privacidade.

## Elementos globais

Traduzir:
- menu
- buscar
- entrar
- cadastrar
- criar bolão
- participar
- ver tabela
- ver ranking
- compartilhar
- rodapé
- categorias
- CTAs

## SEO multilíngue

Cada idioma deve ter:
- title
- meta description
- canonical
- hreflang
- conteúdo localizado

## Lacunas identificadas

- Offence → Forward
- Defence → Defender
- Midfield → Midfielder
- Centre-Forward → Centre-forward
- Brazil → Brasil em pt-BR
- Colombia → Colômbia em pt-BR

## Estratégia recomendada

Manter i18n próprio para widgets e bolão. Avaliar plugin WordPress para conteúdo editorial, menus, páginas e SEO.
