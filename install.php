<?php
/*
** ARRAY: ITEM TYPE
*/
function curatescapeStoryItemType(){
	return array(
		'name'=> 'Curatescape Story',
		'description' => 'A narrative body of text in article format, often describing a physical location.',
	);
}
/*
** ARRAY: ELEMENTS
*/
function curatescapeStoryElements(){
	return array(
		array(
			'name'=>'Subtitle',
			'description'=>'A subtitle or alternate title for the entry.',
			'order'=>1,
		),
		array(
			'name'=>'Lede',
			'description'=>'A brief introductory section that is intended to entice the reader to read the full entry.',
			'order'=>2,
		),
		array(
			'name'=>'Story',
			'description'=>'The primary full-text for the entry.',
			'order'=>3,
		),
		array(
			'name'=>'Sponsor',
			'description'=>'The name of a person or organization that has sponsored the research for this specific entry.',
			'order'=>4,
		),
		array(
			'name'=>'Factoid',
			'description'=>'One or more facts or pieces of information related to the entry, often presented as a list. Examples include architectural metadata, preservation status, FAQs, pieces of trivia, etc.',
			'order'=>5,
		),
		array(
			'name'=>'Related Resources',
			'description'=>'The name of or link to a related resource, often used for citation information.',
			'order'=>6,
		),
		array(
			'name'=>'Official Website',
			'description'=>'An official website related to the entry.',
			'order'=>7,
		),
		array(
			'name'=>'Street Address',
			'description'=>'A single-line street or mailing address for a physical location.',
			'order'=>8,
		),
		array(
			'name'=>'Access Information',
			'description'=>'Information regarding physical access to a location, including restrictions, onsite directions, or other useful details (e.g. "Private Property," Location is approximate," "Demolished," etc.).',
			'order'=>9,
		),
	);
}
/*
** INSTALL ITEM TYPE AND ELEMENTS
*/
$itemTypeMeta = curatescapeStoryItemType();
$elements = curatescapeStoryElements();
$newElements=array();
foreach(curatescapeStoryElements() as $element){
	if(!element_exists('Item Type Metadata',$element['name'])){
		$newElements[]=$element;
	}else{
		$ElementObj=get_record('Element',array(
			'elementSet'=>'Item Type Metadata',
			'name'=>$element['name']));
		$newElements[]=$ElementObj;
	}
}
$itemType=get_record('ItemType',array('name'=>$itemTypeMeta['name']));
if(!$itemType){
	insert_item_type($itemTypeMeta, $newElements);
}else{
	$itemType->addElements($newElements);
	$itemType->save();
}
/*
** INSTALL PLUGIN OPTIONS
*/
$this->_installOptions();