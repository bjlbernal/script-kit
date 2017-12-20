#!/usr/local/bin/php -q
<?php
setlocale(LC_MONETARY, 'en_US');

$penny = 0.01;
$loopLength = 365;

echo "What happens when you are given a penny and then it doubles every day for a year?\n";
usleep(500000);

for ($d = 0; $d < $loopLength; $d++) {
    $day = $d + 1;
    $money = money_format('%.2n', $penny);
    echo "Day $day : $money\n";
    $penny *= 2;
    usleep(75000);
}

