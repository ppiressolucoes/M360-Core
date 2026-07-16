# Sprint v0.6.3 — M360 Editorial Source Connector Pilot

Status: pronta para desenvolvimento
Componente: M360 Editorial Source Connector
Fonte piloto: NETFLA

## 1. Objetivo da sprint

Entregar uma versão básica, funcional e segura do Editorial Source Connector capaz de:

1. receber uma chamada autenticada do n8n;
2. consultar a configuração da fonte no banco;
3. ler o RSS do NETFLA pelo WordPress;
4. retornar as três notícias mais recentes em JSON normalizado;
5. buscar o HTML bruto de uma notícia autorizada;
6. comprovar o caminho feliz em ambiente de homologação.

## 2. Escopo obrigatório

### Banco

- criar `wp_m360_sources`;
- criar `wp_m360_source_hosts`;
- instalar a fonte `netfla` de forma idempotente;
- preservar dados em reativação do plugin.

### API

- registrar `POST /wp-json/m360/v1/connector`;
- validar `X-M360-Key`;
- implementar `discover`;
- implementar `fetch`;
- produzir respostas e erros padronizados.

### Motor HTTP

- usar APIs HTTP nativas do WordPress;
- permitir somente HTTPS;
- validar hosts cadastrados;
- bloquear IPs privados, locais e link-local;
- limitar timeout, redirects e tamanho lógico da resposta;
- aplicar headers cadastrados e permitidos.

### Parser e normalização

- suportar RSS 2.0;
- normalizar `title`, `link`, `published_at` e `guid`;
- ordenar por data decrescente;
- respeitar `limit`, com máximo configurado.

### Cache

- cache de descoberta por URL/fonte;
- cache de conteúdo por URL;
- campo `cached` na resposta.

### Integração n8n

- workflow principal chama `discover`;
- `Split Out` separa `items`;
- subfluxo é chamado uma vez por item;
- `fetch` é exercitado com uma URL retornada.

## 3. Fora do escopo

- interface administrativa;
- descoberta por HTML;
- navegador automatizado;
- publicação automática;
- IA editorial;
- logs avançados e dashboard;
- múltiplos clientes e rate limit distribuído;
- suporte GraphQL.

## 4. Backlog técnico

### ESC-001 — Bootstrap do módulo

- classes isoladas;
- carregamento no runtime do M360 Core;
- ativação idempotente;
- feature flag para desligamento rápido.

### ESC-002 — Schema de dados

- tabela de fontes;
- tabela de hosts;
- índices e versão do schema;
- seed NETFLA.

### ESC-003 — Autenticação

- leitura do segredo por constante ou variável segura;
- comparação em tempo constante;
- retorno `401 unauthorized` sem exposição de detalhes.

### ESC-004 — Segurança de destino

- sanitização da URL;
- HTTPS obrigatório;
- host na allowlist;
- resolução DNS e rejeição de IPs não públicos;
- revalidação após redirect.

### ESC-005 — Operação discover

- carregar configuração;
- buscar feed;
- interpretar RSS;
- ordenar;
- limitar;
- devolver contrato normalizado.

### ESC-006 — Operação fetch

- validar URL da notícia;
- buscar conteúdo bruto;
- validar status e content type;
- devolver HTML sem transformação editorial.

### ESC-007 — Cache

- transient de descoberta;
- transient de conteúdo;
- chave por fonte, operação e URL;
- indicação de cache na resposta.

### ESC-008 — Homologação n8n

- executar caminho feliz;
- registrar payloads sem segredos;
- comprovar três itens;
- comprovar HTML bruto;
- confirmar chamada do subfluxo.

### ESC-009 — Testes negativos mínimos

- chave inválida;
- fonte inativa;
- host não permitido;
- URL `localhost`;
- limite acima do máximo;
- feed indisponível.

## 5. Contrato do caminho feliz

### Requisição 1 — descoberta

```http
POST /wp-json/m360/v1/connector
X-M360-Key: ***
Content-Type: application/json
```

```json
{
  "operation": "discover",
  "source": "netfla",
  "limit": 3
}
```

### Validações

- HTTP 200;
- `success=true`;
- `count=3`;
- `items` possui três objetos;
- todos possuem título e link não vazios;
- links pertencem ao host autorizado;
- datas estão em formato ISO 8601 quando disponíveis.

### Requisição 2 — conteúdo bruto

```json
{
  "operation": "fetch",
  "source": "netfla",
  "url": "{{ items[0].link }}"
}
```

### Validações

- HTTP 200;
- `success=true`;
- `status_code=200`;
- `content_type` indica HTML;
- `content` não está vazio;
- a URL final permanece em host autorizado.

## 6. Matriz de homologação

| ID | Cenário | Resultado esperado |
|---|---|---|
| H-01 | discover válido | três notícias normalizadas |
| H-02 | repetição dentro do TTL | `cached=true` |
| H-03 | fetch do primeiro item | HTML bruto não vazio |
| H-04 | chave inválida | HTTP 401 |
| H-05 | fonte inexistente | HTTP 404 ou erro `source_not_found` |
| H-06 | host não autorizado | HTTP 403 e `host_not_allowed` |
| H-07 | endereço privado | bloqueio antes da requisição externa |
| H-08 | limit 1000 | valor reduzido/rejeitado conforme contrato |
| H-09 | feed indisponível | erro padronizado sem fatal do WordPress |

## 7. Evidências obrigatórias

- captura ou export da execução do n8n;
- resposta JSON de `discover` com dados sensíveis removidos;
- resposta JSON de `fetch` com conteúdo truncado na evidência;
- log do teste de cache;
- log do bloqueio SSRF;
- resultado do lint PHP;
- checklist de rollback.

## 8. Rollback

1. desligar feature flag do ESC;
2. desativar a rota no boot do módulo;
3. preservar tabelas e configuração para análise;
4. restaurar a versão homologada anterior do M360 Core;
5. reativar o fluxo n8n anterior ou pausar a fonte.

## 9. Critério de saída

A sprint pode ser promovida para release somente quando todos os cenários H-01 a H-08 forem aprovados. H-09 pode ser aprovado por resposta controlada, sem exigir disponibilidade contínua da origem.

## 10. Próximas etapas

### v0.6.3.1 — Robustez

- stale cache;
- logs operacionais;
- erros detalhados;
- testes automatizados.

### v0.6.3.2 — Administração

- cadastro e edição de fontes no painel;
- teste de conexão;
- status e última coleta.

### v0.6.4 — Novos adaptadores

- Atom;
- JSON;
- regras de descoberta HTML controladas.

### v0.7.0 — Observabilidade e escala

- métricas;
- health endpoint;
- rate limiting;
- filas e execução assíncrona.
