# Governança do Repositório M360 Core v1

## Fonte única da verdade

O repositório `ppiressolucoes/M360-Core` é a referência oficial para código, arquitetura, ADRs, roadmap, releases, builds e manutenção do M360 Core.

## Baseline oficial

- release homologada: `v0.5.1 — AdSense Approval Readiness`;
- tag imutável: `v0.5.1`;
- arquitetura: `M360 Platform Architecture v2.2`;
- pacote instalável: gerado exclusivamente pelos workflows oficiais.

## Política de branches

- `main`: linha consolidada de referência;
- `sprint/*`: integração e homologação de ciclos evolutivos;
- `agent/*` ou `feature/*`: implementação isolada por entrega;
- `hotfix/*`: correções urgentes originadas da última tag estável.

Nenhuma alteração deve chegar às linhas de integração sem Pull Request, evidências de validação e documentação proporcional ao risco.

## Política de tags

- tags `vX.Y.Z` identificam releases homologadas e são imutáveis;
- uma tag nunca deve ser movida ou reutilizada;
- correções posteriores geram uma nova versão;
- release notes e checksum do pacote devem acompanhar a homologação.

## Ordem de consulta para manutenção

1. tag da release instalada;
2. `VERSION.md` e `CHANGELOG.md`;
3. índice documental;
4. arquitetura e ADR aplicável;
5. release notes e runbook operacional;
6. histórico de PRs relacionado ao componente.

## Definição de pronto

- código revisado e versionado;
- build oficial concluído;
- homologação PT-BR e EN-US;
- desktop e mobile validados;
- cache e rollback considerados;
- documentação e roadmap atualizados;
- PR aprovada antes da integração.
