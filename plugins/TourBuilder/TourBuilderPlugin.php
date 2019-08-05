<?php

if( !defined( 'TOURBUILDER_PLUGIN_DIR' ) )
{
	define( 'TOURBUILDER_PLUGIN_DIR', dirname( __FILE__ ) );
}

class TourBuilderPlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_filters = array(
		'public_navigation_main',
		'admin_dashboard_stats',
		'admin_navigation_main',
		'search_record_types',	
		);

	protected $_hooks = array(
		'install',
		'uninstall',
		'define_acl',
		'define_routes',
		'admin_head',
		'admin_dashboard',
		'upgrade',
	);

	public function hookInstall()
	{
		$db = $this->_db;

		$tourQuery = "
         CREATE TABLE IF NOT EXISTS `$db->Tour` (
            `id` int( 10 ) unsigned NOT NULL auto_increment,
            `title` varchar( 255 ) collate utf8_unicode_ci default NULL,
            `description` text collate utf8_unicode_ci NOT NULL,
            `credits` text collate utf8_unicode_ci,
            `postscript_text` text collate utf8_unicode_ci,
            `featured` tinyint( 1 ) default '0',
            `public` tinyint( 1 ) default '0',
            PRiMARY KEY( `id` )
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ";

		$tourItemQuery = "
         CREATE TABLE IF NOT EXISTS `$db->TourItem` (
            `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `tour_id` INT( 10 ) UNSIGNED NOT NULL,
            `ordinal` INT NOT NULL,
            `item_id` INT( 10 ) UNSIGNED NOT NULL,
            PRIMARY KEY( `id` ),
            KEY `tour` ( `tour_id` )
         ) ENGINE=InnoDB ";

		$db->query( $tourQuery );
		$db->query( $tourItemQuery );
	}

	public function hookUninstall()
	{
		$db = $this->_db;
		$db->query( "DROP TABLE IF EXISTS `$db->TourItem`" );
		$db->query( "DROP TABLE IF EXISTS `$db->Tour`" );
	}

    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];
        $db = $this->_db;
        $prefix=$db->prefix;
        
        if ($oldVersion < '1.4') {
            
            $sql = "ALTER TABLE `$db->Tour` ADD COLUMN `postscript_text` text collate utf8_unicode_ci default NULL";
            $db->query($sql);
          	        
	    }
	    
	    if ($oldVersion < '1.5') {
            $sql = "ALTER TABLE `$db->Tour` DROP COLUMN `slug`";
            $db->query($sql);  

            $sql = "ALTER TABLE `$db->Tour` DROP COLUMN `tour_image`";
            $db->query($sql);  
            		    
	    }
	    if($oldVersion < '1.6'){
			Zend_Registry::get('bootstrap')->getResource('jobs')->sendLongRunning('Job_SearchTextIndex');
	    }
	    
	    if($oldVersion < '1.7'){
			// Get rid of orphan tour_items that weren't being deleted along with their respective tours in previous versions		
			$sql = "DELETE ".$prefix."tour_items 
			FROM ".$prefix."tour_items
			LEFT JOIN ".$prefix."tours 
			ON ".$prefix."tour_items.tour_id = ".$prefix."tours.id
			WHERE ".$prefix."tours.id IS NULL";
			
			$db->query($sql);     
	    }
	}
	
	public function hookDefineAcl( $args )
	{
		$acl = $args['acl'];

		// Create the ACL context
		$acl->addResource( 'TourBuilder_Tours' );
		
		// Allow anyone to look but not touch
		$acl->allow( null, 'TourBuilder_Tours', array('browse', 'show') );
		
		// Allow contributor (and better) to do anything with tours
		$acl->allow( 'contributor','TourBuilder_Tours');

	}

	public function hookDefineRoutes( $args )
	{
		$router = $args['router'];
		$router->addConfig( new Zend_Config_Ini(
				TOURBUILDER_PLUGIN_DIR .
				DIRECTORY_SEPARATOR .
				'routes.ini', 'routes' ) );
	}

	public function filterAdminDashboardStats( $stats )
	{
		if( is_allowed( 'TourBuilder_Tours', 'browse' ) )
		{
			$stats[] = array( link_to( 'tours', array(),
					total_records( 'Tours' ) ),
				__('tours') );
		}
		return $stats;
	}

	public function filterPublicNavigationMain( $navs )
	{
		$navs[] = array(
			'label' => __('Tours'),
			'uri' => url( 'tours' ),
			'visible' => true
		);
		return $navs;
	}

	public function hookAdminDashboard()
	{
		// Get the database.
		$db = get_db();

		// Get the Tour table.
		$table = $db->getTable('Tour');

		// Build the select query.
		$select = $table->getSelect();

		// Fetch some items with our select.
		$results = $table->fetchObjects($select);

		$tourItems = null;
		$html  = null;

		for($i=0;$i<=5;$i++){
			if(array_key_exists($i,$results) && is_object($results[$i])){
				$tourItems .='<div class="recent-row"><p class="recent"><a href="/admin/tours/show/'.$results[$i]->id.'">'
					.$results[$i]->title.'</a></p><p class="dash-edit"><a href="/admin/tours/edit/'.$results[$i]->id.'">Edit</a></p></div>';
			}
		}

		$html .= '<section class="five columns alpha"><div class="panel">';
		$html .= '<h2>'.__('Recent Tours').'</h2>';
		$html .= ''.$tourItems.'';
		$html .= '<p><a class="add-new-item" href="'.html_escape(url('tour-builder/tours/add/')).'">'.__('Add a new tour').'</a></p>';
		$html .= '</div></section>';

		echo $html;

	}

	public function hookAdminHead()
	{
	    $request = Zend_Controller_Front::getInstance()->getRequest();
	    $module = $request->getModuleName();
	    $controller = $request->getControllerName();
	
	    if ($module == 'tour-builder' && $controller == 'tours') {
	        queue_css_file('tour-1.7');
	    }		
	}


	public function filterSearchRecordTypes($recordTypes){
	    $recordTypes['Tour'] = __('Tour');
	    return $recordTypes;		
	}


	public function filterAdminNavigationMain( $nav )
	{
		$nav['Tours'] = array( 'label' => __('Tours'),
			'action' => 'browse',
			'controller' => 'tours' );
		return $nav;
	}
}

include 'helpers/TourFunctions.php';