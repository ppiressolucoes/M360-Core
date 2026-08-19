# M360 Core — Runtime compartilhado e padrão editorial

## Objetivo

Este documento estabelece o M360 Core como fonte única de evolução técnica
para o **Portal Energia Limpa (PEL)**, **Mengão 360** e futuras propriedades
editoriais WordPress. A mesma base de código é portátil; cada portal fornece
somente o seu Site Profile e as integrações explicitamente autorizadas.

## Princípios

1. **Um Core, vários portais.** Módulos, contratos, shortcodes e correções de
   segurança/evolução pertencem ao repositório `ppiressolucoes/M360-Core`.
2. **Profile, não fork.** Nome, vertical, idiomas, cores e capacidades públicas
   são configurados no Site Profile. Não se mantém um fork por tema ou nicho.
3. **Independência de tema.** O HTML e CSS do Core usam escopo `m360-*`; temas
   e Elementor compõem as páginas pela inserção de shortcodes ou widgets.
4. **Portabilidade segura.** Conteúdo, dados pessoais, campanhas, credenciais,
   segredos, snapshots e dumps não integram perfis ou pacotes portáveis.
5. **Ativação progressiva.** Cada capacidade pública exige preflight,
   homologação, rollback e autorização. Não há cutover implícito.

## Matriz operacional

| Aspecto | Mengão 360 | Portal Energia Limpa |
|---|---|---|
| Vertical | esportes | clean-energy-publisher |
| Idiomas | definidos pelo portal | pt-BR, en-US / Polylang |
| Cor primária | definida pelo Site Profile | `#FF3D00` |
| Cor secundária | definida pelo Site Profile | `#FC893C` |
| Tema e templates | preservados | Cream Magazine + Elementor preservados |
| Discovery | perfil próprio | dicionário externo PDO homologado |
| Conteúdo | isolado | isolado |

As cores não são constantes de módulo. Os componentes consomem os tokens
`--m360-primary` e `--m360-secondary` produzidos pelo Site Profile.

## Padrão editorial reutilizável

O módulo **Editorial Layout & Home** fornece contratos que podem ser usados em
qualquer página Elementor ou editor de blocos, sem takeover de template:

- ticker de últimas notícias;
- Newsroom: destaques rotativos e quatro cards complementares;
- widgets #1 a #4, com destaques e mininotícias em grades responsivas;
- widget #5, Latest News em carrossel retrato;
- cabeçalho de seção, metadados e `View all` por instância.

### Widgets por instância

O cadastro é feito em **M360 Dashboard → Widgets editoriais** e o frontend
recebe apenas o shortcode estável:

```text
[m360_editorial_widget id="home-en-newsroom"]
```

Para o Newsroom, as fontes são separadas:

- **Categorias dos destaques:** abastecem o carrossel principal;
- **Editorias dos cards:** abastecem os quatro cards laterais;
- ambas são filtradas pelo idioma selecionado na instância;
- quatro cards aparecem no desktop, dois no tablet e um no mobile;
- com cinco a oito posts de origem, os cards alternam em grupos;
- `avanço automático` aplica a mesma política ao Newsroom, cards e Latest
  News, pausando quando a aba não está visível ou durante interação.

Para a alteração do Dashboard surtir efeito, a página precisa usar o shortcode
da instância, e não um shortcode editorial direto com atributos próprios.

## Internacionalização

O Core consulta o idioma pela integração Polylang, sem duplicar conteúdo.
Categorias e tags são mantidas como termos WordPress traduzidos e o dicionário
de relações internas é uma integração de provider externa opcional. A ausência
do provider não bloqueia os widgets editoriais.

## Content Discovery & SEO

O módulo opera com storage próprio e relações por locale. No PEL, o provider
PDO externo foi homologado como fonte do dicionário de links internos; suas
credenciais permanecem somente no ambiente WordPress. O renderer público está
em modo prospectivo para posts novos, mantendo o legado sem alteração.

## Operação assíncrona

- WP-Cron pode ficar desabilitado quando há cron do host.
- No PEL, Discovery e publicação futura são tarefas dedicadas, para evitar que
  um backlog de Action Scheduler acione integrações não homologadas.
- MailPoet, Ads, Consent e Newsletter continuam sujeitos a homologação e
  autorização específicas; este Core não os ativa por padrão.

## Releases desta consolidação

| Versão | Entrega |
|---|---|
| 0.7.4.0.10 | runner dedicado da fila Discovery |
| 0.7.4.0.11 | tokens de branding do Site Profile |
| 0.7.4.0.12 | padrão editorial e Newsroom responsivo |
| 0.7.4.0.13–0.7.4.0.15 | fontes do Newsroom e categorias por locale |
| 0.7.4.0.16 | grade desktop de cards do Newsroom |
| 0.7.4.0.17 | política unificada de autoplay editorial |

## Manutenção futura

1. Corrigir e testar no Core, nunca em cópia local de portal.
2. Registrar release notes e o impacto em profiles/módulos.
3. Gerar pacote canônico, validar hash e homologar em ambiente controlado.
4. Atualizar a documentação operacional do portal somente com dados não
   sensíveis.
5. Promover por PR no repositório oficial; merge e release permanecem uma
   decisão explícita.
