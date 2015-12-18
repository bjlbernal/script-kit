#!/usr/local/bin/php
<?php
print_r($argv);

$report_count = 0;

function ps_report(&$report_count) {
  $report = array();
  $ps_output = explode("\n", shell_exec("ps"));
  array_shift($ps_output);
  array_pop($ps_output);

  foreach ($ps_output as $output) {
    if (stristr($output, 'pseudofork')) {
      $output = trim($output);
      $_o = preg_replace('/\s+/',' ',$output);
      $_o = explode(" ", $_o);
      $report[] = array('PID' => $_o[0],
                        'TIME' => $_o[2]
                      );
    }
  }

  if ($report_count != count($report)) {
    $report_count = count($report);
  }
  else {
    return true;
  }

  if ($report_count <= 1) {
    return false;
  }

  echo implode('    ', array_keys($report[0]))."\n";

  foreach ($report as $_r) {
    echo implode(' ', $_r)."\n";
  }

  echo "===============\n";

  return true;
}

if (!empty($argv[1]) and $argv[1] === '-f') {
  if (!empty($argv[2]) and is_numeric($argv[2])) {
    for ($i = 0; $i<$argv[2];$i++) {
      $sleep_time = 5*($argv[2]-$i);
      echo "executing pseudofork.php -s $sleep_time in background.\n";
      $cmd = "./pseudofork.php -s $sleep_time &> /dev/null &";
      exec($cmd);
    }
    
    $fork_running = true;

    while ($fork_running) {
      $fork_running = ps_report($report_count);
    }

    echo "\n";
  }
}
else if (!empty($argv[1]) and $argv[1] === '-s') {
  if (!empty($argv[2]) and is_numeric($argv[2])) {
    echo "sleeping {$argv[2]} seconds.\n";
    sleep($argv[2]);
    echo "done sleeping, exiting.\n";
  }
}

