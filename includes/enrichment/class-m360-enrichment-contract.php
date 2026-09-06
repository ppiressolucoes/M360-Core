<?php
if (!defined('ABSPATH')) { exit; }

/** Foundation v1 validates stored standings; fixtures will have a separate contract. */
final class M360_Enrichment_Contract
{
    public const VERSION = '1';
    private const METRICS = ['position', 'points', 'played', 'won', 'drawn', 'lost', 'goals_for', 'goals_against', 'goal_difference'];

    public static function empty(string $status, array $context): array
    {
        return ['schema_version' => self::VERSION, 'status' => $status, 'context' => $context, 'standings' => null];
    }

    public static function normalize(array $raw, array $context, int $now, int $max_age): array
    {
        $origin = is_array($raw['context'] ?? null) ? M360_Enrichment_Context::normalize($raw['context']) : null;
        if ($origin === null || M360_Enrichment_Context::key($origin) !== M360_Enrichment_Context::key($context)) {
            return self::empty('unavailable', $context);
        }
        if (!array_key_exists('standings', $raw) || $raw['standings'] === null) { return self::empty('no_data', $context); }
        $section = $raw['standings'];
        if (!is_array($section) || !is_string($section['dw_updated_at'] ?? null)
            || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}[+-][0-9]{2}:[0-9]{2}$/D', $section['dw_updated_at'])) {
            return self::empty('unavailable', $context);
        }
        // No timezone inference. Invalid calendar dates and future timestamps are rejected.
        $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:sP', $section['dw_updated_at']);
        $errors = DateTimeImmutable::getLastErrors();
        if (!$date || ($errors !== false && ($errors['warning_count'] || $errors['error_count']))
            || $date->format('Y-m-d\TH:i:sP') !== $section['dw_updated_at']
            || $date->getTimestamp() > $now) { return self::empty('unavailable', $context); }
        if ($now - $date->getTimestamp() >= $max_age) { return self::empty('stale', $context); }
        $clean = ['dw_updated_at' => $date->format('Y-m-d\TH:i:sP')];
        foreach (self::METRICS as $name) {
            $value = $section[$name] ?? null;
            // Preserve nulls. Points may be negative after a sporting deduction.
            if ($value !== null && (!is_int($value) || abs($value) > 100000
                || (!in_array($name, ['points', 'goal_difference'], true) && $value < 0)
                || ($name === 'position' && $value < 1))) { return self::empty('unavailable', $context); }
            $clean[$name] = $value;
        }
        if ($clean['position'] === null || $clean['points'] === null || $clean['played'] === null) {
            return self::empty('no_data', $context);
        }
        if ($clean['won'] !== null && $clean['drawn'] !== null && $clean['lost'] !== null
            && $clean['won'] + $clean['drawn'] + $clean['lost'] !== $clean['played']) {
            return self::empty('unavailable', $context);
        }
        if ($clean['goals_for'] !== null && $clean['goals_against'] !== null && $clean['goal_difference'] !== null
            && $clean['goals_for'] - $clean['goals_against'] !== $clean['goal_difference']) {
            return self::empty('unavailable', $context);
        }
        $out = self::empty(in_array(null, array_intersect_key($clean, array_flip(self::METRICS)), true) ? 'partial' : 'ok', $context);
        $out['standings'] = $clean;
        return $out;
    }
}
