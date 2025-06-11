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
** INSTALL PLUGIN OPTIONS
*/
$this->_installOptions();
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
** CREATE/MIGRATE DATABASE TABLES
*/
$db = $this->_db;
// CREATE curatescape_tours table
$db->query(
	<<<SQL
	CREATE TABLE IF NOT EXISTS `{$db->prefix}curatescape_tours` (
		`id` int( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
		`title` varchar( 255 ) DEFAULT NULL,
		`description` text NOT NULL,
		`credits` text,
		`postscript_text` text,
		`featured` tinyint( 1 ) DEFAULT '0',
		`public` tinyint( 1 ) DEFAULT '0',
		`ordinal` INT NOT NULL DEFAULT '0',
		`added` TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
		`modified` TIMESTAMP NOT NULL DEFAULT '2000-01-01 00:00:00',
		PRIMARY KEY( `id` )
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
	SQL
);
// CREATE curatescape_tour_items table
$db->query(
	<<<SQL
	CREATE TABLE IF NOT EXISTS `{$db->prefix}curatescape_tour_items` (
		`id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
		`tour_id` INT( 10 ) UNSIGNED NOT NULL,
		`ordinal` INT NOT NULL,
		`item_id` INT( 10 ) UNSIGNED NOT NULL,
		`subtitle` text DEFAULT NULL,
		`text` text DEFAULT NULL,
		PRIMARY KEY( `id` ),
		KEY `curatescape_tours` ( `tour_id` )
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
	SQL
);
// MIGRATE existing tours from legacy TourBuilder plugin
$db->query(
	<<<SQL
	INSERT INTO `{$db->prefix}curatescape_tours` (id,title,description,credits,postscript_text,featured,public,ordinal,added,modified)
	SELECT id,title,description,credits,postscript_text,featured,public,ordinal,NOW(),NOW()
	FROM `{$db->prefix}tours` as t
	WHERE NOT EXISTS (
		SELECT 1
		FROM `{$db->prefix}curatescape_tours` AS ct
		WHERE t.id = ct.id
	);
	SQL
);
// MIGRATE existing tour items from legacy TourBuilder plugin
$db->query(
	<<<SQL
	INSERT INTO `{$db->prefix}curatescape_tour_items` (id,tour_id,ordinal,item_id,subtitle,text)
	SELECT id,tour_id,ordinal,item_id,subtitle,text
	FROM `{$db->prefix}tour_items` as ti
	WHERE NOT EXISTS (
		SELECT 1
		FROM `{$db->prefix}curatescape_tour_items` AS cti
		WHERE ti.id = cti.id
	);
	SQL
);