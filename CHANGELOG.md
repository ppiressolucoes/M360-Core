# Changelog — M360 Core

## [v0.7.0] — Publisher Platform Foundation

- adiciona contrato formal para módulos internos;
- adiciona registro com dependências, feature flags, ciclo de vida, permissões, configurações, assets e schema por módulo;
- adiciona boot ordenado, detecção de dependências circulares e health report;
- adiciona Site Profile portável, validado e sem dados pessoais ou credenciais;
- adiciona importação e exportação segura do Site Profile em JSON, com validação de versão e campos obrigatórios;
- adiciona painel M360 Platform com diagnóstico de ambiente, módulos e plugins precursores;
- preserva integralmente Home Editorial, Semantic Relations e os recursos da baseline v0.6.5.4;
- não executa absorção funcional nem migração de dados dos plugins precursores.

## [v0.6.5.4] — Newsletter UX Consolidation

- substitui a marca textual do rodapé por ícone informativo acessível;
- reforça contraste, legibilidade e espaçamento da mensagem de cancelamento;
- mantém mensagens próprias em PT-BR e EN-US;
- consolida a linha Newsletter Subscription Placement & UX após homologação.

## [v0.6.5.3] — Newsletter Cancellation Fine Print

- adiciona ao rodapé do formulário a informação de que o usuário pode cancelar quando quiser;
- disponibiliza a mensagem em PT-BR e EN-US.

## [v0.6.5.2] — Newsletter EN-US Localization Hotfix

- adiciona textos configuráveis EN-US para título, descrição, consentimento e CTA;
- traduz rótulos de nome e e-mail no ambiente inglês;
- envia o idioma da página ao endpoint REST;
- traduz respostas de sucesso, validação, antispam, limite e indisponibilidade;
- preserva o posicionamento após Related News e Related Topics.

## [v0.6.5.1] — Placement Order & Settings Preservation Hotfix

- preserva configurações de Delivery Readiness ao salvar o formulário;
- altera a atualização para mesclar campos com a opção existente;
- move a inserção automática para o final da cadeia `the_content`, após blocos relacionados que utilizem o mesmo filtro;
- mantém a origem `article_end` e todas as variantes da v0.6.5.

## [v0.6.5] — Newsletter Subscription Placement & UX

- variantes `card`, `compact`, `inline` e `footer` no shortcode;
- inserção automática opcional ao final dos artigos;
- título, descrição, CTA e período de ocultação configuráveis;
- origens normalizadas para home, artigo, sidebar, rodapé, arquivo e uso manual;
- métricas administrativas de registros por origem;
- cookie funcional para ocultação temporária após solicitação bem-sucedida;
- CSS responsivo e estados acessíveis preservados.

## [v0.6.4] — Newsletter Delivery Readiness

- checklist operacional de sete verificações para prontidão de entrega;
- cadastro do remetente e responsável editorial;
- declarações administrativas de DKIM e DMARC configurados;
- registro UTC do último teste de envio aprovado, válido por 30 dias;
- alertas de erros do provedor nas últimas 24 horas e bloqueios nos últimos sete dias;
- evento de auditoria `delivery_readiness_updated`;
- documentação do fluxo editorial e de homologação.

## [v0.6.3] — Newsletter Configuration & Form Hardening

- seleção administrativa da lista MailPoet, com migração padrão para ID `3`;
- texto e versão de consentimento configuráveis;
- limites por IP e janela de bloqueio configuráveis;
- honeypot e tempo mínimo de preenchimento auditados;
- bloqueio de duplo envio, estado `aria-busy` e foco acessível na resposta;
- validação da lista MailPoet antes de salvar;
- integração preservada com Double Opt-In, auditoria e sincronização da v0.6.2.

## [v0.6.2] — Newsletter Operations & Audit

- painel administrativo com métricas e diagnóstico do MailPoet/lista ID `3`;
- sincronização manual protegida por capacidade e nonce;
- trilha append-only de eventos com e-mail mascarado e hash SHA-256;
- visibilidade da última/próxima execução e resultado do WP-Cron;
- detecção de falhas do provedor e eventos de mudança de estado;
- retenção padrão de auditoria por 365 dias e limpeza de tokens expirados;
- schema Newsletter atualizado para a versão `2`.

## [v0.6.1.3] — Newsletter Foundation Consolidation

- formulário próprio `[m360_newsletter_form]` e adaptador desacoplado do MailPoet;
- lista MailPoet ID `3` com Double Opt-In gerenciado pelo provedor;
- auditoria do consentimento e sincronização de `pending`, `confirmed`, `unsubscribed` e `blocked`;
- reinscrição, rate limit por e-mail/IP e eventos somente em mudanças reais de estado;
- remoção do descadastro REST baseado somente em e-mail;
- consolidação das correções `v0.6.1.1` e `v0.6.1.2`.

## [v0.6.0.5] — Post Language Switch & EN Sticky Menu Hotfix

- incorpora ao M360 Core o seletor de idioma baseado no vínculo real do Polylang;
- navega sempre para a tradução publicada correspondente do post;
- oculta o seletor em posts sem tradução vinculada, evitando troca apenas do cabeçalho e rodapé;
- normaliza o componente legado existente e oferece o shortcode `[m360_language_switcher]`;
- corrige o menu fixo EN-US ao reconhecer a classe real `.m360-header-topbar` usada pelo Elementor;
- mantém compatibilidade com o script legado aplicado à classe `.m360-primary-menu-sticky`.

## [v0.6.0.4] — Compact Cookie Settings Launcher

- reduz o tamanho do botão permanente de preferências;
- adota os rótulos curtos `Ajustar cookies` e `Cookie settings`;
- preserva o posicionamento inferior esquerdo e a hierarquia do modal.

## [v0.6.0.3] — Consent Modal Proportion & Launcher Position Hotfix

- reduz e limita o modal de preferências de forma proporcional ao viewport;
- centraliza o painel e adiciona rolagem interna apenas quando necessária;
- compacta o espaçamento das categorias sem prejudicar a legibilidade;
- move o botão permanente de configurações para o canto inferior esquerdo;
- evita concorrência visual com a âncora de retorno ao topo.

## [v0.6.0.2] — Consent UI Contrast & Layering Hotfix

- isola os botões da interface de consentimento das regras visuais do tema;
- restaura os rótulos de rejeitar, gerenciar e cancelar em PT-BR e EN-US;
- garante contraste explícito dos botões primários e secundários;
- posiciona a camada de consentimento acima do seletor flutuante de idiomas;
- preserva a disposição responsiva dos controles no mobile.

## [v0.6.0.1] — Consent Frontend Initialization Hotfix

- corrige a ordem de inicialização entre o JavaScript e o HTML do banner no `wp_footer`;
- aguarda o DOM completo antes de localizar e ativar a interface de consentimento;
- antecipa a renderização do componente para compatibilidade com a fila de scripts do WordPress;
- renomeia o modo para `Interface própria M360 — homologação controlada`.

## [v0.6.0] — M360 Privacy & Consent Foundation

- contrato de consentimento independente de fornecedor;
- categorias normalizadas e APIs PHP/JavaScript;
- Consent Mode v2 com negação padrão e atualização após decisão;
- central multilíngue local para homologação;
- integração opcional com AdSense/GAM e diagnóstico administrativo;
- operação desativada por padrão e sem alegação de certificação CMP.

## [v0.5.5.1] — Breadcrumb Mobile Overflow Hotfix

- remoção da rolagem horizontal interna no breadcrumb em telas pequenas;
- quebra responsiva do título da página atual, preservando a leitura dentro do viewport;
- nova versão de assets para invalidar caches do navegador e de CDN.

## [v0.5.5] — Breadcrumb Navigation UX

- ajuste de homologação mobile: remoção da rolagem horizontal interna e quebra responsiva do título atual;
- hierarquia real de páginas, categorias e tipos de conteúdo no shortcode `[m360_breadcrumb]`;
- categoria primária do Yoast utilizada quando disponível, com filtro próprio para integrações futuras;
- contextos multilíngues para post, página, busca, categoria, tag, autor, data, arquivo e página 404;
- ícone de início, foco visível, item atual truncado de forma segura e navegação horizontal em telas pequenas;
- HTML semântico com `nav`, lista ordenada, `aria-label` e `aria-current`;
- `BreadcrumbList` em JSON-LD, emitido uma única vez por requisição e desativável por atributo ou filtro;
- compatibilidade preservada com Elementor, Polylang, Yoast SEO e o shortcode existente.

## [v0.5.4.3] — Current Post Exclusion

- exclusão automática do artigo atualmente aberto no componente `[m360_latest_news]`;
- comportamento aplicado aos layouts `list`, `compact` e `sidebar` em páginas individuais de posts;
- quantidade configurada preservada com o próximo post elegível da consulta;
- paginação recalculada sem contabilizar o artigo em leitura;
- chave de cache isolada pelo ID do post atual para impedir resultados cruzados.

## [v0.5.4.2] — Latest News List UX

- primeiro post ampliado como destaque editorial no layout `list`;
- título em negrito restrito ao primeiro post em destaque;
- títulos dos demais posts em peso regular;
- paginação opcional e multilíngue no rodapé do componente;
- navegação paginada por `m360_news_page`, sem conflito com as rotas do tema;
- compatibilidade preservada: paginação desativada por padrão e modo sidebar inalterado.
## [v0.5.4.1] — Latest News Sidebar Mode

- nova variante `sidebar` para `[m360_latest_news]`;
- cards compactos e uniformes, sem destaque ampliado para a primeira notícia;
- títulos das notícias em peso regular no modo sidebar para leitura visual mais suave;
- novo atributo `show_ads` para controlar a inserção automática do slot `latest-inline`;
- contrato recomendado para barras laterais: `[m360_latest_news layout="sidebar" show_ads="false"]`;
- compatibilidade preservada: o layout `list` e a publicidade continuam ativos por padrão.
- empacotamento Windows corrigido para preservar `m360-core/m360-core.php` e caminhos ZIP compatíveis com Linux.

## [v0.5.4] — Header Search & Ad Orchestration

- novo shortcode `[m360_header_orchestrator]` para o cabeçalho;
- prioridade determinística: campanha elegível → AdSense elegível → busca → recolher;
- novo slot exclusivo `header-adsense` com filtro estrito de provedor;
- busca multilíngue homologada como fallback útil;
- diagnóstico administrativo em `M360 Ads > Header Delivery`;
- atualização automática do inventário publicitário para o novo slot.

## [v0.5.3.3] — Minimal Search Hero

- título e subtítulo desativados por padrão no Search Hero;
- campo de busca passa a ser o único elemento informativo do box;
- redução das margens internas, altura, raio e sombra do componente;
- opção `show_intro="true"` preservada para usos editoriais futuros.

## [v0.5.3.2] — Elementor Search Column Width Hotfix

- identificação automática do widget hospedeiro do shortcode no Elementor;
- largura integral e comportamento flexível aplicados somente ao widget da busca;
- correção do Search Hero compactado e alinhado à direita no cabeçalho PT-BR.

## [v0.5.3.1] — Search Hero UX Polish

- remoção do timbre superior para uma composição mais limpa;
- redução das margens internas e da altura do bloco hero;
- botão de busca incorporado ao campo de entrada;
- alinhamento central do conteúdo em telas móveis;
- descrição padrão revisada para `Pesquise e encontre conteúdos em todo o portal.`.

## [v0.5.3] — M360 Search Experience

- novo shortcode `[m360_search_form]` nas variantes `hero`, `header` e `compact`;
- textos, destino e mensagens próprios para PT-BR e EN-US;
- campo acessível, navegação por teclado e botão de busca responsivo;
- bloqueio de consultas vazias, inclusive termos compostos apenas por espaços;
- formulário compacto integrado aos resultados e variante hero no estado vazio.

## [v0.5.2.2] — Multilingual Date Archive

- arquivos por dia, mês e ano incorporados ao M360 Core;
- títulos, descrições, breadcrumb, estado vazio e paginação próprios em PT-BR e EN-US;
- cards responsivos e slot `archive-inline` integrados ao arquivo por data;
- remoção da dependência do título de arquivo padrão do tema.

## [v0.5.2.1] — Post Info Avatar Cache Hotfix

- moldura circular própria com recorte do avatar e neutralização de estilos herdados do tema;
- atualização da versão dos assets para invalidar caches LiteSpeed, CDN e navegador.

## [v0.5.2] — Multilingual Post Navigation

- novo componente `[m360_post_info]` para substituir progressivamente o widget Meta Data do Elementor;
- autor com avatar e link, categoria, publicação, horário e última atualização;
- avatar com recorte circular resistente aos estilos do tema e data vinculada ao arquivo diário;
- rótulos e textos relativos próprios para PT-BR e EN-US;
- controles individuais de exibição, marcação semântica, acessibilidade e layout responsivo.

## [v0.5.1] — AdSense Approval Readiness

- auditoria real de slots, campanhas elegíveis e conteúdo disponível;
- métricas de cobertura e prontidão para entrega;
- recolhimento completo de slots vazios no front-end;
- placeholders preservados apenas no inventário administrativo;
- checklist editorial, institucional, mobile, consentimento e `ads.txt`.

## [v0.5.0] — Ads Manager Slot Management UX

- gestão de vínculos em cartões agrupados por contexto;
- filtros por nome, contexto, runtime e ocupação;
- indicadores visuais Livre/Ocupado e alterações pendentes;
- botão único para salvar todos os vínculos;
- persistência em lote com validação e transação de banco;
- interface responsiva para desktop e mobile.

## [v0.4.4.7] — Slot Runtime Mapping & Diagnostics

- `article-inline-1` canônico após o segundo parágrafo, com fallback legado.
- diagnóstico de slots automáticos, manuais, planejados e legados.
- mapeamento dos consumidores de busca, categoria, tag, autor, últimas notícias e arquivos.

Todas as mudanças relevantes do M360 Core serão registradas neste arquivo.

## [v0.4.4.6] — Campaign CRUD Hotfix

Status: pronta para build e homologação.

### Corrigido

- erro fatal ao editar campanhas com datas inicial/final nulas;
- validação de título, datas, período, enums e prioridade;
- tratamento de falhas do banco em criação, atualização e exclusão;
- validação da existência de campanhas e slots antes de vincular;
- mensagens administrativas de sucesso e erro para o CRUD.

## [v0.4.4.5] — M360 Universal Slot Renderer

Status: baseline estável homologada em PT-BR e EN-US.

### Consolidado

- `M360_Slot_Renderer` e API pública `m360_render_ad_slot()`.
- APIs e shortcodes históricos redirecionados ao renderer universal.
- Inline Ads Engine, Archive Ads Engine e Context Renderer integrados ao pipzם�����k�w��`$allowed = ['schema_version','site_key','site_name','vertical','default_locale','supported_locales'];
        $unknown = array_diff(array_keys($decoded), $allowed);
        if ($unknown) {
            return new WP_Error('m360_profile_keys', 'O perfil contém campos não permitidos: ' . implode(', ', $unknown));
        }
        if ((int) ($decoded['schema_version'] ?? 0) !== self::SCHEMA_VERSION) {
            return new WP_Error('m360_profile_schema', 'Versão de schema do Site Profile incompatível.');
        }
        $required = ['site_key','site_name','vertical','default_locale','supported_locales'];
        $missing = array_diff($required, array_keys($decoded));
        if ($missing) {
            return new WP_Error('m360_profile_required', 'O perfil não contém todos os campos obrigatórios.');
        }
        self::update($decoded);
        return self::get();
    }

    public static function export_json(): string
    {
        return (string) wp_json_encode(self::get(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private static function normalize_locale(string $locale): ?string
    {
        $locale = str_replace('_', '-', trim($locale));
        if (!preg_match('/^[a-zA-Z]{2,3}(?:-[a-zA-Z]{2})?$/', $locale)) { return null; }
        $parts = explode('-', $locale, 2);
        return strtolower($parts[0]) . (isset($parts[1]) ? '-' . strtoupper($parts[1]) : '');
    }
}
