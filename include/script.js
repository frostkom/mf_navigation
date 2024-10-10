jQuery(function($)
{
	/* Breakpoint */
	/* #################### */
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
	/* #################### */

	/* Menu */
	/* #################### */
	$(".widget.navigation").each(function()
	{
		var dom_obj = $(this);

		dom_obj.parent(".wp-block-group").addClass('is_navigation_parent');
	});

	$(document).on('click', ".widget.navigation .toggle_icon", function()
	{
		var dom_obj = $(this),
			parent_nav = dom_obj.parents(".widget.navigation");

		parent_nav.toggleClass('is_open');

		return false;
	});
	/* #################### */
})();