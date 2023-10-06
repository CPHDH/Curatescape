<?php
function sb_get_subjects(){
	$db = get_db();
	$prefix=$db->prefix;
	$select = "
	SELECT TRIM(et.text) as text, count(*) as total, LOWER(LEFT(text, 1)) as letter
	FROM {$prefix}items as i
	INNER JOIN {$prefix}element_texts as et ON i.id = et.record_id
	WHERE public = 1 AND element_id = 49
	GROUP BY text
	ORDER BY text ASC
	";
	$sql = $select;
	$q = $db->query($sql);
	$subjects = $q->fetchAll();
	return $subjects;
}

function sb_subjects_list($subjects){
	if($subjects){
		echo '<ul>';
		foreach($subjects as $subject){
			$link = WEB_ROOT;
			$link .= htmlentities('/items/browse?term=');
			$link .= rawurlencode($subject['text']);
			$link .= htmlentities('&search=&advanced[0][element_id]=49&advanced[0][type]=contains&advanced[0][terms]=');
			$link .= urlencode(str_replace('&amp;', '&', $subject['text']));
			echo '<li data-letter="'.$subject['letter'].'" data-count="'.$subject['total'].'"><a href="'.$link.'">'.$subject['text'].' <span class="count">'.$subject['total'].'</span></a></li>';
		}
		echo '</ul>';
	}
}

function sb_ascend($a, $b){
	return $a['total'] - $b['total'];
}
function sb_descend($a, $b){
	return $b['total'] - $a['total'];
}