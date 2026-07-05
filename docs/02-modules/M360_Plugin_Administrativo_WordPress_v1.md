# Mengão 360 — Plugin Administrativo WordPress

Versão: v1.0  
Finalidade: referência para o plugin/painel administrativo WordPress do Mengão 360.

## 1. Objetivo

O plugin administrativo WordPress deve ser a camada operacional do portal Mengão 360, centralizando acompanhamento, auditoria e ações administrativas sobre:

- Competições publicadas.
- Widgets em `cache_widgets`.
- Fluxos n8n.
- Bolões.
- Rankings.
- Artilharia e estatísticas.
- Futuro produto comercial Mega Bolão 360.

O plugin não substitui o DW Esportivo nem o n8n. Ele funciona como painel de controle dentro do WordPress.

## 2. Bases existentes

O plugin deve considerar como base consolidada:

- API externa de dados esportivos.
- DW Esportivo em MariaDB.
- ETL em n8n.
- Publicador HTML [1] para widgets principais.
- Publicador HTML [2] para Artilharia e Estatísticas.
- Tabela `cache_widgets`.
- Shortcode `[m360_competicao]`.
- WordPress + Elementor + tema News Portal.
- Bolão WC operacional.

## 3. Áreas administrativas recomendadas

Menu principal:

```text
Mengão 360
```

Submenus sugeridos:

```text
Dashboard
Competições
Widgets
Bolões
Ligas
Usuários do Bolão
Resultados
Rankings
Logs / Auditoria
Configurações
```

## 4. Dashboard

Indicadores recomendados:

- Total de competições ativas.
- Total de widgets publicados.
- Widgets ausentes.
- Widgets com HTML pequeno/vazio.
- Widgets desatualizados.
- Última carga ETL de jogos.
- Última carga ETL de artilharia.
- Última geração HTML [1].
- Última geração HTML [2].
- Total de bolões ativos.
- Total de participantes.
- Jogos pendentes de apuração.
- Rankings atualizados.

## 5. Painel de Competições

Deve listar:

- ID da competição.
- Nome.
- Slug.
- Código.
- Temporada.
- Modelo de publicação.
- Total de jogos.
- Total de times.
- Widgets principais publicados.
- Widgets extras publicados.
- Idiomas disponíveis.
- Shortcode da página.

Competições já validadas:

| ID | Competição | Slug | Modelo |
|---:|---|---|---|
| 1 | Brasileirão Série A | campeonato-brasileiro-serie-a | Pontos corridos |
| 11 | Copa Libertadores | copa-libertadores | Grupos + mata-mata ida/volta |
| 13 | FIFA World Cup | fifa-world-cup | Grupos + mata-mata jogo único |

## 6. Painel de Widgets

Padrão de widgets:

```text
widget_competicao_<slug>_<temporada>_<idioma>
widget_competicao_<slug>_<temporada>_artilharia_<idioma>
widget_competicao_<slug>_<temporada>_estatisticas_<idioma>
```

Ações desejáveis:

- Listar widgets.
- Mostrar tamanho do HTML.
- Mostrar data de atualização.
- Filtrar por competição.
- Filtrar por idioma.
- Filtrar por tipo: principal, artilharia, estatísticas.
- Detectar widgets faltantes.
- Copiar shortcode.
- Mostrar link da página.
- Acionar regeneração via n8n futuramente.

## 7. Painel de Bolões

Funções recomendadas:

- Listar bolões ativos.
- Listar ligas públicas/privadas.
- Listar usuários participantes.
- Ver palpites por jogo.
- Ver resultados apurados.
- Ver ranking geral.
- Ver ranking por liga.
- Reprocessar ranking.
- Reprocessar jogo.
- Auditar divergências de resultado.
- Gerenciar convites.

## 8. Mega Bolão 360

Quando o produto comercial avançar, o plugin deve apoiar:

- Planos Free/Pago.
- Limites por plano.
- Bolões criados por usuário.
- Participantes por bolão.
- Status de assinatura.
- Recursos habilitados.
- Métricas de conversão.
- Página pública do bolão.
- Integração futura com pagamento.

## 9. Integração com n8n

Arquitetura futura:

```text
WordPress Admin
→ botão administrativo
→ webhook protegido n8n
→ execução workflow
→ grava log
→ retorna status
```

Ações possíveis:

- Rodar ETL [1] Carga FATO Jogos.
- Rodar HTML [1] Publicar Jogos Fases Competição.
- Rodar ETL [2] Carga FATO Artilharia.
- Rodar HTML [2] Artilharia e Estatística.
- Recalcular ranking.
- Conciliar resultados.
- Regenerar cache.

## 10. SQLs úteis de auditoria

### Widgets por competição

```sql
SELECT
    widget_nome,
    origem,
    CHAR_LENGTH(html) AS tamanho_html,
    atualizado_em,
    created_at
FROM cache_widgets
WHERE widget_nome LIKE 'widget_competicao_%'
ORDER BY widget_nome;
```

### Widgets pequenos ou vazios

```sql
SELECT
    widget_nome,
    CHAR_LENGTH(html) AS tamanho_html,
    atualizado_em
FROM cache_widgets
WHERE widget_nome LIKE 'widget_competicao_%'
  AND (
        html IS NULL
        OR CHAR_LENGTH(html) < 1000
      )
ORDER BY atualizado_em DESC;
```

### Resumo por tipo de widget

```sql
SELECT
    CASE
        WHEN widget_nome LIKE '%_artilharia_%' THEN 'ARTILHARIA'
        WHEN widget_nome LIKE '%_estatisticas_%' THEN 'ESTATISTICAS'
        ELSE 'PRINCIPAL'
    END AS tipo_widget,
    COUNT(*) AS qtd_widgets,
    MIN(CHAR_LENGTH(html)) AS menor_html,
    MAX(CHAR_LENGTH(html)) AS maior_html,
    MAX(atualizado_em) AS ultima_atualizacao
FROM cache_widgets
WHERE widget_nome LIKE 'widget_competicao_%'
GROUP BY
    CASE
        WHEN widget_nome LIKE '%_artilharia_%' THEN 'ARTILHARIA'
        WHEN widget_nome LIKE '%_estatisticas_%' THEN 'ESTATISTICAS'
        ELSE 'PRINCIPAL'
    END
ORDER BY tipo_widget;
```

## 11. Segurança

Regras mínimas:

- Restringir funções sensíveis a `manage_options`.
- Usar nonce em ações administrativas.
- Sanitizar parâmetros.
- Proteger webhooks do n8n com token.
- Não expor credenciais no front-end.
- Registrar ações manuais em log.

## 12. Roadmap do plugin administrativo

### Sprint Admin 1 — Painel de Widgets

- Listar widgets.
- Exibir tamanho e atualização.
- Detectar widgets ausentes.
- Copiar shortcode.
- Exibir links de página.

### Sprint Admin 2 — Painel de Competições

- Listar competições.
- Mostrar modelo.
- Mostrar total de jogos.
- Mostrar widgets por idioma.
- Mostrar status de publicação.

### Sprint Admin 3 — Integração com n8n

- Botão para regenerar HTML [1].
- Botão para regenerar HTML [2].
- Botão para atualizar artilharia.
- Registro de logs.

### Sprint Admin 4 — Operação Bolão

- Resultados pendentes.
- Reprocessamento.
- Rankings.
- Ligas e participantes.
- Logs de apuração.

### Sprint Admin 5 — Mega Bolão 360

- Planos.
- Bolões criados.
- Limites por plano.
- Status de assinatura.
- Métricas comerciais.

## 13. Critério de aceite do MVP

O MVP administrativo estará validado quando permitir:

1. Ver competições ativas.
2. Ver widgets principais e extras por idioma.
3. Identificar widgets ausentes/desatualizados.
4. Copiar shortcode.
5. Ver status básico do Bolão.
6. Apoiar operação diária do portal.
