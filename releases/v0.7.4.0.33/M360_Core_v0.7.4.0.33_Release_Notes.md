# M360 Core v0.7.4.0.33

## Correção do idioma inline

O shortcode `[m360_language_navigation]` agora exibe visualmente o idioma para o qual o visitante será direcionado:

- página PT-BR: `🇺🇸 EN`, com destino em inglês;
- página en-US: `🇧🇷 PT`, com destino em português.

O destino continua sendo a tradução vinculada pelo Polylang. Quando o objeto da prévia do Elementor não possui tradução, o botão aponta para a página inicial do outro idioma.

## Preloader do M360 Core

Foi incorporado um preloader independente do tema, com:

- máscara sólida para ocultar a montagem parcial do layout;
- indicador de três pontos e rótulo em PT-BR ou en-US;
- saída suave após o carregamento da página;
- reapresentação durante a navegação por links internos;
- proteção para cache de navegação, falhas de carregamento e preferência de movimento reduzido;
- shortcode `[m360_preloader]` para homologação pontual.

O recurso automático fica restrito às rotas públicas assumidas pelo Core e às páginas cujo conteúdo ou dados do Elementor possuem componentes `m360_*`. O preloader do News Portal continua atendendo as páginas renderizadas pelo tema.

## Ativação controlada

O preloader automático permanece desativado após a atualização. Para ativá-lo:

1. Acesse **M360 Core → Site Profile e módulos**.
2. Na seção **Navegação**, marque **Ativar preloader nas rotas públicas gerenciadas pelo M360 Core**.
3. Salve o Site Profile.
4. Para um template M360 isolado do Elementor, insira `[m360_preloader]` em um widget Shortcode.
5. Limpe os caches do Elementor, LiteSpeed e CDN.

Para rollback, desmarque a mesma opção. Não é necessário alterar os templates do Elementor.
