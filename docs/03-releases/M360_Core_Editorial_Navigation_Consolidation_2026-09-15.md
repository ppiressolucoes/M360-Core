# Consolidação Editorial e de Navegação — M360 Core v0.7.4.0.42

Data de homologação: 15/09/2026  
Ambiente: Mengão 360  
Idiomas: PT-BR e EN-US  
Status: homologado

## 1. Resultado

A versão `0.7.4.0.42` consolida as evoluções editoriais e de navegação realizadas após a baseline 0.7.4.0.25. Componentes antes mantidos por HTML/CSS/JavaScript no Elementor passaram a ter contratos reutilizáveis no M360 Core. A validação final confirmou o alinhamento dos quatro blocos do Footer: Categorias, Competições, Institucional e Tags.

## 2. Gestão editorial

- widgets reutilizáveis por ID estável;
- seleção livre da categoria principal e de categorias adicionais;
- instâncias próprias para PT-BR e EN-US;
- título e link “Ver todas/View all” derivados da categoria principal;
- resumo com fallback para o conteúdo publicado;
- modelos editoriais #1 a #5 e Newsroom preservados;
- família e tamanhos tipográficos configuráveis no painel Editorial.

Contrato principal:

```text
[m360_editorial_widget id="identificador-estavel"]
```

## 3. Navegação absorvida pelo Core

| Componente | Contrato |
|---|---|
| Breadcrumb | `[m360_breadcrumb]` |
| Pesquisa no header | `[m360_search_toggle]` |
| Troca inline de idioma | `[m360_language_navigation]` |
| Troca flutuante de idioma | `[m360_language_switcher]` |
| Preloader portátil | `[m360_preloader]` |
| Retorno ao topo | `[m360_back_to_top]` |
| Redes sociais | `[m360_social_links]` |
| Categorias | `[m360_categories]` |
| Arquivos | `[m360_archives]` |
| Nuvem de Tags | `[m360_tag_cloud]` |
| Menu nativo do WordPress | `[m360_footer_menu]` |

A fixação da `.m360-header-topbar` também é administrada pelo Core e dispensa script manual no template.

## 4. Footer homologado

```text
[m360_footer_menu menu="Footer Categorias" title="Categorias" surface="dark"]
[m360_footer_menu menu="Footer Competições" title="Competições" surface="dark"]
[m360_footer_menu menu="MNU SUPORTE" title="Institucional" surface="dark"]
[m360_tag_cloud surface="dark" limit="12" min_size="13" max_size="16" order="mixed" show_counts="false"]
```

Os três menus permanecem administrados em **Aparência > Menus**. A nuvem de Tags consulta a taxonomia nativa do WordPress, distribui os itens de forma estável e herda o fundo `#191919` do Footer.

## 5. Limites de responsabilidade

O shortcode `[m360_sports_competitions_ticker]` pertence ao **M360 Plus Editorial**. Ele pode ser usado em outras áreas do site pela versão 0.3.3.8 do Plus, mas não integra o código nem o pacote do M360 Core.

## 6. Evidências de homologação

- widgets editoriais validados em PT-BR e EN-US;
- componentes de navegação testados no Elementor Theme Builder;
- breadcrumb e menu institucional validados em desktop e mobile;
- botão de idioma, preloader, pesquisa e retorno ao topo validados;
- blocos de Categorias, Arquivos e Tags validados;
- Footer final aprovado com títulos e linhas alinhados;
- pacote inspecionado com 119 arquivos e sem o ticker do Plus Editorial.

## 7. Artefato oficial

```text
Versão: 0.7.4.0.42
Tag: v0.7.4.0.42
Arquivo: m360-core-v0.7.4.0.42.zip
SHA-256: 4CA2CB6C6B5A10B718C3EF0DE9CC0DDC9A0BCCE4CF3926332AF4722FB11B5A54
```

## 8. Continuidade

Esta baseline substitui `0.7.4.0.35` como referência oficial da `main`. Próximas evoluções devem partir da tag `v0.7.4.0.42`, preservar os contratos documentados e manter a separação entre M360 Core e M360 Plus Editorial.
