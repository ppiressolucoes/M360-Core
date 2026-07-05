# ADR-002 — Router e View Engine para páginas dinâmicas

Status: aceito

## Contexto

Durante a Sprint 10, foram identificadas diferenças entre páginas WordPress comuns, archives nativos do WordPress, templates do tema e templates do Elementor Theme Builder.

Casos observados:

- `/autor/` funcionava como página WordPress/Elementor controlada pelo M360.
- `/author/luzia-aires/` caía no template hierarchy do WordPress e usava header/footer do tema.
- Busca EN-US renderizava resultados corretamente, mas mantinha header/footer/layout do Theme Builder.

## Decisão

Criar uma camada `M360 Router` e uma camada `M360 View Engine`.

O Router deve resolver rotas especiais e encaminhar para a view correta.

O View Engine deve renderizar a interface da página dinâmica com componentes padronizados.

## Escopo inicial

- Author Hub;
- Search Results;
- Latest News / Radar News;
- Category Hub;
- Tag Hub;
- Competition Hub;
- 404 Hub.

## Consequências

- A lógica de rota deixa de ficar distribuída entre shortcodes, Elementor e tema.
- A renderização passa a ser mais consistente entre PT-BR e EN-US.
- A próxima etapa será consolidar o Radar News Engine como primeira implementação do View Engine.

## Pendências

- Definir Archive Engine completo.
- Reduzir dependência residual do Theme Builder.
- Criar Layout Engine para header, footer e containers.
