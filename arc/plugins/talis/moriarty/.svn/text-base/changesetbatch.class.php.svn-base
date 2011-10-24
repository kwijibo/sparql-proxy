<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_ARC_DIR . "ARC2.php";
require_once MORIARTY_DIR . "changeset.class.php";

class ChangeSetBatch {
  var $changesets;
  var $after;
  var $after_rdfxml;
  var $before;
  var $before_rdfxml;
  var $createdDate = null;
  var $creatorName = null;
  var $changeReason = null;
  var $sparqlService;

  /* args can be after, createdDate, creatorName, changeReason */
  function ChangeSetBatch($args='') {
    if(is_array($args)){foreach($args as $k=>$v){$this->$k=$v;}}
    $this->init();
  }

  function init( ) {

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


    $this->changesets = array();

    $subjectIndex = array();

    for($i=0,$i_max=count($this->after);$i<$i_max;$i++) {
      if ( ! array_key_exists( $this->after[$i]['s'], $subjectIndex ) ) {
        $subjectIndex[ $this->after[$i]['s'] ] = $this->after[$i]['s'];
      }
    }
		
    if ( isset ($this->before) ) {
      $beforeSubjects = array();
      for($i=0,$i_max=count($this->before);$i<$i_max;$i++) {
	   if ( ! array_key_exists( $this->before[$i]['s'], $subjectIndex ) ) {
	        $subjectIndex[ $this->before[$i]['s'] ] = $this->before[$i]['s'];
	      }
        if ( ! array_key_exists( $this->before[$i]['s'], $beforeSubjects ) ) {
          $beforeSubjects[ $this->before[$i]['s'] ] = array();
        }
        array_push( $beforeSubjects[ $this->before[$i]['s'] ], $this->before[$i]);
      }
    }

    $subjects = array_keys( $subjectIndex );
//    var_dump('subjects',  $subjects, 'subjectindex', $subjectIndex,  'bs', $beforeSubjects);

    for($i=0,$i_max=count($subjects);$i<$i_max;$i++) {
      if ( isset ($this->before) && array_key_exists( $subjects[$i], $beforeSubjects ) ) {
          $this->changesets[] = new ChangeSet( array( 'subjectOfChange'=>$subjects[$i], 'after'=>$this->after
                                                                            , 'before'=>$beforeSubjects[ $subjects[$i] ]
                                                                            , 'createdDate'=>$this->createdDate
                                                                            , 'creatorName'=>$this->creatorName
                                                                            , 'changeReason'=>$this->changeReason
                                                                             ) );

      }
      else {
        if ( substr($subjects[$i], 0, 2) != '_:' && isset( $this->sparqlService ) ) {
          $describe_response = $this->sparqlService->describe( $subjects[$i] );
          $before_rdfxml = $describe_response->body;
          // echo "Content-type: text/plain\r\n\r\n";
          // echo $before_rdfxml;
  
          $this->changesets[] = new ChangeSet( array( 'subjectOfChange'=>$subjects[$i], 'after'=>$this->after
                                                                            , 'before_rdfxml'=>$before_rdfxml
                                                                            , 'createdDate'=>$this->createdDate
                                                                            , 'creatorName'=>$this->creatorName
                                                                            , 'changeReason'=>$this->changeReason
                                                                             ) );
        }
        else {
          $this->changesets[] = new ChangeSet( array( 'subjectOfChange'=>$subjects[$i], 'after'=>$this->after
                                                                            , 'createdDate'=>$this->createdDate
                                                                            , 'creatorName'=>$this->creatorName
                                                                            , 'changeReason'=>$this->changeReason
                                                                             ) );
        }
      }
    }


  }

  function get_changesets() {
    return $this->changesets;
  }




}
?>
