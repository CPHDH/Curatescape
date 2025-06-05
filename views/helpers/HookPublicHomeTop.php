<?php
// settings for: homepage top option 
class Curatescape_View_Helper_HookPublicHomeTop extends Zend_View_Helper_Abstract{
	public function HookPublicHomeTop($args)
	{
		// @todo
		if('/' !== current_url()) return null;
		return null;
	}
}