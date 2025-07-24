<?php
class Curatescape_View_Helper_HookConfig extends Zend_View_Helper_Abstract{
	public function HookConfig(){
		set_option('curatescape_admin_bar_edit', $_POST['curatescape_admin_bar_edit']);
		set_option('curatescape_alt_item_type_name_p', $_POST['curatescape_alt_item_type_name_p']);
		set_option('curatescape_alt_item_type_name', $_POST['curatescape_alt_item_type_name']);
		set_option('curatescape_alt_tour_name_p', $_POST['curatescape_alt_tour_name_p']);
		set_option('curatescape_alt_tour_name', $_POST['curatescape_alt_tour_name']);
		set_option('curatescape_app_android', $_POST['curatescape_app_android']);
		set_option('curatescape_app_ios', $_POST['curatescape_app_ios']);
		set_option('curatescape_append_secondary_nav', $_POST['curatescape_append_secondary_nav']);
		set_option('curatescape_auto_subtitle', $_POST['curatescape_auto_subtitle']);
		set_option('curatescape_byline', $_POST['curatescape_byline']);
		set_option('curatescape_dashboard_audit', $_POST['curatescape_dashboard_audit']);
		set_option('curatescape_dashboard_project_mgmt', $_POST['curatescape_dashboard_project_mgmt']);
		set_option('curatescape_dashboard_resources', $_POST['curatescape_dashboard_resources']);
		set_option('curatescape_dashboard_stats', $_POST['curatescape_dashboard_stats']);
		set_option('curatescape_factoids_label', $_POST['curatescape_factoids_label']);
		set_option('curatescape_file_markup', $_POST['curatescape_file_markup']);
		set_option('curatescape_filter_text', $_POST['curatescape_filter_text']);
		set_option('curatescape_form_enforcement', $_POST['curatescape_form_enforcement']);
		set_option('curatescape_form_recommended_only', $_POST['curatescape_form_recommended_only']);
		set_option('curatescape_format_warnings', $_POST['curatescape_format_warnings']);
		set_option('curatescape_gallery_style', $_POST['curatescape_gallery_style']);
		set_option('curatescape_gallery_style_tour', $_POST['curatescape_gallery_style_tour']);
		set_option('curatescape_google_analytics', $_POST['curatescape_google_analytics']);
		set_option('curatescape_home_map', $_POST['curatescape_home_map']);
		set_option('curatescape_home_map_caption', $_POST['curatescape_home_map_caption']);
		set_option('curatescape_home_map_heading', $_POST['curatescape_home_map_heading']);
		set_option('curatescape_inline_factoids', $_POST['curatescape_inline_factoids']);
		set_option('curatescape_inner_heading', $_POST['curatescape_inner_heading']);
		set_option('curatescape_json_cache', $_POST['curatescape_json_cache']);
		set_option('curatescape_json_storage', $_POST['curatescape_json_storage']);
		set_option('curatescape_lightbox_docs', $_POST['curatescape_lightbox_docs']);
		set_option('curatescape_lightbox', $_POST['curatescape_lightbox']);
		set_option('curatescape_map_fixed_center', $_POST['curatescape_map_fixed_center']);
		set_option('curatescape_map_marker_color', $_POST['curatescape_map_marker_color']);
		set_option('curatescape_map_marker_featured_color', $_POST['curatescape_map_marker_featured_color']);
		set_option('curatescape_map_marker_featured_star', $_POST['curatescape_map_marker_featured_star']);
		set_option('curatescape_meta_image', $_POST['curatescape_meta_image']);
		set_option('curatescape_meta_tags', $_POST['curatescape_meta_tags']);
		set_option('curatescape_metadata_browse', $_POST['curatescape_metadata_browse']);
		set_option('curatescape_omit_redundant_elements', $_POST['curatescape_omit_redundant_elements']);
		set_option('curatescape_plugin_styles', $_POST['curatescape_plugin_styles']);
		set_option('curatescape_rss', $_POST['curatescape_rss']);
		set_option('curatescape_shorten_secondary_nav', $_POST['curatescape_shorten_secondary_nav']);
		set_option('curatescape_subtitle_styles', $_POST['curatescape_subtitle_styles']);
		set_option('curatescape_template', $_POST['curatescape_template']);
		set_option('curatescape_theme_fixes', $_POST['curatescape_theme_fixes']);
		set_option('curatescape_tour_thumb_style', $_POST['curatescape_tour_thumb_style']);
	}
}