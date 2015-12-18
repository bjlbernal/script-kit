#!/usr/local/bin/php -q
<?php
function debug($data) {
  print_r($data);
  echo "\n";
}

class mycurl { 
  protected $_useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1'; 
  protected $_url; 
  protected $_followlocation; 
  protected $_timeout; 
  protected $_maxRedirects; 
  protected $_cookieFileLocation = './cookie.txt'; 
  protected $_post; 
  protected $_postFields; 
  protected $_referer ="http://www.google.com"; 

  protected $_session; 
  protected $_webpage; 
  protected $_includeHeader; 
  protected $_noBody; 
  protected $_status; 
  protected $_binaryTransfer; 
  public    $authentication = 0; 
  public    $auth_name      = ''; 
  public    $auth_pass      = ''; 

  public function useAuth($use){ 
    $this->authentication = 0; 
    if($use == true) $this->authentication = 1; 
  } 

  public function setName($name){ 
    $this->auth_name = $name; 
  } 
  public function setPass($pass){ 
    $this->auth_pass = $pass; 
  } 

  public function __construct($url,$followlocation = true,$timeOut = 30,$maxRedirecs = 4,$binaryTransfer = false,$includeHeader = false,$noBody = false) 
  { 
    $this->_url = $url; 
    $this->_followlocation = $followlocation; 
    $this->_timeout = $timeOut; 
    $this->_maxRedirects = $maxRedirecs; 
    $this->_noBody = $noBody; 
    $this->_includeHeader = $includeHeader; 
    $this->_binaryTransfer = $binaryTransfer; 

    $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt'; 

  } 

  public function setReferer($referer){ 
    $this->_referer = $referer; 
  } 

  public function setCookiFileLocation($path) 
  { 
    $this->_cookieFileLocation = $path; 
  } 

  public function setPost ($postFields) 
  { 
    $this->_post = true; 
    $this->_postFields = $postFields; 
  } 

  public function setUserAgent($userAgent) 
  { 
    $this->_useragent = $userAgent; 
  } 

  public function createCurl($url = 'nul') 
  { 
    if($url != 'nul'){ 
      $this->_url = $url; 
    } 

    $s = curl_init(); 

    curl_setopt($s,CURLOPT_URL,$this->_url); 
    curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:')); 
    curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout); 
    curl_setopt($s,CURLOPT_MAXREDIRS,$this->_maxRedirects); 
    curl_setopt($s,CURLOPT_RETURNTRANSFER,true); 
    curl_setopt($s,CURLOPT_FOLLOWLOCATION,$this->_followlocation); 
    curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation); 
    curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation); 

    if($this->authentication == 1){ 
      curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass); 
    } 
    if($this->_post) 
    { 
      curl_setopt($s,CURLOPT_POST,true); 
      curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postFields); 

    } 

    if($this->_includeHeader) 
    { 
      curl_setopt($s,CURLOPT_HEADER,true); 
    } 

    if($this->_noBody) 
    { 
      curl_setopt($s,CURLOPT_NOBODY,true); 
    } 
    /* 
    if($this->_binary) 
    { 
      curl_setopt($s,CURLOPT_BINARYTRANSFER,true); 
    } 
    */ 
    curl_setopt($s,CURLOPT_USERAGENT,$this->_useragent); 
    curl_setopt($s,CURLOPT_REFERER,$this->_referer); 

    $this->_webpage = curl_exec($s); 
    $this->_status = curl_getinfo($s,CURLINFO_HTTP_CODE); 
    curl_close($s); 

  } 

  public function getHttpStatus() 
  { 
    return $this->_status; 
  } 

  public function __tostring(){ 
    return $this->_webpage; 
  } 
}

class siteMap {
  public $domain = null;
  public $url_keyed_list = array();
  public $sitemap = array();
  public $non200s = array();

  public function __construct() {
  }

  public function parseFollowLinksOnly($aTags) {
    foreach ($aTags as $k => $aTag) {
      $attributes = explode('" ', ltrim(rtrim($aTag, '>'), '<a '));
      $aTags[$k] = array('link' => $aTag);
  
      foreach ($attributes as $attribute) {
        $attribute = explode('=', $attribute);
        $attribute[1] = trim($attribute[1], '"');
        $aTags[$k][$attribute[0]] = $attribute[1];
      }
  
      if ($aTags[$k]['rel'] === 'nofollow' or 
          empty($aTags[$k]['href']) or
          in_array($aTags[$k]['href'], array('#', '/')) or
          substr($aTags[$k]['href'], 0, 1) === '#' or
          substr($aTags[$k]['href'], 0, 7) === 'mailto:') {
        unset($aTags[$k]);
      }
      else if (strpos($aTags[$k]['href'], '/') === 0) {
        $aTags[$k]['href'] = $this->domain . $aTags[$k]['href'];
      }
      else if (in_array(substr($aTags[$k]['href'], 0, strpos($aTags[$k]['href'], '://')), array('http', 'https'))) {
        $aTags[$k]['href'] = substr($aTags[$k]['href'], strpos($aTags[$k]['href'], '://')+3);
  
        if (substr($aTags[$k]['href'], 0, strpos($aTags[$k]['href'], '/')) !== $this->domain) {
          unset($aTags[$k]);
        }
      }
    }
  
    return $aTags;
  }

  public function pregMatchAllATags($string) {
    $aTags = array();
    preg_match_all('/<a [^>]+>/', $string, $aTags);
    return $aTags;
  }

  public function generate($url) {
    if (!isset($this->domain)) {
      $this->domain = $url;
      $this->sitemap[] = $url;
    }

    echo 'CURLing '. $url ."\n";

    $MyCurl = new mycurl();
    $MyCurl->createCurl($url);
      
    if ($MyCurl->getHttpStatus() === 200) {
      $string = $MyCurl->__tostring();
      $aTags = $this->pregMatchAllATags($string);
      $aTags = $this->parseFollowLinksOnly($aTags[0]);
      $this->url_keyed_list[$url] = $aTags;
  
      foreach ($aTags as $aTag) {
        if (!in_array($aTag['href'], $this->sitemap)) {
          $this->sitemap[] = $aTag['href'];
        }
      }

      foreach ($this->url_keyed_list[$url] as $link) {
        if (isset($this->url_keyed_list[$link['href']])) {
          continue;
        }

        foreach ($this->non200s as $status => $list) {
          if (in_array($link['href'], $list)) {
            continue 2;
          }
        }

        $this->generate($link['href']);
      }
    }
    else {
      echo 'Http Status: ' . $MyCurl->getHttpStatus() . "\n";
      echo 'CURL of ' . $url . " failed.\n";

      if (!isset($this->non200s[$MyCurl->getHttpStatus()])) {
        $this->non200s[$MyCurl->getHttpStatus()] = array();
      }

      $this->non200s[$MyCurl->getHttpStatus()][] = $url;

      if (in_array($url, $this->sitemap)) {
        $key = array_search($url, $this->sitemap);
        if ($key !== false) {
          unset($this->sitemap[$key]);
        }
      }

      return false;
    }

    echo 'Http Status: ' . $MyCurl->getHttpStatus() . "\n";
    echo 'CURL of '. $url ." completed.\n";

    return true;
  }
}

if (!empty($argv[1]) and in_array($argv[1], array('--help', '-h'))) {
  echo <<<HELP
This script takes a provided domain and curls the page that is rendered. 
From the provided html, domain based links that are not rel="nofollow" are parsed out and stored in a url keyed list.
Each link from the url keyed list is compared against the sitemap list and added if missing.
That link is then curled and the process begins again.
\t--help (-h)\t\tHelp information.
HELP;
}
else if (!empty($argv[1])) {
  $SiteMap = new siteMap();
  $SiteMap->generate($argv[1]);
  debug($SiteMap->sitemap);
}
