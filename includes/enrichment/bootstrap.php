<?php
if (!defined('ABSPATH')) { exit; }

require_once __DIR__ . '/class-m360-enrichment-context.php';
require_once __DIR__ . '/interface-m360-enrichment-provider.php';
require_once __DIR__ . '/class-m360-enrichment-contract.php';
require_once __DIR__ . '/class-m360-enrichment-service.php';
require_once __DIR__ . '/class-m360-enrichment-module.php';

add_action('m360_platform_register_modules', static function (M360_Module_Registry $registry): void {
    $registry->register(new M360_Enrichment_Module());
});
