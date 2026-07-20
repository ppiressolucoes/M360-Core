<?php
if (!defined('ABSPATH')) { exit; }

final class M360_Site_Profile
{
    private const OPTION = 'm360_site_profile';
    private const SCHEMA_VERSION = 1;

    public static function activate(): void
    {
        if (get_option(self::OPTION, null) === null) {
            add_option(self::OPTION, self::defaults(), '', false);
        }
    }

    public static function defaults(): array
    {
        $locale = self::normalize_locale((string) get_locale()) ?: 'pt-BR';
        return [
            'schema_version' => self::SCHEMA_VERSION,
            'site_key' => sanitize_key((string) wp_parse_url(home_url('/'), PHP_URL_HOST)),
            'site_name' => sanitize_text_field((string) get_bloginfo('name')),
            'vertical' => 'publisher',
            'default_locale' => $locale,
            'supported_locales' => [$locale],
        ];
    }

    public static function get(): array
    {
        $stored = get_option(self::OPTION, []);
        return self::sanitize(is_array($stored) ? array_merge(self::defaults(), $stored) : self::defaults());
    }

    public static function update(array $input): bool
    {
        return update_option(self::OPTION, self::sanitize($input), false);
    }

    public static function sanitize(array $input): array
    {
        $default_locale = self::normalize_locale((string) ($input['default_locale'] ?? '')) ?: 'pt-BR';
        $locales = $input['supported_locales'] ?? [$default_locale];
        if (is_string($locales)) { $locales = preg_split('/[\s,]+/', $locales) ?: []; }
        $locales = array_values(array_unique(array_filter(array_map(
            static fn($value) => self::normalize_locale((string) $value),
            is_array($locales) ? $locales : []
        ))));
        if (!$locales) { $locales = [$default_locale]; }
        if (!in_array($default_locale, $locales, true)) { array_unshift($locales, $default_locale); }

        return [
            'schema_version' => self::SCHEMA_VERSION,
            'site_key' => sanitize_key((string) ($input['site_key'] ?? 'portal')) ?: 'portal',
            'site_name' => sanitize_text_field((string) ($input['site_name'] ?? get_bloginfo('name'))),
            'vertical' => sanitize_key((string) ($input['vertical'] ?? 'publisher')) ?: 'publisher',
            'default_locale' => $default_locale,
            'supported_locales' => array_slice($locales, 0, 20),
        ];
    }

    public static function import_json(string $json)
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('m360_profile_json', 'JSON de perfil inválido.');
        }
        $allowed = ['schema_version','site_key','site_name','vertical','default_locale','supported_locales'];
        $unknown = array_diff(array_keys($decoded), $allowed);
        if ($unknown) {
            return new WP_Error('m360_profile_keys', 'O perfil contém campos não permitidos: ' . implode(', ', $unknown));
        }
        if ((int) ($decoded['schema_version'] ?? 0) !== self::SCHEMA_VERSION) {
            return new WP_Error('m360_profile_schema', 'Versão de schema do Site Profile incompatível.');
        }
        $required = ['site_key','site_name','vertical','default_locale','supported_locales'];
        $missing = array_diff($required, array_keys($decoded));
        if ($missing) {
            return new WP_Error('m360_profile_required', 'O perfil não contém todos os campos obrigatórios.');
        }
        self::update($decoded);
        return self::get();
    }

    public static function export_json(): string
    {
        return (string) wp_json_encode(self::get(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private static function normalize_locale(string $locale): ?string
    {
        $locale = str_replace('_', '-', trim($locale));
        if (!preg_match('/^[a-zA-Z]{2,3}(?:-[a-zA-Z]{2})?$/', $locale)) { return null; }
        $parts = explode('-', $locale, 2);
        return strtolower($parts[0]) . (isset($parts[1]) ? '-' . strtoupper($parts[1]) : '');
    }
}
