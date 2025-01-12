<?php

class mf_navigation
{
	var $post_type = 'wp_navigation';

	function __construct(){}

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
				case 'page-list':
					do_log(__FUNCTION__.": Get all published pages with get_post_children()");
				break;

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
					do_log(__FUNCTION__.": Unknown type ".$type." (".htmlspecialchars($markup)." -> ".var_export($arr_matches, true).")");
				break;
			}
		}

		return $menu_items;
	}
	
	function get_menu_children($arr_menu_object)
	{
		global $post;

		$has_children = (isset($arr_menu_object['children']) && count($arr_menu_object['children']) > 0);

		$is_button = (isset($arr_menu_object['className']) && strpos($arr_menu_object['className'], 'button') !== false);

		$out_temp = "<li class='wp-block-navigation-item"
			.(isset($arr_menu_object['className']) && $arr_menu_object['className'] != '' ? " ".$arr_menu_object['className'] : "")
			.(isset($post->ID) && isset($arr_menu_object['id']) && $arr_menu_object['id'] == $post->ID ? " current_menu_item" : "")
			.($has_children ? " has-child" : "")
		."'>";

			if($is_button)
			{
				$out_temp .= "<div class='wp-block-button'>";
			}

				$out_temp .= "<a class='".($is_button ? "wp-block-button__link" : "wp-block-navigation-item__content")."' href='".$arr_menu_object['url']."'>";

					$out_temp .= $arr_menu_object['label']; // <span class='wp-block-navigation-item__label'></span>

					if($has_children)
					{
						$out_temp .= "<button class='wp-block-navigation__submenu-icon wp-block-navigation-submenu__toggle'>
							<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12' fill='none'>
								<path d='M1.50002 4L6.00002 8L10.5 4' stroke-width='1.5'></path>
							</svg>
						</button>";
					}

				$out_temp .= "</a>";

			if($is_button)
			{
				$out_temp .= "</div>";
			}

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

		if(!isset($attributes['navigation_is_in_header'])){			$attributes['navigation_is_in_header'] = 'yes';}
		if(!isset($attributes['navigation_id'])){					$attributes['navigation_id'] = 0;}
		if(!isset($attributes['navigation_mobile_ready'])){			$attributes['navigation_mobile_ready'] = 'yes';}
		//if(!isset($attributes['navigation_breakpoint_tablet'])){	$attributes['navigation_breakpoint_tablet'] = "";}
		//if(!isset($attributes['navigation_breakpoint_mobile'])){	$attributes['navigation_breakpoint_mobile'] = "";}

		$out = "";

		if($attributes['navigation_id'] > 0)
		{
			$widget_id = "widget_navigation_".md5(var_export($attributes, true));

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
				$style = "";

				/*if($attributes['navigation_link_color'] != '')
				{
					$style .= "color: ".$attributes['navigation_link_color'];
				}*/

				if(isset($attributes['style']) && is_array($attributes['style']))
				{
					//$out .= var_export($attributes, true);

					foreach($attributes['style'] as $key_parent => $arr_value)
					{
						switch($key_parent)
						{
							case 'color':
								// You need to use a unique ID/Class and override style.php for this to work
								//$setting_navigation_breakpoint_mobile = ($attributes['navigation_breakpoint_mobile'] > 0 ? $attributes['navigation_breakpoint_mobile'] : get_option_or_default('setting_navigation_breakpoint_mobile', 930));
								$setting_navigation_breakpoint_mobile = get_option_or_default('setting_navigation_breakpoint_mobile', 930);

								if(isset($arr_value['text']) && $arr_value['text'] != '')
								{
									if($attributes['navigation_is_in_header'] == 'yes')
									{
										$style .= "header .wp-block-site-title a
										{
											color: ".$arr_value['text'].";
										}";
									}
									
									$style .= "#".$widget_id." .wp-block-navigation, #".$widget_id." .has-child .wp-block-navigation-item
									{
										color: ".$arr_value['text'].";
									}
									
									#".$widget_id." .wp-block-navigation-item.border a
									{
										border-color: ".$arr_value['text'].";
									}
									
									#".$widget_id." .wp-block-navigation-item.invert a
									{
										background-color: ".$arr_value['text']." !important;
										border-color: ".$arr_value['text']." !important;
									}
									
									@media screen and (max-width: ".($setting_navigation_breakpoint_mobile - 1)."px)
									{
										#".$widget_id." .toggle_line
										{
											background-color: ".$arr_value['text'].";
										}

										#".$widget_id.".mobile_ready .wp-block-navigation
										{
											background: ".$arr_value['text'].";
										}

										#".$widget_id.".mobile_ready .wp-block-navigation .wp-block-navigation-item.invert a
										{
											color: ".$arr_value['text']." !important;
										}

										#".$widget_id.".mobile_ready .has-child:hover .wp-block-navigation-item, #".$widget_id.".mobile_ready .has-child.is_open .wp-block-navigation-item
										{
											background-color: ".$arr_value['text']." !important;
										}
									}";
								}

								if(isset($arr_value['background']) && $arr_value['background'] != '')
								{
									$style .= "#".$widget_id." .has-child .wp-block-navigation__submenu-container
									{
										background-color: ".$arr_value['background'].";
									}
									
									#".$widget_id." .wp-block-navigation-item.invert
									{
										color: ".$arr_value['background'].";
									}
									
									@media screen and (max-width: ".($setting_navigation_breakpoint_mobile - 1)."px)
									{
										.menu_is_open header figure.wp-block-image img
										{
											background-color: ".$arr_value['background'].";
										}

										.menu_is_open header .wp-block-site-title a
										{
											color: ".$arr_value['background'].";
										}

										#".$widget_id.".is_open .toggle_line
										{
											background-color: ".$arr_value['background'].";
										}

										#".$widget_id.".mobile_ready .wp-block-navigation
										{
											color: ".$arr_value['background'].";
										}

										#".$widget_id.".mobile_ready .wp-block-navigation .wp-block-navigation-item.invert a
										{
											background-color: ".$arr_value['background']." !important;
											border-color: ".$arr_value['background']." !important;
										}

										#".$widget_id.".mobile_ready .has-child:hover .wp-block-navigation-item, #".$widget_id.".mobile_ready .has-child.is_open .wp-block-navigation-item
										{
											color: ".$arr_value['background']." !important;
										}
									}";
								}

								//$style .= $key_parent.": ".$arr_value['text'].";";
							break;

							case 'elements':
								//array ( 'link' => array ( 'color' => array ( 'text' => '#ffffff', ), ), )
							break;

							default:
								do_log(__FUNCTION__.": The key parent '".$key_parent."' with value '".var_export($arr_value, true)."' has to be taken care of");
							break;
						}
					}
				}

				$plugin_include_url = plugin_dir_url(__FILE__);

				mf_enqueue_style('wp-block-navigation', "/wp-content/plugins/gutenberg/build/block-library/blocks/navigation/style.css");
				mf_enqueue_style('style_navigation', $plugin_include_url."style.php");
				mf_enqueue_script('script_navigation', $plugin_include_url."script.js");

				if($style != '')
				{
					$out .= "<style>".$style."</style>";
				}

				$out .= "<div id='".$widget_id."'".parse_block_attributes(array('class' => "widget navigation".($attributes['navigation_mobile_ready'] == 'yes' ? " mobile_ready" : ""), 'attributes' => $attributes)).">";

					if($attributes['navigation_mobile_ready'] == 'yes')
					{
						$out .= "<div class='toggle_icon toggle_hamburger'>
							<div class='toggle_line'></div>
							<div class='toggle_line'></div>
							<div class='toggle_line'></div>
						</div>";
					}

					$out .= "<nav class='wp-block-navigation is-layout-flex'>"
						."<div class='wp-block-navigation__responsive-container'>
							<div class='wp-block-navigation__responsive-close'>
								<div class='wp-block-navigation__responsive-dialog'>
									<div class='wp-block-navigation__responsive-container-content'>
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

		wp_register_script('script_navigation_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'), $plugin_version);

		$arr_data = array();
		get_post_children(array('post_type' => $this->post_type, 'order_by' => 'post_title', 'add_choose_here' => true), $arr_data);

		wp_localize_script('script_navigation_block_wp', 'script_navigation_block_wp', array(
			'block_title' => __("Navigation+", 'lang_navigation'),
			'block_description' => __("Display a Navigation+", 'lang_navigation'),
			'navigation_is_in_header_label' => __("Is in Header", 'lang_navigation'),
			'navigation_id_label' => __("Menu", 'lang_navigation'),
			'arr_navigation' => $arr_data,
			'navigation_mobile_ready_label' => __("Mobile Ready", 'lang_navigation'),
			'yes_no_for_select' => get_yes_no_for_select(),
			//'navigation_breakpoint_tablet' => __("Breakpoint", 'lang_navigation')." (".__("Tablet", 'lang_navigation').")",
			//'navigation_breakpoint_mobile' => __("Breakpoint", 'lang_navigation')." (".__("Mobile", 'lang_navigation').")",
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