# M360 Newsletter Foundation v0.6.1

Issue #19 introduces a provider-neutral newsletter flow. The public form is exposed with `[m360_newsletter_form]` and posts to `POST /wp-json/m360/v1/newsletter/subscribe`. Opt-out uses MailPoet's signed unsubscribe link; no public endpoint can cancel a subscription from an e-mail address alone.

The form requires explicit, unchecked newsletter consent. The service stores the name, e-mail, consent version, UTC acceptance time, IP, user agent and source as an audit record. It creates a cryptographically random token, persists only its SHA-256 hash, and expires it after 72 hours. `GET /newsletter/confirm/{token}` consumes the token once and changes the local subscription to `confirmed`.

`M360_Newsletter_Service` is the sole business-flow owner. `M360_Newsletter_Provider_Interface` isolates provider operations (`subscribe`, `confirm`, `unsubscribe`, `status`, and `healthCheck`). The initial `M360_MailPoet_Adapter` subscribes exclusively to MailPoet list ID `3`, which sends the Double Opt-In confirmation email. M360 stores the consent immediately as `pending` and checks the provider hourly. Provider states are normalized as `unconfirmed -> pending`, `subscribed -> confirmed`, `unsubscribed -> unsubscribed`, and `bounced -> blocked`. Reinscriptions are therefore synchronized back to `confirmed`, and audit actions fire only when the local state actually changes.

Actions provide audit integration points: `m360_newsletter_subscribed`, `m360_newsletter_confirmed`, `m360_newsletter_unsubscribed`, `m360_newsletter_confirmation_failed`, and `m360_newsletter_provider_error`.
