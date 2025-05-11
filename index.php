<?php
/*
Plugin Name: MF Navigation+
Plugin URI: https://github.com/frostkom/mf_navigation
Description:
Version: 1.3.17
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://martinfors.se
Text Domain: lang_navigation
Domain Path: /lang

Depends: Meta Box, MF Base
GitHub Plugin URI: frostkom/mf_navigation
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_navigation = new mf_navigation();

	add_action('init', array($obj_navigation, 'init'));

	if(is_admin())
	{
		register_uninstall_hook(__FILE__, 'uninstall_navigation');

		add_action('admin_init', array($obj_navigation, 'settings_navigation'));
	}

	add_filter('filter_cache_logged_in_cookies', array($obj_navigation, 'filter_cache_logged_in_cookies'), 10);

	load_plugin_textdomain('lang_navigation', false, dirname(plugin_basename(__FILE__))."/lang/");

	function uninstall_navigation()
	{
		include_once("include/classes.php");

		$obj_navigation = new mf_navigation();

		mf_uninstall_plugin(array(
			'options' => array('setting_navigation_background_color', 'setting_navigation_text_color', 'setting_navigation_container_padding_mobile', 'setting_navigation_item_border_margin_left', 'setting_navigation_item_border_margin_right', 'setting_navigation_item_border_radius', 'setting_navigation_item_padding', 'setting_navigation_item_padding_mobile', 'setting_navigation_dim_content', 'setting_navigation_breakpoint_tablet', 'setting_navigation_breakpoint_mobile', 'option_navigation_logged_in_cookies', 'setting_navigation_logged_in_cookies'),
		));
	}
}