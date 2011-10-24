<?php
ARC2::inc('StoreLoadQueryHandler');

class Talis_StoreLoadQueryHandler extends ARC2_StoreLoadQueryHandler {
	
	var $tripleBuffer = array();
	var $serialiser = false;
	var $bufferCount =0;
	
	function __construct($a = '', &$caller){
		$this->serialiser = ARC2::getRDFXMLSerializer();
		parent::__construct($a = '', &$caller);
	}
	function runQuery($infos, $data = '', $keep_bnode_ids = 0) {
    $url = $infos['query']['url'];
    $graph = $infos['query']['target_graph'];
    $this->target_graph = $graph ? $graph : false;
    $this->keep_bnode_ids = $keep_bnode_ids;
    /* reader */
    ARC2::inc('Reader');
    $reader =& new ARC2_Reader($this->a, $this);
    $reader->activate($url, $data);
    /* format detection */
    $mappings = array(
	  'xml' => 'RDFXML',
	
      'rdfxml' => 'RDFXML', 
      'turtle' => 'Turtle', 
      'ntriples' => 'Turtle', 
      'rss' => 'RSS',
      'n3' => 'Turtle', 
      'html' => 'SemHTML'
    );
    $format = $reader->getFormat();
    if (!$format || !isset($mappings[$format])) {
      return $this->addError('No loader available for "' .$url. '": ' . $format);
    }
    /* format loader */
    $suffix = 'Store' . $mappings[$format] . 'Loader';
    ARC2::inc($suffix);
    $cls = 'ARC2_' . $suffix;
    $loader =& new $cls($this->a, $this);
    $loader->setReader($reader);
    /* table lock */

    // /* logging */
    $this->t_count = 0;
     $this->t_start = ARC2::mtime();
  
   // $this->log_inserts = $this->v('store_log_inserts', 0, $this->a);
    // if ($this->log_inserts) {
    //   @unlink("arc_insert_log.txt");
    //   $this->inserts = array();
    //   $this->insert_times = array();
    //   $this->t_prev = $this->t_start;
    //   $this->t_count_prev = 0 ;
    // }

    /* load and parse */
    $r = $loader->parse($url, $data);
	$this->sendBuffer();

    /* done */
    // if ($this->log_inserts) {
    //   $this->logInserts();
    // }

    if ((rand(1, 50) == 1)) $this->store->optimizeTables();
    $t2 = ARC2::mtime();
    $dur = round($t2 - $this->t_start, 4);
    $r = array(
      't_count' => $this->t_count,
      'load_time' => $dur,
    );

    // if ($this->log_inserts) {
    //   $r['inserts'] = $this->inserts;
    //   $r['insert_times'] = $this->insert_times;
    // }
    return $r;
  }

  /*  */

  function addT($s, $p, $o, $s_type, $o_type, $o_dt = '', $o_lang = '') {
	$this->t_count++;
	$g = $this->target_graph;
    // $type_ids = array ('iri' => '0', 'bnode' => '1' , 'literal' => '2');
    // $g = $this->getTermID($this->target_graph, '0', 'id');

    $s = (($s_type == 'bnode') ) ? $this->store->uri.'/items/bnodes/' . abs(crc32($g . $s)) . '_' . substr(substr($s, 2), -10) : $s;
    $o = (($o_type == 'bnode') ) ? $this->store->uri.'/items/bnodes/' . abs(crc32($g . $o)) . '_' . substr(substr($o, 2), -10) : $o;
    /* triple */
	$triple = compact('s','p','o','o_type','o_dt','o_lang');

	/* add triple to buffer */
	$this->bufferCount++;
	$bufferSize = 1000;
	if($this->bufferCount < $bufferSize){
		$this->tripleBuffer[]=$triple;
	}else{
		$this->sendBuffer();
	}
	
  }


  function sendBuffer(){
	$this->bufferCount=0;
	$rdfxml = $this->serialiser->getSerializedTriples($this->tripleBuffer);
	$this->store->insert($rdfxml, $this->target_graph, true);
	$this->tripleBuffer = array();
  }

  
}
?>