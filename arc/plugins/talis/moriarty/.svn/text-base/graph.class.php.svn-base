<?php
class Graph {
  var $uri;
  var $credentials;
  var $request_factory;

  function Graph($uri, $credentials = null)  {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function apply_changeset($cs) {
    return $this->apply_changeset_rdfxml( $cs->to_rdfxml());
  }
  
  function apply_versioned_changeset($cs) {
    return $this->apply_versioned_changeset_rdfxml( $cs->to_rdfxml());
  }
  
  function apply_changeset_rdfxml($rdfxml) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $uri = $this->uri;

    $request = $this->request_factory->make( 'POST', $uri, $this->credentials);
    $request->set_accept("*/*");
    $request->set_content_type("application/vnd.talis.changeset+xml");
    $request->set_body( $rdfxml );

    return $request->execute();
  }

  function apply_versioned_changeset_rdfxml($rdfxml) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $uri = $this->uri . '/changesets';

    $request = $this->request_factory->make( 'POST', $uri, $this->credentials);
    $request->set_accept("*/*");
    $request->set_content_type("application/vnd.talis.changeset+xml");
    $request->set_body( $rdfxml );

    return $request->execute();
  }

  function submit_rdfxml($rdfxml) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $uri = $this->uri;
    $request = $this->request_factory->make( 'POST', $uri, $this->credentials);
    $request->set_content_type("application/rdf+xml");
    $request->set_accept("*/*");
    $request->set_body( $rdfxml );
    return $request->execute();
  }


  function describe( $uri, $output = null ) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $request_uri = $this->uri . '?about=' . urlencode($uri);
    if ($output) {
      $request_uri .= '&output=' . urlencode($output);
    }
    $request = $this->request_factory->make( 'GET', $request_uri, $this->credentials);
    $request->set_accept("application/rdf+xml");
    $request->set_content_type("application/x-www-form-urlencoded");

    return $request->execute();
  }
  function describe_to_triple_list( $uri ) {
    $triples = array();

    $response = $this->describe( $uri );
    $parser_args=array(
      "bnode_prefix"=>"genid",
      "base"=> $this->uri
    );
    $parser = ARC2::getRDFXMLParser($parser_args);

    if ( $response->body ) {
      $parser->parse($this->uri, $response->body );
      $triples = $parser->getTriples();
    }

    return $triples;
  }

  function describe_to_simple_graph( $uri ) {
    $graph = new SimpleGraph();

    $response = $this->describe( $uri );

    if ( $response->is_success() ) {
      $graph->from_rdfxml( $response->body );
    }

    return $graph;
  }
  
  function has_description( $uri ) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $request_uri = $this->uri . '?about=' . urlencode($uri);
    $request = $this->request_factory->make( 'GET', $request_uri, $this->credentials);
    $request->set_accept("application/rdf+xml");
    $request->set_if_match("*");

    $response = $request->execute();

    if ($response->status_code == 200) {
      return true;
    }
    else if ($response->status_code == 412) {
      return false;
    }

    

  }
}
?>
