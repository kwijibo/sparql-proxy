<?php
define('MORIARTY_DIR', 'moriarty/');
define('MORIARTY_ARC_DIR', 'arc/');
define('PHPCOHODO_DIR', 'phpcohodo/');
require 'settings.php';
require 'cache.php';
require PHPCOHODO_DIR.'Node.php';
require MORIARTY_DIR.'credentials.class.php';

$credentials = new Credentials(user,pass);
$node = new Node(KernelHostName);
echo "Connected to Kernel \n";
$node->subscribe(bucketID);
echo "Subscribing to Bucket \n";

$cache = new Cache();
echo "Connecting to Cache\n";


while (true) {
      echo "+++++++++++++++++++\n\n";
      if($data = $node->getUpdatesWithDigestAuthentication($credentials)){
        echo "\n**Flushing Cache**\n";
        $cache->flush();
      }
}

?>
