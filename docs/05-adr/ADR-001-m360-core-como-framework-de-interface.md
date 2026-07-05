# ADR-001 — M360 Core como framework de interface

Status: aceito

## Contexto

O Mengão 360 começou como portal WordPress apoiado no tema News Portal, Elementor, Polylang e plugins auxiliares. Com a evolução do DW Esportivo, internacionalização PT-BR/EN-US, SEO semântico, widgets esportivos e componentes próprios de navegação, tornou-se necessário reduzir a dependência do tema e centralizar a experiência do usuário em uma camada própria.

## Decisão

Criar e manter o `M360 Core` como framework oficial de interface da plataforma Mengão 360.

O M360 Core será responsável por:

- navegação principal;
- navegação de seção;
- breadcrumb;
- dynamic views;
- router;
- view engine;
- layout foundation;
- SEO estrutural e schema;
- componentes reutilizáveis.

## Consequências

- O tema News Portal passa a ser tratado como camada de compatibilidade temporária.
- O Elementor permanece útil como ferramenta de composição, mas não deve ser a fonte única da lógica de navegação ou renderização dinâmica.
- Novas entregas devem nascer como módulos ou componentes do M360 Core.
- O plugin deve ser versionado, documentado e publicado via GitHub.

## Regra operacional

Nenhuma alteração estrutural no M360 Core deve ser aplicada sem changelog, documentação, checklist de validação e rollback.
