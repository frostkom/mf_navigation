jQuery(function($)
{
	/* Breakpoint */
	/*var breakpoints = 'is_mobile is_tablet is_desktop';

	function set_breakpoint()
	{
		var dom_obj = $("body"),
			value = window.getComputedStyle(document.querySelector("body"), ':before').getPropertyValue('content').replace(/\"/g, '');

		if(typeof value !== 'undefined' && value != '')
		{
			dom_obj.addClass(value).removeClass(breakpoints.replace(value, ''));
		}
	};

	set_breakpoint();

	$(window).resize(function()
	{
		set_breakpoint();
	});*/

	/* Menu */
	$(".widget.navigation .wp-block-navigation-item.item_gap").each(function()
	{
		var dom_obj = $(this).parents(".widget.navigation").parent(".wp-block-group");

		if(dom_obj.hasClass('has_item_gap') == false)
		{
			dom_obj.addClass('has_item_gap');
		}
	});

	$(document).on('click', ".widget.navigation .toggle_icon", function()
	{
		var dom_obj = $(this),
			parent_nav = dom_obj.parents(".widget.navigation"),
			is_open = parent_nav.hasClass('is_open');

		if(is_open)
		{
			parent_nav.removeClass('is_open');
			parent_nav.find(".wp-block-navigation__responsive-container").fadeOut();
			$("body").removeClass('menu_is_open');
		}

		else
		{
			parent_nav.addClass('is_open');
			parent_nav.find(".wp-block-navigation__responsive-container").fadeIn();
			$("body").addClass('menu_is_open');
		}

		return false;
	});

	$(document).on('click', ".widget.navigation .has-child > a", function()
	{
		var dom_obj = $(this).parent(".has-child");

		if(dom_obj.hasClass('is_open') == false)
		{
			$(".widget.navigation .has-child").removeClass('is_open');
			dom_obj.addClass('is_open').parents(".has-child").addClass('is_open');

			return false;
		}
	});
});