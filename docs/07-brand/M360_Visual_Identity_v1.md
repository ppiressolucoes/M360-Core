# M360 Visual Identity v1

Status: oficial

Projeto: Mengão 360 | DW Esportivo

Baseline visual: julho de 2026

## 1. Objetivo

Este documento consolida a identidade visual oficial do M360 para uso no portal Mengão 360, nos ambientes PT-BR e EN-US e nos modos claro e escuro.

A marca combina três elementos em uma assinatura compacta:

- `M`, como símbolo principal da plataforma;
- `36`, preservando a identificação M360;
- bola de futebol no lugar do zero, conectando a marca diretamente ao posicionamento de hub de informações esportivas.

## 2. Ativos canônicos

| Nome do ativo | Dimensões | Uso recomendado |
|---|---:|---|
| `m360-logo-3d-transparent-640w.webp` | 640 × 204 px | Cabeçalhos, Elementor, tema News Portal e interfaces web |
| `m360-logo-3d-transparent-1280w.webp` | 1280 × 408 px | Fonte de alta resolução, materiais institucionais e novas derivações |
| `m360-logo-3d-light-dark-preview.jpg` | 760 × 520 px | Referência visual nos fundos claro e escuro; não usar como logotipo |

Os WebP transparentes são as fontes oficiais para publicação e são distribuídos pela biblioteca de mídia controlada do portal. O ativo de 640 px deve ser priorizado no cabeçalho por equilibrar nitidez e carregamento ágil.

## 3. Arquitetura visual do cabeçalho

O cabeçalho homologado utiliza três faixas:

1. faixa superior vermelha para data, links institucionais e redes sociais;
2. faixa central grafite `#0f0f12` para logotipo e busca M360;
3. faixa inferior vermelha para o menu principal.

PT-BR e EN-US devem usar o mesmo arquivo, proporção, contraste e hierarquia visual. A largura recomendada do logotipo é de até `300px` no desktop e entre `160px` e `220px` no mobile, preservando sempre a proporção original.

## 4. Regras de aplicação

- Manter fundo transparente e proporção original de `640:204`.
- Não esticar, comprimir, recortar, girar, recolorir ou aplicar novos efeitos.
- Não adicionar caixas, contornos ou fundos diretamente ao arquivo da marca.
- Preservar área livre mínima equivalente a 8% da altura exibida em todos os lados.
- Usar a mesma assinatura nos modos claro e escuro; o contraste é fornecido pela faixa central grafite.
- Não substituir a bola por numeral, ícone genérico ou outra modalidade esportiva.
- O texto alternativo deve ser `Mengão 360` em ambos os idiomas.

## 5. Desempenho e acessibilidade

Para a imagem acima da dobra:

- declarar `width="640"` e `height="204"` para evitar deslocamento de layout;
- manter `height: auto` no CSS;
- não aplicar carregamento tardio (`loading="lazy"`);
- usar `fetchpriority="high"` quando o tema ou Elementor permitir;
- evitar carregar simultaneamente versões duplicadas da marca;
- manter link da logo para a home correspondente ao idioma atual.

## 6. Referência de implementação

URL homologada no WordPress:

```text
https://mengao360.com/wp-content/uploads/2026/07/m360-logo-3d-transparent-640w.webp
```

CSS de referência para o tema News Portal:

```css
.np-logo-section-wrapper {
  background: #0f0f12 !important;
}

.np-logo-section-wrapper img.custom-logo {
  display: block !important;
  width: clamp(160px, 19vw, 300px) !important;
  height: auto !important;
  max-width: 100% !important;
  margin: 0 auto !important;
  object-fit: contain;
}
```

O uso de `content: url(...)` no CSS é aceito como compatibilidade transitória. A configuração direta da imagem no WordPress ou Elementor é preferível porque preserva melhor semântica, atributos de dimensão, texto alternativo e prioridade de carregamento.

## 7. Checklist de publicação

- [ ] Arquivo WebP transparente oficial.
- [ ] Largura máxima de 300 px no desktop.
- [ ] Proporção preservada no mobile.
- [ ] Faixa central grafite aplicada.
- [ ] Texto alternativo `Mengão 360`.
- [ ] Link para a home do idioma atual.
- [ ] Sem lazy loading acima da dobra.
- [ ] PT-BR, EN-US, claro, escuro, desktop e mobile validados.

## 8. Governança

Qualquer alteração na geometria, cores, acabamento 3D ou composição `M + 36 + bola` deve gerar nova versão deste documento e novos arquivos canônicos. Os ativos publicados nesta baseline não devem ser sobrescritos silenciosamente.
