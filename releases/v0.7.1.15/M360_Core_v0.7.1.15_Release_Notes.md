# M360 Core v0.7.1.15 — Editorial Ticker Autoplay Policy Hotfix

## Causa confirmada

Na Home EN-US publicada, o ticker apresentava `data-autoplay="true"`, oito itens, intervalo de 4500 ms e inicializador ativo. O navegador, porém, retornava `prefers-reduced-motion: reduce`, e a v0.7.1.14 cancelava silenciosamente o timer.

## Correções

- `autoplay="true"` agora inicia o ticker por padrão, inclusive sob preferência de movimento reduzido;
- novo botão visível de pausa/retomada, entre os controles anterior e próximo;
- estado, rótulo acessível e ícone do botão são atualizados durante execução e pausa;
- pausa temporária por hover, foco e aba oculta permanece preservada;
- política opcional `reduced_motion="respect"` inicia o ticker pausado quando a preferência do sistema estiver ativa;
- preview dinâmico no Elementor e navegação manual permanecem preservados.

## Shortcode de homologação EN-US

```text
[m360_editorial_ticker lang="en" label="Latest News" limit="8" interval="4500" autoplay="true"]
```

Para respeitar a preferência de movimento reduzido no carregamento:

```text
[m360_editorial_ticker lang="en" label="Latest News" limit="8" interval="4500" autoplay="true" reduced_motion="respect"]
```

## Escopo

Somente o ticker da Home EN-US participa desta homologação. A Home PT-BR e o tema News Portal permanecem independentes e inalterados.

## Rollback

Reinstalar o ZIP canônico v0.7.1.14. O M360 Home Editorial 0.1.2 permanece ativo até o aceite final do cutover.
