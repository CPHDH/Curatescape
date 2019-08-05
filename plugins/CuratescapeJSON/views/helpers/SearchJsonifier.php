<?php

class CuratescapeJSON_View_Helper_SearchJsonifier extends Zend_View_Helper_Abstract
{
	public function __construct(){ }
	
	
	public function searchJsonifier( $searchText )
	{
		$type=$searchText->record_type;
		$id = $searchText->record_id;
		$result = get_record_by_id($type, $id );
		
		if($type=='Item'){
			
			if(metadata($result, 'has thumbnail')){
				$imgTag=item_image('square_thumbnail',array(),0,$result);
				if(preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $imgTag, $src)){
					$thumbSrc = array_pop($src);
				};
			}

			$itemMetadata=array(
				'result_id'=>$id,
				'result_type'=>$type,
				'result_title'=>$searchText->title,
				'result_thumbnail'=>$thumbSrc ? $thumbSrc : '',
			);
			
			return $itemMetadata;
			
		}elseif($type=='Tour'){
			
			$tour_items=$result->getItems();

			$itemMetadata=array(
				'result_id'=>$id,
				'result_type'=>$type,
				'result_title'=>$searchText->title,
				'result_tour_items'=>count($tour_items),
			);
			
			return $itemMetadata;			
			
		}elseif($type=='File'){
			
			$subtype=metadata($result,'mime_type');
			$subtype=explode('/',$subtype);
			$parent_id=$result->item_id;
			$parent=get_record_by_id('Item', $parent_id );
			$thumbSrc=( metadata($result,'has_derivative_image') && $subtype[0] !=='video' ) ? file_display_url($result,'square_thumbnail') : null;
			
			$itemMetadata=array(
				'result_id'=>$id,
				'result_type'=>$type,
				'result_title'=>$searchText->title,
				'result_thumbnail'=>$thumbSrc ? $thumbSrc : '',
				'result_subtype'=>isset($subtype[0]) ? $subtype[0] : 'Unknown',
				'result_parent_id'=>$parent_id,
				'result_parent_title'=>metadata($parent, array('Dublin Core', 'Title')),
			);
			
			return $itemMetadata;			
			
		}else{
			return false;
		}
	}
}