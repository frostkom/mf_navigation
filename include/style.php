<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_navigation/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

$setting_navigation_background_color = get_option_or_default('setting_navigation_background_color', "#ffffff");
$setting_navigation_text_color = get_option_or_default('setting_navigation_text_color', "#000000");
$setting_navigation_container_padding_mobile = get_option_or_default('setting_navigation_container_padding_mobile', "6em 2em 2em");
$setting_navigation_item_border_margin_left = get_option_or_default('setting_navigation_item_border_margin_left', "1em");
$setting_navigation_item_border_margin_right = get_option_or_default('setting_navigation_item_border_margin_right', "1em");
$setting_navigation_item_border_radius = get_option('setting_navigation_item_border_radius', ".33em");
$setting_navigation_item_vertical_padding_left = get_option('setting_navigation_item_vertical_padding_left');
$setting_navigation_item_padding = get_option('setting_navigation_item_padding', ".6em 1em");
$setting_navigation_item_padding_vertical = get_option('setting_navigation_item_padding_vertical');
$setting_navigation_item_padding_mobile = get_option_or_default('setting_navigation_item_padding_mobile', ".3em .6em");
//$setting_navigation_dim_content = get_option_or_default('setting_navigation_dim_content', 'yes');

$setting_breakpoint_tablet = apply_filters('get_styles_content', '', 'max_width');

if($setting_breakpoint_tablet != '')
{
	preg_match('/^([0-9]*\.?[0-9]+)([a-zA-Z%]+)$/', $setting_breakpoint_tablet, $matches);

	$setting_breakpoint_tablet = $matches[1];
	$setting_breakpoint_suffix = $matches[2];

	$setting_breakpoint_mobile = ($setting_breakpoint_tablet * .775);
}

else
{
	$setting_breakpoint_tablet = get_option_or_default('setting_navigation_breakpoint_tablet', 1200);
	$setting_breakpoint_mobile = get_option_or_default('setting_navigation_breakpoint_mobile', 930);

	$setting_breakpoint_suffix = "px";
}

$transition = "transition: all .5s ease;";

echo "@media all
{
	body .menu_items_public, body .menu_items_logged_in
	{
		display: none;
	}

		body.not-logged-in .menu_items_public
		{
			display: flex;
		}

		body.logged-in .menu_items_logged_in
		{
			display: flex;
		}

	/* General */
	header .wp-block-image, header .wp-block-site-logo, header .wp-block-site-title, header .wp-block-site-tagline
	{
		position: relative;
		z-index: 10000;
	}

		header .wp-block-image img
		{
			border-radius: ".$setting_navigation_item_border_radius.";
			padding: 0;"
			.$transition
		."}

		header .wp-block-site-title a
		{
			color: ".$setting_navigation_text_color.";
		}

	.widget.navigation .toggle_hamburger
	{
		display: none;
		padding: .3em 1em;
	}

		.widget.navigation .wp-block-navigation
		{
			color: ".$setting_navigation_text_color.";
			z-index: 1000;
		}

		.widget.navigation .wp-block-navigation__responsive-container
		{
			display: block;
			position: relative;
		}

		.widget.navigation.is_vertical .wp-block-navigation__responsive-container, .widget.navigation.is_vertical .wp-block-navigation-item, .widget.navigation.is_vertical .wp-block-navigation-item > a
		{
			width: 100%;
		}

			.widget.navigation.aligncenter.is_horizontal .wp-block-navigation__container
			{
				flex-grow: unset;
				margin: 0 auto;
			}

			.widget.navigation.alignwide.is_horizontal .wp-block-navigation__container
			{
				flex-grow: unset;
				width: 100%;
			}

				.widget.navigation.alignwide.is_horizontal .wp-block-navigation .wp-block-navigation-item
				{
					flex-grow: 1;
				}

					.widget.navigation.alignwide.is_horizontal .wp-block-navigation > .wp-block-navigation-item > a
					{
						text-align: center;
						width: 100%;
					}

			.widget.navigation.is_centered.is_vertical .wp-block-navigation-item, .widget.navigation.is_centered.is_vertical .wp-block-navigation-item > a,
			.widget.navigation.aligncenter.is_vertical .wp-block-navigation-item, .widget.navigation.aligncenter.is_vertical .wp-block-navigation-item > a
			{
				text-align: center;
			}";

				if($setting_navigation_item_vertical_padding_left != '')
				{
					echo ".widget.navigation:not(.is_centered).is_vertical > .wp-block-navigation-item:not(.invert) > a
					{
						padding-left: ".$setting_navigation_item_vertical_padding_left.";
					}";
				}

			echo ".widget.navigation .wp-block-navigation-item > a
			{
				border-radius: ".$setting_navigation_item_border_radius.";
			}";

				if($setting_navigation_item_padding != '')
				{
					echo ".widget.navigation.is_horizontal .wp-block-navigation-item > a
					{
						padding: ".$setting_navigation_item_padding.";
					}";
				}

				if($setting_navigation_item_padding_vertical != '')
				{
					echo ".widget.navigation.is_vertical .wp-block-navigation-item > a
					{
						padding: ".$setting_navigation_item_padding_vertical.";
					}";
				}

				echo ".widget.navigation .wp-block-navigation-item > a img
				{
					display: inline-block;
					margin-right: .3em;
					margin-bottom: -.2em;
					max-width: 1.2em;
				}

				.widget.navigation:not(.is_centered).is_vertical > .wp-block-navigation-item.invert
				{
					margin-left: 0 !important;
				}

			.widget.navigation .wp-block-navigation .wp-block-navigation-item > img
			{
				display: block;
			}

				.widget.navigation .wp-block-navigation-item.current_menu_item > a
				{
					font-weight: bold;
				}

			.widget.navigation .has-child.current_menu_parent > a
			{
				font-weight: bold;
			}

			.widget.navigation .has-child > a > button.wp-block-navigation__submenu-icon
			{
				margin-left: .25em !important;
				transform: rotate(0deg) translateX(0);"
				.$transition
			."}

				.widget.navigation .has-child.current_menu_parent > a > button
				{
					transform: rotate(-".(360 + 90)."deg) translateY(-20%);
				}

				.widget.navigation .has-child:hover > a > button, .widget.navigation .has-child.is_open > a > button
				{
					transform: rotate(-".(360 + 180)."deg) translateY(-20%);
				}

			.widget.navigation .has-child .wp-block-navigation__submenu-container
			{
				background-color: ".$setting_navigation_background_color.";
				border-radius: .5em;
				overflow: hidden;
			}

				.widget.navigation .has-child .wp-block-navigation-item
				{
					border-radius: .5em;
					color: ".$setting_navigation_text_color.";
				}

	/* Invert / Border */";

	if($setting_navigation_item_border_margin_left != '' && $setting_navigation_item_border_margin_right != '')
	{
		echo ".widget.navigation .wp-block-navigation-item.border:not(:last-of-type), .widget.navigation .wp-block-navigation-item.invert:not(:last-of-type)
		{";

			if($setting_navigation_item_border_margin_left != '')
			{
				echo "margin-left: ".$setting_navigation_item_border_margin_left.";";
			}

			if($setting_navigation_item_border_margin_right != '')
			{
				echo "margin-right: ".$setting_navigation_item_border_margin_right.";";
			}

		echo "}";
	}

		echo ".widget.navigation .wp-block-navigation-item.border a
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
			}

	/* Separator */
	.widget.navigation.is_horizontal .wp-block-navigation-item.separator
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
			height: .05em;
			width: auto;
		}";

	/*if($setting_navigation_dim_content == 'yes')
	{
		echo ".wp-site-blocks::before
		{
			background: rgba(0, 0, 0, .5);
			content: '';
			opacity: 0;
			pointer-events: none;
			position: absolute;
			top: 0; left: 0; right: 0; bottom: 0;
			transition: opacity 1s ease;
			z-index: 1;
		}

			.wp-site-blocks:has(header:hover)::before
			{
				opacity: 1;
			}

		.wp-site-blocks header
		{
			position: relative;
			z-index: 2;
		}";
	}*/

echo "}";

if($setting_breakpoint_tablet > 0)
{
	echo "@media screen and (min-width: ".$setting_breakpoint_tablet.$setting_breakpoint_suffix.")
	{
		.widget.navigation .wp-block-navigation__responsive-container
		{
			display: block !important;
		}

		/* Add gap */
		.has_item_gap
		{
			flex-grow: 1;
		}

			.has_item_gap .widget.navigation
			{
				width: 100%;
			}

				.has_item_gap .widget.navigation .wp-block-navigation__container
				{
					flex-grow: 1;
				}

					.has_item_gap .widget.navigation .wp-block-navigation-item.item_gap
					{
						margin-left: auto;
					}
	}";
}

if($setting_breakpoint_mobile > 0 && $setting_breakpoint_tablet > $setting_breakpoint_mobile)
{
	echo "@media screen and (min-width: ".$setting_breakpoint_mobile.$setting_breakpoint_suffix.") and (max-width: ".($setting_breakpoint_tablet - 1).$setting_breakpoint_suffix.")
	{
		.widget.navigation .wp-block-navigation__responsive-container-open
		{
			display: none !important;
		}

			.widget.navigation .wp-block-navigation__responsive-container
			{
				display: block !important;
			}
	}";
}

if($setting_breakpoint_mobile > 0)
{
	echo "@media screen and (max-width: ".$setting_breakpoint_mobile.$setting_breakpoint_suffix.")
	{
		.menu_is_open header .wp-block-site-title a
		{"
			.$transition
		."}

			.menu_is_open header figure.wp-block-image img
			{
				background-color: ".$setting_navigation_background_color.";
				padding: .2em;
			}

			.menu_is_open header .wp-block-site-title a
			{
				color: ".$setting_navigation_background_color.";
			}

			.widget.navigation .toggle_hamburger
			{
				display: block;
			}

			.widget.navigation .toggle_hamburger
			{
				cursor: pointer;
				position: relative;
				width: 1.5em;
				z-index: 10000;
			}

				.widget.navigation .toggle_hamburger > div
				{
					background-color: ".$setting_navigation_text_color.";
					border-radius: 2em;
					display: block;
					height: .15em;
					margin: .4em 0;"
					.$transition
					."width: 1.6em;
				}

					.widget.navigation:not(.is_open) .toggle_hamburger > div:nth-child(2)
					{
						width: 1.1em;
					}

						.widget.navigation:not(.is_open):hover .toggle_hamburger > div:nth-child(2)
						{
							width: 1.4em;
						}

					.widget.navigation:not(.is_open) .toggle_hamburger > div:nth-child(3)
					{
						width: 1.4em;
					}

						.widget.navigation:not(.is_open):hover .toggle_hamburger > div:nth-child(3)
						{
							width: 1.6em;
						}

						.widget.navigation.is_open .toggle_hamburger > div
						{
							background-color: ".$setting_navigation_background_color.";
						}";

						// Original
						echo ".widget.navigation.is_open .toggle_hamburger > div:nth-child(1)
						{
							transform: rotate(45deg) translate(.4em, .4em);
							width: 1.6em;
						}

						.widget.navigation.is_open .toggle_hamburger > div:nth-child(2)
						{
							margin-left: .8em;
							width: 0;
						}

						.widget.navigation.is_open .toggle_hamburger > div:nth-child(3)
						{
							transform: rotate(-45deg) translate(.4em, -.4em);
							width: 1.6em;
						}";

						// Cross
						/*echo ".widget.navigation.is_open .toggle_hamburger > div:nth-child(1)
						{
							transform: rotate(-135deg) translate(-.4em, -.4em);
							width: 1.6em;
						}

						.widget.navigation.is_open .toggle_hamburger > div:nth-child(2)
						{
							margin-left: .8em;
							width: 0;
						}

						.widget.navigation.is_open .toggle_hamburger > div:nth-child(3)
						{
							transform: rotate(135deg) translate(-.4em, .4em);
							width: 1.6em;
						}";*/

						// Rotate
						/*echo ".widget.navigation.is_open .toggle_hamburger > div:nth-child(1)
						{
							transform: rotate(45deg) translate(.4em, .4em);
							width: 1.6em;
						}

						.widget.navigation.is_open .toggle_hamburger > div:nth-child(2)
						{
							margin-left: .8em;
							width: 0;
						}

						.widget.navigation.is_open .toggle_hamburger > div:nth-child(3)
						{
							transform: rotate(135deg) translate(-.4em, .4em);
							width: 1.6em;
						}";*/

						// Arrow
						/*echo ".widget.navigation.is_open .toggle_hamburger > div:nth-child(1)
						{
							transform: rotate(135deg) translate(.8em, -1.2em);
							width: .8em;
						}

						.widget.navigation.is_open .toggle_hamburger > div:nth-child(2)
						{
							transform: rotate(90deg) translate(.4em, .4em);
							width: 1.6em;
						}

						.widget.navigation.is_open .toggle_hamburger > div:nth-child(3)
						{
							transform: rotate(45deg) translate(0, .4em);
							width: .8em;
						}";*/

			echo ".widget.navigation.mobile_ready .wp-block-navigation
			{
				background: ".$setting_navigation_text_color.";
				color: ".$setting_navigation_background_color.";
				display: block;
				height: 100vh;
				left: 0;
				opacity: 0;
				position: absolute;
				top: 0;"
				.$transition
				."transform: translate(0%, -100%);
				width: 100%;
				z-index: 0;
			}

				.widget.navigation.is_open .wp-block-navigation
				{
					opacity: 1;
					transform: translate(0%, 0%);
					z-index: 1000;
				}

			.widget.navigation.mobile_ready .wp-block-navigation__responsive-container-open
			{
				border-radius: ".$setting_navigation_item_border_radius.";
				padding: ".$setting_navigation_item_padding_mobile." !important;
			}

				.widget.navigation.mobile_ready .wp-block-navigation__responsive-container
				{
					display: none;
					position: fixed;
				}

					.widget.navigation.mobile_ready .wp-block-navigation__container
					{
						box-sizing: border-box;
						display: block;
						padding: ".$setting_navigation_container_padding_mobile.";
						text-align: center;
						width: 100%;
					}

						body:not(.logged-in) .widget.navigation.mobile_ready .menu_items_logged_in
						{
							display: none;
						}

						body.logged-in .widget.navigation.mobile_ready .menu_items_public
						{
							display: none;
						}

						.widget.navigation.mobile_ready .wp-block-navigation .wp-block-navigation-item
						{
							display: block;
							float: none;
						}

							.widget.navigation.mobile_ready .wp-block-navigation .wp-block-navigation-item + .wp-block-navigation-item
							{
								margin-top: .2em;
							}

							.widget.navigation.mobile_ready .wp-block-navigation .wp-block-navigation-item a
							{
								border-radius: ".$setting_navigation_item_border_radius.";
								padding: ".$setting_navigation_item_padding_mobile." !important;
							}

								.widget.navigation.mobile_ready .wp-block-navigation .wp-block-navigation-item.invert a
								{
									background-color: ".$setting_navigation_background_color." !important;
									border: .1em solid ".$setting_navigation_background_color." !important;
									color: ".$setting_navigation_text_color." !important;
								}

								.widget.navigation.mobile_ready .wp-block-navigation .wp-block-navigation-item img
								{
									display: inline;
								}

						.widget.navigation.mobile_ready .has-child:hover > .wp-block-navigation__submenu-container, .widget.navigation.mobile_ready .has-child.is_open > .wp-block-navigation__submenu-container
						{
							background-color: transparent !important;
							border: none;
							display: block;
							height: auto;
							left: 0;
							opacity: 1;
							position: relative;
							visibility: visible;
							width: 100%;
						}

							.widget.navigation.mobile_ready .has-child:hover .wp-block-navigation-item, .widget.navigation.mobile_ready .has-child.is_open .wp-block-navigation-item
							{
								border-radius: none;
								background-color: ".$setting_navigation_text_color." !important;
								color: ".$setting_navigation_background_color." !important;
							}

								.widget.navigation.mobile_ready .has-child:hover .wp-block-navigation-item > a, .widget.navigation.mobile_ready .has-child.is_open .wp-block-navigation-item > a
								{
									display: block;
								}
	}";
}