<?php
// set_include_path('/Applications/MAMP/bin/php5/lib/php'); //set path to PHPUnit.php directory
// require_once('PHPUnit.php');
require_once('../../ARC2.php');
require_once('../ARC2_IndexUtilsPlugin.php');

class ARC2_TriXSerializerPluginTest extends PHPUnit_TestCase{
	
	var $o;
	var $g1 = array(
			'http://example.org' => array(
				'http://purl.org/dc/elements/1.1/title' => array(
						array('value'=> 'Hello World', 'type' => 'literal'),
					),
				'http://purl.org/dc/elements/1.1/subject' => array(
						array('value'=> 'http://dbpedia.org/resource/Hello_World', 'type' => 'uri'),
					),
				'http://purl.org/dc/elements/1.1/subject' => array(
							array('value'=> '_:bnode1', 'type' => 'bnode'),
						),
				'http://purl.org/dc/elements/1.1/comments' => array(
							array('value'=> 'Some text here', 'type' => 'literal'),
						),
				
				
				),
			'_:bnode1' => array(
						'http://purl.org/dc/elements/1.1/title' => array(
								array('value'=> 'Foo Bar', 'type' => 'literal'),
							),
				),
		);
	
	
	function __construct($name)
	{
		$this->PHPUnit_TestCase($name);
	}
	
	function setUp(){
		// $classname = str_replace('Tests','', get_class($this));
		// $this->o = new $classname();
	}
	
	function tearDown(){
		unset($this->o);
	}
	
	function test_top_level_is_graph(){
		$ser = ARC2::getComponent('ARC2_TriXSerializerPlugin');
		$xml = $ser->getSerializedIndex($this->g1);
		$doc = new DomDocument();
		$doc->loadXML($xml);
		$xpath = new DomXPath($doc);
		$xpath->registerNamespace('trix', 'http://www.w3.org/2004/03/trix/trix-1/');
		return $this->assertTrue($xpath->query('/trix:TriX/trix:graph')->item(0));
		
	}
	
	function test_triple_count(){
		
	
		
	}
	
	function test_ns(){
		$ser = ARC2::getComponent('ARC2_TriXSerializerPlugin');
		$xml = $ser->getSerializedIndex($this->g1);
		$doc = new DomDocument();
		$doc->loadXML($xml);
		$xpath = new DomXPath($doc);
		$xpath->registerNamespace('trix', 'http://www.w3.org/2004/03/trix/trix-1/');
		return $this->assertTrue($xpath->query('/trix:TriX')->item(0));
		
	}
	
	function test_valid_xml(){
		$ser = ARC2::getComponent('ARC2_TriXSerializerPlugin');
		$xml = $ser->getSerializedIndex($this->g1);
		$doc = new DomDocument();
		return $this->assertTrue($doc->loadXML($xml));
	}
	

}
foreach(get_declared_classes() as $class)
{
	$suite = new PHPUnit_TestSuite($class);
	$result = PHPUnit::run($suite);
	print $result->toString();
}
?>
