<?php
require_once '../../ARC2.php';

/* configuration */ 
$talis_config = array(
  // 'db_user' => 'kwijibo',
  // 'db_pwd' => 'j67dke03rr',
  'store_name' => 'schema-cache', // your store name
   'fetch_graphs' => false, // If set to true, using FROM will fetch the graph as a datasource over the web, and store it in /meta
);

$talis = ARC2::getComponent('Talis_StorePlugin', $talis_config);

//$r = $talis->load('file:///Users/robert/Code/Ruby/pbac.rdf');
$r = $talis->query('DELETE {  ?s <http://purl.org/dc/terms/format> ?x   } WHERE { ?s <http://purl.org/dc/terms/format> ?x . OPTIONAL { ?s <http://purl.org/dc/terms/date> ?date } . FILTER(!bound(?date)) .  }  ');

var_dump($r, $talis->getErrors()); die;

/* Now we have a Store object that we can add data to and query */

//$talis->reset(); //empty the store

// $talis->query("LOAD <http://twitter.com/bengee>"); //LOAD some data
// 
// $query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> DESCRIBE ?s WHERE { ?s foaf:knows ?someone . }";
// 
// $resources = $talis->query($query, 'raw');	
// $ser = ARC2::getTurtleSerializer();
// echo "First Query: \n\n";
// echo $ser->getSerializedIndex($resources);
// 
// 
// $result = $talis->query("prefix xfn: <http://gmpg.org/xfn/11#>
// prefix foaf: <http://xmlns.com/foaf/0.1/>
// INSERT INTO <http://api.talis.com/stores/kwijibo-dev3/meta> { ?s foaf:knows ?o; a foaf:Person } WHERE { ?s xfn:contact ?o } ");
// var_dump('INSERT', $result);
// 
// $resources = $talis->query($query, 'raw');
// $ser = ARC2::getTurtleSerializer();
// echo "Query run again after INSERT of foaf:knows : \n\n";
// echo $ser->getSerializedIndex($resources);
// 	
// 
// $result = $talis->query("prefix xfn: <http://gmpg.org/xfn/11#>
// prefix foaf: <http://xmlns.com/foaf/0.1/>
// DELETE { ?s xfn:contact ?o; a foaf:Person } WHERE { ?s foaf:knows ?o } ");
// var_dump('DELETE', $result);
// 
// $resources = $talis->query($query, 'raw');
// $ser = ARC2::getTurtleSerializer();
// echo "Query run after DELETE of xfn:contact: \n\n";
// echo $ser->getSerializedIndex($resources);

$talis->query('prefix foaf:<http://xmlns.com/foaf/0.1/> DELETE FROM <http://api.talis.com/stores/kwijibo-dev3/meta> { <http://keithalexander.co.uk/#me>  a foaf:Person . } ');

$r = $talis->query('DESCRIBE <http://keithalexander.co.uk/#me>');

print_r($r);
	

print_r($talis->getErrors());



$arc_config = array(
  /* db */
  'db_host' => 'localhost', /* optional, default is localhost */
  'db_name' => 'ARC_1',
  'db_user' => 'root',
  'db_pwd' => 'root',

  /* store name (= table prefix) */
  'store_name' => 'arc_demo_store',
);

/* instantiation */
$arc = ARC2::getStore($arc_config);

if (!$arc->isSetUp()) {
 var_dump( $arc->setUp());
}

$talis->export($arc);

var_dump( $talis->getErrors(), $arc->getErrors());

?>