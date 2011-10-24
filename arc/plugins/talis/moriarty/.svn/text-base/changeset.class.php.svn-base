<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';
require_once MORIARTY_ARC_DIR . DIRECTORY_SEPARATOR . "ARC2.php";
require_once MORIARTY_DIR. 'simplegraph.class.php';

class ChangeSet extends SimpleGraph {

  var $subjectOfChange;
  var $before;
  var $before_rdfxml;
  var $after;
  var $after_rdfxml;
  var $createdDate = null;
  var $creatorName = null;
  var $changeReason = null;
  var $has_changes = false;
  var $cs_resource = null;
  var $include_count = 0;

  function ChangeSet($args='') {
    if(is_array($args)){foreach($args as $k=>$v){$this->$k=$v;}}/* subjectOfChange, after, createdDate, creatorName, changeReason */
    $this->init();
  }

  function init( ) {
    if ( isset( $this->after_rdfxml) || isset( $this->before_rdfxml) ) {
      $parser_args=array(
        "bnode_prefix"=>"genid",
        "base"=>""
      );

      if ( isset( $this->after_rdfxml) ) {
        $parser = ARC2::getRDFXMLParser($parser_args);
        $parser->parse("", $this->after_rdfxml );
        $this->after = $parser->getTriples();
      }
      if ( isset( $this->before_rdfxml) ) {
        $parser = ARC2::getRDFXMLParser($parser_args);
        $parser->parse("", $this->before_rdfxml );
        $this->before = $parser->getTriples();
      }
    }

    $this->_triples = array();

    $this->cs_resource = "_:cs";

    $this->add_resource_triple($this->cs_resource, RDF_TYPE, CS_CHANGESET );

    if ( substr( $this->subjectOfChange, 0, 2 ) == '_:') {
      $cs_cos = 'bnode';
    }
    else {
      $cs_cos = 'iri';
    }
    $this->add_resource_triple($this->cs_resource, CS_SUBJECTOFCHANGE, $this->subjectOfChange );

    if ($this->createdDate != null) {
      $this->add_literal_triple($this->cs_resource, CS_CREATEDDATE, $this->createdDate );
    }
    if ($this->creatorName != null) {
      $this->add_literal_triple($this->cs_resource, CS_CREATORNAME, $this->creatorName );
    }
    if ($this->changeReason != null) {
      $this->add_literal_triple($this->cs_resource, CS_CHANGEREASON, $this->changeReason );
    }

    if ( ! isset( $this->before ) ) {
      for($i=0,$i_max=count($this->after);$i<$i_max;$i++) {
        if ( $this->after[$i]['s'] == $this->subjectOfChange ) {

          $after_triple = $this->after[$i];
          $this->include_addition( $after_triple );
        }
      }
    }
    else {
      for($i=0,$i_max=count($this->after);$i<$i_max;$i++) {
        if ( $this->after[$i]['s'] == $this->subjectOfChange) {

          $after_triple = $this->after[$i];
          if ( ! $this->triple_in_list( $after_triple, $this->before)) {
            $this->include_addition( $after_triple );
          }
        }
      }
    }

    if ( ! isset( $this->after ) ) {
      for($i=0,$i_max=count($this->before);$i<$i_max;$i++) {
        if ( $this->before[$i]['s'] == $this->subjectOfChange ) {

          $before_triple = $this->before[$i];
          $this->include_removal( $before_triple );
        }
      }
    }
    else {
      for($i=0,$i_max=count($this->before);$i<$i_max;$i++) {
        if ( $this->before[$i]['s'] == $this->subjectOfChange ) {

          $before_triple = $this->before[$i];

          if ( ! $this->triple_in_list( $before_triple, $this->after)) {
            $this->include_removal($before_triple);

          }
        }
      }
    }
  }

  function include_removal( $triple) {
    if ( $triple['p'] == 'http://schemas.talis.com/2005/dir/schema#etag' ) return; // Platform always overrides this
    $this->has_changes = true;
    $this->include_count++;
    $removal = "_:r" . $this->include_count;

    $this->add_resource_triple($this->cs_resource, CS_REMOVAL, $removal );
    $this->add_resource_triple($removal, RDF_TYPE, RDF_STATEMENT);
    $this->add_resource_triple($removal, RDF_SUBJECT, $triple['s'] );
    $this->add_resource_triple($removal, RDF_PREDICATE, $triple['p'] );
    if ( $triple['o_type'] == 'iri' || $triple['o_type'] == 'bnode') {
      $this->add_resource_triple($removal, RDF_OBJECT, $triple['o'] );
    }
    else {
      $this->add_literal_triple($removal, RDF_OBJECT, $triple['o'], $triple['o_lang'], $triple['o_datatype'] );
    }

  }

  function include_addition( $triple) {
    if ( $triple['p'] == 'http://schemas.talis.com/2005/dir/schema#etag' ) return; // Platform always overrides this
    $this->has_changes = true;
    $this->include_count++;
    $addition = "_:a" . $this->include_count;

    $this->add_resource_triple($this->cs_resource, CS_ADDITION, $addition );
    $this->add_resource_triple($addition, RDF_TYPE, RDF_STATEMENT );
    $this->add_resource_triple($addition, RDF_SUBJECT, $triple['s'] );
    $this->add_resource_triple($addition, RDF_PREDICATE, $triple['p'] );
    if ( $triple['o_type'] == 'iri' || $triple['o_type'] == 'bnode') {
      $this->add_resource_triple($addition, RDF_OBJECT, $triple['o'] );
    }
    else {
      $this->add_literal_triple($addition, RDF_OBJECT, $triple['o'], $triple['o_lang'], $triple['o_datatype'] );
    }

  }


  function has_changes() {
    return $this->has_changes;
  }



  function triple_in_list( $triple, $list ) {
    foreach ($list as $candidate) {
      if ( $triple['s_type'] == $candidate['s_type'] && $triple['o_type'] == $candidate['o_type'] && $triple['p'] == $candidate['p'] ) {
        if ( $triple['s'] == $candidate['s'] ) {
          if ( $triple['o'] == $candidate['o']  )  {

            if ( $triple['o_type'] == 'literal' ) {
              if ( array_key_exists('o_lang', $triple) && array_key_exists('o_lang', $candidate) && $triple['o_lang'] == $candidate['o_lang']) {
                return true;
              }
              elseif (! array_key_exists('o_lang', $triple) && ! array_key_exists('o_lang', $candidate) ) {
                return true;
              }
              elseif ( array_key_exists('o_lang', $triple) && ! array_key_exists('o_lang', $candidate) && ! isset($triple['o_lang'] ) ) {
                return true;
              }
              elseif ( ! array_key_exists('o_lang', $triple) && array_key_exists('o_lang', $candidate) && ! isset($candidate['o_lang']) ) {
                return true;
              }
            }
            else {
              return true;
            }
          }
        }
      }
    }

    return false;
  }


}

/*****************************************************************************
*****************************************************************************/


?>
