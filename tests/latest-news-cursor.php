<?php
/**
 * Contract test for Latest News cursor encoding and compatibility mode.
 * Run: php tests/latest-news-cursor.php
 */
define('ABSPATH', __DIR__ . DIRECTORY_SEPARATOR);
function wp_json_encode($value) { return json_encode($value, JSON_UNESCAPED_SLASHES); }
function wp_salt($scheme = '') { return 'm360-latest-news-test-salt'; }
function absint($value) { return abs((int) $value); }
function sanitize_key($value) { return strtolower((string) preg_replace('/[^a-z0-9_\-]/i', '', (string) $value)); }
function wp_unslash($value) { return $value; }
function wp_parse_url($url) { return parse_url($url); }
function untrailingslashit($value) { return rtrim((string) $value, '/\\'); }
function home_url($path = '') { return 'https://example.test' . ('/' . ltrim((string) $path, '/')); }
function remove_query_arg($keys, $url) {
    $parts = parse_url((string) $url);
    parse_str((string) ($parts['query'] ?? ''), $query);
    foreach ((array) $keys as $key) { unset($query[$key]); }
    $result = (string) ($parts['path'] ?? '/');
    return $query ? $result . '?' . http_build_query($query) : $result;
}
require_once __DIR__ . '/../plugin/includes/latest-news/class-m360-latest-news-component.php';

$reflection = new ReflectionClass(M360_Latest_News_Component::class);
$encode = $reflection->getMethod('encode_cursor');
$decode = $reflection->getMethod('decode_cursor');
$mode = $reflection->getMethod('navigation_mode');
$encode->setAccessible(true);
$decode->setAccessible(true);
$mode->setAccessible(true);

$assert = static function (bool $condition, string $message): void {
    if (!$condition) { throw new RuntimeException($message); }
};

$cursor = $encode->invoke(null, '2026-10-26 12:30:00', 1729, 'pt');
$valid = $decode->invoke(null, $cursor, 'pt');
$assert(is_array($valid), 'A cursor válida deve ser aceita.');
$assert($valid['id'] === 1729 && $valid['date'] === '2026-10-26 12:30:00', 'A cursor deve preservar data e ID.');
$assert($decode->invoke(null, $cursor, 'en') === null, 'A cursor não pode atravessar idiomas.');
$assert($decode->invoke(null, $cursor . 'x', 'pt') === null, 'A cursor adulterada deve ser rejeitada antes da consulta.');
$assert($decode->invoke(null, str_repeat('x', 257), 'pt') === null, 'Uma cursor extrema deve ser rejeitada antes da consulta.');
$assert($mode->invoke(null, ['pagination' => 'true'], 'list', '') === 'cursor', 'A paginação legada deve migrar para cursor.');
$assert($mode->invoke(null, ['pagination' => 'false'], 'list', '') === 'none', 'Posts sem paginação devem permanecer sem navegação.');
$assert($mode->invoke(null, ['navigation' => 'cursor'], 'sidebar', '') === 'none', 'Sidebar não pode habilitar navegação profunda.');
$_SERVER['REQUEST_URI'] = '/ultimas-noticias/?m360_news_cursor=ignored';
$assert(
    $mode->invoke(null, ['pagination' => 'false'], 'list', 'https://example.test/ultimas-noticias/') === 'cursor',
    'A página dedicada deve habilitar o cursor mesmo com a paginação temporariamente desativada.'
);
$assert(
    $mode->invoke(null, ['pagination' => 'false'], 'list', 'https://example.test/outra-pagina/') === 'none',
    'Um bloco-resumo fora da página dedicada não pode ativar o cursor.'
);
echo "Latest News cursor contract: OK\n";
