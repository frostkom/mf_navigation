<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_navigation/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

$setting_navigation_background_color = get_option('setting_navigation_background_color');
$setting_navigation_text_color = get_option('setting_navigation_text_color');
$setting_navigation_container_padding_mobile = get_option('setting_navigation_container_padding_mobile', "6rem 2rem 2rem");
$setting_navigation_item_border_margin_left = get_option_or_default('setting_navigation_item_border_margin_left', "1rem");
$setting_navigation_item_border_margin_right = get_option_or_default('setting_navigation_item_border_margin_right', "1rem");
$setting_navigation_item_border_radius = get_option_or_default('setting_navigation_item_border_radius', ".33rem");
$setting_navigation_item_padding = get_option_or_default('setting_navigation_item_padding', ".6rem 1rem");
$setting_navigation_item_padding_mobile = get_option_or_default('setting_navigation_item_padding_mobile', ".3rem .6rem");
$setting_navigation_breakpoint_tablet = get_option('setting_navigation_breakpoint_tablet');
$setting_navigation_breakpoint_mobile = get_option('setting_navigation_breakpoint_mobile');

$transition = "transition: all .5s ease;";

echo "@media all
{
	body:before
	{
		content: 'is_desktop';
		display: none;
	}

	/* General */
	header figure.wp-block-image, header .wp-block-site-title
	{
		position: relative;
		z-index: 10000;
	}

		header figure.wp-block-image img
		{
			border-radius: ".$setting_navigation_item_border_radius.";
			padding: 0;"
			.$transition
		."}

	.widget.navigation .toggle_icon
	{
		display: none;
		padding: .3rem 1rem;
	}

		.widget.navigation .wp-block-navigation
		{
			z-index: 1000;
		}

		.widget.navigation .wp-block-navigation__responsive-container
		{
			display: block;
		}

			.widget.navigation .wp-block-navigation-item a
			{
				border-radius: ".$setting_navigation_item_border_radius.";
				/*display: inline-block;*/ /* Make this a setting? */
				padding: ".$setting_navigation_item_padding.";
			}

				.widget.navigation .wp-block-navigation .wp-block-navigation-item img
				{
					display: block;
				}

	/* Invert/Border */
	.widget.navigation .wp-block-navigation-item.border, .widget.navigation .wp-block-navigation-item.invert
	{
		margin-left: ".$setting_navigation_item_border_margin_left.";
		margin-right: ".$setting_navigation_item_border_margin_right.";
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

	/* Separate to left and right */
	/*.is_desktop .has_item_left
	{
		flex-grow: 1;
	}

		.is_desktop .has_item_left .widget.navigation
		{
			width: 100%;
		}

			.is_desktop .has_item_left .widget.navigation .wp-block-navigation__container
			{
				display: block;
				width: 100%;
			}

				.is_desktop .has_item_left .widget.navigation .wp-block-navigation-item.left
				{
					float: left;
				}

				.is_desktop .has_item_left .widget.navigation .wp-block-navigation-item.right
				{
					float: right;
				}*/

	/* Add gap */
	.is_desktop .has_item_gap
	{
		flex-grow: 1;
	}

		.is_desktop .has_item_gap .widget.navigation
		{
			width: 100%;
		}

			.is_desktop .has_item_gap .widget.navigation .wp-block-navigation__container
			{
				flex-grow: 1;
			}

				.is_desktop .has_item_gap .widget.navigation .wp-block-navigation-item.item_gap
				{
					margin-left: auto;
				}

	/* Desktop */
	.is_desktop .widget.navigation .wp-block-navigation__responsive-container
	{
		display: block !important;
	}

	/* Tablet */
	.is_tablet .widget.navigation .wp-block-navigation__responsive-container-open
	{
		display: none !important;
	}

		.is_tablet .widget.navigation .wp-block-navigation__responsive-container
		{
			display: block !important;
		}

	/* Mobile */
	.is_mobile.menu_is_open header .wp-block-site-title a
	{"
		.$transition
	."}

	.is_mobile.menu_is_open
	{
		overflow: hidden;
	}

		.is_mobile.menu_is_open header figure.wp-block-image img
		{
			background-color: ".$setting_navigation_background_color.";
			padding: .2rem;
		}

		.is_mobile.menu_is_open header .wp-block-site-title a
		{
			color: ".$setting_navigation_background_color.";
		}

		.is_mobile .widget.navigation .toggle_icon
		{
			display: block;
		}

		.is_mobile .widget.navigation .toggle_icon.toggle_hamburger
		{
            cursor: pointer;
			position: relative;
            width: 1.5rem;
			z-index: 10000;
        }

			.is_mobile .widget.navigation .toggle_line
			{
				background-color: ".$setting_navigation_text_color.";
				display: block;
				height: .2rem;
				margin: .3rem 0;"
				.$transition
				."width: 100%;
			}

		/*.is_mobile .widget.navigation .toggle_icon.fa-bars
		{
			opacity: 1;"
			.$transition
		."}

			.is_mobile .widget.navigation.is_open .toggle_icon.fa-bars
			{
				opacity: 0;
			}*/

			.is_mobile .widget.navigation.is_open .toggle_line
			{
				background-color: ".$setting_navigation_background_color.";
			}

				.is_mobile .widget.navigation.is_open .toggle_icon.toggle_hamburger .toggle_line:nth-child(1)
				{
					transform: rotate(45deg) translate(.3rem, .4rem);
				}

				.is_mobile .widget.navigation.is_open .toggle_icon.toggle_hamburger .toggle_line:nth-child(2)
				{
					opacity: 0; /* Hide the middle line */
				}

				.is_mobile .widget.navigation.is_open .toggle_icon.toggle_hamburger .toggle_line:nth-child(3)
				{
					transform: rotate(-45deg) translate(.3rem, -.4rem);
				}

		.is_mobile .widget.navigation .wp-block-navigation
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
			width: 100vw;
		}

			.is_mobile .widget.navigation.is_open .wp-block-navigation
			{
				opacity: 1;
				transform: translate(0%, 0%);
			}

		.is_mobile .widget.navigation .wp-block-navigation__responsive-container-open
		{
			/*display: block !important;*/
			border-radius: ".$setting_navigation_item_border_radius.";
			padding: ".$setting_navigation_item_padding_mobile." !important;
		}

			.is_mobile .widget.navigation .wp-block-navigation__responsive-container
			{
				display: none;
			}

				.is_mobile .widget.navigation .toggle_icon.fa-times
				{
					display: block;
					padding: 1.5rem;
					position: absolute;
					top: 0;
					right: 0;
				}

				.is_mobile .widget.navigation .wp-block-navigation__container
				{
					display: block;
					padding: ".$setting_navigation_container_padding_mobile.";
					text-align: center;
					width: 100%;
				}

					.is_mobile .widget.navigation .wp-block-navigation .wp-block-navigation-item
					{
						display: block;
						float: none;
					}

						.is_mobile .widget.navigation .wp-block-navigation .wp-block-navigation-item + .wp-block-navigation-item
						{
							margin-top: .2rem;
						}

						.is_mobile .widget.navigation .wp-block-navigation .wp-block-navigation-item a
						{
							border-radius: ".$setting_navigation_item_border_radius.";
							padding: ".$setting_navigation_item_padding_mobile." !important;
						}

							.is_mobile .widget.navigation .wp-block-navigation .wp-block-navigation-item.invert a
							{
								background-color: ".$setting_navigation_background_color." !important;
								border: .1rem solid ".$setting_navigation_background_color." !important;
								color: ".$setting_navigation_text_color." !important;
							}

							.is_mobile .widget.navigation .wp-block-navigation .wp-block-navigation-item img
							{
								display: inline;
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