<?php
define('MORIARTY_ARC_DIR', 'arc/');
define('MORIARTY_ALWAYS_CACHE_EVERYTHING', true);

require 'moriarty/httprequestfactory.class.php';
require 'moriarty/sparqlservice.class.php';
require 'moriarty/credentials.class.php';
require 'cache.php';
require 'settings.php';

if(isset($_REQUEST['query'])){

$HttpRequestFactory = new HttpRequestFactory();

  if(function_exists('memcache_connect')){
    $MemCacheObject = new Cache();
    $HttpRequestFactory->set_cache($MemCacheObject);
  }

  $SparqlService = new SparqlService(endpoint, new Credentials(user,pass) , $HttpRequestFactory);

  $Response = $SparqlService->query($_REQUEST['query'], 'application/json');

  header("content-type: application/json");
  echo $Response->body;
  exit; 
} else {

  require 'form.html';
}

?>
