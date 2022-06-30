<?php

$itemTypeMeta = cah_item_type();
$elements = cah_elements();
// Sort out which elements already exist
$add_elements=array();
foreach($elements as $element){
	if(!element_exists('Item Type Metadata',$element['name'])){
		// add the new elements
		$add_elements[]=$element;
	}else{
		// add the existing Element objects
		$ElementObj=get_record('Element',array(
			'elementSet'=>'Item Type Metadata',
			'name'=>$element['name']));
		$add_elements[]=$ElementObj;
	}
}
$the_itemType=get_record('ItemType',array('name'=>$itemTypeMeta['name']));
if(!$the_itemType){
	insert_item_type($itemTypeMeta,$add_elements);
}else{
	$the_itemType->addElements($add_elements);
	$the_itemType->save();
}