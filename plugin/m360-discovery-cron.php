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

$lock_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'm360-discovery-' . hash('sha256', $wp_load) . '.lock';
$lock = @fopen($lock_file, 'c');
if ($lock === false) {
    fwrite(STDERR, "M360 Discovery: cannot open CLI lock\n");
    exit(1);
}
if (!flock($lock, LOCK_EX | LOCK_NB)) {
    fclose($lock);
    echo "M360 Discovery queue: skipped (another run is active)\n";
    exit(0);
}

$exit_code = 0;
try {
    require_once $wp_load;
    if (!class_exists('M360_Discovery_Scheduler')) {
        throw new RuntimeException('Core scheduler unavailable');
    }

    // One due post per invocation keeps the hosting cost predictable. The
    // writer/backfill settings remain under the site's existing controls.
    $result = M360_Discovery_Scheduler::process_queued(1);
    echo 'M360 Discovery queue: considered=' . $result['considered']
        . ' processed=' . $result['processed']
        . ' skipped=' . $result['skipped']
        . ' disabled=' . ($result['disabled'] ? '1' : '0') . PHP_EOL;
} catch (Throwable $error) {
    fwrite(STDERR, 'M360 Discovery error: ' . get_class($error) . ': ' . $error->getMessage() . PHP_EOL);
    $exit_code = 1;
} finally {
    flock($lock, LOCK_UN);
    fclose($lock);
}
exit($exit_code);
