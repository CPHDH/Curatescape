<?php

class CuratescapeJSONPlugin extends Omeka_Plugin_AbstractPlugin
{

	protected $_filters = array(
		'response_contexts',
		'action_contexts',
		'items_browse_per_page',
		'search_texts_browse_per_page' );
	
	public function filterItemsBrowsePerPage( $perPage ){
				
		if( isset($_GET["output"]) && $_GET["output"] == 'mobile-json'){
			$perPage=null; // no pagination
		}
		
		return $perPage;
	}

	public function filterSearchTextsBrowsePerPage( $perPage ){
				
		if( isset($_GET["output"]) && $_GET["output"] == 'mobile-json'){
			$perPage=null; // no pagination
		}
		
		return $perPage;
	}	

	public function filterResponseContexts( $contexts )
	{
		$contexts['mobile-json'] = array(
			'suffix' => 'mjson',
			'headers' => array( 'Content-Type' => 'application/json','Access-Control-Allow-Origin'=>'*' ) );
		return $contexts;
	}

	public function filterActionContexts( $contexts, $args ) {
		$controller = $args['controller'];

		if( is_a( $controller, 'ItemsController' ) or
			is_a( $controller, 'TourBuilder_ToursController' ) or
			is_a( $controller, 'SearchController' ) or
			is_a( $controller, 'SimplePages_PageController' ) )
		{
			$contexts['browse'][] = 'mobile-json' ;
			$contexts['show'][] = 'mobile-json' ;
			$contexts['index'][] = 'mobile-json' ;
		}

		return $contexts;
	}
}