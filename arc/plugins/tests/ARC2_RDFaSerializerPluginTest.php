<?php
// set_include_path('/Applications/MAMP/bin/php5/lib/php'); //set path to PHPUnit.php directory
// require_once('PHPUnit.php');
require_once('../../ARC2.php');

class ARC2_RDFaSerializerPluginTest extends PHPUnit_TestCase{
	
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
	
	function test_getSerializedIndex(){
		$ser = ARC2::getComponent('ARC2_RDFaSerializerPlugin');
		$frag = $ser->getSerializedIndex($this->g1);
		$rdfa = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">'."\n".'<html><head></head><body>'.$frag.'</html>';
		$p = ARC2::getSemHTMLParser('rdfa');
		$p->parse(false, $rdfa);
		$ser = ARC2::getComponent('ARC2_RDFaSerializerPlugin');
		$frag2 = $ser->getSerializedIndex($p->getSimpleIndex(0));
		 $this->assertEquals($frag, $frag2);
	}

	

}
foreach(get_declared_classes() as $class)
{
	$suite = new PHPUnit_TestSuite($class);
	$result = PHPUnit::run($suite);
	print $result->toString();
}
?>
