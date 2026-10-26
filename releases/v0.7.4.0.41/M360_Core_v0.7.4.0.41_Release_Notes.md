# M360 Core v0.7.4.0.41

## Menus padronizados no Footer

Esta versão adiciona o shortcode genérico `[m360_footer_menu]`, que renderiza um menu nativo do WordPress com título e linha inferior na cor primária do portal. A edição dos itens, links e ordem continua centralizada em **Aparência > Menus**.

### Aplicação no Footer atual

```text
[m360_footer_menu menu="Footer Categorias" title="Categorias" surface="dark"]
[m360_footer_menu menu="Footer Competições" title="Competições" surface="dark"]
[m360_footer_menu menu="MNU SUPORTE" title="Institucional" surface="dark"]
```

O alias `[m360_navigation_menu]` aceita os mesmos atributos.

### Atributos

- `menu`: nome, slug ou ID do menu cadastrado no WordPress; obrigatório.
- `title`: título público; quando vazio, usa o nome do menu.
- `surface`: `dark` para Footer escuro ou `light` para superfícies claras.
- `primary`: cor do sublinhado, foco e hover; padrão `#d71920`.
- `secondary`: cor secundária reservada para integrações do tema; padrão `#b81218`.
- `depth`: níveis exibidos do menu, de 1 a 3; padrão 1.

### Comportamento

- não cria cópias dos menus nem fixa seus itens no código;
- atualizações feitas em Aparência > Menus aparecem automaticamente no Footer;
- não exige HTML ou CSS manual no Elementor;
- retorna vazio quando o menu informado não existe ou não contém itens.
