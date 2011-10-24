<?php
// set_include_path('/Applications/MAMP/bin/php5/lib/php'); //set path to PHPUnit.php directory
// require_once('PHPUnit.php');
require_once('../../ARC2.php');
require_once('../ARC2_IndexUtilsPlugin.php');

class ARC2_ExhibitJSONSerializerPluginTest extends PHPUnit_TestCase{
	
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
	
	function test_getSerializedIndex_has_right_keys(){
		$ser = ARC2::getComponent('ARC2_ExhibitJSONSerializerPlugin');
		$json = $ser->getSerializedIndex($this->g1); 
		$data = json_decode($json,1);
		$conditions = true;
		foreach(array('items','properties','types') as $k){
			if(!isset($data[$k])){
				$conditions = false;
				break;
			}
		}
		$this->assertTrue($conditions);
	}

	function test_getSerializedIndex_validJSON(){
		$ser = ARC2::getComponent('ARC2_ExhibitJSONSerializerPlugin');
		$json = $ser->getSerializedIndex($this->g1);
		$data = json_decode($json,1);
		$this->assertTrue(!empty($data));
	}
	
	function test_items_have_labels_and_types(){
		$ser = ARC2::getComponent('ARC2_ExhibitJSONSerializerPlugin');
		$json = $ser->getSerializedIndex($this->g1);
		$data = json_decode($json,1);
		foreach($data['items'] as $item){
			if(!is_string($item['label']) OR  !isset($item['type'])){
				return $this->assertTrue(false);
			} 
			
		}
		return $this->assertTrue(true);		
	}

	

}
foreach(get_declared_classes() as $class)
{
	$suite = new PHPUnit_TestSuite($class);
	$result = PHPUnit::run($suite);
	print $result->toString();
}
?>
