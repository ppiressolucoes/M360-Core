# M360 Core Editorial v0.7.4.0.55

## Objetivo

Estabelecer uma base única do M360 Core para Portal Energia Limpa e Mengão 360, sem acoplar conteúdo, credenciais, pessoas, campanhas ou preferências entre os portais.

## Proveniência

- base de código: pacote instalado e homologado no Mengão 360, `m360-core-v0.7.4.0.54.zip`;
- SHA-256 do artefato de origem: `3117C7C4AB056BE02AEF9D1D818931120DD883DBE8D15D5292A6C78ACA8CE313`;
- complementos preservados do PEL: provider opcional de dicionário externo, runner manual de Discovery e resolução de categoria principal.

## Incluído

- Home controlada pelo Core e respectivos template, controlador, estilos e scripts;
- navegação complementar: rodapé, utilidades de cabeçalho, preloader, sidebar e links sociais;
- Newsroom, ticker, widgets editoriais, Discovery, Consent, Newsletter e demais módulos presentes na base 0.7.4.0.54;
- resolução de categoria principal em Últimas Notícias, para que uma categoria auxiliar não prevaleça sobre a editorial.

## Limites de segurança

- o Core não ativa capacidades públicas, takeover de templates, Consent, Discovery ou integrações de dados sem as opções já definidas no Site Profile de cada portal;
- o provider de dicionário externo não contém DSN, usuário, senha ou nome de banco; essas informações permanecem na integração local do PEL;
- atualização não migra posts, menus, dados de visitantes, cookies, snapshots ou tabelas.

## Homologação requerida

1. Instalar em staging de cada portal e conferir a preservação do Site Profile.
2. Conferir Home, Header, Footer, ticker, Newsroom e arquivos PT-BR/EN-US.
3. Confirmar Discovery e categoria principal no PEL, com provider externo configurado localmente.
4. Confirmar componentes esportivos e navegação no Mengão 360.
5. Executar rollback por reinstalação do ZIP anterior se qualquer capacidade pública se comportar diferente.
