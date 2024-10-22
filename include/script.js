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
	$(".widget.navigation .wp-block-navigation-item.left:first-of-type").each(function()
	{
		var dom_obj = $(this);

		dom_obj.parents(".widget.navigation").parent(".wp-block-group").addClass('has_item_left');
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
		}

		else
		{
			parent_nav.addClass('is_open');
			parent_nav.find(".wp-block-navigation__responsive-container").fadeIn();
		}

		return false;
	});
});