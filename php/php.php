#!/usr/local/bin/php -q
<?php
//$time = $argv[1];
$times = array(
  1429246808,
  1429246950,
  1429246950,

  1429246807,

  1429246889,
  1429246834,
  1429246859,
  1429246895
);
foreach ($times as $time) {
  echo $time .": (". $time .") ". date('Y-m-d H:i:s A T', $time) ."\n";
}
/*$time_strings = array(
  "midnight",
  "midnight EST",
  "today",
  "today midnight",
  "today midnight EST",
  'today 11:59:59 pm',
  'today 11:59:59 pm EST',
  "tonight",
  "tonight midnight",
  "tonight midnight EST",
  'tonight 11:59:59 pm',
  'tonight 11:59:59 pm EST',
  "tomorrow",
  "tomorrow midnight",
  "tomorrow midnight EST",
  'tomorrow 11:59:59 pm',
  'tomorrow 11:59:59 pm EST',
);

foreach ($time_strings as $time_string) {
  $time = strtotime($time_string);
  echo $time_string .": (". $time .") ". date('Y-m-d H:i:s A T', $time) ."\n";
}*/
