<?php

//TODO: load only if user JS is available
//TODO: redirect after file save to edit item files

/*
** after files are uploaded, user is redirected to add file metadata
*/

function stah_after_upload_file(){
//	$id=item('ID');
//	header( 'Location: '.WEB_ROOT.'/admin/items/edit/'.$id.'/#files-metadata' ) ;
}

/*
** load plugin CSS and JS in header
** loaded first to hide
*/

function stah_admin_theme_header($request)
{
    if (($request->getControllerName() == 'items' && $request->getActionName() == 'edit')||($request->getControllerName() == 'items' && $request->getActionName() == 'add')||($request->getControllerName() == 'files' && $request->getActionName() == 'edit')) {
        echo '<link href="'.WEB_ROOT.'/plugins/SendToAdminHeader/stah-header.css" media="all" rel="stylesheet" type="text/css" > ';
        echo '<script language="javascript" src="'.WEB_ROOT.'/plugins/SendToAdminHeader/stah-header.js"></script>';
    }

} 

/*
** load plugin CSS and JS in footer
** loaded second to un-hide
*/

function stah_admin_theme_footer($request)
{
    if (($request->getControllerName() == 'items' && $request->getActionName() == 'edit')||($request->getControllerName() == 'items' && $request->getActionName() == 'add')||($request->getControllerName() == 'files' && $request->getActionName() == 'edit')) {
        echo '<link href="'.WEB_ROOT.'/plugins/SendToAdminHeader/stah-footer.css" media="all" rel="stylesheet" type="text/css" > ';
        echo '<script language="javascript" src="'.WEB_ROOT.'/plugins/SendToAdminHeader/stah-footer.js"></script>';
    }

} 

/*
** modify item/add tabs (currently using default values, but keeping this handy just in case)
*/

function stah_admin_theme_tabs($tabs,$item){
 
  return $tabs;
}

/*
** for users without JS enabled (not currently implemented)
*/
function stah_noscript()
{
echo'
<noscript>JavaScript must be enabled in order for you to effectively add content to this site. It seems JavaScript is either disabled or not supported by your browser. Please enable JavaScript by changing your browser options, then <a href="">try again</a>.
</noscript>
';
} 


?>