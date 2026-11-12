<?php
define('ABSPATH', __DIR__);
$scenario = $argv[1] ?? '';
$locale = $argv[2] ?? 'pt_BR';
if (in_array($scenario, ['yoast', 'force', 'false'], true)) { define('WPSEO_VERSION', 'test'); }
if ($scenario === 'class') { class WPSEO_Options {} }
class WP_Term { public $term_id = 12; public $taxonomy = 'category'; public $name = 'Mercado'; }
function shortcode_atts($defaults, $values, $shortcode = '') { return array_merge($defaults, $values); }
function wp_style_is(...$args) { return false; } function wp_script_is(...$args) { return false; }
function get_locale() { global $locale; return $locale; }
function is_front_page() { return false; } function is_author() { return false; } function is_category() { return false; }
function is_tag() { return false; } function is_date() { return false; } function is_search() { return false; }
function is_404() { return false; } function is_singular() { return true; } function is_page() { return false; }
function get_queried_object_id() { return 55; } function get_post_type($id) { return 'post'; }
function get_the_category($id) { return [new WP_Term()]; } function get_ancestors(...$args) { return []; }
function get_term_link($term) { return 'https://example.test/category/mercado/'; }
function is_wp_error($v) { return false; } function home_url($path) { return 'https://example.test' . $path; }
function get_the_title($id) { return 'Artigo'; } function get_permalink($id) { return 'https://example.test/artigo/'; }
function get_query_var($key) { return 0; } function wp_strip_all_tags($s) { return strip_tags($s); }
function esc_html($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); } function esc_attr($s) { return esc_html($s); }
function esc_url($s) { return esc_html($s); } function wp_json_encode($data, $flags) { return json_encode($data, $flags); }
function apply_filters($name, $value, ...$args) { global $scenario; return $name === 'm360_breadcrumb_schema_enabled' && in_array($scenario, ['force', 'false'], true) ? true : $value; }
require __DIR__ . '/../plugin/includes/navigation/class-m360-navigation-shortcodes.php';
$atts = ['show_current' => 'true'];
if ($scenario === 'false') { $atts['schema'] = 'false'; }
$html = M360_Navigation_Shortcodes::breadcrumb($atts);
$expected = in_array($scenario, ['no-yoast', 'force'], true) ? 1 : 0;
if (substr_count($html, 'BreadcrumbList') !== $expected) { throw new RuntimeException('Unexpected schema: ' . $scenario); }
foreach (['<nav ', 'aria-label="' . ($locale === 'en_US' ? 'Breadcrumb' : 'Trilha de navegação') . '"', 'https://example.test/category/mercado/', 'Mercado', 'aria-current="page"', 'Artigo'] as $part) {
    if (!str_contains($html, $part)) { throw new RuntimeException('Visual breadcrumb missing: ' . $part); }
}
if (!str_contains($html, $locale === 'en_US' ? 'Home' : 'Início')) { throw new RuntimeException('Locale label missing'); }
if ($expected && substr_count(M360_Navigation_Shortcodes::breadcrumb($atts), 'BreadcrumbList') !== 0) { throw new RuntimeException('Duplicate core schema'); }
echo "$scenario $locale: OK\n";
