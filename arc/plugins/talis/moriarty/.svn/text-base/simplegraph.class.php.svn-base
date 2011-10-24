<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';
require_once MORIARTY_ARC_DIR . "ARC2.php";

class SimpleGraph {
  var $_index = array();
  var $_ns = array (
                    'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
                    'owl' => 'http://www.w3.org/2002/07/owl#',
                    'cs' => 'http://purl.org/vocab/changeset/schema#',
                    'bf' => 'http://schemas.talis.com/2006/bigfoot/configuration#',
                    'frm' => 'http://schemas.talis.com/2006/frame/schema#',

                    'dc' => 'http://purl.org/dc/elements/1.1/',
                    'dct' => 'http://purl.org/dc/terms/',
                    'dctype' => 'http://purl.org/dc/dcmitype/',

                    'foaf' => 'http://xmlns.com/foaf/0.1/',
                    'bio' => 'http://purl.org/vocab/bio/0.1/',
                    'geo' => 'http://www.w3.org/2003/01/geo/wgs84_pos#',
                    'rel' => 'http://purl.org/vocab/relationship/',
                    'rss' => 'http://purl.org/rss/1.0/',
                    'wn' => 'http://xmlns.com/wordnet/1.6/',
                    'air' => 'http://www.daml.org/2001/10/html/airport-ont#',
                    'contact' => 'http://www.w3.org/2000/10/swap/pim/contact#',
                    'ical' => 'http://www.w3.org/2002/12/cal/ical#',
                    'frbr' => 'http://purl.org/vocab/frbr/core#',

                    'ad' => 'http://schemas.talis.com/2005/address/schema#',
                    'lib' => 'http://schemas.talis.com/2005/library/schema#',
                    'dir' => 'http://schemas.talis.com/2005/dir/schema#',
                    'user' => 'http://schemas.talis.com/2005/user/schema#',
                    'sv' => 'http://schemas.talis.com/2005/service/schema#',
                  );
  function set_namespace_mapping($prefix, $uri) {
    $this->_ns[$prefix] = $uri;
  }


  function add_resource_triple($s, $p, $o) {
    $triple = array( 's' => $s,'p' => $p,'o' => $o);
    $triple['s_type'] = strpos($s, '_:' ) === 0 ? 'bnode' : 'uri';
    $triple['o_type'] = strpos($o, '_:' ) === 0 ? 'bnode' : 'uri';
    $triples = array( $triple );
    $this->_add_arc2_triple_list( $triples );
  }

  function add_literal_triple($s, $p, $o, $lang = null, $dt = null) {
    $triple = array( 's' => $s,'p' => $p,'o' => $o, 'o_type' => 'literal');
    $triple['s_type'] = strpos($s, '_:' ) === 0 ? 'bnode' : 'uri';
    if ( $lang != null ) {
      $triple['o_lang'] = $lang;
    }
    if ( $dt != null ) {
      $triple['o_dt'] = $dt;
    }
    $triples = array( $triple );
    $this->_add_arc2_triple_list( $triples );
  }

  function get_triples() {
    return ARC2::getTriplesFromIndex($this->_to_arc_index($this->_index));
  }

  function get_index() {
    return $this->_index;
  }


  function to_rdfxml() {
    $serializer = ARC2::getRDFXMLSerializer(
        array(
          'ns' => $this->_ns,
        )
      );
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
 }

  function to_turtle() {
    $serializer = ARC2::getTurtleSerializer(
        array(
          'ns' => $this->_ns,
        )
      );
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
  }

  function to_ntriples() {
    $serializer = ARC2::getComponent('NTriplesSerializer', array());
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
  }



  function to_json() {
    $serializer = ARC2::getRDFJSONSerializer(
        array(
          'ns' => $this->_ns,
        )
      );
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
  }
  function get_first_literal($s, $p, $default = null) {
    if ( array_key_exists($s, $this->_index) && array_key_exists($p, $this->_index[$s]) ) {
      foreach ($this->_index[$s][$p] as $value) {
        if ($value['type'] == 'literal') {
          return $value['value'];
        }
      }
    }
    else {
      return $default;
    }
  }

  function get_first_resource($s, $p, $default = null) {
    if ( array_key_exists($s, $this->_index) && array_key_exists($p, $this->_index[$s]) ) {
      foreach ($this->_index[$s][$p] as $value) {
        if ($value['type'] == 'uri' || $value['type'] == 'bnode' ) {
          return $value['value'];
        }
      }
    }
    else {
      return $default;
    }
  }

  function remove_resource_triple( $s, $p, $o) {
    for ($i = count($this->_index[$s][$p]) - 1; $i >= 0; $i--) {
      if (($this->_index[$s][$p][$i]['type'] == 'uri' || $this->_index[$s][$p][$i]['type'] == 'bnode') && $this->_index[$s][$p][$i]['value'] == $o)  {
        array_splice($this->_index[$s][$p], $i, 1);
      }
    }

    if (count($this->_index[$s][$p]) == 0) {
      unset($this->_index[$s][$p]);
    }
    if (count($this->_index[$s]) == 0) {
      unset($this->_index[$s]);
    }

  }

  function remove_triples_about($s) {
    unset($this->_index[$s]);
  }


  function from_rdfxml($rdfxml, $base='') {
    if ($rdfxml) {
      $this->remove_all_triples();
      $this->add_rdfxml($rdfxml, $base);
    }
  }

  function add_rdfxml($rdfxml, $base='') {
    if ($rdfxml) {
      $parser = ARC2::getRDFXMLParser();
      $parser->parse($base, $rdfxml );
      $this->_add_arc2_triple_list($parser->getTriples());
    }
  }

  function from_turtle($turtle, $base='') {
    if ($turtle) {
      $this->remove_all_triples();
      $this->add_turtle($turtle, $base);
    }
  }

  function add_turtle($turtle, $base='') {
    if ($turtle) {
      $parser = ARC2::getTurtleParser();
      $parser->parse($base, $turtle );
      $this->_add_arc2_triple_list($parser->getTriples());
    }
  }


  function _add_arc2_triple_list(&$triples) {
    foreach ($triples as $t) {
      $obj = array();
      $obj['value'] = $t['o'];
      if ($t['o_type'] === 'iri' ) {
        $obj['type'] = 'uri';
      }
      elseif ($t['o_type'] === 'literal1' ||  
              $t['o_type'] === 'literal2' || 
              $t['o_type'] === 'long_literal1' || 
              $t['o_type'] === 'long_literal2' 
      ) {
        $obj['type'] = 'literal';
      }
      else {
        $obj['type'] = $t['o_type'];
      }
      
      if ($obj['type'] == 'literal') {
        if ( isset( $t['o_dt'] ) && $t['o_dt'] ) {
          $obj['datatype'] = $t['o_dt'];
        }
        if ( isset( $t['o_lang']) && $t['o_lang'])  {
          $obj['lang'] = $t['o_lang'];
        }
      }         

      if (!isset($this->_index[$t['s']])) { 
        $this->_index[$t['s']] = array();
        $this->_index[$t['s']][$t['p']] = array($obj);
      }
      elseif (!isset($this->_index[$t['s']][$t['p']])) {
        $this->_index[$t['s']][$t['p']] = array($obj);
      }
      else {          
        if ( ! in_array( $obj, $this->_index[$t['s']][$t['p']] ) ) {
          $this->_index[$t['s']][$t['p']][] = $obj;       
        }
      }
    }
  }


  // until ARC2 upgrades to support RDF/PHP we need to rename all types of "uri" to "iri"
  function _to_arc_index(&$index) {
    $ret = array();

    foreach ($index as $s => $s_info) {
      $ret[$s] = array();
      foreach ($s_info as $p => $p_info) {
        $ret[$s][$p] = array();
        foreach ($p_info as $o) {
          $o_new = array();
          foreach ($o as $key => $value) {
            if ( $key == 'type' && $value == 'uri' ) {
              $o_new['type'] = 'iri';
            }
            else {
              $o_new[$key] = $value;
            }
          }
          $ret[$s][$p][] = $o_new;
        }
      }
    }
    return $ret;
  }

  function has_resource_triple($s, $p, $o) {
    if (array_key_exists($s, $this->_index) ) {
      if (array_key_exists($p, $this->_index[$s]) ) {
        foreach ($this->_index[$s][$p] as $value) {
          if ( ( $value['type'] == 'uri' || $value['type'] == 'bnode') && $value['value'] == $o) {
            return true;
          }
        }
      }
    }

    return false;
  }


  function get_resource_triple_values($s, $p) {
    $values = array();
    if (array_key_exists($s, $this->_index) ) {
      if (array_key_exists($p, $this->_index[$s]) ) {
        foreach ($this->_index[$s][$p] as $value) {
          if ( ( $value['type'] == 'uri' || $value['type'] == 'bnode')) {
            $values[] = $value['value'];
          }
        }
      }
    }
    return $values;
  }
  
  function subject_has_property($s, $p) {
    if (array_key_exists($s, $this->_index) ) {
      return (array_key_exists($p, $this->_index[$s]) );
    }
    return false;
  }  
  
  function remove_property_values($s, $p) {
    unset($this->_index[$s][$p]);

  }

  function remove_all_triples() {
    $this->_index = array();
  }



}

?>
