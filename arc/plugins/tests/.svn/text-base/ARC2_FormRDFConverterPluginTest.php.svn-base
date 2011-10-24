<?php
// set_include_path('/Applications/MAMP/bin/php5/lib/php'); //set path to PHPUnit.php directory
// require_once('PHPUnit.php');
require_once('../../ARC2.php');


class ARC2_FormRDFConverterPluginTest extends PHPUnit_TestCase{
	
	var $o;
	var $ex = array(
	  'http://example.org/test/JohnSmith' => 
	  array(
	    'http://xmlns.com/foaf/0.1/name' => 
	    array(
	      0 => 
	      array(
	        'value' => 'John Smith',
	        'type' => 'literal',
	      ),
	    ),
	    'http://www.w3.org/2000/01/rdf-schema#label' => 
	    array(
	      0 => 
	      array(
	        'value' => 'Label',
	        'type' => 'literal',
	        'lang' => 'en-gb',
	      ),
	    ),
	    'http://xmlns.com/foaf/0.1/mbox' => 
	    array(
	      0 => 
	      array(
	        'value' => 'mailto:john@example.org',
	        'type' => 'literal',
	      ),
	    ),
	    'http://xmlns.com/foaf/0.1/nick' => 
	    array(
	      0 => 
	      array(
	        'value' => 'johnno',
	        'type' => 'literal',
	      ),
	      1 => 
	      array(
	        'value' => 'johnsmith',
	        'type' => 'literal',
	      ),
	    ),
	    'http://xmlns.com/foaf/0.1/age' => 
	    array(
	      0 => 
	      array(
	        'value' => 21,
	        'type' => 'literal',
	        'datatype' => 'http://www.w3.org/2001/XMLSchema#integer',
	      ),
	    ),
	  ),
	  '_:bnode1' => 
	  array(
	    'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' => 
	    array(
	      0 => 
	      array(
	        'type' => 'uri',
	        'value' => 'http://xmlns.com/foaf/0.1/Person',
	      ),
	    ),
	    'http://xmlns.com/foaf/0.1/name' => 
	    array(
	      0 => 
	      array(
	        'value' => 'John Smith',
	        'type' => 'literal',
	      ),
	    ),
	    'http://www.w3.org/2000/01/rdf-schema#label' => 
	    array(
	      0 => 
	      array(
	        'value' => 'Label',
	        'type' => 'literal',
	        'lang' => 'en-gb',
	      ),
	    ),
	    'http://xmlns.com/foaf/0.1/mbox' => 
	    array(
	      0 => 
	      array(
	        'value' => 'mailto:john@example.org',
	        'type' => 'literal',
	      ),
	    ),
	    'http://xmlns.com/foaf/0.1/nick' => 
	    array(
	      0 => 
	      array(
	        'value' => 'johnno',
	        'type' => 'literal',
	      ),
	      1 => 
	      array(
	        'value' => 'johnsmith',
	        'type' => 'literal',
	      ),
	    ),
	    'http://xmlns.com/foaf/0.1/age' => 
	    array(
	      0 => 
	      array(
	        'value' => 21,
	        'type' => 'literal',
	        'datatype' => 'http://www.w3.org/2001/XMLSchema#integer',
	      ),
	    ),
	  ),
	);
	
	var $namespaces = array(
	    'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
	    'sioc' => 'http://rdfs.org/sioc/ns#',
	    'dc' => 'http://purl.org/dc/elements/1.1/',
	    'terms' => 'http://purl.org/dc/terms/',
	    'foaf' => 'http://xmlns.com/foaf/0.1/',
	    'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
	    'rss' => 'http://purl.org/rss/1.0/',
	    'owl' => 'http://www.w3.org/2002/07/owl#',
	    'rel' => 'http://purl.org/vocab/relationship/',
	    'ibis' => 'http://dannyayers.com/2003/12/ibis.rdfs#',
	    'doap' => 'http://usefulinc.com/ns/doap#',
	    'frbr' => 'http://purl.org/vocab/frbr/core#',
		'xsd' => 'http://www.w3.org/2001/XMLSchema#',
	  );

	var $resources = array (
	  'http://example.org/test/JohnSmith' => 
	  array (
	    'http://xmlns.com/foaf/0.1/name' => 
	    array (
	      0 => 
	      array (
	        'value' => 'John Smith',
	        'type' => 'literal',
	      ),
	    ),
	    'http://xmlns.com/foaf/0.1/mbox' => 
	    array (
	      0 => 
	      array (
	        'value' => 'mailto:john@example.org',
	        'type' => 'uri',
	      ),
	    ),
	    'http://www.w3.org/2000/01/rdf-schema#label' => 
	    array (
	      0 => 
	      array (
	        'value' => 'Label',
	        'type' => 'literal',
	        'lang' => 'en-gb',
	      ),
	    ),
	  ),
	);
	var $form_array_with_uris;
	
	var $resources_from_form;

	
	function __construct($name)
	{
		$this->PHPUnit_TestCase($name);
		$this->o = ARC2::getComponent('ARC2_FormRDFConverterPlugin');
		$this->form_array_with_uris = array(
				'prefixes' => $this->namespaces,
				'resources' => array(
					'http://example.org/test/JohnSmith' => array(
						'foaf:name' => 'John Smith',
		    			'rdfs:label@en-gb' => 'Label',
						'foaf:mbox' => 'mailto:john@example.org',
						'foaf:nick' => array('johnno','johnsmith'),
						'foaf:age^^xsd:integer' => 21,
						),
					'foaf:Person' => array(
						'foaf:name' => 'John Smith',
						'rdfs:label@en-gb' => 'Label',
						'foaf:mbox' => 'mailto:john@example.org',
						'foaf:nick' => array('johnno','johnsmith'),
						'foaf:age^^xsd:integer' => 21,
						),
					),
			);

		$properties = array_shift(array_values($this->resources));
		$bnode_resources = array('_:bnode1' => $properties);
	   	$this->resources_from_form = array_merge_recursive($this->resources);
		
	}
	
	function setUp(){
		// $classname = str_replace('Tests','', get_class($this));
		// $this->o = new $classname();
	}
	
	function tearDown(){
		unset($this->o);
	}
	
	function test_qname_to_uri(){
		$act = $this->o->qname_to_uri('foaf:Person', $this->namespaces);
		$ex = $this->namespaces['foaf'].'Person';
		$this->assertEquals($ex,$act);	
	}
	
	function count_triples($r){
		$c = 0;
		foreach($r as $u => $ps){
			foreach($ps as $p => $os) $c+= count($os);
		}
	}
	
	function test_convertToSimpleIndexExact(){

			$act = $this->o->convertToSimpleIndex($this->form_array_with_uris);
			$this->assertEquals($this->ex,$act);	
		}

		

		function test_convertToSimpleIndexRightLength(){

				$act = $this->o->convertToSimpleIndex($this->form_array_with_uris);

				$this->assertEquals($this->count_triples($this->ex),$this->count_triples($act));	
			}
		
		

		function test_formsArray_parse_property_lang(){
			$ex = array('lang'=>'en-gb','datatype'=>false,'property'=>'rdfs:label');
			$act = $this->o->parse_property('rdfs:label@en-gb');
			$this->assertEquals($ex,$act);	
		}
		function test_formsArray_parse_property_datatype(){
			$ex = array('lang'=>false,'datatype'=>'xsd:string','property'=>'rdfs:label');
			$act = $this->o->parse_property('rdfs:label^^xsd:string');
			$this->assertEquals($ex,$act);	
		}	

		function test_formsArray_parse_property(){
			$ex = array('lang'=>false,'datatype'=>false,'property'=>'rdfs:label');
			$act = $this->o->parse_property('rdfs:label');
			$this->assertEquals($ex,$act);	
		}

	

}
foreach(get_declared_classes() as $class)
{
	$suite = new PHPUnit_TestSuite($class);
	$result = PHPUnit::run($suite);
	print $result->toString();
}
?>
