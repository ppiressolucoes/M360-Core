# Governança do Repositório M360 Core v1.1

## Fonte única da verdade

O repositório `ppiressolucoes/M360-Core` é a referência oficial para código, arquitetura, ADRs, roadmap, releases, builds e manutenção do M360 Core.

## Baseline oficial

- release homologada: `v0.7.4.0.42`;
- tag imutável: `v0.7.4.0.42`;
- arquitetura: `M360 Platform Architecture v2.2` e ADR-0008/ADR-0010;
- ambiente de referência: Mengão 360, PT-BR e EN-US;
- pacote instalável: `m360-core-v0.7.4.0.42.zip`, gerado pelo script/workflow oficial;
- checksum SHA-256: `4CA2CB6C6B5A10B718C3EF0DE9CC0DDC9A0BCCE4CF3926332AF4722FB11B5A54`.

## Política de branches

- `main`: linha consolidada e homologada de referência;
- `sprint/*`: integração e homologação de ciclos evolutivos;
- `agent/*` ou `feature/*`: implementação isolada por entrega;
- `hotfix/*`: correções urgentes originadas da última tag estável.

Toda integração em `main` deve manter histórico revisável, evidência de validação e documentação proporcional ao risco.

## Política de tags e releases

- tags `vX.Y.Z...` identificam versões homologadas e são imutáveis;
- uma tag nunca é movida ou reutilizada;
- correções posteriores geram uma nova versão;
- release notes, pacote e checksum acompanham a homologação;
- componentes pertencentes a outros módulos, como o ticker do M360 Plus Editorial, não são copiados para o Core.

## Ordem de consulta para manutenção

1. tag da release instalada;
2. `VERSION.md` e `CHANGELOG.md`;
3. índice documental;
4. consolidação operacional da baseline;
5. arquitetura e ADR aplicável;
6. release notes e runbook operacional;
7. histórico de PRs relacionado ao componente.

## Definição de pronto

- código revisado e versionado;
- build completo inspecionado;
- homologação PT-BR e EN-US;
- desktop e mobile validados;
- cache e rollback considerados;
- documentação, histórico e índice atualizados;
- PR integrada à `main`;
- tag e release publicadas no repositório oficial.
