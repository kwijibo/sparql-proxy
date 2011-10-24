<?php
/*
homepage: 
license:  http://arc.semsol.org/license

class:    Talis_Store
author:   Keith Alexander
version:  2008-10-06

notes: 
		tightened up some conditionals in platform_sparql method
		changed sparql method from GRAPH() to query() (content-type agostic) 
*/

define('MORIARTY_ARC_DIR', dirname(__FILE__).'../../../'); 

ARC2::inc('Store');

require_once 'moriarty/store.class.php';
require_once 'moriarty/credentials.class.php';

class Talis_StorePlugin extends ARC2_Store {
	
	var $a;
	var $store;
	var $contentbox;
	var $sparqlservice;
	var $credentials = false;
	var $errors = array();
	var $uri;
	var $fetch_graphs = false;
	var $platform_uri ='http://api.talis.com/stores/';
	var $parser;

	function __construct($a = '', &$caller) {
	  $this->credentials = (isset($a['db_user']) AND isset($a['db_pwd']))? new Credentials($a['db_user'], $a['db_pwd']) : false;
	  if(isset($a['platform_uri'])) $this->platform_uri =  $a['platform_uri'];
	  $this->uri = $this->platform_uri.$a['store_name'];
	  $this->store = new Store($this->uri, $this->credentials);
	  if(!$this->credentials AND !isset($a['multisparql'])) $this->sparqlservice = $this->store->get_sparql_service();
	  else $this->sparqlservice = $this->store->get_multisparql_service();
	
	  $this->contentbox = $this->store->get_contentbox();
	  $this->fetch_graphs = isset($a['fetch_graphs']);
	  $this->parser = ARC2::getRDFParser();
	  parent::__construct($a, $caller);
	}

	function Talis_Store ($a = '', &$caller) {
	  $this->__construct($a, $caller);
	}  

	function __init() {
	  parent::__init();
	}
	
	function isSetUp(){
		return true;
	}
	function setUp(){
		return true;
	}

	function reset(){
		$t1 = ARC2::mtime();
		$response = $this->store->get_job_queue()->schedule_reset_data($t1);
		if(!$response->is_success()){
			$this->addError($response->body);
		}else{
			return 'Rest data job scheduled for '.$t1;
		}
	}
	
	function drop(){
		return false;
	}
	
	function import($arc_store, $graph_uri=false, $offset= 0, $chunksize=1000){
		$t1 = ARC2::mtime();
		
		$q = 'CONSTRUCT { ?resource ?p ?v } WHERE { ?resource ?p ?v } LIMIT '.$chunksize;
		$continue = true;
		$t_count = 0;
		while($continue){
			$data = $arc_store->query($q.' OFFSET '.$offset, 'raw');
			if(empty($data)) $continue=false;
			else{
				echo "\r\n importing records from ".$offset.' to '.($offset+$chunksize);
				$data = $this->urize_resources($data);
				$r = $this->insert($data, $graph_uri);
				$t_count+=$r['result']['t_count'];
			}
			$offset+=$chunksize;
		}
		$t2 = ARC2::mtime();
		
		$rs = array(
			'result' => array(
				't_count' => $t_count,
				'load_time' => ($t2-$t1),
				),
			
			);
		return $rs;
		
		
	}
	
	function export($arc_store, $graph_uri=false, $offset= 0, $chunksize=1000){
		$t1 = ARC2::mtime();
		$q = 'CONSTRUCT  { ?resource ?p ?v } '.(($graph_uri)? 'FROM <'.$graph_uri.'>' : '').' WHERE { ?resource ?p ?v } LIMIT '.$chunksize;
		$continue = true;
		$t_count = 0;
		$results = array();
		if(!$graph_uri) $graph_uri = $this->uri.'/meta';
		while($continue){
			echo "\r\n exporting records from ".$offset.' to '.($offset+$chunksize);
			$data = $this->query($q.' OFFSET '.$offset, 'raw');
			if(empty($data)) $continue=false;
			else {
				$r = $arc_store->insert($data, $graph_uri);
				$t_count+=$r['t_count'];
				$results[]=$r;
			}
			$offset+=$chunksize;
		}
		
		$t2 = ARC2::mtime();
		
		$rs = array(
			'result' => array(
				't_count' => $t_count,
				'load_time' => ($t2-$t1),
				'results' => $results,
				),
			
			);
		return $rs;
		
		
	}
	
	function change($before, $after, $graph_uri=false){
		$before = is_array($before) ? $this->toRDFXML($before) : $before;
		$after = is_array($after) ? $this->toRDFXML($after) : $after;
		
		$t1 = ARC2::mtime();
		if(!$graph_uri OR !stristr($graph_uri, $this->platform_uri)) $graph_uri=$this->uri.'/meta';
		
		$Graph = new Graph($graph_uri, $this->credentials);
		
		require_once 'moriarty/changesetbatch.class.php';
		/* args can be after, createdDate, creatorName, changeReason */
	  $args = array(
			'before' => $before,
			'after' => $after,
			'creatorName' => 'ARC2_Talis_Store class',
			'changeReason' => 'change method',
		);
		$cs = ARC2::getComponent('Talis_ChangeSetBuilderPlugin', $args);
		$cs_response = $Graph->apply_versioned_changeset($cs);
		if(!$cs_response->is_success()){
			$this->addError('Error with changeset from CHANGE method: '.$cs_response->body);
		}
		
		$t2 = ARC2::mtime();
		$del_time = $t2 - $t1;
		$rs = array(
			'result' => array(
				'delete_time' => $del_time,
				'index_update_time' =>$del_time,
				),

			);
		return $rs;
		
		
	}

	
	function search($query, $format='rss'){
		
	}
	
	function loadContent($content, $mimetype){
		$t1 = ARC2::mtime();
		$response =  $this->contentbox->post_content($content, $mimetype);
		$t2 = ARC2::mtime();
		$rs =  array(
			'result' => array( 'insert_time' => ($t2-$t1), ),
			);
		if($response->is_success()){
			if(isset($response->headers['location'])) $rs['location'] = $response->headers['location'];
			$rs['status code'] = $response->status_code;
			$rs['time'] = date(DATE_ATOM);
		}else{
			$rs['error'] = $response->body;
			$rs['status code'] = $response->status_code;
			$this->addError('Error Loading Content. The platform returned:'.$response->status_code.' | '.$response->body);
		}
		return $rs;
	}
	
	function load($url, $graph=''){
		$infos = array('query'=>array('url' => $url, 'target_graph'=>$graph));
		require_once('Talis_StoreLoadQueryHandler.php');
		$h =& new Talis_StoreLoadQueryHandler($this->a, $this);
	    $r = $h->runQuery($infos); 
	    $this->processTriggers('insert', $infos);
		return $r;
	}
	function insert($doc, $graph_uri=false, $dont_parse=false){
		$doc = is_array($doc) ? $this->toTurtle($doc) : $doc;
		
		if(!$dont_parse){
			$this->parser->__init();
			$this->parser->parse('', $doc);
			$triples = $this->parser->getTriples();
			$doc = $this->parser->toRDFXML($triples);
		}
		
		$t1 = ARC2::mtime();
		$success = false;
		
		if(!$graph_uri OR !stristr($graph_uri, $this->platform_uri)) $graph_uri=$this->uri.'/meta';
		$graph = new Graph($graph_uri, $this->credentials);
		$response = $graph->submit_rdfxml($doc);
		if(!$response->is_success()){
			$this->addError('Error with INSERT changeset: '.$response->body);
		} else {
			$success = true;
		}
		
	    $infos = array('query' => array('url' => $graph_uri, 'target_graph' => $graph_uri));
		$this->processTriggers('insert', $infos);
		$t2 = ARC2::mtime();
		$insert_time = $t2 - $t1;
		$rs = array(
				'success' => $success ,
				'load_time' => $insert_time,
				'index_update_time' =>$insert_time,
			
			);
		if(isset($triples)) $rs['t_count'] = count($triples);
		return $rs;
	}

	function query($q, $result_format = '', $src = ''){
		ARC2::inc('SPARQLPlusParser');
	    $p = & new ARC2_SPARQLPlusParser($this->a, $this);
	    $p->parse($q, $src);
	    $infos = $p->getQueryInfos();
	    $infos['result_format'] = $result_format;
	    if (!$p->getErrors()) {
	      $qt = $infos['query']['type'];
	      if (!in_array($qt, array('select', 'ask', 'describe', 'construct', 'load', 'insert', 'delete'))) {
	        return $this->addError('Unsupported query type "'.$qt.'"');
	      }
	      $t1 = ARC2::mtime();
	      $r = array('result' => $this->runQuery($q, $infos, $qt));
	      $t2 = ARC2::mtime();
	      $r['query_time'] = $t2 - $t1;
	      if ($result_format == 'raw') {
	        return $r['result'];
	      }
	      if ($result_format == 'rows') {
	        return $r['result']['rows'] ? $r['result']['rows'] : array();
	      }
	      if ($result_format == 'row') {
	        return $r['result']['rows'] ? $r['result']['rows'][0] : array();
	      }
	      return $r;
	    }
	    return 0;
	}

	function runQuery($q, $infos, $type){
		switch($type){
			case 'load':
				$result = $this->load($infos['query']['url'], $infos['query']['target_graph']);				    	
				$this->processTriggers('insert', $infos);
				break;
			case 'insert':
			case 'delete':
				$result = $this->sparqlPLUS($infos);
			break;
			case 'select':
			case 'ask':
			case 'describe':
			case 'construct':
				$result = $this->platform_SPARQL($q, $infos, $type);
			break;
			
		}
		return $result;
	}

	function platform_SPARQL($q, $infos, $type){
		$t = $infos['query'];
		if(!empty($t['dataset']) AND $this->fetch_graphs){
			foreach($t['dataset'] as $no => $dataset){
				if(!stristr($dataset['graph'], $this->platform_uri)){
					$this->load($dataset['graph']);
					unset($t['dataset'][$no]);
				}
			}

			$q = $this->sparql_info_to_string($t);
		}
		
		
		
		$prefix_str = '';
		foreach($infos['prefixes'] as $p => $n){
			$prefix_str.='PREFIX '.$p.' <'.$n.'> ';
		}
		
		$response = $this->sparqlservice->query($prefix_str.$q);
		if(!$response->is_success()){
			$this->addError($response->body);
		}else{
			$doc = $response->body;
			switch($type){
				case 'select':
				$array = $this->sparqlservice->parse_select_results($doc);
				return $this->moriarty_select_array_to_arc_rows($array);
				break;
				case 'ask':
				return $this->sparqlservice->parse_ask_results($doc);
				break;
				case 'describe':
				case 'construct':
				$parser = ARC2::getRDFXMLParser();
				$parser->parse(false, $doc);
				return $parser->getSimpleIndex(0);
				break;
			}
		}
	}
	
	function moriarty_select_array_to_arc_rows($results){
		$rows = array();
		foreach($results as $result){
				$row = array();			
			foreach($result as $bindingName => $binding){
				$row[$bindingName] = $binding['value'];
				$row[$bindingName.' type'] = $binding['type'];
				if(isset($binding['datatype'])) $row[$bindingName.' dt'] = $binding['datatype'];
				if(isset($binding['lang'])) $row[$bindingName.' lang'] = $binding['lang'];

			}

			$rows[]=$row;
		}
		return array('rows'=> $rows);
	}
	
	
	function sparqlPLUS($infos){
		$t1 = ARC2::mtime();
		
		/*GET CONSTRUCT query to fetch RDF*/
		$query = $infos['query'];
		$type = $query['type'];
		$query['type'] = 'construct';
		
		if(!isset($query['construct_triples']) AND isset($query['pattern'])){
			$query['construct_triples'] = $query['pattern'];
		}
		elseif(!isset($query['pattern']) AND isset($query['construct_triples'])){
			if($type=='insert'){
				 $ser = ARC2::getComponent('ARC2_SPARQLSerializerPlugin');
				 $rdf = $ser->toString($query['construct_triples']);
				 return $this->insert($rdf, $query['target_graph']);
			}else if($type=='delete'){
				$query['pattern'] = array(
					'type' => 'group',
					'patterns' => $query['construct_triples']
					);	
			}
		}
		elseif(!empty($query['target_graphs'])){
			$sparql = 'CONSTRUCT { ?s ?p ?o } ';
			foreach($query['target_graphs'] as $from){
				$sparql.= ' FROM <'.$from.'> ';
			}
			$sparql .= 'WHERE { ?s ?p ?o }';
			$p = & new ARC2_SPARQLPlusParser($this->a, $this);
		    $p->parse($sparql, false);
		    $new_infos = $p->getQueryInfos();
		    $new_infos['query']['type'] = $type;
		 	return $this->sparqlPLUS($new_infos);
		}
		$new_infos = $infos;
		$new_infos['query'] =$query;
		if(!ARC2::inc('ARC2_SPARQLSerializerPlugin')){
			die("INSERT and DELETE depends on the ARC2_SPARQLSerializerPlugin available from http://n2.talis.com/svn/playground/kwijibo/PHP/arc/plugins/trunk/ARC2_SPARQLSerializerPlugin/ARC2_SPARQLSerializerPlugin.php");
		}
		$ser = ARC2::getComponent('ARC2_SPARQLSerializerPlugin');
		$construct_string = $ser->toString($new_infos);
				
		$response = $this->sparqlservice->graph($construct_string);
		$query_time = ARC2::mtime() - $t1;
		if(!$response->is_success()){
			$this->addError('Error  in CONSTRUCT: '.$response->body.' query was: '.$construct_string);
			$rs = array(
				'query_time' => $query_time,
				'result' => false
				);
			return $rs;
			
		}else{
			$rdf = $response->body;
			$parser = ARC2::getRDFParser();
			$parser->parse('', $rdf);
			$triples = $parser->getTriples();
					/*fetched RDF*/
			/* Now depending on whether it was 
			 an insert or a delete, we submit to metabox, 
			or apply the changeset to delete */
			if($type=='insert'){
				
			    $this->insert($rdf, $query['target_graph']);
				$t2 = ARC2::mtime();
				$insert_time = $t2 - $t1;
				$rs = array(
					'construct_query' => $construct_string,
					'query_time' => $query_time,
					'result' => array(
						't_count' => count($triples),
						'load_time' => $insert_time,
						'index_update_time' =>$insert_time,
						),

					);
				return $rs;
			}
			else if($type=='delete'){
				$graph = (isset($query['dataset']))? $query['dataset'][0]['graph'] : false;
				if(isset($query['dataset'][1])) $this->addError("Cross-graph deletions are not supported yet. Awaiting Improved Named Graph - see PDN10");
				$this->change($rdf, false, $graph);
				
				$t2 = ARC2::mtime();
				$del_time = $t2 - $t1;
				$rs = array(
					'construct query' => $construct_string,
					'query_time' => $query_time,
					'result' => array(
						't_count' => count($triples),
						'delete_time' => $del_time,
						'index_update_time' =>$del_time,
						),

					);
				return $rs;
				
			}
			else{
				$this->addError('unknown query type');
				$rs = array(
					'query_time' => $query_time,
					'result' => false
					);
				return $rs;
				
			}
			
		}
		


	}
	
	
	function triple_to_string($t){
		$str = '';
		if(empty($t)) return '';
		foreach(array('s','p','o') as $term){
			$str.= ' '.$this->term_to_string($term, $t);
		}
		return $str.' . ';
	}
	
	function term_to_string($term, $triple){
		switch($triple[$term.'_type']){
			case 'var':
				return '?'.$triple[$term];
			case 'literal':
			case 'literal2':
				return '"""'.str_replace('"""','\""""', $term).'"""';
			case 'iri':
				return '<'.$triple[$term].'>';
		}
	}
	
	function urize_resources($resources){
		$bnodes = array();
		foreach($resources as $uri => $props){
			if($this->is_bnode($uri)){
				$new_uri = $this->bnode_to_uri($uri);
				$resources[$new_uri] = $resources[$uri];
				unset($resources[$uri]);
				$bnodes[]=$uri;
			}
		}
		if(!empty($bnodes)){
			$s1 = array_shift(array_keys($resources));
			$p1 = array_shift(array_keys($resources[$s1]));
			$o1 = $resources[$s1][$p1][0];
			$k = (isset($o1['value']))? 'value' : 'val';
			foreach($resources as $uri => $props){
				foreach($props as $p => $objs){
					foreach($objs as $n =>  $o){
						if($o['type']=='bnode'){
							$new_uri = $this->bnode_to_uri($o[$k]);
							$resources[$uri][$p][$n][$k] = $new_uri;
						}
					}
				}
			}
		}
		return $resources;
	}
	
	function is_bnode($id){
		return substr($id,0,2)=='_:';
	}

	function bnode_to_uri($uri){
		return $this->uri.'/items/bnodes/'.substr($uri,2);
	}

}


?>
