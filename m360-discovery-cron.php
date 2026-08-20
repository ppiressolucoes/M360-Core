<?php
/**
 * M360 Discovery prospective queue runner.
 * Intended exclusively for a server-side PHP cron command.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

$wp_load = dirname(__DIR__, 3) . '/wp-load.php';
if (!is_readable($wp_load)) {
    fwrite(STDERR, "M360 Discovery: wp-load.php not found\n");
    exit(1);
}

require_once $wp_load;

if (!class_exists('M360_Discovery_Scheduler')) {
    fwrite(STDERR, "M360 Discovery: Core scheduler unavailable\n");
    exit(1);
}

$result = M360_Discovery_Scheduler::process_queued(5);
echo 'M360 Discovery queue: considered=' . $result['considered']
    . ' processed=' . $result['processed']
    . ' skipped=' . $result['skipped'] . PHP_EOL;
