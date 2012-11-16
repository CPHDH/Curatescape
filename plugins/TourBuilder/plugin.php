<?php

define( 'TOURS_PLUGIN_DIR', dirname( __FILE__ ) );

require_once 'Tour.php';
require_once 'TourItem.php';

add_plugin_hook( 'install', 'TourBuilder::install' );
add_plugin_hook( 'uninstall', 'TourBuilder::uninstall' );
add_plugin_hook( 'define_acl', 'TourBuilder::defineAcl' );
add_plugin_hook( 'define_routes', 'tours_define_routes' );
add_plugin_hook( 'admin_append_to_dashboard_primary', 'tours_dashboard' );
add_plugin_hook( 'admin_theme_header', 'tours_admin_header' );
add_filter('admin_navigation_main', 'TourBuilder::adminNavigationMain');

class TourBuilder
{
   public function __construct()
   {
      $this->_db = get_db();
   }

   public static function install()
   {
      $tours = new TourBuilder;
      $tours->_createTables();
   }

   public static function uninstall()
   {
      $tours = new TourBuilder;
      $tours->_dropTables();
   }

   public static function defineAcl( $acl )
   {
      $tours = new TourBuilder;
      $tours->_defineAcl( $acl );
   }

   private function _createTables()
   {
      $db = $this->_db;
      $db->exec( <<<SQL
         CREATE TABLE IF NOT EXISTS `$db->Tour` (
            `id` int( 10 ) unsigned NOT NULL auto_increment,
            `title` varchar( 255 ) collate utf8_unicode_ci default NULL,
            `description` text collate utf8_unicode_ci NOT NULL,
            `credits` text collate utf8_unicode_ci,
            `featured` tinyint( 1 ) default '0',
            `public` tinyint( 1 ) default '0',
            `slug` varchar( 30 ) collate utf8_unicode_ci default NULL,
            PRiMARY KEY( `id` ),
            UNIQUE KEY `slug` ( `slug` )
         ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
SQL
      );
      $db->exec( <<<SQL
         CREATE TABLE IF NOT EXISTS `$db->TourItem` (
            `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `tour_id` INT( 10 ) UNSIGNED NOT NULL,
            `ordinal` INT NOT NULL,
            `item_id` INT( 10 ) UNSIGNED NOT NULL,
            PRIMARY KEY( `id` ),
            KEY `tour` ( `tour_id` )
         ) ENGINE=MyISAM
SQL
      );
   }

   private function _dropTables()
   {
      $db = $this->_db;
      $db->exec( "DROP TABLE IF EXISTS $db->Tour" );
      $db->exec( "DROP TABLE IF EXISTS $db->TourItem" );
   }

   private function _defineAcl( $acl )
   {
      $resource = new Omeka_Acl_Resource( 'TourBuilder_Tours' );
      $resource->add( array( 'add','editSelf', 'editAll', 'deleteSelf', 'deleteAll',
         'add-item', 'delete-item', 'showNotPublic' ) );
      $acl->add($resource);

      // Deny contributor users editAll, deleteAll
      $acl->deny('contributor', 'TourBuilder_Tours', array('editAll','deleteAll'));

      // Allow contributors everything else
      $acl->allow('contributor', 'TourBuilder_Tours');
   }
    public static function adminNavigationMain($nav)
    {
        $nav['Tours'] = uri('tour-builder/tours');
        return $nav;
    }   
}

function tours_define_routes( $router )
{
   $singleRoute = new Zend_Controller_Router_Route( 'tours/:action/:id',
      array( 'module' => 'TourBuilder', 'controller' => 'tours', 'id' => '1' ),
      array() );
   $router->addRoute( 'tours', $singleRoute );

   $singleIdRoute = new Zend_Controller_Router_Route( 'tours/:action/id/:id',
      array( 'module' => 'TourBuilder', 'controller' => 'tours', 'id' => '1' ),
      array() );
   $router->addRoute( 'tours_single_id', $singleRoute );

   $collectionRoute = new Zend_Controller_Router_Route( 'tours/:action',
      array( 'module' => 'TourBuilder', 'controller' => 'tours', 'action' => 'browse' ),
      array() );
   $router->addRoute( 'tours_collection', $collectionRoute );
}

function tours_dashboard()
{
   if( has_permission( 'TourBuilder_Tours', 'browse' ) ): ?>
	<dt class="tours"><a href="<?php echo html_escape(uri('tours')); ?>">Tours</a></dt>
	<dd class="tours">
		<ul>
			<li><a class="add-tour use-icon" href="<?php echo html_escape(uri('tour-builder/tours/add/')); ?>">Create a Tour</a></li>
			<li><a class="browse browse-tour" href="<?php echo html_escape(uri('tour-builder/tours')); ?>">Browse Tours</a></li>
		</ul>
		<p>Add and manage mobile tours that display items from the archive.</p>
	</dd>
   <?php endif;
}

function tours_admin_header( $request )
{
   # Add our stylesheet to admin pages in which we take part
   if( $request->getControllerName() == 'tours' ||
      ($request->getModuleName() == 'default' &&
      $request->getControllerName() == 'index' &&
      $request->getActionName() == 'index') )
   {
      echo '<link rel="stylesheet" media="screen" href="' . html_escape(css('tour')) . '" /> ';
   }
}

/*
 * Helper functions for use in all themes
 */

function has_tours()
{
   return( total_tours() > 0 );
}

function has_tours_for_loop()
{
   $view = __v();
   return $view->tours && count( $view->tours );
}


function loop_tours()
{
    return loop_records('tours', get_tours_for_loop(), 'set_current_tour');
}


function tour( $fieldName, $options=array(), $tour=null )
{
   if( ! $tour ) {
      $tour = get_current_tour();
   }

   switch( strtolower( $fieldName ) ) {
      case 'id':
         $text = $tour->id;
         break;
      case 'title':
         $text = $tour->title;
         break;
      case 'description':
         $text = $tour->description;
         break;
      case 'credits':
         $text = $tour->credits;
         break;
      case 'slug':
         $text = $tour->slug;
         break;

      default:
         throw new Exception( "\"$fieldName\" does not exist for tours!" );
         break;
   }

   if( isset( $options['snippet'] ) ) {
      $text = snippet( $text, 0, (int)$options['snippet'] );
   }

   if( !is_array( $text ) ) {
      $text = html_escape( $text );
   } else {
      $text = array_map( 'html_escape', $text );

      if( isset( $options['delimiter'] ) ) {
         $text = join( (string) $options['delimiter'], (array) $text );
      }
   }

   return $text;
}

function set_current_tour( $tour )
{
   __v()->tour = $tour;
}

function get_current_tour()
{
   return __v()->tour;
}

function link_to_tour(
   $text=null, $props=array(), $action='show', $tourObj = null )
{
   # Use the current tour object if none given
   if( ! $tourObj ) {
      $tourObj = get_current_tour();
   }

   # Create default text, if it was not passed in.
   if( empty( $text ) ) {
      $tourName = tour('title', array(), $tourObj);
      $text = (! empty( $tourName )) ? $tourName : '[Untitled]';
   }

   return link_to($tourObj, $action, $text, $props);
}

function get_tours_for_loop()
{
   return __v()->tours;
}

function total_tours()
{
   return get_db()->getTable( 'Tours' )->count();
}