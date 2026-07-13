# ADR-0007 — M360 Core como Camada de Interface da Plataforma

Status: Aceita
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Data de consolidação: Sprint v0.4.4.x
Tipo: Architecture Decision Record

## 1. Contexto

O projeto Mengão 360 evoluiu de um portal WordPress tradicional para uma plataforma esportiva modular, multilíngue e orientada por componentes próprios.

Durante a evolução da internacionalização PT-BR / EN-US, o tema News Portal e o Elementor atenderam parcialmente à composição visual inicial, mas demonstraram limitações estruturais para sustentar uma experiência multilíngue completa.

O problema mais evidente ocorreu na composição de topo, rodapé, menus e elementos globais: páginas EN-US podiam exibir footer, menus ou componentes em PT-BR quando a renderização dependia diretamente do tema, do Elementor Theme Builder ou de widgets manuais.

Para resolver esse problema sem criar retrabalho recorrente, o projeto passou a desenvolver headers, footers, navegação, busca, views dinâmicas e publicidade em uma camada própria: o M360 Core.

## 2. Problema

Manter a interface principal dependente do tema ou do Elementor causaria riscos contínuos:

- inconsistência de idioma entre PT-BR e EN-US;
- duplicação de templates;
- dificuldade de manutenção em headers, footers e menus;
- retrabalho em cada nova sprint visual;
- dependência de limitações do tema News Portal;
- acoplamento entre layout, conteúdo e lógica de plataforma;
- maior custo para futura substituição do tema;
- dificuldade de padronizar anúncios, widgets e views dinâmicas.

## 3. Decisão

O M360 Core é a camada oficial de interface da Plataforma Mengão 360.

A partir desta decisão, toda nova funcionalidade visual, estrutural ou reutilizável deverá ser desenvolvida prioritariamente no M360 Core, utilizando componentes independentes do tema, compatíveis com internacionalização e governados por documentação, sprint, release e ADR quando aplicável.

O tema WordPress News Portal e o Elementor passam a ser tratados como camadas de compatibilidade, composição e transição, não como origem da lógica principal da interface.

## 4. Princípios arquiteturais

- O M360 Core é a fonte primária da interface da plataforma.
- O tema é camada de compatibilidade e não deve concentrar regra de negócio.
- O Elementor pode compor páginas, mas não deve ser a fonte da lógica dos componentes.
- Todo componente deve ser compatível com PT-BR e EN-US.
- Header, navegação e footer devem ser resolvidos pelo mesmo contexto de idioma da página.
- Modelos globais PT-BR e EN-US devem permanecer independentes, sem fallback cruzado de menus ou conteúdo entre idiomas.
- Toda nova renderização deve priorizar shortcode, API PHP ou componente Core reutilizável.
- Toda dependência direta do tema deve ser tratada como temporária.
- Toda nova sprint deve evitar criar dívida de refatoração futura.
- Toda decisão estrutural deve ser documentada e linkada aos documentos mestres.

## 5. Componentes abrangidos

Esta decisão se aplica a:

- Header;
- Footer;
- Navigation Library;
- Language Switcher;
- Search;
- Dynamic Views;
- View Engine;
- Layout Foundation;
- Advertising;
- M360 Ads Manager;
- Inventory Library;
- Inline Ads Engine;
- Widgets esportivos;
- Semantic Relations;
- Community;
- futuros módulos da Plataforma Comercial M360.

## 6. Diretriz para novas sprints

Nenhuma sprint deve introduzir dependência direta de templates do tema quando existir ou puder existir um componente equivalente no M360 Core.

Toda nova sprint deve responder:

1. O componente pertence ao M360 Core?
2. Existe shortcode ou API PHP reutilizável?
3. O comportamento é compatível com PT-BR e EN-US?
4. A implementação depende do tema ou apenas se integra a ele?
5. Há documentação atualizada?
6. Há release/checklist?
7. O fluxo permite rollback sem refatoração?

## 7. Consequências

Consequências positivas:

- redução de retrabalho entre sprints;
- independência progressiva do News Portal;
- menor dependência do Elementor;
- maior consistência multilíngue;
- componentes reutilizáveis;
- evolução visual sem reescrever templates;
- maior previsibilidade de release e rollback;
- base mais segura para monetização, widgets e views dinâmicas.

Trade-offs aceitos:

- coexistência temporária entre tema, Elementor e M360 Core;
- necessidade de manter shortcodes e APIs compatíveis;
- necessidade de documentação rigorosa;
- migração gradual em vez de substituição abrupta.

## 8. Relação com Advertising e AdSense Ready

A Sprint v0.4.4.x confirma esta decisão ao mover a publicidade para componentes do M360 Core:

```text
Inventory Library → Seeder → Context Renderer → Inline Engine → Ad Slot Component → Front-end PT-BR / EN-US
```

Com isso, anúncios deixam de ser HTML solto em tema/Elementor e passam a ser renderizados por uma camada única, com:

- labels automáticas;
- wrapper semântico;
- placeholders;
- data attributes;
- CSS centralizado;
- compatibilidade futura com AdSense e Google Ad Manager.

## 9. Roadmap de independência do tema

Itens já em transição para o Core:

- Header;
- Footer;
- Navigation;
- Search;
- Dynamic Views;
- Advertising;
- Inventory Library;
- Inline Ads.

Próximas frentes:

- Universal Slot Renderer;
- Archive Ads Engine;
- Layout Engine;
- Universal Component Library;
- Theme Independence Program;
- Plataforma Comercial M360.

## 10. Referências oficiais

Este ADR deve ser lido em conjunto com:

- `docs/00-platform/M360_Platform_Architecture_v2.md`;
- `docs/00-platform/M360_Infrastructure_Architecture_v1.md`;
- `docs/00-platform/M360_Arquitetura_Ads_Manager_v1.md`;
- `docs/00-platform/M360_Inventory_Library_v1.md`;
- `docs/M360_Documentation_Index_v1.md`;
- `docs/03-releases/M360_Release_History_v2.md`;
- `docs/04-operations/M360_Core_Plugin_Publication_Workflow_v1.md`.

## 11. Regra de governança

O ADR-0007 passa a ser referência obrigatória para evoluções visuais, comerciais e estruturais do M360 Core.

Qualquer sprint que proponha nova camada de interface, template, componente visual, slot publicitário, view dinâmica ou integração com tema deve verificar aderência a este ADR antes de iniciar codificação.
