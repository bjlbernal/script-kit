#!/usr/local/bin/php
<?php
print_r($argv);

$reportCount = 0;

function ps_report(&$reportCount) {
    $report = [];
    $psOutput = explode("\n", shell_exec("ps"));
    array_shift($psOutput);
    array_pop($psOutput);

    foreach ($psOutput as $output) {
        if (stristr($output, 'pseudofork')) {
            $output = trim($output);
            $_o = preg_replace('/\s+/', ' ', $output);
            $_o = explode(" ", $_o);
            $report[] = [
                'PID'   => $_o[0],
                'TIME'  => $_o[2]
            ];
        }
    }

    if ($reportCount != count($report)) {
        $reportCount = count($report);
    } else {
        return true;
    }

    if ($reportCount <= 1) {
        return false;
    }

    echo implode('    ', array_keys($report[0])) . "\n";

    foreach ($report as $_r) {
        echo implode(' ', $_r) . "\n";
    }

    echo "===============\n";

    return true;
}

if (!empty($argv[1]) && $argv[1] === '-f') {
    if (!empty($argv[2]) && is_numeric($argv[2])) {
        for ($i = 0; $i < $argv[2]; $i++) {
            $sleepTime = 5 * ($argv[2] - $i);
            echo "executing pseudofork.php -s $sleepTime in background.\n";
            $cmd = "./pseudofork.php -s $sleepTime &> /dev/null &";
            exec($cmd);
        }

        $forkRunning = true;

        while ($forkRunning) {
            $forkRunning = ps_report($reportCount);
        }

        echo "\n";
    }
} else if (!empty($argv[1]) && $argv[1] === '-s') {
    if (!empty($argv[2]) && is_numeric($argv[2])) {
        echo "sleeping {$argv[2]} seconds.\n";
        sleep($argv[2]);
        echo "done sleeping, exiting.\n";
    }
}

