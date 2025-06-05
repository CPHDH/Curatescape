<?php
class Curatescape_View_Helper_FilterEmptyItemDescription extends Zend_View_Helper_Abstract{
	public function FilterEmptyItemDescription($text, $args, $options = array('snippet'=>200)){
		if( $text ) return $text;
		if( !$args['record'] ) return null;
		if( $lede = itm($args['record'], 'Lede', $options) ) return strip_formatting($lede);
		if( $story = itm($args['record'], 'Story', $options) ) return strip_formatting($story);
		return null;
	}
}