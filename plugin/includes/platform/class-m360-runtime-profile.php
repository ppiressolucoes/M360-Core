<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Controls runtime ownership independently from portable editorial settings.
 *
 * Existing M360 installations are migrated to legacy-compatible mode so an
 * upgrade cannot remove already-homologated output. Fresh installations start
 * in portable-safe mode and expose no automatic public output until an
 * administrator explicitly enables each capability.
 */
final class M360_Runtime_Profile
{
    private const OPTION = 'm360_runtime_profile';
    private const DIAGNOSTICS_OPTION = 'm360_runtime_profile_diagnostics';
    private const SCHEMA_VERSION = 1;

    public static function activate(?bool $existing_install = null): void
    {
        $stored = get_option(self::OPTION, null);
        if ($stored !== null) {
            self::record_diagnostics(
                'preserved-existing-profile',
                self::historical_evidence(),
                is_array($stored) ? (string) ($stored['mode'] ?? 'portable-safe') : 'portable-safe',
                false
            );
            return;
        }

        $evidence = self::historical_evidence();
        $legacy = $existing_install === true || $evidence !== [];
        $mode = $legacy ? 'legacy-compatible' : 'portable-safe';
        add_option(self::OPTION, self::defaults($legacy), '', false);
        self::record_diagnostics(
            $legacy ? 'historical-installation-evidence' : 'fresh-installation-no-evidence',
            $evidence,
            $mode,
            true
        );
    }

    public static function defaults(bool $legacy = false): array
    {
        return [
            'schema_version' => self::SCHEMA_VERSION,
            'mode' => $legacy ? 'legacy-compatible' : 'portable-safe',
            'capabilities' => [
                'public_views' => $legacy,
                'ads_runtime' => $legacy,
                'ads_auto_insert' => $legacy,
                'newsletter_runtime' => $legacy,
                'consent_runtime' => $legacy,
            ],
        ];
    }

    public static function get(): array
    {
        $stored = get_option(self::OPTION, []);
        $fallback = self::defaults(false);
        return self::sanitize(is_array($stored) ? array_replace_recursive($fallback, $stored) : $fallback);
    }

    public static function update(array $input): bool
    {
        $updated = update_option(self::OPTION, self::sanitize($input), false);
        return $updated || self::get() === self::sanitize($input);
    }

    public static function enabled(string $capability): bool
    {
        $settings = self::get();
        return !empty($settings['capabilities'][sanitize_key($capability)]);
    }

    public static function is_legacy_compatible(): bool
    {
        return self::get()['mode'] === 'legacy-compatible';
    }

    public static function diagnostics(): array
    {
        $stored = get_option(self::DIAGNOSTICS_OPTION, []);
        $stored = is_array($stored) ? $stored : [];
        return [
            'reason' => sanitize_key((string) ($stored['reason'] ?? 'unavailable')),
            'evidence' => array_values(array_filter(array_map(
                'sanitize_text_field',
                is_array($stored['evidence'] ?? null) ? $stored['evidence'] : []
            ))),
            'selected_mode' => sanitize_key((string) ($stored['selected_mode'] ?? self::get()['mode'])),
            'classified_at' => sanitize_text_field((string) ($stored['classified_at'] ?? '')),
            'classified_version' => sanitize_text_field((string) ($stored['classified_version'] ?? '')),
        ];
    }

    public static function sanitize(array $input): array
    {
        $mode = sanitize_key((string) ($input['mode'] ?? 'portable-safe'));
        if (!in_array($mode, ['portable-safe', 'legacy-compatible'], true)) {
            $mode = 'portable-safe';
        }
        $capabilities = is_array($input['capabilities'] ?? null) ? $input['capabilities'] : [];
        $allowed = array_keys(self::defaults(false)['capabilities']);
        $clean = [];
        foreach ($allowed as $capability) {
            $clean[$capability] = !empty($capabilities[$capability]);
        }
        return [
            'schema_version' => self::SCHEMA_VERSION,
            'mode' => $mode,
            'capabilities' => $clean,
        ];
    }

    private static function historical_evidence(): array
    {
        $evidence = [];
        $option_names = [
            'm360_core_version',
            'm360_core_activated_at',
            'm360_platform_activated_at',
            'm360_platform_module_states',
            'm360_platform_module_schemas',
            'm360_site_profile',
            'm360_ads_db_version',
            'm360_newsletter_schema_version',
            'm360_newsletter_settings',
            'm360_discovery_db_version',
            'm360_discovery_settings',
        ];
        foreach ($option_names as $option_name) {
            if (get_option($option_name, null) !== null) {
                $evidence[] = 'option:' . $option_name;
            }
        }

        global $wpdb;
        if (isset($wpdb) && is_object($wpdb)) {
            $table_names = [
                $wpdb->prefix . 'm360_ads_slots',
                $wpdb->prefix . 'm360_newsletter_consents',
                $wpdb->prefix . 'm360_discovery_runs',
                $wpdb->prefix . 'm360_discovery_relations',
            ];
            foreach ($table_names as $table_name) {
                $found = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name)));
                if ((string) $found === $table_name) {
                    $evidence[] = 'table:' . $table_name;
                }
            }
        }

        return array_values(array_unique($evidence));
    }

    private static function record_diagnostics(
        string $reason,
        array $evidence,
        string $selected_mode,
        bool $replace
    ): void {
        if (!$replace && get_option(self::DIAGNOSTICS_OPTION, null) !== null) { return; }
        update_option(self::DIAGNOSTICS_OPTION, [
            'reason' => sanitize_key($reason),
            'evidence' => array_values(array_filter(array_map('sanitize_text_field', $evidence))),
            'selected_mode' => sanitize_key($selected_mode),
            'classified_at' => current_time('mysql'),
            'classified_version' => defined('M360_CORE_VERSION') ? M360_CORE_VERSION : '',
        ], false);
    }
}
