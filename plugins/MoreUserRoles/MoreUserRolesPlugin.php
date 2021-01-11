<?php

class MoreUserRolesPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
    	'define_acl',
    	'uninstall',
    	'admin_users_form'
    	);


    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];


        // AUTHORS inherit the rights of Contributors...
		$acl->addRole(new Zend_Acl_Role('author'), 'contributor');		
		// ... but are able to publish their own items
		$acl->allow('author','Items',array('makePublic'));

        
        // EDITORS inherit the rights of Authors...
		$acl->addRole(new Zend_Acl_Role('editor'), 'author');		
		// ... but are able to edit and delete Items and Files created by other users, make items Featured, and search by User
		$acl->allow('editor','Items',array('makeFeatured','edit','delete'));
		$acl->allow('editor','Files',array('edit','delete'));
		$acl->allow('editor','Users',array('browse'));
		
    }
    
    public function hookAdminUsersForm($args){
			// reorder and annotate the Role dropdown menu choices and update the explanatory text.
			?>
			<script type="text/javascript">
				var betterOrder=['researcher','contributor','author','editor','admin','super'];
				var selected = jQuery("#role").val();
				resortedRoles=[]
				betterOrder.forEach(function(name){
					var note=(name=='author'||name=='editor') ? ' (More User Roles plugin)' : '';
					var opt='<option value="'+name+'">'+name.charAt(0).toUpperCase()+ name.slice(1)+note+'</option>';
					resortedRoles.push(opt);
				});
				var parser = document.createElement('a');
				parser.href = jQuery(location).attr('href');
				var slug=parser.pathname;
				jQuery("#role").empty().append( resortedRoles );
				if(slug.indexOf('edit')){
					jQuery("#role").val(selected); 	
					}
				jQuery('#role-label ~ div p').append(' See also <a href="https://github.com/ebellempire/MoreUserRoles" target=_blank">More User Roles plugin documentation</a>.');
			</script>
			<?php
    }
    
        
    public function hookUninstall(){
	    // Upon uninstalling the plugin, revert the roles of Authors and Editors back to Contributor
        $db = $this->_db;
        $prefix = $db->prefix;
        $sql = "UPDATE ".$prefix."users SET role='contributor' WHERE role= 'author' or role='editor' ";
        $db->query($sql);
    }

}