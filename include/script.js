jQuery(function($)
{
	/* Breakpoint */
	function set_breakpoint()
	{
		var dom_obj = $("body"),
			value = window.getComputedStyle(document.querySelector("body"), ':before').getPropertyValue('content').replace(/\"/g, '');

		dom_obj.removeClass('is_mobile is_tablet is_desktop');

		if(typeof value !== 'undefined' && value != '')
		{
			dom_obj.addClass(value);
		}
	};

	set_breakpoint();

	$(window).resize(function()
	{
		set_breakpoint();
	});

	/* Menu */
	/*$(".widget.navigation .wp-block-navigation-item.left:first-of-type").each(function()
	{
		$(this).parents(".widget.navigation").parent(".wp-block-group").addClass('has_item_left');
	});*/

	$(".widget.navigation .wp-block-navigation-item.item_gap").each(function()
	{
		$(this).parents(".widget.navigation").parent(".wp-block-group").addClass('has_item_gap');
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
});