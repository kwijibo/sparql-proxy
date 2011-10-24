<?php
// set_include_path('/Applications/MAMP/bin/php5/lib/php'); //set path to PHPUnit.php directory
// require_once('PHPUnit.php');
require_once('../../ARC2.php');
require_once('../ARC2_IndexUtilsPlugin.php');

class ARC2_IndexUtilsPluginTest extends PHPUnit_TestCase{
	
	var $o;
	
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
	
	// function test_map(){
	// 	$input = array(
	// 		'#x' => array('#name' => array(array('value'=> 'Keith'),), '#nick'=> array(array('value'=> 'keithA')), '#foo' => array(array('value'=>'foo')) )
	// 		);
	// 		
	// 	$expected = ?
	// 	
	// }

	function test_filter(){
		$old = array(
			'#x' => array('#name' => array(array('value'=> 'Keith'),), '#nick'=> array(array('value'=> 'keithA')), '#foo' => array(array('value'=>'foo')) )
			);
		list($old2,$old3) = array($old, $old);
		unset($old2['#x']['#foo']);
		unset($old2['#x']['#name']);
		unset($old3['#x']['#nick']);
		unset($old3['#x']['#name']);
		$uri_filter = array('uri'=>  create_function('$u,$ps','return $u=="#x";'), );
		$property_filter = array('property'=>  create_function('$u,$p,$os','return $p=="#nick";'), );
		$property_filter2 = array('property'=>  create_function('$u,$p,$os','return $p=="#foo";'), );
		$object_filter = array('object'=>  create_function('$u,$p,$o','return $o[\'value\']=="foo";'), );
		$uri_and_property_filter = array_merge_recursive($uri_filter, $property_filter);
		$property_and_object_filter = array_merge_recursive($property_filter2, $object_filter);
		$uri_and_object_filter = array_merge_recursive($uri_filter, $object_filter);
		$uri_property_and_object_filter = array_merge_recursive($uri_filter, $property_filter2, $object_filter);
		
		$this->assertTrue(ARC2_IndexUtilsPlugin::filter($old, $uri_filter)==$old);
		$this->assertTrue(ARC2_IndexUtilsPlugin::filter($old, $property_filter)==$old2);
		$this->assertTrue(ARC2_IndexUtilsPlugin::filter($old, $object_filter)==$old3);
		$this->assertTrue(ARC2_IndexUtilsPlugin::filter($old, $uri_and_property_filter)==$old2);
		$this->assertTrue(ARC2_IndexUtilsPlugin::filter($old, $uri_and_object_filter)==$old3);
		$this->assertTrue(ARC2_IndexUtilsPlugin::filter($old, $property_and_object_filter)==$old3);
		$this->assertTrue(ARC2_IndexUtilsPlugin::filter($old, $uri_property_and_object_filter)==$old3);
		
	}

	function test_diff(){
	
		$_1 = array(
			'#x' => array('#name' => array(array('value'=> 'Keith'),), '#nick'=> array(array('value'=> 'keithA')), '#foo' => array(array('value'=>'foo')) )
			);	
		
		$_2 = array(
				'#x' => array('#name' => array(array('value'=> 'Keith'),), '#nick'=> array(array('value'=> 'keithAlexander')), '#foo' => array(array('value'=>'foo')) )
				);
		$expected = array(
					'#x' => array( '#nick'=> array(array('value'=> 'keithA'))),
					);
		$actual = ARC2_IndexUtilsPlugin::diff($_1,$_2);

		$this->assertEquals( $expected, $actual);
	}
	
	function test_intersect(){
		$_1 = array(
			'#x' => array('#name' => array(array('value'=> 'Keith'),), '#nick'=> array(array('value'=> 'keithA')), '#foo' => array(array('value'=>'foo')) )
			);	
		
		$_2 = array(
				'#x' => array('#name' => array(array('value'=> 'Keith'),), '#nick'=> array(array('value'=> 'keithAlexander')), '#foo' => array(array('value'=>'foo')) )
				);

		$actual = ARC2_IndexUtilsPlugin::intersect($_1,$_2);
		unset($_2['#x']['#nick']);
		$expected = $_2;
		$this->assertEquals( $expected, $actual);
		
		
	}
	
	function test_merge(){
		
		$g1 = array(						//uri
			'#x' => array(						//prop
					'name' => array(				//obj
						array(
						'value' => 'Joe',
						'type' => 'literal',
							),
						),				//obj
				),					//prop
			'_:y' => array(
					'name' => array(array(
						'value' => 'Joan',
						'type' => 'literal',
						),),
				),

			);

			$g2 = array(
				'#x' => array(
						'knows' => array( array(
							'value' => '_:y',
							'type' => 'bnode',
							),
						),
					),

				'_:y' => array(
						'name' => array(
							array(
							'value' => 'Susan',
							'type' => 'literal',
							),
							),
					),

				);

			$g3 = array (
			  '#x' => 
			  array (
			    'name' => 
			    array (
			      0 => 
			      array (
			        'value' => 'Joe',
			        'type' => 'literal',
			      ),
			    ),
			    'knows' => 
			    array (
			      0 => 
			      array (
			        'value' => '_:y1',
			        'type' => 'bnode',
			      ),
			    ),
			  ),
			  '_:y' => 
			  array (
			    'name' => 
			    array (
			      0 => 
			      array (
			        'value' => 'Joan',
			        'type' => 'literal',
			      ),
			    ),
			  ),
			  '_:y1' => 
			  array (
			    'name' => 
			    array (
			      0 => 
			      array (
			        'value' => 'Susan',
			        'type' => 'literal',
			      ),
			    ),
			  ),
			);

		$g4 = array(
			'#x' => array('#knows' => array(
				'type' => 'uri',
				'value' => 'Me'
				),
			),
			);

		$r1 = (ARC2_IndexUtilsPlugin::merge($g1,$g2));
		$this->assertEquals($r1, $g3);
	}
	
	function test_reify(){

		$triple = array(
			'#foo' => array('#knows' => array(array('type'=>'uri','value' =>'#bar'))),
			);
			$RDF = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
		$expected = array(
			'_:Statement1' => array(
				$RDF.'type' => array(
					array(
							'type' => 'uri',
							'value' => $RDF.'Statement',
						)
					),
				$RDF.'subject' => array(
						array(
								'type' => 'uri',
								'value' => '#foo',
							)
					),
				$RDF.'predicate' => array(
						array(
								'type' => 'uri',
								'value' => '#knows',
							)
					),
				$RDF.'object' => array(
						array(
								'type' => 'uri',
								'value' => '#bar',
							)
					),
				
				)
			);
		$actual = ARC2_IndexUtilsPlugin::reify($triple);
		
		$this->assertEquals($expected, $actual);
	}
	
	
	function test_dereify(){
		
		$triple = array(
			'#foo' => array('#knows' => array(array('type'=>'uri','value' =>'#bar'))),
			);
		
		$expected = $triple;
		$actual = ARC2_IndexUtilsPlugin::dereify(ARC2_IndexUtilsPlugin::reify($triple));
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
