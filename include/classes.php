<?php

class mf_navigation
{
	var $post_type = 'wp_navigation';

	function __construct(){}

	function parse_navigation_menu($markup)
	{
		$menu = array();
		$current_submenu = null;
		
		$pattern = '/<!-- wp:navigation-(.*?) (.*?) \/-->/';
		preg_match_all($pattern, $markup, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match)
		{
			$type = $match[1];
			$data = json_decode($match[2], true);

			switch($type)
			{
				case 'submenu':
					$current_submenu = $data;
					$current_submenu['children'] = array();
					$menu[] = $current_submenu;
				break;

				case 'link':
					if($current_submenu)
					{
						$current_submenu['children'][] = $data;
					}

					else
					{
						$menu[] = $data;
					}
				break;

				default:
					do_log(__FUNCTION__.": ".$type." has to be handled");
				break;
			}
		}
		
		return $menu;
	}

	function block_render_callback($attributes)
	{
		global $wpdb, $post;

		if(!isset($attributes['navigation_id'])){			$attributes['navigation_id'] = 0;}

		$out = "";

		if($attributes['navigation_id'] > 0)
		{
			$plugin_include_url = plugin_dir_url(__FILE__);
			$plugin_version = get_plugin_version(__FILE__);

			mf_enqueue_style('style_navigation', $plugin_include_url."style.php", $plugin_version);
			mf_enqueue_script('script_navigation', $plugin_include_url."script.js", $plugin_version);

			$out_temp = "";

			$result = $wpdb->get_results($wpdb->prepare("SELECT post_content FROM ".$wpdb->prefix."posts WHERE post_type = %s AND ID = '%d'", $this->post_type, $attributes['navigation_id']));

			foreach($result as $r)
			{
				$post_content = $r->post_content;

				$arr_menu = $this->parse_navigation_menu($post_content);

				foreach($arr_menu as $arr_menu_object)
				{
					$out_temp .= "<li class='wp-block-navigation-item"
						.(isset($arr_menu_object['className']) && $arr_menu_object['className'] != '' ? " ".$arr_menu_object['className'] : "")
						.($arr_menu_object['id'] == $post->ID ? " current_menu_item" : "")
					."'>" // wp-block-navigation-link
						."<a class='wp-block-navigation-item__content' href='".$arr_menu_object['url']."'>
							<span class='wp-block-navigation-item__label'>".$arr_menu_object['label']."</span>
						</a>
					</li>";

					if(isset($arr_menu_object['children']))
					{
						do_log(__FUNCTION__.": Display submenu here");
					}
				}

				//$out_temp .= var_export($arr_menu, true).", ".var_export($post, true);
			}

			if($out_temp != '')
			{
				$out .= "<div class='widget navigation'>
					<i class='fa fa-bars toggle_icon'></i>
					<nav class='wp-block-navigation is-layout-flex'>" // wp-block-navigation-is-layout-flex wp-container-core-navigation-is-layout-1
						."<div class='wp-block-navigation__responsive-container'>
							<div class='wp-block-navigation__responsive-close'>
								<div class='wp-block-navigation__responsive-dialog'>
									<div class='wp-block-navigation__responsive-container-content'>
										<i class='fa fa-times toggle_icon'></i>
										<ul class='wp-block-navigation__container'>"
											.$out_temp
										."</ul>
									</div>
								</div>
							</div>
						</div>
					</nav>
				</div>";
			}
		}

		else if(IS_SUPER_ADMIN)
		{
			$out .= __("There is no menu here yet", 'lang_navigation');
		}

		return $out;
	}

	function init()
	{
		// Blocks
		#######################
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_navigation_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor'), $plugin_version);

		$arr_data = array();
		get_post_children(array('post_type' => $this->post_type, 'order_by' => 'post_title'), $arr_data);

		wp_localize_script('script_navigation_block_wp', 'script_navigation_block_wp', array(
			'arr_navigation' => $arr_data,
			'yes_no_for_select' => get_yes_no_for_select(),
		));

		register_block_type('mf/navigation', array(
			'editor_script' => 'script_navigation_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_callback'),
			//'style' => 'style_base_block_wp',
		));
		#######################
	}

	function settings_navigation()
	{
		$options_area = __FUNCTION__;

		add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

		$arr_settings = array();
		$arr_settings['setting_navigation_background_color'] = __("Background Color", 'lang_navigation');
		$arr_settings['setting_navigation_text_color'] = __("Text Color", 'lang_navigation');
		$arr_settings['setting_navigation_breakpoint_tablet'] = __("Breakpoint", 'lang_navigation')." (".__("Tablet", 'lang_navigation').")";
		$arr_settings['setting_navigation_breakpoint_mobile'] = __("Breakpoint", 'lang_navigation')." (".__("Mobile", 'lang_navigation').")";

		show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
	}

	function settings_navigation_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, "Navigation+");
	}

	function setting_navigation_background_color_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_textfield(array('type' => 'color', 'name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_text_color_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_textfield(array('type' => 'color', 'name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_breakpoint_tablet_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 1200);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'suffix' => "px"));
	}

	function setting_navigation_breakpoint_mobile_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 930);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'suffix' => "px"));
	}
}