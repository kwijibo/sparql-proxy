<?php
function logMessage($msg){
  $time = date('c');
  $line = "{$time}\t{$msg}\n";
  file_put_contents('./log.txt', $line, FILE_APPEND);
}
define('Memcache_host', 'localhost');
define('Memcache_port', '11211');
define('Memcache_age', '60*60*24*31');

class CachedResponse
{
	var $eTag;
	var $generatedTime;
	var $lastModified;
	var $mimetype;
	var $body;
	var $cacheable;

	function serve()
	{
		header("Content-type: {$this->mimetype}");
		header("Last-Modified: {$this->lastModified}");
		header("ETag: {$this->eTag}");
		header("x-served-from-cache: true");
		echo $this->body;
	}
}

class Cache
{

  var $connection = false;
  var $_options = array();

    function __construct($options=array()){
        $this->_options = $options;
        $this->connection = memcache_connect(Memcache_host, Memcache_port);
    }

    public function flush(){
        logMessage("Flushing Cache");
        if(!$this->connection) $this->connection = memcache_connect(Memcache_host, Memcache_port);
        $this->connection->flush();
    }

    public function load($id, $doNotTestCacheValidity = FALSE, $doNotUnserialize = FALSE) {
        logMessage("Retrieving {$id} from Cache");
        if(!$this->connection) $this->connection = memcache_connect(Memcache_host, Memcache_port);
        if(@$tmp = $this->connection->get($id)){
          if (is_array($tmp)) {
              return $tmp[0];
          }
        }
        return false;
    }

    function getLifetime($specificLifetime){
      if($specificLifetime){
          return Memcache_age;
      }
    }

    public function save($data, $id, $tags = array(), $specificLifetime = false, $priority=0){
      
      logMessage("Saving {$id} to Cache");

      $lifetime = $this->getLifetime($specificLifetime);
        
        if(!$this->connection) $this->connection = memcache_connect(Memcache_host, Memcache_port);
        
        if (isset($this->_options['compression']) AND $this->_options['compression']) {
            $flag = MEMCACHE_COMPRESSED;
        } else {
            $flag = 0;
        }
        if ($this->test($id)) {
            // because set and replace seems to have different behaviour
            @$result = $this->connection->replace($id, array($data, time(), $lifetime), $flag, $lifetime);
        } else {
            @$result = $this->connection->set($id, array($data, time(), $lifetime), $flag, $lifetime);
        }
       return $result;
 
    }

    public function remove($id){
      
      if(!$this->connection) $this->connection = memcache_connect(Memcache_host, Memcache_port);
      
      return $this->connection->delete($id);    
    }

    public function test($id){
    
        if(@$tmp = $this->connection->get($id)){
          if (is_array($tmp)) {
              return $tmp[1];
          }
        }

        return false;
    
    }


	
	private static function cacheKey($requestUri, $mimetype)
	{
		$key  = $requestUri;
		$key .= trim($mimetype);
		return md5($key);
	}
	
}

?>
