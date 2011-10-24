<?php
// set_include_path('/Applications/MAMP/bin/php5/lib/php'); //set path to PHPUnit.php directory
// require_once('PHPUnit.php');
require_once('../../ARC2.php');
require_once('../talis/Talis_ChangeSetBuilderPlugin.php');

class Talis_ChangeSetBuilderPluginTest extends PHPUnit_TestCase{
	
	var $o;
	
	var $cs = 'http://purl.org/vocab/changeset/schema#';
	
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
		$args =  array(
			'before' => $this->g1,
			'after' => array(),
			);
		$this->o = ARC2::getComponent('Talis_ChangeSetBuilderPlugin', $args);
	}
	
	function tearDown(){
		unset($this->o);
	}
	
	function test_delete_resources(){
		$expected = 0;
		foreach($this->g1 as $uri => $props){
			foreach($props as $p => $os){
				$expected += count($os);
			}
		}
		$actual = 0;
		foreach($this->o->__index as $uri => $props){
			if(isset($props[$this->cs.'removal'])) $actual += count($props[$this->cs.'removal']);
		}
		$this->assertEquals($expected, $actual);
	}	

	function test_added_metadata(){
		$args =  array(
			'after' => $this->g1,
			'before' => array(),
			'properties' => array(
				'http://purl.org/dc/elements/1.1/source' => array(array('value' => 'http://example.org/home.rdf', 'type' => 'uri')),
				),
			);
		$this->o = ARC2::getComponent('Talis_ChangeSetBuilderPlugin', $args);
		$test = false;
		foreach($this->o->__index as $uri => $props){
			if($types = ($props['http://www.w3.org/1999/02/22-rdf-syntax-ns#type']) AND $types[0]['value'] == 'http://purl.org/vocab/changeset/schema#ChangeSet' ){
				foreach($args['properties'] as $mp => $mobjs){
					$test = (isset($props[$mp]) AND $props[$mp]==$mobjs);
				}
			}
		}
		return $this->assertTrue($test);
	}


	function test_add_resources(){
		
		$args =  array(
			'after' => $this->g1,
			'before' => array(),
			);
		$this->o = ARC2::getComponent('Talis_ChangeSetBuilderPlugin', $args);
		
		
		$expected = 0;
		foreach($this->g1 as $uri => $props){
			foreach($props as $p => $os){
				$expected += count($os);
			}
		}
		$actual = 0;
		foreach($this->o->__index as $uri => $props){
			if(isset($props[$this->cs.'addition'])) $actual += count($props[$this->cs.'addition']);
		}
		$this->assertEquals($expected, $actual);
	}	


}
foreach(get_declared_classes() as $class)
{
	$suite = new PHPUnit_TestSuite($class);
	$result = PHPUnit::run($suite);
	print $result->toString();
}
?>