<?php
if (!defined('ABSPATH')) { exit; }

/** Validated canonical scope. Does not infer IDs, seasons or a principal team. */
final class M360_Enrichment_Context
{
    public const COMPETITIONS = ['brasileirao-serie-a', 'libertadores', 'premier-league', 'la-liga', 'bundesliga', 'ligue-1'];

    public static function id($value): ?string
    {
        if (!is_string($value) || !preg_match('/^[1-9][0-9]{0,19}$/D', $value)) { return null; }
        if (strlen($value) === 20 && strcmp($value, '18446744073709551615') > 0) { return null; }
        return $value;
    }

    /** IDs are decimal strings to preserve BIGINT precision in JSON. */
    public static function normalize(array $scope): ?array
    {
        foreach (['team_id', 'competition_id'] as $field) {
            if (self::id($scope[$field] ?? null) === null) { return null; }
        }
        if (!in_array($scope['competition_key'] ?? null, self::COMPETITIONS, true)
            || !in_array($scope['locale'] ?? null, ['pt-BR', 'en-US'], true)
            || !is_string($scope['season'] ?? null)
            || !preg_match('/^[a-zA-Z0-9_\/-]{1,20}$/D', $scope['season'])) { return null; }
        foreach (['phase', 'group'] as $field) {
            if (!array_key_exists($field, $scope)) { return null; }
            $value = $scope[$field];
            if ($value !== null && (!is_string($value) || !preg_match('/^[\p{L}\p{N} _.-]{1,80}$/uD', $value))) { return null; }
        }
        return array_intersect_key($scope, array_flip(['team_id', 'competition_id', 'competition_key', 'season', 'phase', 'group', 'locale']));
    }

    public static function key(array $scope): string
    {
        $scope = self::normalize($scope);
        if ($scope === null) { throw new InvalidArgumentException('Invalid enrichment scope.'); }
        ksort($scope);
        return hash('sha256', json_encode($scope, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }

    /**
     * Bindings must come from a curated/validated editorial mapping, not text matches.
     * $allowed maps one of COMPETITIONS to the actual DW competition ID.
     * Any competing valid scope remains ambiguous; array order is never a tiebreaker.
     */
    public static function resolve(array $bindings, array $allowed, string $locale): array
    {
        if (!in_array($locale, ['pt-BR', 'en-US'], true)) { return ['status' => 'unsupported', 'context' => null]; }
        if (!$bindings) { return ['status' => 'unmatched', 'context' => null]; }
        $scopes = [];
        foreach ($bindings as $binding) {
            if (!is_array($binding)) { return ['status' => 'unmatched', 'context' => null]; }
            $scope = self::normalize(array_merge($binding, ['locale' => $locale]));
            if ($scope === null) { return ['status' => 'unmatched', 'context' => null]; }
            if (($allowed[$scope['competition_key']] ?? null) !== $scope['competition_id']) {
                return ['status' => 'unsupported', 'context' => null];
            }
            $scopes[self::key($scope)] = $scope;
        }
        return count($scopes) === 1
            ? ['status' => 'ok', 'context' => reset($scopes)]
            : ['status' => 'ambiguous', 'context' => null];
    }
}
