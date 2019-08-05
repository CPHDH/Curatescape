<?php

require_once dirname(__FILE__) . '/helpers/SuperRssFunctions.php';


class SuperRssPlugin extends Omeka_Plugin_AbstractPlugin
{
    
    const DEFAULT_REPLACE_DEFAULT_RSS = 1;
    const DEFAULT_READ_MORE = 1;
    const DEFAULT_READ_MORE_STATS = 1;
    const DEFAULT_SOCIAL_MEDIA_LINKS = 0;
    const DEFAULT_APP_STORE_LINKS = 0;
    const DEFAULT_ENABLE_FIELDTRIP = 0;

    protected $_hooks = array(
    	'install', 
    	'uninstall',
        'config_form', 
        'config');

	protected $_filters = array(
		'response_contexts',
		'action_contexts' );


    protected $_options = array(
        'srss_replace_default_rss' => self::DEFAULT_REPLACE_DEFAULT_RSS,
        'srss_include_read_more_link' => self::DEFAULT_READ_MORE,
        'srss_include_mediastats_footer' => self::DEFAULT_READ_MORE_STATS,
        'srss_include_social_footer' => self::DEFAULT_SOCIAL_MEDIA_LINKS,
        'srss_include_applink_footer' => self::DEFAULT_APP_STORE_LINKS,
        'srss_enable_ft'=>self::DEFAULT_ENABLE_FIELDTRIP,        
        'srss_facebook_link' => null,
        'srss_twitter_user' => null,
        'srss_youtube_user' => null,
        'srss_ios_id' => null,
        'srss_android_id' => null,
        'srss_about_text' => null,
        'srss_image_url' => null,
        'srss_omit_from_fieldtrip' => null
    );


	public function filterResponseContexts( $contexts )
	{
		
		if(get_option('srss_replace_default_rss')){
			$contexts['rss2'] = array(
				'suffix' => 'srss',
				'headers' => array( 'Content-Type' => 'text/xml' )
			);
		}

		$contexts['srss'] = array(
			'suffix' => 'srss',
			'headers' => array( 'Content-Type' => 'text/xml' )
		);
		
		if(get_option('srss_enable_ft')){
			$contexts['fieldtrip'] = array(
				'suffix' => 'fieldtrip',
				'headers' => array( 'Content-Type' => 'text/xml' )
			);	
		}
		
		return $contexts;
	}

	public function filterActionContexts( $contexts, $args ) {
		
		$controller = $args['controller'];

		if( is_a( $controller, 'ItemsController' ) )
		
		{
			$contexts['browse'][] = 'srss' ;
			
			if(get_option('srss_replace_default_rss')){
				$contexts['browse'][] = 'rss2' ;
			}	
			
			if(get_option('srss_enable_ft')){
				$contexts['browse'][] = 'fieldtrip' ;
			}
		}

		return $contexts;
	}

        
    /*
    ** Plugin options
    */
    
    public function hookConfigForm()
    {
        require dirname(__FILE__) . '/config_form.php';
    }	
        
    public function hookConfig()
    {
        set_option('srss_replace_default_rss', $_POST['srss_replace_default_rss']);
        set_option('srss_facebook_link', $_POST['srss_facebook_link']);
        set_option('srss_twitter_user', $_POST['srss_twitter_user']);
        set_option('srss_youtube_user', $_POST['srss_youtube_user']);
        set_option('srss_ios_id', $_POST['srss_ios_id']);
        set_option('srss_android_id', $_POST['srss_android_id']);
        set_option('srss_about_text', $_POST['srss_about_text']);
        set_option('srss_image_url', $_POST['srss_image_url']);
        set_option('srss_include_social_footer', (int)(boolean)$_POST['srss_include_social_footer']);
        set_option('srss_include_applink_footer', (int)(boolean)$_POST['srss_include_applink_footer']);
        set_option('srss_include_read_more_link', (int)(boolean)$_POST['srss_include_read_more_link']);
        set_option('srss_include_mediastats_footer', (int)(boolean)$_POST['srss_include_mediastats_footer']);  
        set_option('srss_enable_ft', (int)(boolean)$_POST['srss_enable_ft']); 
        set_option('srss_omit_from_fieldtrip', $_POST['srss_omit_from_fieldtrip']);   
        
    }	
    

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {		
		$this->_installOptions();    
    
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {        
		$this->_uninstallOptions();	
		
    }	
}