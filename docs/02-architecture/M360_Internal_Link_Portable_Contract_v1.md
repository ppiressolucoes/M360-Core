# M360 Internal Link Portable Contract v1

Status: autorizado para shadow em 23/07/2026.

## Semântica

`internal_link` representa uma recomendação de navegação para um arquivo de taxonomia. O contrato não representa um post relacionado e não contém HTML, âncora, URL persistida, metadados de usuário ou conteúdo editorial.

| Campo | Regra |
| --- | --- |
| `relation_kind` | Sempre `internal_link` |
| `target_type` | Sempre `term` |
| `target_id` | ID de termo existente em taxonomia configurada |
| origem | Termo atribuído ao post publicado de origem |
| máximo | Quatro destinos por snapshot |
| ordenação | Taxonomia e, em seguida, ID do termo |

O renderer futuro deverá resolver o arquivo do termo em tempo de renderização e ignorar destinos inexistentes. Ele não está incluído nesta versão.

## Limites de migração

O contrato substitui a implementação shadow inicial que usava `post` para `internal_link`. `related_post` é o único tipo autorizado a apontar para posts. O precursor M360 Semantic Relations 0.9.0 continua como writer e renderer exclusivo até a homologação de paridade e uma autorização de canário separada.

## Aceite

- novos snapshots shadow registram apenas `term` em `internal_link`;
- Comparator não aponta tipo sem destinos compartilhados nos posts de referência;
- zero destino inválido e zero locale cruzado;
- sem mudança visível, sem hook público e sem escrita legada.
