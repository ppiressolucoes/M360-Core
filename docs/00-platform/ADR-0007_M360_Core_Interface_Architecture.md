# ADR-0007 — M360 Core como Camada de Interface da Plataforma

Status: Aceita

## Contexto
O tema News Portal e o Elementor atenderam à fase inicial do portal, porém apresentaram limitações para suportar uma plataforma multilíngue PT-BR/EN-US com headers, footers, navegação, busca e componentes independentes por idioma. Para garantir consistência funcional e evolutiva, o M360 Core passou a assumir progressivamente a responsabilidade pela interface da plataforma.

## Decisão
O M360 Core é a camada oficial de interface do Projeto Mengão 360.

Toda nova funcionalidade visual deverá ser implementada prioritariamente no M360 Core, utilizando componentes reutilizáveis, independentes do tema e compatíveis com internacionalização.

O tema WordPress e o Elementor passam a ser tratados como camada de compatibilidade e composição, não como origem da lógica da interface.

## Princípios
- Independência de tema.
- Independência de construtor visual.
- Componentes reutilizáveis.
- Internacionalização nativa PT-BR/EN-US.
- APIs e shortcodes estáveis.
- Evolução incremental sem refatorações estruturais.
- Compatibilidade retroativa.

## Componentes abrangidos
- Header
- Footer
- Navigation Library
- Search
- Dynamic Views
- Advertising
- Widgets esportivos
- Semantic Relations
- Futuros módulos.

## Diretriz para novas sprints
Nenhuma sprint deve introduzir dependência direta de templates do tema quando existir ou puder existir um componente equivalente no M360 Core.

## Referências
- docs/00-platform/M360_Platform_Architecture_v2.md
- docs/00-platform/M360_Arquitetura_Ads_Manager_v1.md
- docs/00-platform/M360_Inventory_Library_v1.md
- docs/03-releases/M360_Release_History_v2.md