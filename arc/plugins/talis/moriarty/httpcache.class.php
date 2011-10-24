<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';

class HttpCache {
  var $_directory;
  
  function __construct($directory) {
    $this->_directory = $directory;
    if (substr($this->_directory, -1) != DIRECTORY_SEPARATOR) {
      $this->_directory .= DIRECTORY_SEPARATOR;
    }
  }


  function make_conditional_request($request) {
    return $request;
  }
  
  function write($request, $response) {
    $filename = $this->get_cache_filename($request);
    $fp = fopen($filename, 'w');
    if ($fp) {
      fwrite($fp,serialize($response));
      fclose($fp);    
      chmod($filename, 0777);
    }
  }

  function get_cached_response($request) {
    $filename = $this->get_cache_filename($request);
    if ( file_exists($filename)) {
      $content = file_get_contents($filename);
      if ($content !== FALSE ) {
        $response = unserialize($content);
        return $response;
      }       
    }
    return null;
  }
  
  function is_fresh(&$request, &$response) {
    $filename = $this->get_cache_filename($request);
    $cache_control = $response->headers['cache-control'];
    $cache_control_tokens = split(',', $cache_control);
    foreach ( $cache_control_tokens as $token) {
      $token = trim($token);
      if ( preg_match('/max-age=(.+)/', $token, $m) ) {
        $max_age = $m[1];
        if ( time() - filectime($filename) > $max_age ) {
          return false;
        } 
      }
    }
    
    return true;
  }
  
  function remove_from_cache($request) {
    $filename = $this->get_cache_filename($request);
    if ( file_exists($filename)) {
      unlink($filename);
    }   
  }
  
  function get_cache_filename($request) {
    $accept = $request->headers['Accept'];
    $accept_parts = split(',', $accept);
    sort($accept_parts);
    $accept = join(',', $accept_parts);
    return $this->_directory . md5('<' . $request->uri . '>' . $accept . $request->get_body()); 
  }
  

}
?>
