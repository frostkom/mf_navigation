<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_navigation/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

$setting_navigation_background_color = get_option('setting_navigation_background_color');
$setting_navigation_text_color = get_option('setting_navigation_text_color');
$setting_navigation_breakpoint_tablet = get_option('setting_navigation_breakpoint_tablet');
$setting_navigation_breakpoint_mobile = get_option('setting_navigation_breakpoint_mobile');

$nav_item_border_radius = ".33rem";
$nav_item_padding = ".6rem 1rem";
$nav_item_mobile_padding = ".3rem .6rem";

echo "@media all
{
	body:before
	{
		content: 'is_desktop';
		display: none;
	}

	/* Separate to left and right */
	/* ################# */
	.is_desktop .is_navigation_parent
	{
		flex-grow: 1;
	}
	
		.is_desktop .is_navigation_parent .widget.navigation
		{
			width: 100%;
		}

			.is_navigation_parent .widget.navigation .wp-block-navigation__container
			{
				display: block;
				width: 100%;
			}

				.is_navigation_parent .widget.navigation .wp-block-navigation-item.left
				{
					float: left;
				}

				.is_navigation_parent .widget.navigation .wp-block-navigation-item.right
				{
					float: right;
				}
	/* ################# */

	.widget.navigation .toggle_icon
	{
		display: none;
		padding: .3rem 1rem;
	}

		.widget.navigation .wp-block-navigation__responsive-container
		{
			display: block;
		}
		
			.widget.navigation .wp-block-navigation-item + .wp-block-navigation-item
			{
				margin-left: .5rem;
			}

				.widget.navigation .wp-block-navigation-item a
				{
					border-radius: ".$nav_item_border_radius.";
					/*display: inline-block;*/ /* Make this a setting? */
					padding: ".$nav_item_padding.";
				}

				.widget.navigation .wp-block-navigation-item.border a
				{
					border: .1rem solid ".$setting_navigation_text_color.";
				}

				.widget.navigation .wp-block-navigation-item.invert
				{
					color: ".$setting_navigation_background_color.";
				}

					.widget.navigation .wp-block-navigation-item.invert a
					{
						background-color: ".$setting_navigation_text_color." !important;
						border: .1rem solid ".$setting_navigation_text_color." !important;
					}

	.is_mobile .widget.navigation .fa.fa-bars
	{
		display: block;
	}

	.is_mobile .widget.navigation .wp-block-navigation
	{
		transition: transform .5s ease;
		transform: translate(0%, -100%);
	}
		
	.is_mobile .wp-block-navigation__responsive-container-open
	{
		display: block !important;
		border-radius: ".$nav_item_border_radius.";
		padding: ".$nav_item_mobile_padding." !important;
	}

	.is_mobile .wp-block-navigation__responsive-container
	{
		display: none !important;
	}
		
	.is_tablet .wp-block-navigation__responsive-container-open
	{
		display: none !important;
	}

	.is_tablet .wp-block-navigation__responsive-container
	{
		display: block !important;
	}

	.widget.navigation.is_open .toggle_icon.fa-bars
	{
		display: none;
	}
	
	.widget.navigation.is_open
	{
		left: 0;
		position: absolute;
		top: 0;
	}

		.widget.navigation.is_open .wp-block-navigation
		{
			background: ".$setting_navigation_text_color.";
			color: ".$setting_navigation_background_color.";
			display: block;
			height: 100vh;
			transform: translate(0%, 0%);
			width: 100vw;
			z-index: 1000;
		}

		.widget.navigation.is_open .wp-block-navigation__responsive-container
		{
			display: block !important;
		}

			.widget.navigation.is_open .toggle_icon.fa-times
			{
				display: block;
				position: absolute;
				top: .3rem;
				right: 0;
			}

			.widget.navigation.is_open .wp-block-navigation__container
			{
				display: block;
				padding: 2rem;
				text-align: center;
				width: 100%;
			}

				.widget.navigation.is_open .wp-block-navigation__responsive-container
				{
					display: block !important;
				}

					.widget.navigation.is_open .wp-block-navigation .wp-block-navigation-item
					{
						display: block;
						float: none;
					}

						.widget.navigation.is_open .wp-block-navigation .wp-block-navigation-item + .wp-block-navigation-item
						{
							margin-top: .2rem;
						}

						.widget.navigation.is_open .wp-block-navigation .wp-block-navigation-item a
						{
							border-radius: ".$nav_item_border_radius.";
							padding: ".$nav_item_mobile_padding." !important;
						}

							.widget.navigation.is_open .wp-block-navigation .wp-block-navigation-item.invert a
							{
								background-color: ".$setting_navigation_background_color." !important;
								border: .1rem solid ".$setting_navigation_background_color." !important;
								color: ".$setting_navigation_text_color." !important;
							}
}";

if($setting_navigation_breakpoint_mobile > 0)
{
	echo "@media screen and (max-width: ".($setting_navigation_breakpoint_mobile - 1)."px)
	{
		body:before
		{
			content: 'is_mobile';
		}
	}";
}

if($setting_navigation_breakpoint_mobile > 0 && $setting_navigation_breakpoint_tablet > $setting_navigation_breakpoint_mobile)
{
	echo "@media screen and (min-width: ".$setting_navigation_breakpoint_mobile."px) and (max-width: ".($setting_navigation_breakpoint_tablet - 1)."px)
	{
		body:before
		{
			content: 'is_tablet';
		}
	}";
}

echo "@media print
{
	body:before
	{
		content: 'is_print';
	}
}";