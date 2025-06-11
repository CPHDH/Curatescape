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
	DROP TABLE IF EXISTS `{$db->prefix}curatescape_tours` 
	SQL
);
//curatescape_tour_items
$db->query(
	<<<SQL
	DROP TABLE IF EXISTS `{$db->prefix}curatescape_tour_items`
	SQL
);