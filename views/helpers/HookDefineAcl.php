<?php
class Curatescape_View_Helper_HookDefineAcl extends Zend_View_Helper_Abstract{
	public function HookDefineAcl($args){
		$acl = $args['acl'];
		$acl->addResource('Curatescape_CuratescapeTours');
		$acl->allow(null, 'Curatescape_CuratescapeTours', array('browse', 'show', 'tags'));
		$acl->allow('contributor', 'Curatescape_CuratescapeTours');
	}
}