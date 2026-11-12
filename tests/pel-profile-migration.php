<?php
define('ABSPATH', __DIR__);
$options = [];
function get_option($key, $default = false) { global $options; return $options[$key] ?? $default; }
function update_option($key, $value, $autoload = null) { global $options; $options[$key] = $value; return true; }
function register_activation_hook(...$args) {}
function add_action(...$args) {}
final class M360_Site_Profile {
    public static array $profile = [];
    public static int $writes = 0;
    public static function get(): array { return self::$profile; }
    public static function update(array $profile): bool { self::$writes++; self::$profile = $profile; return true; }
}
require __DIR__ . '/../deployments/portal-energia-limpa/plugin/m360-pel-deployment.php';
function check($condition, $message) { if (!$condition) { throw new RuntimeException($message); } }
$key = 'm360_pel_controlled_deployment';
M360_Site_Profile::$profile = ['runtime' => ['mode' => 'legacy-compatible', 'capabilities' => ['ads_runtime' => true, 'newsletter_runtime' => true, 'search_view' => false]]];
M360_PEL_Controlled_Deployment::migrate_dynamic_view_ownership();
check(M360_Site_Profile::$writes === 0, 'Unapplied profile changed');
$options[$key] = ['applied' => true];
M360_PEL_Controlled_Deployment::migrate_dynamic_view_ownership();
$runtime = M360_Site_Profile::$profile['runtime'];
check($runtime['mode'] === 'legacy-compatible' && $runtime['capabilities']['ads_runtime'] && $runtime['capabilities']['newsletter_runtime'], 'Unrelated settings changed');
foreach (['search_view', 'author_view', 'category_view', 'tag_view', 'date_view'] as $name) { check($runtime['capabilities'][$name] === true, 'Missing view: ' . $name); }
check(!empty($options[$key]['dynamic_views_migrated']), 'Migration marker missing');
M360_Site_Profile::$profile['runtime']['capabilities']['search_view'] = false;
M360_PEL_Controlled_Deployment::migrate_dynamic_view_ownership();
check(M360_Site_Profile::$writes === 1 && !M360_Site_Profile::$profile['runtime']['capabilities']['search_view'], 'User change overwritten');
echo "PEL migration checks passed\n";
