<?php
/*
** UNINSTALL OPTIONS
*/
$this->_uninstallOptions();
/*
** DELETE TOUR TABLES
*/
$db = $this->_db;
// curatescape_tours
$db->query(
	<<<SQL
	DROP TABLE IF EXISTS `{$db->CuratescapeTour}` 
	SQL
);
//curatescape_tour_items
$db->query(
	<<<SQL
	DROP TABLE IF EXISTS `{$db->CuratescapeTourItem}`
	SQL
);
/*
** CLEANUP
*/
// remove tags for curatescape_tours
$db->query(
	<<<SQL
	DELETE FROM `{$db->RecordsTags}`
	WHERE `record_type` = 'CuratescapeTour'
	SQL
);