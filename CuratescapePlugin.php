<?php

// https://omeka.readthedocs.io/en/latest/Tutorials/understandingOmeka_Record_AbstractRecord.html


/*
* @major: TOURS
	* <TOUR-NAV> COMPONENT
	* HookPublicHomeTop, HookPublicHomeEnd
	* New features: multi-stop map directions

* @todo: audio and video playlists?
* @todo: option to exclude audio and video from lightbox
* @todo: media fallback thumbnails (transparent, SVG, object fit: cover)
* @todo: detect theme and display relevant compatibility info on config form

* @later: languages
* @later: matomo.org integration? or just use plugin?
* @later: maps (Geolocation PRs?)
*/

// Includes
include 'functions.php';

// Constants
define('_PLUGIN_NAME_', 'Curatescape');
define('_PLUGIN_DIR_', dirname(__FILE__));
define('_JSON_ITEMS_FILE_', dirname(__FILE__).'/views/shared/items/items.cache.json');
define('_JSON_TOURS_FILE_', dirname(__FILE__).'/views/shared/tours/tours.cache.json');
define('_CURATESCAPE_ITEM_TYPE_NAME_', 'Curatescape Story');
define('_CURATESCAPE_ITEM_TYPE_NAME_PLURAL_', 'Curatescape Stories');
define('_CURATESCAPE_ITEM_TYPE_SETNAME_', _CURATESCAPE_ITEM_TYPE_NAME_.' Item Type Metadata');

class CuratescapePlugin extends Omeka_Plugin_AbstractPlugin{
	protected $_hooks = array(
		'admin_dashboard',
		'admin_head',
		'after_save_item',
		'config_form',
		'config',
		'define_acl',
		'define_routes',
		'initialize',
		'install',
		'public_head',
		'public_home',
		'public_content_top',
		'uninstall',
	);

	protected $_filters = array(
		'action_contexts',
		'admin_dashboard_stats',
		'admin_navigation_main',
		'all_element_texts_options',
		'body_tag_attributes',
		'display_elements',
		'display_option_description',
		'files_for_item',
		'file_markup',
		'filterCreatorAsLink' => array('Display', 'Item', 'Dublin Core', 'Creator'),
		'filterDCTitleWithSubtitle' => array('Display', 'Item', 'Dublin Core', 'Title'),
		'filterDisplayTitleWithSubtitle' => array('Display', 'Item', 'display_title'),
		'filterEmptyItemDescription' => array('Display', 'Item', 'Dublin Core', 'Description'),
		'filterItemTypeName' => array('Display', 'Item', 'Item Type Name'),
		'filterItemTypeNameUnderscores' => array('Display', 'Item', 'item_type_name'),
		'filterRichTitleWithSubtitle' => array('Display', 'Item', 'rich_title'),
		'filterSubjectAsLink' => array('Display', 'Item', 'Dublin Core', 'Subject'),
		// 'geolocation_map_browse',
		// 'geolocation_map_browse_ui',
		// 'geolocation_map_pagination',
		// 'geolocation_map_single',
		'items_browse_per_page',
		'item_next',
		'item_previous',
		'item_search_filters',
		'public_navigation_admin_bar',
		'public_navigation_items',
		'public_navigation_main',
		'response_contexts',
		'search_record_types',
		'search_texts_browse_per_page',
	);

	protected $_options = array(
		'curatescape_admin_bar_edit' => 1,
		'curatescape_alt_item_type_name_p' => 'Stories',
		'curatescape_alt_item_type_name' => 'Story',
		'curatescape_alt_tour_name_p' => 'Tours',
		'curatescape_alt_tour_name' => 'Tour',
		'curatescape_app_android' => null,
		'curatescape_app_ios' => null,
		'curatescape_append_primary_nav' => 1,
		'curatescape_append_secondary_nav' => 1,
		'curatescape_auto_subtitle' => 1,
		'curatescape_byline' => 'after_lede',
		'curatescape_dashboard_resources' => 1,
		'curatescape_factoids_label' => 'Did you know?',
		'curatescape_file_markup'=> 1,
		'curatescape_filter_text' => 1,
		'curatescape_form_enforcement' => 1,
		'curatescape_format_warnings' => 1,
		'curatescape_gallery_style_tour' => 'gallery-inline-captions',
		'curatescape_gallery_style'=> 'gallery-grid',
		'curatescape_google_analytics' => null,
		'curatescape_inline_factoids' => 1,
		'curatescape_inner_heading' => 0,
		'curatescape_json_cache' => '600',
		'curatescape_json_storage' => '300',
		'curatescape_lightbox_docs' => 0,
		'curatescape_lightbox_tours' => 1,
		'curatescape_lightbox' => 1,
		'curatescape_meta_image' => null,
		'curatescape_meta_tags'=> 1,
		'curatescape_metadata_browse' => 1,
		'curatescape_omit_redundant_elements' => 1,
		'curatescape_plugin_styles' => 1,
		'curatescape_rss' => 1,
		'curatescape_shorten_secondary_nav' => 1,
		'curatescape_subtitle_styles' => 1,
		'curatescape_template' => 1,
		'curatescape_theme_fixes' => 1,
		'curatescape_tour_map_style' => 'inline',
		'curatescape_tour_thumb_style' => 'composite',
	);

	public function hookInitialize()
	{
		add_translation_source(_PLUGIN_DIR_.'/languages');
		add_shortcode( 'curatescape_app_buttons', array(get_view(),'ShortcodeCuratescapeAppButtons'));
	}

	public function hookInstall()
	{
		require _PLUGIN_DIR_.'/install.php';
	}

	public function hookUninstall()
	{
		require _PLUGIN_DIR_.'/uninstall.php';
	}

	public function hookConfig()
	{
		return get_view()->HookConfig();
	}

	public function hookConfigForm()
	{
		return get_view()->HookConfigForm();
	}

	public function hookAdminDashboard($view)
	{
		return get_view()->HookAdminDashboard($view);
	}

	public function hookAdminHead($args)
	{
		return get_view()->HookAdminHead($args);
	}

	public function hookPublicHead($args)
	{
		return get_view()->HookPublicHead($args);
	}

	public function hookPublicHome($args)
	{
		return get_view()->HookPublicHomeEnd($args);
	}

	public function hookDefineRoutes($args)
	{
		return get_view()->HookDefineRoutes($args);
	}

	public function hookDefineAcl($args)
	{
		return get_view()->HookDefineAcl($args);
	}

	public function hookPublicContentTop($args)
	{
		return get_view()->HookPublicHomeTop($args);
	}

	public function hookAfterSaveItem($post)
	{
		return get_view()->HookAfterSaveItem($post);
	}

	// public function filterGeolocationMapBrowse($html, $args)
	// {
	// 	return get_view()->FilterGeolocationMapBrowse($html, $args);
	// }
	// 	// public function filterGeolocationMapBrowseUI($html)
	// {
	// 	return get_view()->FilterGeolocationMapBrowseUI($html);
	// }
	// 	// public function filterGeolocationMapPagination($html)
	// {
	// 	return get_view()->FilterGeolocationMapPagination($html);
	// }
	// 	// public function filterGeolocationMapSingle($html, $args)
	// {
	// 	return get_view()->FilterGeolocationMapSingle($html, $args);
	// }

	public function filterAdminNavigationMain($nav)
	{
		return get_view()->FilterAdminNavigationMain($nav);
	}

	public function filterAdminDashboardStats($stats)
	{
		return get_view()->FilterAdminDashboardStats($stats);
	}

	public function filterResponseContexts($contexts)
	{
		return get_view()->FilterResponseContexts($contexts);
	}

	public function filterActionContexts($contexts, $args)
	{
		return get_view()->FilterActionContexts($contexts, $args);
	}

	public function filterItemSearchFilters($displayArray)
	{
		return get_view()->FilterItemTypeNameDisplay(null, $displayArray);
	}

	public function filterItemTypeName($text){
		return get_view()->FilterItemTypeNameDisplay($text);
	}

	public function filterItemTypeNameUnderscores($text)
	{
		return get_view()->FilterItemTypeNameDisplay($text);
	}

	public function filterAllElementTextsOptions($options, $args)
	{
		return get_view()->FilterAllElementTextsOptions($options, $args);
	}

	public function filterPublicNavigationAdminBar($nav) 
	{
		return get_view()->FilterPublicNavigationAdminBar($nav);
	}

	public function filterPublicNavigationItems($nav)
	{
		return get_view()->FilterPublicNavigationItems($nav);
	}

	public function filterPublicNavigationMain($nav)
	{
		return get_view()->FilterPublicNavigationMain($nav);
	}

	public function filterItemNext($nextItem)
	{
		return get_view()->FilterItemNextPrevious($nextItem, null);
	}

	public function filterItemPrevious($previousItem)
	{
		return get_view()->FilterItemNextPrevious(null, $previousItem);
	}

	public function filterBodyTagAttributes($attributes)
	{
		return get_view()->FilterBodyTagAttributes($attributes);
	}

	public function filterDisplayElements($elementSets)
	{
		return get_view()->FilterDisplayElements($elementSets);
	}

	public function filterFilesForItem($html, $args)
	{
		return get_view()->FilterFilesForItem($html, $args);
	}

	public function filterFileMarkup($html, $args)
	{
		return get_view()->FilterFileMarkup($html, $args);
	}

	public function filterSubjectAsLink($text)
	{
		return get_view()->FilterMetadataBrowseLink($text, 49);
	}

	public function filterCreatorAsLink($text)
	{
		return get_view()->FilterMetadataBrowseLink($text, 39);
	}

	public function filterRichTitleWithSubtitle($text, $args)
	{
		return get_view()->FilterTitleWithSubtitle($text, $args, true);
	}

	public function filterDisplayTitleWithSubtitle($text, $args)
	{
		return get_view()->FilterTitleWithSubtitle($text, $args, false);
	}

	public function filterDCTitleWithSubtitle($text, $args)
	{
		return get_view()->FilterTitleWithSubtitle($text, $args, false);
	}

	public function filterDisplayOptionDescription($option)
	{
		return get_view()->FilterMetaDescription($option);
	}

	public function filterEmptyItemDescription($text, $args)
	{
		return get_view()->FilterEmptyItemDescription($text, $args);
	}

	public function filterItemsBrowsePerPage($perPage){
		return get_view()->FilterPerPage($perPage);
	}

	public function filterSearchTextsBrowsePerPage($perPage){
		return get_view()->FilterPerPage($perPage);
	}

	public function filterSearchRecordTypes($recordTypes)
	{
		return get_view()->FilterSearchRecordTypes($recordTypes);
	}

}
