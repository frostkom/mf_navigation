<?php

class mf_navigation
{
	var $post_type = 'wp_navigation';
	var $has_separator;
	var $has_item_border;

	function __construct(){}

	function cron_base()
	{
		global $wpdb, $obj_group;

		$obj_cron = new mf_cron();
		$obj_cron->start(__CLASS__);

		if($obj_cron->is_running == false)
		{
			mf_uninstall_plugin(array(
				'options' => array('setting_navigation_dim_content', 'setting_navigation_breakpoint_tablet', 'setting_navigation_breakpoint_mobile'),
			));
		}

		$obj_cron->end();
	}

	function parse_navigation_menu($markup)
	{
		$menu_items = $current_menu = [];

		preg_match_all('/<!-- (\/*)wp:(.*?) (.*?)(\/*)-->/', $markup, $arr_matches, PREG_SET_ORDER);

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
					$arr_data = [];
					get_post_children(array('add_choose_here' => false), $arr_data);

					foreach($arr_data as $key => $value)
					{
						$menu_items[$key] = array(
							'url' => get_permalink($key),
							'label' => $value,
						);
					}
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

	function loop_through_menu($data)
	{
		global $post;

		if(!isset($data['follow'])){	$data['follow'] = true;}

		$html = "";

		$data['menu'] = apply_filters('filter_navigation_menu', $data['menu']);

		foreach($data['menu'] as $arr_menu_object)
		{
			if(!isset($arr_menu_object['id'])){			$arr_menu_object['id'] = "";}
			if(!isset($arr_menu_object['className'])){	$arr_menu_object['className'] = "";}
			if(!isset($arr_menu_object['children'])){	$arr_menu_object['children'] = [];}

			$has_children = (count($arr_menu_object['children']) > 0);
			$is_button = (strpos($arr_menu_object['className'], 'button') !== false);

			$html .= "<li class='wp-block-navigation-item";

				if($arr_menu_object['className'] != '')
				{
					if(strpos($arr_menu_object['className'], "separator") !== false)
					{
						$this->has_separator = true;
					}

					if(strpos($arr_menu_object['className'], "border") !== false || strpos($arr_menu_object['className'], "invert") !== false)
					{
						$this->has_item_border = true;
					}

					$html .= " ".$arr_menu_object['className'];
				}

				$follow_link = $data['follow'];

				if($follow_link == true)
				{
					if($arr_menu_object['id'] > 0)
					{
						$follow_link = apply_filters('filter_is_indexed', $follow_link, $arr_menu_object['id']);
					}

					else
					{
						$follow_link = false;
					}
				}

				$http_protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
				$http_host = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : remove_protocol(array('url' => get_site_url(), 'clean' => true)));
				$http_request = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');

				if((isset($post->ID) && $arr_menu_object['id'] == $post->ID) || ($arr_menu_object['url'] == $http_protocol."://".$http_host.$http_request))
				{
					$html .= " current_menu_item";
				}

				if($has_children)
				{
					$html .= " has-child";

					foreach($arr_menu_object['children'] as $key_temp => $arr_value_temp)
					{
						if(isset($post->ID) && isset($arr_value_temp['id']) && $arr_value_temp['id'] == $post->ID)
						{
							$html .= " current_menu_parent";
						}
					}
				}

			$html .= "'>";

				if($is_button)
				{
					$plugin_base_include_url = plugins_url()."/mf_base/include/";
					mf_enqueue_style('style_base_button', $plugin_base_include_url."style_button.css");

					$html .= "<div class='wp-block-button'>";
				}

					$html .= "<a class='".($is_button ? "wp-block-button__link" : "wp-block-navigation-item__content")."' href='".$arr_menu_object['url']."'".($follow_link == true ? "" : " rel='nofollow'").">"
						.$arr_menu_object['label'];

						if($has_children)
						{
							$html .= "<button class='wp-block-navigation__submenu-icon wp-block-navigation-submenu__toggle' aria-label='".__("An icon to display if the submenu is open or not", 'lang_navigation')."'>
								<svg viewBox='0 0 12 12' fill='none'>
									<path d='M1.50002 4L6.00002 8L10.5 4' stroke-width='1.5'></path>
								</svg>
							</button>";
						}

					$html .= "</a>";

				if($is_button)
				{
					$html .= "</div>";
				}

				if($has_children)
				{
					$data_temp = $data;
					$data_temp['menu'] = $arr_menu_object['children'];

					$html .= "<ul class='wp-block-navigation__submenu-container wp-block-navigation-submenu'>" // has-base-color has-main-background-color has-background has-text-color
						.$this->loop_through_menu($data_temp)
					."</ul>";
				}

			$html .= "</li>";
		}

		return $html;
	}

	function is_cookie_in_htaccess($cookie)
	{
		$setting_navigation_logged_in_cookies = get_option_or_default('setting_navigation_logged_in_cookies', []);

		return in_array($cookie, $setting_navigation_logged_in_cookies);
	}

	function does_cookie_exist($cookie)
	{
		if(isset($_COOKIE) && count($_COOKIE) > 0)
		{
			foreach($_COOKIE as $cookie_key => $cookie_value)
			{
				if(substr($cookie_key, 0, strlen($cookie)) == $cookie)
				{
					return true;
				}
			}
		}

		return false;
	}

	function block_render_callback($attributes)
	{
		global $wpdb, $post;

		if(!isset($attributes['navigation_is_in_header'])){			$attributes['navigation_is_in_header'] = 'yes';}
		if(!isset($attributes['navigation_id'])){					$attributes['navigation_id'] = 0;}
		if(!isset($attributes['navigation_id_logged_in'])){			$attributes['navigation_id_logged_in'] = 0;}
		if(!isset($attributes['navigation_id_logged_in_cookie'])){	$attributes['navigation_id_logged_in_cookie'] = 'wp-settings-time';}
		if(!isset($attributes['navigation_mobile_ready'])){			$attributes['navigation_mobile_ready'] = 'yes';}
		if(!isset($attributes['navigation_orientation'])){			$attributes['navigation_orientation'] = 'horizontal';}

		$out = "";

		if($attributes['navigation_id'] > 0)
		{
			$widget_id = "widget_navigation_".md5(var_export($attributes, true));

			$menu_items_public = $menu_items_logged_in = "";
			$this->has_separator = $this->has_item_border = false;

			$result = $wpdb->get_results($wpdb->prepare("SELECT post_content FROM ".$wpdb->prefix."posts WHERE post_type = %s AND ID = '%d'", $this->post_type, $attributes['navigation_id']));

			foreach($result as $r)
			{
				$post_content = $r->post_content;

				$arr_menu = $this->parse_navigation_menu($post_content);

				$menu_items_public .= $this->loop_through_menu(array('menu' => $arr_menu));
			}

			if($menu_items_public != '')
			{
				$style = $script = "";

				if($attributes['navigation_id_logged_in'] > 0 && $attributes['navigation_id_logged_in'] != $attributes['navigation_id'])
				{
					$result = $wpdb->get_results($wpdb->prepare("SELECT post_content FROM ".$wpdb->prefix."posts WHERE post_type = %s AND ID = '%d'", $this->post_type, $attributes['navigation_id_logged_in']));

					foreach($result as $r)
					{
						$post_content = $r->post_content;

						$arr_menu = $this->parse_navigation_menu($post_content);

						$menu_items_logged_in .= $this->loop_through_menu(array('menu' => $arr_menu, 'follow' => true));
					}

					if($attributes['navigation_id_logged_in_cookie'] != '')
					{
						$option_navigation_logged_in_cookies = get_option_or_default('option_navigation_logged_in_cookies', []);

						if(!in_array($attributes['navigation_id_logged_in_cookie'], $option_navigation_logged_in_cookies))
						{
							$option_navigation_logged_in_cookies[] = $attributes['navigation_id_logged_in_cookie'];

							update_option('option_navigation_logged_in_cookies', $option_navigation_logged_in_cookies, false);
						}

						if($this->is_cookie_in_htaccess($attributes['navigation_id_logged_in_cookie']) == false)
						{
							$script .= "(function()
							{
								function is_logged_in()
								{
									return document.cookie.split(';').some(function(item)
									{
										return (item.trim().indexOf('".$attributes['navigation_id_logged_in_cookie']."') == 0);
									});
								}

								if(is_logged_in())
								{
									document.body.classList.remove('not-logged-in');
									document.body.classList.add('logged-in');
								}

								else
								{
									document.body.classList.remove('logged-in');
									document.body.classList.add('not-logged-in');
								}
							})();";
						}
					}
				}

				if(isset($attributes['style']) && is_array($attributes['style']))
				{
					foreach($attributes['style'] as $key_parent => $arr_value)
					{
						switch($key_parent)
						{
							case 'color':
								$arr_breakpoints = apply_filters('get_layout_breakpoints', ['tablet' => 1200, 'mobile' => 930, 'suffix' => "px"]);

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
									}";

									if($attributes['navigation_mobile_ready'] == 'yes')
									{
										$style .= "@media screen and (max-width: ".($arr_breakpoints['mobile'] - 1).$arr_breakpoints['suffix'].")
										{
											#".$widget_id." .toggle_hamburger > div
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
									}";

									if($attributes['navigation_mobile_ready'] == 'yes')
									{
										$style .= "@media screen and (max-width: ".($arr_breakpoints['mobile'] - 1).$arr_breakpoints['suffix'].")
										{
											.menu_is_open header figure.wp-block-image img
											{
												background-color: ".$arr_value['background'].";
											}

											.menu_is_open header .wp-block-site-title a
											{
												color: ".$arr_value['background'].";
											}

											#".$widget_id.".is_open .toggle_hamburger > div
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
								}
							break;

							case 'elements':
								//array ( 'link' => array ( 'color' => array ( 'text' => '#ffffff', ), ), )
							break;

							case 'border':
								// Do nothing. Already taken care of in parse_block_attributes()
							break;

							default:
								do_log(__FUNCTION__.": The key parent '".$key_parent."' with value '".var_export($arr_value, true)."' has to be taken care of");
							break;
						}
					}
				}

				if($this->has_separator == true)
				{
					$setting_navigation_item_padding = get_option('setting_navigation_item_padding', ".6em 1em");

					$style .= ".widget.navigation.is_horizontal .wp-block-navigation-item.separator
					{
						padding-left: 2em;
					}

					.widget.navigation.is_vertical .wp-block-navigation-item.separator
					{
						padding-top: 1em;
					}

					.widget.navigation .wp-block-navigation-item.separator:before
					{
						background: #ccc;
						content: '';
						margin: ".$setting_navigation_item_padding.";
						position: absolute;
						top: 0;
						right: 0;
						bottom: 0;
						left: 0;
					}

						.widget.navigation.is_horizontal .wp-block-navigation-item.separator:before
						{
							height: auto;
							width: .05em;
						}

						.widget.navigation.is_vertical .wp-block-navigation-item.separator:before
						{
							height: .06em;
							width: auto;
						}";
				}

				if($this->has_item_border == true)
				{
					$setting_navigation_background_color = get_option_or_default('setting_navigation_background_color', "#fff");
					$setting_navigation_text_color = get_option_or_default('setting_navigation_text_color', "#000");

					$setting_navigation_item_border_margin_left = get_option_or_default('setting_navigation_item_border_margin_left', "1em");
					$setting_navigation_item_border_margin_right = get_option_or_default('setting_navigation_item_border_margin_right', "1em");

					if($setting_navigation_item_border_margin_left != '' && $setting_navigation_item_border_margin_right != '')
					{
						$style .= ".widget.navigation .wp-block-navigation-item.border:not(:last-of-type), .widget.navigation .wp-block-navigation-item.invert:not(:last-of-type)
						{";

							if($setting_navigation_item_border_margin_left != '')
							{
								$style .= "margin-left: ".$setting_navigation_item_border_margin_left.";";
							}

							if($setting_navigation_item_border_margin_right != '')
							{
								$style .= "margin-right: ".$setting_navigation_item_border_margin_right.";";
							}

						$style .= "}";
					}

						$style .= ".widget.navigation .wp-block-navigation-item.border a
						{
							border: .1em solid ".$setting_navigation_text_color.";
						}

						.widget.navigation .wp-block-navigation-item.invert
						{
							color: ".$setting_navigation_background_color.";
						}

							.widget.navigation .wp-block-navigation-item.invert a
							{
								background-color: ".$setting_navigation_text_color." !important;
								border: .1em solid ".$setting_navigation_text_color." !important;
							}";
				}

				$plugin_include_url = plugin_dir_url(__FILE__);

				wp_enqueue_style('wp-block-navigation');
				mf_enqueue_style('style_navigation', $plugin_include_url."style.php");
				mf_enqueue_script('script_navigation', $plugin_include_url."script.js");

				if($style != '')
				{
					$out .= "<style>".$style."</style>";
				}

				if(isset($attributes['class']) && $attributes['class'] != '')
				{
					$data['class'] = str_replace("is_centered", "aligncenter", $attributes['class']);
				}

				$out .= "<div id='".$widget_id."'".parse_block_attributes(array('class' => "widget navigation".($attributes['navigation_mobile_ready'] == 'yes' ? " mobile_ready" : "")." ".($attributes['navigation_orientation'] == 'vertical' ? "is_vertical" : "is_horizontal"), 'attributes' => $attributes)).">";

					if($attributes['navigation_mobile_ready'] == 'yes')
					{
						$out .= "<div class='toggle_hamburger'><div></div><div></div><div></div></div>";
					}

					$out .= "<nav class='wp-block-navigation is-layout-flex'>"
						."<div class='wp-block-navigation__responsive-container'>";

							//$out .= "<div class='wp-block-navigation__responsive-close'>";
								//$out .= "<div class='wp-block-navigation__responsive-dialog'>";
									//$out .= "<div class='wp-block-navigation__responsive-container-content'>";

										if($menu_items_logged_in != '' && $this->is_cookie_in_htaccess($attributes['navigation_id_logged_in_cookie']))
										{
											$out .= "<ul class='wp-block-navigation__container wp-block-navigation'>";

												if($this->does_cookie_exist($attributes['navigation_id_logged_in_cookie']) == false)
												{
													$out .= $menu_items_public;
												}

												else
												{
													$out .= $menu_items_logged_in;
												}

											$out .= "</ul>";
										}

										else
										{
											$out .= "<ul class='wp-block-navigation__container wp-block-navigation".($menu_items_logged_in != '' ? " menu_items_public" : "")."'>"
												.$menu_items_public
											."</ul>";

											if($menu_items_logged_in != '')
											{
												$out .= "<ul class='wp-block-navigation__container wp-block-navigation menu_items_logged_in'>"
													.$menu_items_logged_in
												."</ul>";
											}
										}

									//$out .= "</div>";
								//$out .= "</div>";
							//$out .= "</div>";

						$out .= "</div>
					</nav>
				</div>";

				if($script != '')
				{
					$out .= "<script id='script_navigation_inline-js'>".$script."</script>";
				}
			}
		}

		else if(IS_SUPER_ADMIN)
		{
			$out .= "<span class='grey'>(".__("There is no menu here yet", 'lang_navigation').")</span>";
		}

		return $out;
	}

	function enqueue_block_editor_assets()
	{
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_navigation_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'), $plugin_version, true);

		$arr_data = [];
		get_post_children(array('post_type' => $this->post_type, 'order_by' => 'post_title', 'add_choose_here' => true), $arr_data);

		wp_localize_script('script_navigation_block_wp', 'script_navigation_block_wp', array(
			'block_title' => __("Navigation+", 'lang_navigation'),
			'block_description' => __("Display a Navigation", 'lang_navigation'),
			'navigation_is_in_header_label' => __("Is next to logo", 'lang_navigation'),
			'navigation_id_label' => __("Menu", 'lang_navigation'),
			'arr_navigation' => $arr_data,
			'navigation_id_logged_in_label' => " - ".__("Logged In", 'lang_navigation'),
			'navigation_id_logged_in_cookie_label' => " - ".__("Cookie Key", 'lang_navigation'),
			'navigation_mobile_ready_label' => __("Mobile Ready", 'lang_navigation'),
			'yes_no_for_select' => get_yes_no_for_select(),
			'navigation_orientation_label' => __("Orientation", 'lang_navigation'),
			'navigation_orientation_for_select' => array('horizontal' => __("Horizontal", 'lang_navigation'), 'vertical' => __("Vertical", 'lang_navigation')),
		));
	}

	function init()
	{
		load_plugin_textdomain('lang_navigation', false, str_replace("/include", "", dirname(plugin_basename(__FILE__)))."/lang/");

		register_block_type('mf/navigation', array(
			'editor_script' => 'script_navigation_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_callback'),
		));
	}

	function settings_navigation()
	{
		$options_area = __FUNCTION__;

		add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

		$has_horizontal_menu = $has_vertical_menu = $has_item_with_border = false;

		// Check if there are horizontal or vertical menus
		######################
		$block_code = '<!-- wp:mf/navigation {%} /-->';
		$arr_ids = apply_filters('get_page_from_block_code', [], $block_code);

		foreach($arr_ids as $post_id)
		{
			$post_content = get_post_field('post_content', $post_id);

			if(preg_match('/<!--\s*wp:mf\/navigation\s*(\{.*?\})\s*\/-->/s', $post_content, $matches))
			{
				if(isset($matches[1]))
				{
					$arr_json = json_decode($matches[1], true);

					if(isset($arr_json['navigation_orientation']))
					{
						if($arr_json['navigation_orientation'] == 'horizontal')
						{
							$has_horizontal_menu = true;
						}

						else if($arr_json['navigation_orientation'] == 'vertical')
						{
							$has_vertical_menu = true;
						}
					}

					else
					{
						$has_horizontal_menu = true;
					}
				}
			}
		}
		######################

		// Check if there are menu items with border as class
		######################
		$block_code = '<!-- wp:navigation-link {%} /-->';
		$arr_ids = apply_filters('get_page_from_block_code', [], $block_code);

		foreach($arr_ids as $post_id)
		{
			$post_content = get_post_field('post_content', $post_id);

			if(preg_match('/<!--\s*wp:mf\/navigation-link\s*(\{.*?\})\s*\/-->/s', $post_content, $matches))
			{
				if(isset($matches[1]))
				{
					$arr_json = json_decode($matches[1], true);

					if(isset($arr_json['className']) && preg_match("/(border|invert)/", $arr_json['className']))
					{
						$has_item_with_border = true;
					}
				}
			}
		}
		######################

		$arr_settings = [];
		$arr_settings['setting_navigation_background_color'] = __("Background Color", 'lang_navigation');
		$arr_settings['setting_navigation_text_color'] = __("Text Color", 'lang_navigation');
		$arr_settings['setting_navigation_container_padding_mobile'] = __("Container Padding", 'lang_navigation')." (".__("Mobile", 'lang_navigation').")";

		if($has_item_with_border == true)
		{
			$arr_settings['setting_navigation_item_border_margin_left'] = __("Item Border Margin", 'lang_navigation')." (".__("Left", 'lang_navigation').")";
			$arr_settings['setting_navigation_item_border_margin_right'] = __("Item Border Margin", 'lang_navigation')." (".__("Right", 'lang_navigation').")";
		}

		else
		{
			delete_option('setting_navigation_item_border_margin_left');
			delete_option('setting_navigation_item_border_margin_right');
		}

		$arr_settings['setting_navigation_item_vertical_padding_left'] = __("Item Vertical Padding", 'lang_navigation')." (".__("Left", 'lang_navigation').")";
		$arr_settings['setting_navigation_item_border_radius'] = __("Item Border Radius", 'lang_navigation');

		if($has_horizontal_menu == true)
		{
			$arr_settings['setting_navigation_item_padding'] = __("Item Padding", 'lang_navigation')." (".__("Horizontal", 'lang_navigation').")";
		}

		else
		{
			delete_option('setting_navigation_item_padding');
		}

		if($has_vertical_menu == true)
		{
			$arr_settings['setting_navigation_item_padding_vertical'] = __("Item Padding", 'lang_navigation')." (".__("Vertical", 'lang_navigation').")";
		}

		else
		{
			delete_option('setting_navigation_item_padding_vertical');
		}

		$arr_settings['setting_navigation_item_padding_mobile'] = __("Item Padding", 'lang_navigation')." (".__("Mobile", 'lang_navigation').")";

		if(count(get_option_or_default('option_navigation_logged_in_cookies', [])) > 0)
		{
			$arr_settings['setting_navigation_logged_in_cookies'] = __("Logged in Cookies", 'lang_navigation');
		}

		else
		{
			delete_option('setting_navigation_logged_in_cookies');
		}

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

		echo get_form_accents(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_text_color_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, "#000000");

		echo get_form_accents(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_container_padding_mobile_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, "6em 2em 2em");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_border_margin_left_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, "1em");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_border_margin_right_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, "1em");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_vertical_padding_left_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_border_radius_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, ".33em");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_padding_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, ".6em 1em");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_padding_vertical_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, ".6em 0");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_navigation_item_padding_mobile_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, ".3em .6em");

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function get_logged_in_cookies_for_select()
	{
		$arr_data = [];

		$option_navigation_logged_in_cookies = get_option_or_default('option_navigation_logged_in_cookies', []);

		foreach($option_navigation_logged_in_cookies as $key => $value)
		{
			$arr_data[$value] = $value;
		}

		return $arr_data;
	}

	function setting_navigation_logged_in_cookies_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, []);

		echo show_select(array('data' => $this->get_logged_in_cookies_for_select(), 'name' => $setting_key."[]", 'value' => $option));
	}

	function filter_cache_logged_in_cookies($arr_cookies)
	{
		$setting_navigation_logged_in_cookies = get_option_or_default('setting_navigation_logged_in_cookies', []);

		foreach($setting_navigation_logged_in_cookies as $key => $value)
		{
			$arr_cookies[] = $value;
		}

		return $arr_cookies;
	}
}