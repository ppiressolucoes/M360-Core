# M360 Core v0.7.4.0.12 — Editorial Layout Standard

## Objetivo

Consolida o padrão editorial portátil para o Portal Energia Limpa, Mengão 360
e futuras acoplagens, sem assumir templates do tema ativo.

## Editorial Layout & Home

- Newsroom: reduz a escala do título de destaque e limita o resumo para uma
  leitura equilibrada sobre imagem.
- Newsroom: controles anterior/próximo passam a usar o mesmo padrão quadrado,
  com SVG acessível, do ticker.
- Newsroom: autoplay respeita a visibilidade da página e a preferência de
  movimento reduzido.
- Newsroom: mantém quatro cards visíveis e alterna grupos automaticamente
  quando a consulta retornar de cinco a oito cards.
- Widgets editoriais: formaliza os cinco presets portáteis:
  1. destaque integral + três cards;
  2. destaque em duas colunas + seis mininotícias;
  3. dois destaques + quatro mininotícias;
  4. três destaques + seis mininotícias;
  5. Latest News em carrossel retrato (padrão de nove notícias).
- Todos os presets preservam título, metadados e `View all` configuráveis por
  instância, sem alterar conteúdo, tema ou templates existentes.

## Compatibilidade e limites

- Tema-independente: os componentes usam CSS com escopo `m360-*`.
- Branding: a cor de destaque continua vindo de `Site Profile > branding`.
- Sem alteração automática de páginas, posts, menus, Elementor ou conteúdo
  legado. A publicação continua pelo shortcode da instância cadastrada.
- As consultas continuam sujeitas a idioma/categorias definidos no widget e à
  disponibilidade de posts publicados no locale.

## Homologação recomendada

1. Atualizar o ZIP e confirmar a versão `0.7.4.0.12` no Dashboard M360.
2. No widget Newsroom, configurar oito cards de origem para testar a rotação
   lateral; com quatro ou menos cards a grade permanece corretamente estática.
3. Testar desktop, tablet, mobile, pausa por hover/foco e a preferência de
   movimento reduzido.
4. Confirmar que os cinco presets em PT-BR e EN-US respeitam título, metadados,
   `View all` e as cores do Site Profile.
