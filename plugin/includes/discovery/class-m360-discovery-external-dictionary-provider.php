<?php
if (!defined('ABSPATH')) { exit; }

/**
 * Builds an optional, read-only PDO connection for an environment-owned
 * keyword dictionary. Secrets remain outside WordPress options and exports.
 */
final class M360_Discovery_External_Dictionary_Provider
{
    private static bool $resolved = false;
    private static ?PDO $connection = null;
    private static string $status = 'not_configured';

    public static function connection(): ?PDO
    {
        if (self::$resolved) { return self::$connection; }
        self::$resolved = true;
        try {
            $filtered = apply_filters('m360_discovery_dictionary_pdo', null);
            if ($filtered instanceof PDO) { return self::$connection = self::harden($filtered); }
            if (function_exists('m360_discovery_dictionary_pdo')) {
                $provided = m360_discovery_dictionary_pdo();
                if ($provided instanceof PDO) { return self::$connection = self::harden($provided); }
            }
            if (!self::constants_configured() || !class_exists('PDO')) {
                if (self::constants_present()) { self::$status = 'configuration_incomplete'; }
                return null;
            }
            $host = (string) constant('M360_DISCOVERY_DB_HOST');
            $database = (string) constant('M360_DISCOVERY_DB_NAME');
            $user = (string) constant('M360_DISCOVERY_DB_USER');
            $password = (string) constant('M360_DISCOVERY_DB_PASSWORD');
            $port = defined('M360_DISCOVERY_DB_PORT') ? max(1, (int) constant('M360_DISCOVERY_DB_PORT')) : 3306;
            $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';charset=utf8mb4';
            self::$connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            self::$status = 'connected';
            return self::$connection;
        } catch (Throwable $exception) {
            self::$status = 'connection_failed';
            return null;
        }
    }

    public static function configured(): bool
    {
        return self::constants_present()
            || function_exists('m360_discovery_dictionary_pdo')
            || has_filter('m360_discovery_dictionary_pdo');
    }

    public static function status(): string
    {
        self::connection();
        return self::$connection instanceof PDO ? 'connected' : self::$status;
    }

    public static function table(): string
    {
        $table = defined('M360_DISCOVERY_DB_TABLE') ? (string) constant('M360_DISCOVERY_DB_TABLE') : 'WP_links_internos';
        return self::valid_identifier($table) ? $table : 'WP_links_internos';
    }

    public static function quote_identifier(string $identifier): string
    {
        if (!self::valid_identifier($identifier)) { $identifier = 'WP_links_internos'; }
        return implode('.', array_map(static fn(string $part): string => '`' . $part . '`', explode('.', $identifier)));
    }

    private static function harden(PDO $pdo): PDO
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        self::$status = 'connected';
        return $pdo;
    }

    private static function constants_configured(): bool
    {
        foreach (['M360_DISCOVERY_DB_HOST', 'M360_DISCOVERY_DB_NAME', 'M360_DISCOVERY_DB_USER', 'M360_DISCOVERY_DB_PASSWORD'] as $name) {
            if (!defined($name) || trim((string) constant($name)) === '') { return false; }
        }
        return true;
    }

    private static function constants_present(): bool
    {
        foreach (['M360_DISCOVERY_DB_HOST', 'M360_DISCOVERY_DB_NAME', 'M360_DISCOVERY_DB_USER', 'M360_DISCOVERY_DB_PASSWORD', 'M360_DISCOVERY_DB_TABLE'] as $name) {
            if (defined($name)) { return true; }
        }
        return false;
    }

    private static function valid_identifier(string $identifier): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_]+(?:\.[A-Za-z0-9_]+)?$/', $identifier);
    }
}
