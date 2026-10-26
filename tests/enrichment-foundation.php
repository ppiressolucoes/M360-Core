<?php
/** Standalone tests, synthetic fixtures only. No WordPress/SQL/network access. */
define('ABSPATH', __DIR__);
$hooks = []; $cache = []; $checks = 0;
function add_action($name, $callback, ...$rest) { $GLOBALS['hooks'][$name][] = $callback; }
function sanitize_key($v) { return preg_replace('/[^a-z0-9_-]/', '', strtolower($v)); }
function get_option($key, $default = false) { return $default; }
function get_transient($key) { return $GLOBALS['cache'][$key]['value'] ?? false; }
function set_transient($key, $value, $ttl) { $GLOBALS['cache'][$key] = ['value' => $value, 'ttl' => $ttl]; return true; }
function check(bool $value, string $name): void {
    $GLOBALS['checks']++;
    if (!$value) { throw new RuntimeException('FAIL: ' . $name); }
}
// The accepted 0.7.4.0.20 source is layered: the root contains production
// overrides and plugin/ supplies unchanged files when the installable ZIP is built.
require __DIR__ . '/../plugin/includes/platform/interface-m360-module.php';
require __DIR__ . '/../plugin/includes/platform/class-m360-module-registry.php';
require __DIR__ . '/../includes/enrichment/bootstrap.php';
$runtime = file_get_contents(__DIR__ . '/../includes/class-m360-core.php');
check(is_string($runtime) && substr_count($runtime, "includes/enrichment/bootstrap.php") === 1, 'root runtime loads enrichment bootstrap once');
final class SyntheticProvider implements M360_Enrichment_Provider {
    public int $calls = 0;
    public array $raw;
    public bool $fail = false;
    public string $name = 'synthetic-test';
    public function cache_namespace(): string { return $this->name; }
    public function fetch(array $context): array {
        $this->calls++;
        if ($this->fail) { throw new RuntimeException('DO_NOT_EXPOSE_SQL_OR_SECRET'); }
        return $this->raw;
    }
}
$registry = new M360_Module_Registry();
foreach ($hooks['m360_platform_register_modules'] as $hook) { $hook($registry); }
$module = $registry->get('editorial-dw-enrichment');
check($module instanceof M360_Enrichment_Module, 'platform module registered');
check(!$registry->is_enabled($module->id()), 'disabled by default');
$module->boot();
check(array_keys($hooks) === ['m360_platform_register_modules'], 'no public content/save/network hooks');
check($module->health()['status'] === 'warning', 'incomplete integration is not reported healthy');
// Synthetic IDs deliberately do not represent a production mapping.
$scope = ['team_id' => '9007199254740993', 'competition_id' => '999', 'competition_key' => 'premier-league', 'season' => '2025/2026', 'phase' => 'LEAGUE_STAGE', 'group' => null, 'locale' => 'en-US'];
$allowed = ['premier-league' => '999'];
$resolve = fn($bindings, $locale = 'en-US') => M360_Enrichment_Context::resolve($bindings, $allowed, $locale);
check($resolve([])['status'] === 'unmatched', 'no entity, no default team');
check($resolve([$scope])['context']['team_id'] === '9007199254740993', 'BIGINT precision preserved');
check($resolve([$scope, $scope])['status'] === 'ok', 'same explicit scope deduplicated');
$other = array_replace($scope, ['team_id' => '2']);
check($resolve([$scope, $other])['status'] === 'ambiguous', 'two teams never resolved by array order');
check($resolve([$scope], 'es-ES')['status'] === 'unsupported', 'unsupported locale');
check($resolve([array_replace($scope, ['competition_id' => '1'])])['status'] === 'unsupported', 'actual DW competition ID allowlist');
check($resolve([array_replace($scope, ['team_id' => 42])])['status'] === 'unmatched', 'no lossy numeric ID coercion');
check(M360_Enrichment_Context::id('18446744073709551616') === null, 'uint64 overflow rejected');
check(M360_Enrichment_Context::id('18446744073709551615') !== null, 'uint64 maximum preserved');
$reordered = array_reverse($scope, true);
check(M360_Enrichment_Context::key($scope) === M360_Enrichment_Context::key($reordered), 'context key independent of key order');
$now = strtotime('2026-09-06T12:00:00+00:00');
$metrics = ['position' => 2, 'points' => 7, 'played' => 3, 'won' => 2, 'drawn' => 1, 'lost' => 0, 'goals_for' => 5, 'goals_against' => 2, 'goal_difference' => 3, 'dw_updated_at' => '2026-09-06T11:59:00+00:00'];
$raw = ['context' => $scope, 'standings' => $metrics, 'sql' => 'MUST_NOT_LEAK'];
$validate = fn($r) => M360_Enrichment_Contract::normalize($r, $scope, $now, 300);
$result = $validate($raw);
check($result['status'] === 'ok' && !isset($result['sql']), 'valid scoped factual payload, unknown fields removed');
check($validate(['context' => $scope, 'standings' => null])['status'] === 'no_data', 'absent data not manufactured');
$partial = $raw; unset($partial['standings']['won']);
check($validate($partial)['status'] === 'partial' && $validate($partial)['standings']['won'] === null, 'missing statistic stays null');
$deduction = $raw; $deduction['standings']['points'] = -2;
check($validate($deduction)['status'] === 'ok', 'points deductions not recalculated from matches');
$bad = $raw; $bad['standings']['played'] = 10;
check($validate($bad)['status'] === 'unavailable', 'inconsistent played/won/drawn/lost rejected');
$bad = $raw; $bad['standings']['goal_difference'] = 99;
check($validate($bad)['status'] === 'unavailable', 'inconsistent goal difference rejected');
$bad = $raw; $bad['context']['season'] = '2026/2027';
check($validate($bad)['status'] === 'unavailable', 'wrong season from provider rejected');
foreach (['2026-09-06T11:59:00', '2026-02-30T11:59:00+00:00', '2026-09-06T13:00:00+00:00'] as $date) {
    $bad = $raw; $bad['standings']['dw_updated_at'] = $date;
    check($validate($bad)['status'] === 'unavailable', 'invalid/future/offset-free timestamp rejected');
}
$bad = $raw; $bad['standings']['dw_updated_at'] = "2026-09-06T11:59:00+00:00\0";
check($validate($bad)['status'] === 'unavailable', 'malformed timestamp cannot throw ValueError');
$bad = $raw; $bad['standings']['dw_updated_at'] = '2026-09-06T11:55:00+00:00';
check($validate($bad)['status'] === 'stale', 'age limit uses DW timestamp, inclusive expiry');
$bad = $raw; $bad['standings']['dw_updated_at'] = '2026-09-06T08:59:00-03:00';
check($validate($bad)['status'] === 'ok', 'equivalent explicit offset accepted');
$provider = new SyntheticProvider(); $provider->raw = $raw;
$service = new M360_Enrichment_Service($provider, $allowed, 120, 30, 300);
check($service->read([$scope], 'en-US', $now)['status'] === 'no_data' && $provider->calls === 0, 'cache miss does not call provider');
check($service->refresh([$scope, $other], 'en-US', $now)['status'] === 'ambiguous' && $provider->calls === 0, 'ambiguous scope never queries provider');
check($service->refresh([$scope], 'en-US', $now)['status'] === 'ok' && $provider->calls === 1, 'explicit refresh stores normalized data');
for ($i = 0; $i < 10; $i++) { $service->read([$scope], 'en-US', $now + 1); }
check($provider->calls === 1, 'repeated reads hit cache, no provider calls');
check($service->read([$scope], 'en-US', $now + 120)['status'] === 'no_data', 'expired cache suppressed even when store retains it');
check($service->read([$other], 'en-US', $now)['status'] === 'no_data', 'different team cannot consume previous snapshot');
check($service->read([array_replace($scope, ['season' => '2026/2027'])], 'en-US', $now)['status'] === 'no_data', 'different season isolated');
check($service->read([$scope], 'pt-BR', $now)['status'] === 'no_data', 'locale isolated');
$newProvider = new SyntheticProvider(); $newProvider->name = 'synthetic-test-v2'; $newProvider->raw = $raw;
$otherService = new M360_Enrichment_Service($newProvider, $allowed, 120, 30, 300);
check($otherService->read([$scope], 'en-US', $now)['status'] === 'no_data', 'provider version isolates cache');
$provider->fail = true;
$failure = $service->refresh([$scope], 'en-US', $now);
check($failure['status'] === 'unavailable' && !str_contains(json_encode($failure), 'SECRET'), 'exceptions reduced to safe failure status');
$before = $provider->calls;
check($service->read([$scope], 'en-US', $now + 1)['status'] === 'unavailable' && $provider->calls === $before, 'negative result cached');
check($service->read([$scope], 'en-US', $now + 30)['status'] === 'no_data', 'negative cache expires');
$provider->fail = false; $provider->raw = $raw;
// Revalidation must catch max age earlier than the nominal cache TTL.
$longCache = new M360_Enrichment_Service($provider, $allowed, 300, 30, 300);
$longCache->refresh([$scope], 'en-US', $now);
check($longCache->read([$scope], 'en-US', $now + 240)['status'] === 'stale', 'cache write does not renew factual freshness');
foreach ($GLOBALS['cache'] as &$entry) {
    if (($entry['value']['result']['status'] ?? '') === 'ok') { $entry['value']['result']['context']['team_id'] = '2'; }
}
unset($entry);
check($longCache->read([$scope], 'en-US', $now)['status'] === 'unavailable', 'cached mismatched identity rejected');
echo 'PASS: ' . $checks . " assertions; synthetic data, no DW or WordPress connection.\n";
