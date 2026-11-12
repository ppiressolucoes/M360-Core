# M360 Core v0.7.4.0.62 — Acoplagem do Portal Energia Limpa

## Entregas

- paleta global dos componentes M360 derivada do `Site Profile` (`appearance.primary_color` e `appearance.secondary_color`);
- navegação principal en-US aceita `menu_en` e também `menu_pt`, preservando compatibilidade com shortcodes existentes; o fallback é `EL360 Menu Principal en-US`;
- perfil PEL de cutover controlado prepara a Home por idioma e os widgets editoriais do Core, sem takeover automático;
- Cream Magazine, Elementor, Ads, Newsletter e Consent permanecem preservados até a ativação explícita do gate de Home.

## Primeiro uso no PEL

1. instalar o Core 0.7.4.0.62 e o Controlled Deployment PEL correspondente em staging;
2. aplicar o perfil PEL sem takeover para validar paleta, menus, shortcodes e widgets;
3. cadastrar/revisar widgets pt-BR e en-US;
4. habilitar o gate de takeover da Home somente após a validação visual e funcional;
5. medir regressão, registrar aceite e manter a versão anterior para rollback.

## Shortcode en-US

```text
[m360_main_navigation menu_pt="EL360 Menu Principal en-US"]
```

O atributo `menu_en` continua recomendado quando o template puder separar os idiomas:

```text
[m360_main_navigation menu_en="EL360 Menu Principal en-US"]
```
