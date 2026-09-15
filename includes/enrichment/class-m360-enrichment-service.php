<?php
if (!defined('ABSPATH')) { exit; }

/** Cache reader never fetches from DW. Refresh is an explicit background integration point. */
final class M360_Enrichment_Service
{
    private M360_Enrichment_Provider $provider;
    private array $allowed;
    private int $ttl;
    private int $negative_ttl;
    private int $max_age;

    /** TTLs must be chosen by the caller after source freshness/cadence validation. */
    public function __construct(M360_Enrichment_Provider $provider, array $allowed, int $ttl, int $negative_ttl, int $max_age)
    {
        if (min($ttl, $negative_ttl, $max_age) < 1 || max($ttl, $negative_ttl, $max_age) > 604800) {
            throw new InvalidArgumentException('Invalid cache policy.');
        }
        $namespace = $provider->cache_namespace();
        if (!preg_match('/^[a-zA-Z0-9_.-]{1,80}$/D', $namespace)) { throw new InvalidArgumentException('Invalid provider namespace.'); }
        $this->provider = $provider; $this->allowed = $allowed;
        $this->ttl = $ttl; $this->negative_ttl = $negative_ttl; $this->max_age = $max_age;
    }

    private function resolve(array $bindings, string $locale): array
    {
        return M360_Enrichment_Context::resolve($bindings, $this->allowed, $locale);
    }

    private function key(array $context): string
    {
        return 'm360_enrich_' . hash('sha256', json_encode([
            M360_Enrichment_Contract::VERSION, $this->provider->cache_namespace(),
            M360_Enrichment_Context::key($context), $this->ttl, $this->negative_ttl, $this->max_age,
        ], JSON_THROW_ON_ERROR));
    }

    public function read(array $bindings, string $locale, ?int $now = null): array
    {
        $now = $now ?? time();
        $resolution = $this->resolve($bindings, $locale);
        if ($resolution['status'] !== 'ok') { return $resolution + ['schema_version' => '1', 'standings' => null]; }
        $context = $resolution['context'];
        $cached = get_transient($this->key($context));
        if (!is_array($cached) || !is_int($cached['expires_at'] ?? null) || $cached['expires_at'] <= $now
            || !is_array($cached['result'] ?? null)) { return M360_Enrichment_Contract::empty('no_data', $context); }
        $result = $cached['result'];
        if (in_array($result['status'] ?? null, ['unavailable', 'no_data', 'stale'], true)) {
            return M360_Enrichment_Contract::empty($result['status'], $context);
        }
        // Revalidate identity, age and shape even if an object cache outlives its TTL.
        return M360_Enrichment_Contract::normalize($result, $context, $now, $this->max_age);
    }

    /** Not called by a public render/save hook; scheduler/locking is a later integration. */
    public function refresh(array $bindings, string $locale, ?int $now = null): array
    {
        $now = $now ?? time();
        $resolution = $this->resolve($bindings, $locale);
        if ($resolution['status'] !== 'ok') { return $resolution + ['schema_version' => '1', 'standings' => null]; }
        $context = $resolution['context'];
        try {
            $result = M360_Enrichment_Contract::normalize($this->provider->fetch($context), $context, $now, $this->max_age);
        } catch (Throwable $error) {
            $result = M360_Enrichment_Contract::empty('unavailable', $context);
        }
        $ttl = $result['standings'] === null ? $this->negative_ttl : min($this->ttl, $this->max_age);
        set_transient($this->key($context), ['result' => $result, 'expires_at' => $now + $ttl], $ttl);
        return $result;
    }
}
