# M360 Editorial Source Connector v1

Status: desenvolvimento aprovado — piloto planejado
Projeto: Mengão 360 | DW Esportivo
Produto: M360 Core
Componente: Editorial Source Connector (ESC)

## 1. Propósito

O M360 Editorial Source Connector é a camada de aquisição técnica entre o n8n e fontes editoriais externas. Seu contrato central é:

> Conector único interpreta a configuração da fonte e fornece lista normalizada ou conteúdo bruto.

O componente existe para fontes que bloqueiam o IP do n8n, exigem cabeçalhos específicos, possuem feeds em formatos distintos ou precisam de cache e controle centralizado.

## 2. Limites de responsabilidade

O ESC executa:

- autenticação do cliente;
- consulta da configuração da fonte no banco;
- validação da URL e do host autorizado;
- requisição HTTP externa;
- leitura de RSS/Atom/JSON no modo de descoberta;
- normalização mínima de título, link, data e identificador;
- entrega de HTML ou outro conteúdo bruto no modo de coleta;
- cache, timeout, limites e telemetria.

O ESC não executa:

- reescrita editorial;
- interpretação semântica do artigo;
- geração por IA;
- deduplicação editorial definitiva;
- publicação no WordPress;
- seleção de pauta.

Essas responsabilidades permanecem no n8n, no DW Esportivo e na camada editorial.

## 3. Operações oficiais

### 3.1 `discover`

Localiza as publicações mais recentes da fonte cadastrada.

Entrada mínima:

```json
{
  "operation": "discover",
  "source": "netfla",
  "limit": 3
}
```

Saída mínima:

```json
{
  "success": true,
  "operation": "discover",
  "source": "netfla",
  "count": 3,
  "cached": false,
  "items": [
    {
      "title": "Título da notícia",
      "link": "https://netfla.com.br/noticias/exemplo",
      "published_at": "2026-07-15T19:35:08-03:00",
      "guid": "https://netfla.com.br/noticias/exemplo"
    }
  ]
}
```

### 3.2 `fetch`

Busca uma URL individual previamente validada e devolve o conteúdo bruto.

Entrada mínima:

```json
{
  "operation": "fetch",
  "source": "netfla",
  "url": "https://netfla.com.br/noticias/exemplo"
}
```

Saída mínima:

```json
{
  "success": true,
  "operation": "fetch",
  "source": "netfla",
  "url": "https://netfla.com.br/noticias/exemplo",
  "status_code": 200,
  "content_type": "text/html; charset=utf-8",
  "cached": false,
  "content": "<!doctype html>..."
}
```

## 4. Endpoint

```text
POST /wp-json/m360/v1/connector
```

Cabeçalhos mínimos:

```text
Content-Type: application/json
X-M360-Key: segredo configurado no servidor
```

## 5. Configuração orientada a dados

As regras específicas das fontes devem ficar em banco, evitando novas versões do plugin para inclusão ou ajuste operacional.

Tabela principal proposta:

```text
wp_m360_sources
```

Campos mínimos do piloto:

| Campo | Finalidade |
|---|---|
| `source_key` | identificador estável da fonte |
| `source_name` | nome de exibição |
| `base_url` | URL base da origem |
| `discovery_url` | feed ou endpoint de descoberta |
| `discovery_type` | `rss`, `atom` ou `json` |
| `default_limit` | quantidade padrão |
| `max_limit` | limite máximo aceito |
| `request_headers` | JSON com cabeçalhos permitidos |
| `cache_discovery_seconds` | TTL de descoberta |
| `cache_content_seconds` | TTL do conteúdo bruto |
| `timeout_seconds` | timeout por fonte |
| `is_active` | habilitação operacional |

Tabela de hosts:

```text
wp_m360_source_hosts
```

Tabela de logs:

```text
wp_m360_connector_logs
```

## 6. Segurança obrigatória

O motor deve manter regras fixas, não alteráveis pelo cadastro da fonte:

- somente `https` no piloto;
- bloqueio de `localhost`, loopback, redes privadas e link-local;
- bloqueio de `file://`, `ftp://` e protocolos não HTTP;
- validação do host inicial e de cada redirecionamento;
- limite global de resposta;
- timeout máximo global;
- limite máximo global de itens;
- autenticação por header;
- sanitização de entradas;
- ausência de endpoint público de proxy genérico.

## 7. Integração com n8n

### Descoberta

```text
Schedule Trigger
  → Configuração da fonte
  → HTTP Request: operation=discover
  → Split Out: items
  → Deduplicação
  → Execute Sub-workflow
```

### Extração

```text
Sub-workflow recebe link
  → tenta acesso direto quando configurado
  → em falha ou modo connector, chama operation=fetch
  → recebe conteúdo bruto
  → extrai e trata no n8n/DW Esportivo
```

## 8. Piloto oficial

A primeira homologação será feita com a fonte `netfla`.

Configuração de referência:

```text
source_key: netfla
discovery_url: https://netfla.com.br/noticias/rss/
discovery_type: rss
default_limit: 3
max_limit: 10
cache_discovery_seconds: 300
cache_content_seconds: 3600
timeout_seconds: 10
hosts: netfla.com.br, www.netfla.com.br
```

## 9. Critérios de aceite do piloto

O piloto será considerado funcional quando:

1. a rota responder somente com autenticação válida;
2. `discover` retornar exatamente os três itens mais recentes do feed;
3. cada item contiver `title`, `link`, `published_at` e `guid`;
4. o segundo acesso dentro do TTL sinalizar `cached=true`;
5. `fetch` retornar o HTML bruto de um item descoberto;
6. URL fora da allowlist for rejeitada;
7. URL local ou privada for rejeitada;
8. falhas retornarem códigos padronizados;
9. o n8n receber os três itens e chamar o subfluxo uma vez por item;
10. nenhuma publicação for realizada durante a homologação técnica.

## 10. Caminho feliz de homologação

```text
1. Fonte netfla ativa no banco
2. n8n envia discover com limit=3
3. ESC autentica e valida a fonte
4. ESC consulta o RSS
5. ESC normaliza e ordena os itens
6. ESC devolve três notícias
7. n8n separa os itens
8. n8n seleciona o primeiro link
9. n8n envia fetch
10. ESC valida o host e retorna HTML bruto
11. n8n confirma conteúdo não vazio
12. execução é registrada como homologada
```

## 11. Requisitos não funcionais do piloto

- PHP 8.0 ou superior;
- WordPress 6.0 ou superior;
- resposta de cache em até 500 ms no ambiente de homologação;
- timeout externo padrão de 10 s;
- payload bruto limitado a 2 MB;
- logs sem chave secreta e sem conteúdo integral da matéria;
- código isolado em namespace/classes do M360 Core;
- rollback por desativação do módulo e remoção segura das rotas.

## 12. Evolução planejada

### Fase P0 — documentação e contrato

- ADR aprovado;
- contrato REST documentado;
- sprint e critérios de homologação publicados.

### Fase P1 — piloto básico funcional

- tabelas mínimas;
- autenticação por `X-M360-Key`;
- operações `discover` e `fetch`;
- suporte RSS;
- cache via transients;
- fonte NETFLA cadastrada;
- teste do caminho feliz pelo n8n.

### Fase P2 — robustez operacional

- logs persistentes;
- stale cache em falha da origem;
- limites de redirecionamento e tamanho;
- códigos de erro completos;
- testes automatizados de segurança e contrato.

### Fase P3 — gestão administrativa

- tela de fontes;
- teste de conectividade;
- ativação/desativação;
- edição de headers permitidos;
- visualização de status e última execução.

### Fase P4 — novos formatos

- Atom;
- JSON configurável;
- descoberta HTML por adaptadores controlados;
- REST, GraphQL e sitemap quando justificados.

### Fase P5 — observabilidade e escala

- health check;
- métricas de latência, cache, 403, 429 e timeout;
- rate limiting por cliente e fonte;
- filas e processamento assíncrono quando necessário.

## 13. Definition of Done do piloto

- código revisado em Pull Request;
- lint PHP sem erros;
- migração idempotente;
- segredo fora do repositório;
- fonte NETFLA homologada;
- evidência da resposta `discover` com três itens;
- evidência da resposta `fetch` com conteúdo bruto;
- evidência de bloqueio SSRF;
- documentação e índice atualizados;
- rollback testado;
- nenhuma alteração direta em tag homologada.
