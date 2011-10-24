<?php
require_once '../../ARC2.php';
// set_include_path('/Applications/MAMP/bin/php5/lib/php'); //set path to PHPUnit.php directory
// require_once('PHPUnit.php');

class ARC2_SPARQLSerializerPluginTest extends PHPUnit_TestCase {

	function test_toString(){
		$ser = ARC2::getComponent('ARC2_SPARQLSerializerPlugin');

$q = <<<_Q_

DESCRIBE ?s WHERE {

	?s ?p ?o .
	?s  <http://example.com/predicate> "Hello" .
		?s  <http://example.com/predicate> """Hello
		
		this is a long literal \"""
		
		"""@en-gb .
	FILTER(?o != ?s && REGEX(STR(?o), 'foo'))
}
LIMIT 10
OFFSET 0
_Q_;

		ARC2::inc('SPARQLPlusParser');
		$p = ARC2::getSPARQLParser();
		$p->parse($q);
		$infos = $p->getQueryInfos();
//		var_dump($infos);
		$q2 = ($ser->toString($infos));
		$p = ARC2::getSPARQLParser();
		$p->parse($q2);
		$infos2 = $p->getQueryInfos();
		$q3 = ($ser->toString($infos2));
		$this->assertEquals($q3, $q2);
	}

}
foreach(get_declared_classes() as $class)
{
	$suite = new PHPUnit_TestSuite($class);
	$result = PHPUnit::run($suite);
	print $result->toString();
}
?>