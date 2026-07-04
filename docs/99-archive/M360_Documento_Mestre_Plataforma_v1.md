# Mengão 360 — Documento Mestre da Plataforma

Versão: v1.0  
Status: base operacional consolidada.

## Visão

O Mengão 360 evoluiu de portal editorial para plataforma esportiva baseada em dados, publicação automática de competições e produto de engajamento por bolões.

## Arquitetura macro

API externa → ETL n8n → MariaDB / DW Esportivo → Views frontend → JSON consolidado → HTML cacheado → cache_widgets → WordPress / Elementor / News Portal → shortcodes.

## Fontes e bases

- API externa consolidada para jogos, resultados, times, status, artilharia e dados de competição.
- DW Esportivo maduro com dimensões, fatos, views frontend e cache.
- WordPress + Elementor + tema News Portal como camada de publicação.
- Bolão WC operacional como base do produto Mega Bolão 360.

## Competições e modelos validados

| Competição | Slug | Modelo | Status |
|---|---|---|---|
| FIFA World Cup 2026 | fifa-world-cup | Grupos + mata-mata jogo único | Validado |
| Copa Libertadores 2026 | copa-libertadores | Grupos + mata-mata ida/volta | Validado |
| Brasileirão Série A 2026 | campeonato-brasileiro-serie-a | Pontos corridos | Validado |

## Publicadores HTML

### HTML [1] Publicar Jogos Fases Competição

Gera o widget principal da competição, com suporte a grupos, classificação, jogos por rodada, mata-mata jogo único, mata-mata ida/volta e liga/pontos corridos.

### HTML [2] Artilharia e Estatística

Gera widgets extras de Artilharia e Estatísticas por competição e idioma.

## Padrão de widgets

Principal:
widget_competicao_<slug>_<temporada>_<idioma>

Artilharia:
widget_competicao_<slug>_<temporada>_artilharia_<idioma>

Estatísticas:
widget_competicao_<slug>_<temporada>_estatisticas_<idioma>

## Shortcode principal

[m360_competicao slug="<slug_competicao>" temporada="2026"]

O shortcode carrega o widget principal e concatena Artilharia e Estatísticas quando existirem.

## Internacionalização atual

- pt-BR como fallback padrão.
- en-US ativo.
- es-ES planejado.
- Uso de m360_i18n_publico, m360_idiomas_publicacao e m360_get_lang().
- Regra: sem ?lang → pt-BR; ?lang=en-US → en-US.

## Produto Bolão

O Bolão WC está operacional com login, palpites, bloqueio por horário, ranking, ligas, convite WhatsApp e apuração. Será base para o produto Mega Bolão 360.

## Próxima fase

1. Internacionalização geral do site Mengão 360.
2. Produto comercial Mega Bolão 360.
3. Landing page comercial e SEO.
4. Versionamento dos artefatos estáveis.
5. Refatoração HTML como sprint evolutiva acumulativa.
