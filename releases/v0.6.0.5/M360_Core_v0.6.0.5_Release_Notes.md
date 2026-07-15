# M360 Core v0.6.0.5 — Post Language Switch & EN Sticky Menu Hotfix

## Resultado

Estabiliza a troca PT-BR/EN-US durante a leitura individual de posts e transfere ao M360 Core o controle do menu fixo do cabeçalho EN-US.

## Capacidades consolidadas

- resolução do post traduzido diretamente pelo vínculo publicado do Polylang;
- navegação completa para a URL canônica da tradução, substituindo conteúdo, cabeçalho e rodapé;
- ocultação do seletor quando o post não possui tradução correspondente;
- normalização automática do botão legado `.m360-lang-toggle`;
- shortcode independente `[m360_language_switcher]`;
- controle nativo de sticky pela classe `.m360-header-topbar` usada no Elementor;
- CSS responsivo do Elementor preservado sem o JavaScript manual de sticky;
- proteção contra conflito com a implementação legada `.m360-primary-menu-sticky`.

## Homologação

- menu superior EN-US fixo durante a rolagem: aprovado;
- troca PT-BR/EN-US com substituição dos modelos de topo e rodapé: aprovada;
- desktop, janela anônima e fluxo real de leitura de post: aprovados;
- pacote canônico `m360-core/m360-core.php` confirmado;
- instalação duplicada e inativa preexistente removida do ambiente;
- ocultação em post sem tradução mantida como verificação pós-entrega assim que houver novo conteúdo sem vínculo Polylang.
