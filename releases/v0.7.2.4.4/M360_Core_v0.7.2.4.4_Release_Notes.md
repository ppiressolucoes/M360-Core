# M360 Core v0.7.2.4.4 — Locale-safe Renderer Canary

## Entrega

- títulos padrão localizados para PT-BR e EN-US;
- origem resolvida pelo post público corrente em templates compartilhados;
- validação fail-closed do locale de cada post e termo de destino;
- `[m360_discovery_canary type="read_more"]` para chamada intermediária;
- `[m360_discovery_canary type="related_posts"]` com miniaturas;
- `[m360_discovery_canary type="topics"]` para tags e categorias relacionadas;
- CSS responsivo próprio do renderer;
- nenhum `the_content`, auto append, fallback legado ou escrita em visita pública.

## Homologação

Validar os posts 7804 (PT-BR) e 7807 (EN-US) com shortcodes sem `post_id`. Confirmar títulos, destinos e conteúdo no mesmo locale, além da ausência de duplicidade com o precursor.

## Rollback

Remover os shortcodes e reinstalar a v0.7.2.4.3. O precursor e seus dados permanecem inalterados.
