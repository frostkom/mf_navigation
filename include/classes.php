<?php

class mf_navigation
{
	var $post_type = 'wp_navigation';
	var $arr_settings = array();

	function __construct(){}

	function get_all_settings()
	{
		$this->arr_settings['background_color'] = get_option_or_default('setting_navigation_background_color', "#ffffff");
		$this->arr_settings['text_color'] = get_option_or_default('setting_navigation_text_color', "#000000");
		$this->arr_settings['container_padding_mobile'] = get_option_or_default('setting_navigation_container_padding_mobile', "6rem 2rem 2rem");
		$this->arr_settings['item_border_margin_left'] = get_option_or_default('setting_navigation_item_border_margin_left', "1rem");
		$this->arr_settings['item_border_margin_right'] = get_option_or_default('setting_navigation_item_border_margin_right', "1rem");
		$this->arr_settings['item_border_radius'] = get_option_or_default('setting_navigation_item_border_radius', ".33rem");
		$this->arr_settings['item_padding'] = get_option_or_default('setting_navigation_item_padding', ".6rem 1rem");
		$this->arr_settings['item_padding_mobile'] = get_option_or_default('setting_navigation_item_padding_mobile', ".3rem .6rem");
		$this->arr_settings['breakpoint_tablet'] = get_option_or_default('setting_navigation_breakpoint_tablet', 1200);
		$this->arr_settings['breakpoint_mobile'] = get_option_or_default('setting_navigation_breakpoint_mobile', 930);
	}

	function parse_navigation_menu($markup)
	{
		$menu_items = $current_menu = array();

		preg_match_all('/<!-- (\/*)wp:(.*?) (.*?)(\/*)-->/', $markup, $arr_matches, PREG_SET_ORDER);

		//echo var_export($arr_matches, true);
		$count_temp = count($arr_matches);

		for($i = 0; $i < $count_temp; $i++)
		{
			$is_end = ($arr_matches[$i][1] == "/");
			$type = $arr_matches[$i][2];
			$data = json_decode($arr_matches[$i][3], true);
			$is_single = ($arr_matches[$i][4] == "/");

			$current_level = count($current_menu);

			switch($type)
			{
				case 'navigation-submenu':
					if($is_end)
					{
						unset($current_menu[$current_level - 1]);
					}

					else
					{
						switch($current_level)
						{
							case 0:
								$menu_items[$i] = $data;
							break;

							case 1:
								$menu_items[$current_menu[0]]['children'][$i] = $data;
							break;

							case 2:
								$menu_items[$current_menu[0]]['children'][$current_menu[1]]['children'][$i] = $data;
							break;
						}

						$current_menu[$current_level] = $i;
					}
				break;

				case 'navigation-link':
					switch($current_level)
					{
						case 0:
							$menu_items[$i] = $data;
						break;

						case 1:
							$menu_items[$current_menu[0]]['children'][$i] = $data;
						break;

						case 2:
							$menu_items[$current_menu[0]]['children'][$current_menu[1]]['children'][$i] = $data;
						break;
					}
				break;

				default:
					do_log(__FUNCTION__.": Unknown type ".$type);
				break;
			}
		}

		return $menu_items;
	}
	
	function get_menu_children($arr_menu_object)
	{
		$has_children = (isset($arr_menu_object['children']) && count($arr_menu_object['children']) > 0);

		$out_temp = "<li class='wp-block-navigation-item"
			.(isset($arr_menu_object['className']) && $arr_menu_object['className'] != '' ? " ".$arr_menu_object['className'] : "")
			.(isset($post->ID) && isset($arr_menu_object['id']) && $arr_menu_object['id'] == $post->ID ? " current_menu_item" : "")
			.($has_children ? " has-child" : "")
		."'>"
			."<a class='wp-block-navigation-item__content' href='".$arr_menu_object['url']."'>"
				.$arr_menu_object['label']; // <span class='wp-block-navigation-item__label'></span>

				if($has_children)
				{
					$out_temp .= "<button class='wp-block-navigation__submenu-icon wp-block-navigation-submenu__toggle'>
						<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12' fill='none'>
							<path d='M1.50002 4L6.00002 8L10.5 4' stroke-width='1.5'></path>
						</svg>
					</button>";
				}

			$out_temp .= "</a>";

			if($has_children)
			{
				$out_temp .= "<ul class='wp-block-navigation__submenu-container has-text-color has-base-color has-background has-main-background-color wp-block-navigation-submenu'>";

					foreach($arr_menu_object['children'] as $arr_submenu_object)
					{
						$out_temp .= $this->get_menu_children($arr_submenu_object);
					}

				$out_temp .= "</ul>";
			}

		$out_temp .= "</li>";

		return $out_temp;
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

			$arr_settings = $this->get_all_settings();

			mf_enqueue_style('wp-block-navigation', "/wp-content/plugins/gutenberg/build/block-library/blocks/navigation/style.css", $plugin_version);
			mf_enqueue_style('style_navigation', $plugin_include_url."style.php", $plugin_version."-".md5(var_export($arr_settings, true)));
			mf_enqueue_script('script_navigation', $plugin_include_url."script.js", $plugin_version);

			$out_temp = "";

			$result = $wpdb->get_results($wpdb->prepare("SELECT post_content FROM ".$wpdb->prefix."posts WHERE post_type = %s AND ID = '%d'", $this->post_type, $attributes['navigation_id']));

			foreach($result as $r)
			{
				$post_content = $r->post_content;

				$arr_menu = $this->parse_navigation_menu($post_content);

				foreach($arr_menu as $arr_menu_object)
				{
					$out_temp .= $this->get_menu_children($arr_menu_object);
				}

				//$out_temp .= htmlspecialchars($post_content)."<br><br>";
				//$out_temp .= var_export($arr_menu, true)."<br><br>";
				//$out_temp .= var_export($post, true)."<br><br>";
			}

			if($out_temp != '')
			{
				$out .= "<div".parse_block_attributes(array('class' => "widget navigation", 'attributes' => $attributes)).">"
					//."<i class='fa fa-bars toggle_icon'></i>"
					."<div class='toggle_icon toggle_hamburger'>
						<div class='toggle_line'></div>
						<div class='toggle_line'></div>
						<div class='toggle_line'></div>
					</div>"
					."<nav class='wp-block-navigation is-layout-flex'>" // wp-block-navigation-is-layout-flex wp-container-core-navigation-is-layout-1
						."<div class='wp-block-navigation__responsive-container'>
							<div class='wp-block-navigation__responsive-close'>
								<div class='wp-block-navigation__responsive-dialog'>
									<div class='wp-block-navigation__responsive-container-content'>"
										//."<i class='fa fa-times toggle_icon'></i>"
										."<ul class='wp-block-navigation__container'>"
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
		load_plugin_textdomain('lang_navigation', false, str_replace("/include", "", dirname(plugin_basename(__FILE__)))."/lang/");

		// Blocks
		#######################
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_navigation_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor'), $plugin_version);

		$arr_data = array();
		get_post_children(array('post_type' => $this->post_type, 'order_by' => 'post_title', 'add_choose_here' => true), $arr_data);

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
		$arr_settings['setting_navigation_container_padding_mobile'] = __("Container Padding", 'lang_navigation')." (".__("Mobile", 'lang_navigation').")";
		$arr_settings['setting_navigation_item_border_margin_left'] = __("Item Border Margin", 'lang_navigation')." (".__("Left", 'lang_navigation').")";
		$arr_settings['setting_navigation_item_border_margin_right'] = __("Item Border Margin", 'lang_navigation')." (".__("Right", 'lang_navigation').")";
		$arr_settings['setting_navigation_item_border_radius'] = __("Item Border Radius", 'lang_navigation');
		$arr_settings['setting_navigation_item_padding'] = __("Item Padding", 'lang_navigation');
		$arr_settings['setting_navigation_item_padding_mobile'] = __("Item Padding", 'lang_navigation')." (".__("Mobile", 'lang_navigation').")";
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
		$option = get_option_or_default($setting_key, "#ffffff");

		echo show_textfield(array('type' => 'color', 'name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_text_color_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, "#000000");

		echo show_textfield(array('type' => 'color', 'name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_container_padding_mobile_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, "6rem 2rem 2rem");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_border_margin_left_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, "1rem");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_border_margin_right_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, "1rem");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_border_radius_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, ".33rem");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_padding_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, ".6rem 1rem");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_padding_mobile_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, ".3rem .6rem");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_breakpoint_tablet_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, 1200);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'suffix' => "px"));
	}

	function setting_navigation_breakpoint_mobile_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, 930);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option, 'suffix' => "px"));
	}
}