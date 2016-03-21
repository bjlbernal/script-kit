<?php
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

