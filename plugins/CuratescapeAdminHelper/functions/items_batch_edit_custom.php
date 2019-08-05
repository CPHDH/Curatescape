<?php
$it_info=cah_item_type();
$it_name=$it_info['name'];
$item=$args['item'];
$custom=$args['custom'];

if($custom['migration-helper']){
		
	
	// Copy DC:Description to Story
	if( $description_text = metadata($item, array('Dublin Core', 'Description')) ){
	
		// are we dealing with HTML?
		$description_html=( strlen($description_text) != strlen(strip_tags($description_text) ) ) ? true : false;
		
		if(!metadata($item,array('Item Type Metadata','Story'))){ // if empty
		update_item($item,
			array('item_type_name'=>$it_name),
			array('Item Type Metadata' =>
				array('Story' =>
				array(array('text' => $description_text, 'html' => $description_html))
				))); 
		}
	}
	
	// Copy 2nd DC:Title to Subtitle
	if( count(metadata($item, array('Dublin Core', 'Title'),array('all'=>true)) > 1) ){
		
		// the second title field...
		$subtitle_text=metadata($item, array('Dublin Core', 'Title'),array('index' => 1));
		
		if(!metadata($item,array('Item Type Metadata','Subtitle'))){ // if empty
		update_item($item,
			array('item_type_name'=>$it_name),
			array('Item Type Metadata' =>
				array('Subtitle' =>
				array(array('text' => $subtitle_text, 
					'html' => false))
				))); 
		}		


	}

	// Copy DC:Relation to Related Resources
	if( $relations = metadata($item, array('Dublin Core', 'Relation'),array('all' => true)) ){
		
		foreach($relations as $relation_text){
			
			// are we dealing with HTML?
			$relation_html = ( strlen($relation_text) != strlen(strip_tags($relation_text)) ) ? true : false;
			if(!metadata($item,array('Item Type Metadata','Related Resources'))){ // if empty
			update_item($item,
				array('item_type_name'=>$it_name),
				array('Item Type Metadata' =>
					array('Related Resources' =>
					array(array('text' => $relation_text, 'html' => $relation_html))
					))); 
								
			}
		}			

	}
}