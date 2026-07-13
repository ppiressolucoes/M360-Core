# Roadmap M360 Core — v0.5.2 a v0.5.4

## Diretriz

Evoluir navegação, descoberta e internacionalização reduzindo dependências funcionais do tema News Portal e dos widgets do Elementor.

## v0.5.2 — Multilingual Post Navigation

### Escopo

- componente `[m360_post_info]`;
- autor, categoria, publicação, horário e última atualização;
- rótulos, datas e tempo relativo em PT-BR e EN-US;
- evolução do `[m360_breadcrumb]`;
- `BreadcrumbList` e HTML semântico;
- responsividade, teclado, leitores de tela e truncamento acessível;
- migração gradual do widget Meta Data do Elementor.

### Critérios de aceite

- artigo PT-BR e EN-US sem rótulos no idioma incorreto;
- datas e tempos relativos localizados;
- links de autor e categoria preservados;
- breadcrumbs funcionais em post, busca, categoria, tag, autor e últimas notícias;
- ausência de regressão em SEO e mobile.

## v0.5.3 — M360 Search Experience

### Escopo

- `[m360_search_form variant="header|hero|compact"]`;
- pesquisa por título, conteúdo, categoria, tag e autor;
- sugestões e estados de carregamento/vazio;
- operação completa por teclado;
- rótulos e mensagens PT-BR/EN-US;
- integração com o Search Engine existente;
- telemetria somente com consentimento aplicável.

### Critérios de aceite

- formulário funcional no header, páginas e mobile;
- busca acessível com nome, instruções e foco visível;
- resultados consistentes nos dois idiomas;
- desempenho e cache sem regressão relevante.

## v0.5.4 — Header Search & Ad Orchestration

### Regra de decisão

```text
Campanha comercial elegível
        ↓
AdSense elegível
        ↓
M360 Search Experience
```

### Escopo

- preservar o inventário premium quando houver entrega válida;
- usar busca como fallback útil, sem espaço vazio;
- variantes desktop e compacta mobile;
- integração com o Universal Slot Renderer;
- evitar duplicidade, CLS e densidade publicitária excessiva.

### Critérios de aceite

- alternância previsível entre anúncio e busca;
- nenhum banner vazio ou house ad obrigatório;
- cabeçalho estável em PT-BR/EN-US e desktop/mobile;
- acessibilidade, Core Web Vitals e rastreabilidade operacional validadas.
