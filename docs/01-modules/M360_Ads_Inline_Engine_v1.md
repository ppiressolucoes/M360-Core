# M360 Ads Inline Engine v1

Status: implementação inicial
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Módulo: M360 Advertising
Versão de referência: M360 Core v0.4.4.3

## 1. Objetivo

O M360 Ads Inline Engine é responsável por inserir automaticamente slots publicitários dentro do conteúdo editorial de artigos, sem editar templates do tema, Elementor ou conteúdo manualmente.

Esta é a primeira entrega funcional visível da evolução da M360 Inventory Library.

## 2. Comportamento inicial

Na versão `0.4.4.3`, o engine atua somente em posts individuais.

Regra inicial:

```text
Inserir o slot article-after-paragraph-2 após o 2º parágrafo do artigo.
```

## 3. Slot utilizado

```text
article-after-paragraph-2
```

O slot é definido pela M360 Inventory Library e cadastrado pelo Inventory Seeder.

## 4. Condições de execução

O engine só injeta anúncio quando todas as condições forem verdadeiras:

- não está no admin;
- não é feed;
- não é AJAX;
- não é REST;
- é post individual;
- está no loop principal;
- é a query principal;
- o filtro `m360_ads_inline_enabled` retorna `true`.

## 5. Controle por filtro

Desativar globalmente:

```php
add_filter('m360_ads_inline_enabled', '__return_false');
```

Alterar os placements:

```php
add_filter('m360_ads_inline_article_placements', function () {
    return [
        [
            'after_paragraph' => 2,
            'slot' => 'article-after-paragraph-2',
            'class' => 'm360-inline-ad--after-paragraph-2',
        ],
    ];
});
```

## 6. Impacto visual esperado

Ao instalar a versão `0.4.4.3`, artigos passam a exibir automaticamente um slot após o segundo parágrafo.

Se não houver campanha ativa vinculada ao slot, o M360 Ad Slot Component renderizará o placeholder configurado.

## 7. CSS

A classe principal é:

```text
.m360-inline-ad
```

O espaçamento padrão fica em:

```text
plugin/assets/css/m360-ads.css
```

## 8. Relação com AdSense Ready

O Inline Engine reutiliza o M360 Ad Slot Component, portanto mantém:

- label automática;
- wrapper semântico;
- data attributes;
- placeholder;
- comentários HTML de diagnóstico;
- provider ready.

## 9. Próximas evoluções

- múltiplos placements por artigo;
- controle administrativo de posições;
- desativação por post;
- desativação por categoria/tag;
- integração com Search, Category, Tag e Author engines;
- preparação para AdSense nativo e Google Ad Manager.
