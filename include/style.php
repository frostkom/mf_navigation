<?php

if(!defined('ABSPATH'))
{
	header("Content-Type: text/css; charset=utf-8");

	$folder = str_replace("/wp-content/plugins/mf_navigation/include", "/", dirname(__FILE__));

	require_once($folder."wp-load.php");
}

$obj_navigation = new mf_navigation();

$obj_navigation->get_all_settings();

$transition = "transition: all .5s ease;";

echo "@media all
{
	body:before
	{
		content: 'is_desktop';
		display: none;
	}

	/* General */
	header .wp-block-image, header .wp-block-site-logo, header .wp-block-site-title, header .wp-block-site-tagline
	{
		position: relative;
		z-index: 10000;
	}

		header .wp-block-image img
		{
			border-radius: ".$obj_navigation->arr_settings['item_border_radius'].";
			padding: 0;"
			.$transition
		."}

		header .wp-block-site-title a
		{
			color: ".$obj_navigation->arr_settings['text_color'].";
		}

	.widget.navigation .toggle_icon
	{
		display: none;
		padding: .3rem 1rem;
	}

		.widget.navigation .wp-block-navigation
		{
			color: ".$obj_navigation->arr_settings['text_color'].";
			z-index: 1000;
		}

		.widget.navigation .wp-block-navigation__responsive-container
		{
			display: block;
		}

			.widget.navigation .wp-block-navigation-item a
			{
				border-radius: ".$obj_navigation->arr_settings['item_border_radius'].";
				/*display: inline-block;*/ /* Make this a setting? */
				padding: ".$obj_navigation->arr_settings['item_padding'].";
			}

			.widget.navigation .wp-block-navigation .wp-block-navigation-item img
			{
				display: block;
			}

			.widget.navigation .has-child > a > button.wp-block-navigation__submenu-icon
			{
				margin-left: .25em !important;
				transform: rotate(0deg) translateX(0);"
				.$transition
			."}

				.widget.navigation .has-child:hover > a > button, .widget.navigation .has-child.is_open > a > button
				{
					transform: rotate(-".(360 + 180)."deg) translateY(-20%);
				}

			.widget.navigation .has-child .wp-block-navigation__submenu-container
			{
				background-color: ".$obj_navigation->arr_settings['background_color'].";
				border-radius: .5em;
				overflow: hidden;
			}

				.widget.navigation .has-child .wp-block-navigation-item
				{
					border-radius: .5em;
					color: ".$obj_navigation->arr_settings['text_color'].";
				}

	/* Invert / Border */
	.widget.navigation .wp-block-navigation-item.border:not(:last-of-type), .widget.navigation .wp-block-navigation-item.invert:not(:last-of-type)
	{
		margin-left: ".$obj_navigation->arr_settings['item_border_margin_left'].";
		margin-right: ".$obj_navigation->arr_settings['item_border_margin_right'].";
	}

		.widget.navigation .wp-block-navigation-item.border a
		{
			border: .1rem solid ".$obj_navigation->arr_settings['text_color'].";
		}

		.widget.navigation .wp-block-navigation-item.invert
		{
			color: ".$obj_navigation->arr_settings['background_color'].";
		}

			.widget.navigation .wp-block-navigation-item.invert a
			{
				background-color: ".$obj_navigation->arr_settings['text_color']." !important;
				border: .1rem solid ".$obj_navigation->arr_settings['text_color']." !important;
			}
}";

if($obj_navigation->arr_settings['breakpoint_tablet'] > 0)
{
	echo "@media screen and (min-width: ".$obj_navigation->arr_settings['breakpoint_tablet']."px)
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

if($obj_navigation->arr_settings['breakpoint_mobile'] > 0 && $obj_navigation->arr_settings['breakpoint_tablet'] > $obj_navigation->arr_settings['breakpoint_mobile'])
{
	echo "@media screen and (min-width: ".$obj_navigation->arr_settings['breakpoint_mobile']."px) and (max-width: ".($obj_navigation->arr_settings['breakpoint_tablet'] - 1)."px)
	{
		body:before
		{
			content: 'is_tablet';
		}

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

if($obj_navigation->arr_settings['breakpoint_mobile'] > 0)
{
	echo "@media screen and (max-width: ".($obj_navigation->arr_settings['breakpoint_mobile'] - 1)."px)
	{
		body:before
		{
			content: 'is_mobile';
		}

		.menu_is_open header .wp-block-site-title a
		{"
			.$transition
		."}

		.menu_is_open
		{
			overflow: hidden;
		}

			.menu_is_open header figure.wp-block-image img
			{
				background-color: ".$obj_navigation->arr_settings['background_color'].";
				padding: .2rem;
			}

			.menu_is_open header .wp-block-site-title a
			{
				color: ".$obj_navigation->arr_settings['background_color'].";
			}

			.widget.navigation .toggle_icon
			{
				display: block;
			}

			.widget.navigation .toggle_icon.toggle_hamburger
			{
				cursor: pointer;
				position: relative;
				width: 1.5rem;
				z-index: 10000;
			}

				.widget.navigation .toggle_line
				{
					background-color: ".$obj_navigation->arr_settings['text_color'].";
					display: block;
					height: .2rem;
					margin: .3rem 0;"
					.$transition
					."width: 1.5rem;
				}

					.widget.navigation.is_open .toggle_line
					{
						background-color: ".$obj_navigation->arr_settings['background_color'].";
					}

						.widget.navigation.is_open .toggle_icon.toggle_hamburger .toggle_line:nth-child(1)
						{
							transform: rotate(45deg) translate(.3rem, .4rem);
						}

						.widget.navigation.is_open .toggle_icon.toggle_hamburger .toggle_line:nth-child(2)
						{
							opacity: 0; /* Hide the middle line */
						}

						.widget.navigation.is_open .toggle_icon.toggle_hamburger .toggle_line:nth-child(3)
						{
							transform: rotate(-45deg) translate(.3rem, -.4rem);
						}

			.widget.navigation.mobile_ready .wp-block-navigation
			{
				background: ".$obj_navigation->arr_settings['text_color'].";
				color: ".$obj_navigation->arr_settings['background_color'].";
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

				.widget.navigation.is_open .wp-block-navigation
				{
					opacity: 1;
					transform: translate(0%, 0%);
				}

			.widget.navigation.mobile_ready .wp-block-navigation__responsive-container-open
			{
				border-radius: ".$obj_navigation->arr_settings['item_border_radius'].";
				padding: ".$obj_navigation->arr_settings['item_padding_mobile']." !important;
			}

				.widget.navigation.mobile_ready .wp-block-navigation__responsive-container
				{
					display: none;
				}

					.widget.navigation .toggle_icon.fa-times
					{
						display: block;
						padding: 1.5rem;
						position: absolute;
						top: 0;
						right: 0;
					}

					.widget.navigation.mobile_ready .wp-block-navigation__container
					{
						display: block;
						padding: ".$obj_navigation->arr_settings['container_padding_mobile'].";
						text-align: center;
						width: 100%;
					}

						.widget.navigation.mobile_ready .wp-block-navigation .wp-block-navigation-item
						{
							display: block;
							float: none;
						}

							.widget.navigation.mobile_ready .wp-block-navigation .wp-block-navigation-item + .wp-block-navigation-item
							{
								margin-top: .2rem;
							}

							.widget.navigation.mobile_ready .wp-block-navigation .wp-block-navigation-item a
							{
								border-radius: ".$obj_navigation->arr_settings['item_border_radius'].";
								padding: ".$obj_navigation->arr_settings['item_padding_mobile']." !important;
							}

								.widget.navigation.mobile_ready .wp-block-navigation .wp-block-navigation-item.invert a
								{
									background-color: ".$obj_navigation->arr_settings['background_color']." !important;
									border: .1rem solid ".$obj_navigation->arr_settings['background_color']." !important;
									color: ".$obj_navigation->arr_settings['text_color']." !important;
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
								background-color: ".$obj_navigation->arr_settings['text_color']." !important;
								color: ".$obj_navigation->arr_settings['background_color']." !important;
							}

								.widget.navigation.mobile_ready .has-child:hover .wp-block-navigation-item > a, .widget.navigation.mobile_ready .has-child.is_open .wp-block-navigation-item > a
								{
									display: block;
								}
	}";
}

/*echo "@media print
{
	body:before
	{
		content: 'is_print';
	}
}";*/