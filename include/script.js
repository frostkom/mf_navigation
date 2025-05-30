jQuery(function($)
{
	/* Menu */
	$(".widget.navigation .wp-block-navigation-item.item_gap").each(function()
	{
		var dom_obj = $(this).parents(".widget.navigation").parent(".wp-block-group");

		if(dom_obj.hasClass('has_item_gap') == false)
		{
			dom_obj.addClass('has_item_gap');
		}
	});

	function open_nav(parent_nav)
	{
		parent_nav.addClass('is_open').find(".wp-block-navigation__responsive-container").fadeIn();
		$("body").addClass('menu_is_open');
	}

	function close_nav(parent_nav)
	{
		parent_nav.removeClass('is_open').find(".wp-block-navigation__responsive-container").fadeOut();
		$("body").removeClass('menu_is_open');
	}

	$(document).on('click', ".widget.navigation .toggle_icon", function()
	{
		var dom_obj = $(this),
			parent_nav = dom_obj.parents(".widget.navigation"),
			is_open = parent_nav.hasClass('is_open');

		if(is_open)
		{
			close_nav(parent_nav);
		}

		else
		{
			$(".widget.navigation.mobile_ready.is_open").each(function()
			{
				close_nav($(this));
			});

			open_nav(parent_nav);
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