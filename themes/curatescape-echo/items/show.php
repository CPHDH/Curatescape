<?php

// Determine which template to use based on the item type

$type = $item->getItemType();
$type = $type['name'];
switch ($type) {

//	case 'Curatescape Story':
//	include('show-template-story.php');
//	break;

    default:
    include('show-template-default.php');
    break;

}
