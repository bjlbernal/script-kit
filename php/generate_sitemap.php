#!/usr/local/bin/php -q
<?php
function debug($data) {
  print_r($data);
  echo "\n";
}

include_once 'class.mycurl.php';
include_once 'class.siteMap.php';

if (!empty($argv[1]) and in_array($argv[1], array('--help', '-h'))) {
  echo <<<HELP
This script takes a provided domain and curls the page that is rendered. 
From the provided html, domain based links that are not rel="nofollow" are parsed out and stored in a url keyed list.
Each link from the url keyed list is compared against the sitemap list and added if missing.
That link is then curled and the process begins again.
\t--help (-h)\t\tHelp information.\n
HELP;
}
else if (empty($argv[1])) {
  echo <<<INVALID
To few arguments were provided. Pass a URL or --help (-h).\n
INVALID;
}
else if (!empty($argv[1])) {
  $SiteMap = new siteMap();
  $SiteMap->generate($argv[1]);
  debug($SiteMap->sitemap);
}
