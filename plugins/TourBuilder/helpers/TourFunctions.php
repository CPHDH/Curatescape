<?php

/*
 * Helper functions
 */
function availableItemsJSON() {
		$db = get_db();
		$prefix=$db->prefix;
		$itemTable = $db->getTable( 'Item' );
		if($tour = get_current_tour()){
			$items = $itemTable->fetchObjects(
				"SELECT i.*, (SELECT count(*) FROM ".$prefix."tour_items ti WHERE ti.item_id = i.id AND ti.tour_id = ?) AS `in_tour`
				FROM ".$prefix."items i ORDER BY i.modified DESC",
				array( $tour->id ) );
		}else{
			$items = $itemTable->fetchObjects( "SELECT i.* FROM ".$prefix."items i ORDER BY i.modified DESC");
		}
		foreach($items as $key => $arr) {
			$items[$key]['label'] = metadata( $arr, array( 'Dublin Core', 'Title' ) );
		}
	
		return json_encode($items);
}

function has_tours()
{
	return( total_tours() > 0 );
}

function has_tours_for_loop()
{
	$view = get_view();
	return $view->tours && count( $view->tours );
}


function tour( $fieldName, $options=array(), $tour=null )
{
	if( ! $tour ) {
		$tour = get_current_tour();
	}

	switch( strtolower( $fieldName ) ) {
	case 'id':
		$text = $tour->id;
		break;
	case 'title':
		$text = $tour->title;
		break;
	case 'description':
		$text = $tour->description;
		break;
	case 'credits':
		$text = $tour->credits;
		break;
	case 'postscript_text':
		$text = $tour->postscript_text;
		break;
	default:
		throw new Exception( "\"$fieldName\" does not exist for tours!" );
		break;
	}

	if( isset( $options['snippet'] ) ) {
		$text = snippet( $text, 0, (int)$options['snippet'] );
	}

	if( !is_array( $text ) ) {
		$text = html_escape( $text );
	} else {
		$text = array_map( 'html_escape', $text );

		if( isset( $options['delimiter'] ) ) {
			$text = join( (string) $options['delimiter'], (array) $text );
		}
	}

	return $text;
}

function set_current_tour( $tour )
{
	get_view()->tour = $tour;
}

function get_current_tour()
{
	return get_view()->tour;
}

function link_to_tour(
	$text=null, $props=array(), $action='show', $tourObj = null )
{
	# Use the current tour object if none given
	if( ! $tourObj ) {
		$tourObj = get_current_tour();
	}

	# Create default text, if it was not passed in.
	if( empty( $text ) ) {
		$tourName = tour('title', array(), $tourObj);
		$text = (! empty( $tourName )) ? $tourName : '[Untitled]';
	}

	return link_to($tourObj, $action, $text, $props);
}


function total_tours()
{
	$view = get_view();
	return count( $view->tours );
}

function nls2p($str) {
	$str = str_replace('<p></p>', '', '<p>'
		. preg_replace('#([
]\s*?[
]){2,}#', '</p><p>', $str)
		. '</p>');
	return $str;
}

function public_nav_tours( array $navArray = null, $maxDepth = 0 )
{
	if( !$navArray )
	{
		$navArray = array();

		$navArray[] = array(
			'label' => __('All'),
			'uri' => url('tours/browse') );

		/* TODO: Tour Tags */

	}

	return nav( $navArray );
}

/*
** Display the thumb for the tour.
** Used to generate slideshow, etc.
** TODO: expand $userDefined option to encompass either a user-set globally-defined img URL or a user-set tour-specific img URL
** USAGE: display_tour_thumb($this->tour,0)
*/
function display_tour_thumb($tour,$i,$userDefined=null){

	$firstTourItem=tour_item_id($tour,$i);

	$html='<div class="item-thumb hidden">';
	$html .= '<a href="'.html_escape(public_url('tours/show/'.tour('id'))).'">';

	if($userDefined){
		$html .= '<img src="'.$userDefined.'"/>';

	}elseif($firstTourItem){
		// use the thumb for the first item in the tour
		$item = get_record_by_id('item', $firstTourItem);
		$html .= item_image('square_thumbnail',array(),0,$item);

	}else{
		// use the fallback if their are no items in the tour
		$html .= '<img src="'.public_url('plugins/TourBuilder/views/public/images/default_thumbnail.png').'"/>';
	}

	$html .= '</a></div>';

	return $html;
}
/*
** Get an ID of an item in a tour
** $tour sets the tour object
** $i is used to choose the position in the item array
** USAGE: tour_item_id($this->tour,0)
*/
function tour_item_id($tour,$i){
	$tourItems =array();
	foreach( $tour->Items as $items ){
		array_push($tourItems,$items->id);
	}
	return isset($tourItems[$i]) ? $tourItems[$i] : null;
}

/*
** Uses the query parameters posted from the tour location links on tours/show
** Adds a prev/info/next link to items/show for navigating tour locations
*/

function tour_nav( $html=null, $label='Tour', $alwaysShow=false, $item_id=null )
{
	$intlLabel = __($label);

	if ( (isset($_GET['tour'])) && (isset($_GET['index'])) )
	{
		$index = htmlspecialchars($_GET[ 'index' ]);
		$tour_id = htmlspecialchars($_GET['tour']);
		$tour = get_record_by_id( 'tour', $tour_id );

		$prevIndex = $index -1;
		$nextIndex = $index +1;

		$tourTitle = metadata( $tour, 'title' );
		$tourURL = html_escape( public_url( 'tours/show/'.$tour_id ) );

		// Items
		$current = tour_item_id( $tour, $index );
		$next = tour_item_id( $tour, $nextIndex );
		$prev = tour_item_id( $tour, $prevIndex );

		// Begin building the tour navigation
		$html = ''
			. '<div class="tour-nav">'
			. "$intlLabel " . __('navigation') . ':&nbsp;&nbsp;'
			. '<span class="tour-nav-links">';

		// Add the previous item to the navigation if present.
		if( $prev )
		{
			$prevUrl = public_url( "items/show/$prev?tour=$tour_id&index=$prevIndex");
			$html .= ''
				. '<a title="' . __('Previous stop on %s', $intlLabel) .'"'
				. "href=\"$prevUrl\">" . __('Previous') . '</a>'
				. ' | ';
		}

		if( $tourURL )
		{
			$html .= '<a title= "'.__('View %1$s: %2$s', $intlLabel, $tourTitle).'"
         href="'.$tourURL.'">'.__('%s Info', $intlLabel).'</a>';
		}

		// Add the next item to the navigation if present
		if( $next )
		{
			$nextUrl = public_url( "items/show/$next?tour=$tour_id&index=$nextIndex");
			$html .= ' | '
				. '<a title="' . __('Next stop on %s', $intlLabel).'" href="'.$nextUrl.'">' . __('Next') . '</a>';
		}

		$html .= '</span>'
			. '</div>';
			
		return $html;
	}else{
		if($alwaysShow && $item_id){
			// theme designers can set $alwaysShow to true and $item_id to [an item id] to show the tour info for all items if it exists (e.g. for sites where all items are part of a tour)
			$html .= cta_tour_for_item($item_id,$intlLabel);
			return $html;			
		}else{
			return null;
		}
	}
}

/* get a list of related tour links for a given item, for use on items/show template */
function tours_for_item($item_id=null,$heading=null){

	if(is_int($item_id)){
		$db = get_db();
		$prefix=$db->prefix;
		$select = $db->select()
		->from(array('ti' => $prefix.'tour_items')) // SELECT * FROM omeka_tour_items as ti
		->join(array('t' => $prefix.'tours'),    	// INNER JOIN omeka_tours as t
			'ti.tour_id = t.id')      				// ON ti.tour_id = t.id
		->where("item_id=$item_id AND public=1");   // WHERE item_id=$item_id
		$q = $select->query();
		$results = $q->fetchAll();

		$html=null;
		if($results){
			$h=(count($results)>1) ? __('Related Tours') : __('Related Tour');
			$h = ($heading) ? $heading : $h;
			$html.='<div id="tour-for-item"><h3>'.$h.'</h3><ul>';
			foreach($results as $result){
				$html.='<li><a class="tour-for-item" href="/tours/show/'.$result['id'].'">';
				$html.=$result['title'];
				$html.='</a></li>';
			}
			$html.='</ul></div>';
		}
		return $html;
	}
}

/* generate a call to action prompting users to navigate from an item page to the tour page (must be enabled at theme level by setting optional params in tour_nav() function) */
function cta_tour_for_item($item_id=null,$intlLabel='Tour'){

	if(is_int($item_id)){
		$db = get_db();
		$prefix=$db->prefix;
		$select = $db->select()
		->from(array('ti' => $prefix.'tour_items')) // SELECT * FROM omeka_tour_items as ti
		->join(array('t' => $prefix.'tours'),    	// INNER JOIN omeka_tours as t
			'ti.tour_id = t.id')      				// ON ti.tour_id = t.id
		->where("item_id=$item_id AND public=1");   // WHERE item_id=$item_id
		$q = $select->query();
		$results = $q->fetchAll();

		$html=null;
		if($results){
			$html .= '<div class="tour-nav-cta">';
			$html .= '<p>'.__('This is an entry from a multi-part %1s:<br><strong><em>%2s</em></strong>',strtolower($intlLabel),$results[0]['title']).'</p>';
			$html .= '<a class="button button-primary" href="/tours/show/'.$results[0]['id'].'" title="'.$results[0]['title'].'">'.__('View %s Info',$intlLabel).'</a>';
			$html .= '</div>';
		}
		return $html;
	}
}
