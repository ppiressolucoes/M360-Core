<?php
/**
 * Plugin Name: M360 Core
 * Version: 0.4.2.7
 * Author: Mengão 360 | DW Esportivo
 * Text Domain: m360-core
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

if (!defined('ABSPATH')) { exit; }

if (!defined('M360_CORE_VERSION')) { define('M360_CORE_VERSION', '0.4.2.7'); }
if (!defined('M360_CORE_FILE')) { define('M360_CORE_FILE', __FILE__); }
if (!defined('M360_CORE_PATH')) { define('M360_CORE_PATH', plugin_dir_path(__FILE__)); }
if (!defined('M360_CORE_URL')) { define('M360_CORE_URL', plugin_dir_url(__FILE__)); }

require_once M360_CORE_PATH . 'includes/class-m360-core.php';

register_activation_hook(__FILE__, ['M360_Core_Runtime_034', 'activate']);
register_deactivation_hook(__FILE__, ['M360_Core_Runtime_034', 'deactivate']);

add_action('plugins_loaded', static function (): void { M360_Core_Runtime_034::instance()->boot(); });
