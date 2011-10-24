<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';
require_once MORIARTY_DIR. 'httprequest.class.php';

class HttpRequestFactory {
  function make( $method, $uri, $credentials = null, $force_revalidate = false ) {
    return new HttpRequest( $method, $uri, $credentials, $force_revalidate );
  }
}
?>
