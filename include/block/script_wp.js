(function()
{
	var __ = wp.i18n.__,
		el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl;

	registerBlockType('mf/navigation',
	{
		title: __("Navigation+", 'lang_navigation'),
		description: __("Display a Navigation+", 'lang_navigation'),
		icon: 'menu',
		category: 'layout',
		'attributes':
		{
			'align':
			{
				'type': 'string',
				'default': ''
			},
			'navigation_id':
			{
                'type': 'string',
                'default': ''
            }
		},
		'supports':
		{
			'html': false,
			'multiple': true,
			'align': true,
			'spacing':
			{
				'margin': true,
				'padding': true
			},
			'color':
			{
				'background': true,
				'gradients': false,
				'text': true
			},
			'defaultStylePicker': true,
			'typography':
			{
				'fontSize': true,
				'lineHeight': true
			},
			"__experimentalBorder":
			{
				"radius": true
			}
		},
		edit: function(props)
		{
			var arr_out = [];

			/* Select */
			/* ################### */
			var arr_options = [];

			jQuery.each(script_navigation_block_wp.arr_navigation, function(index, value)
			{
				if(index == "")
				{
					index = 0;
				}

				arr_options.push({label: value, value: index});
			});

			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					SelectControl,
					{
						label: __("Menu", 'lang_navigation'),
						value: props.attributes.navigation_id,
						options: arr_options,
						onChange: function(value)
						{
							props.setAttributes({navigation_id: value});
						}
					}
				)
			));
			/* ################### */

			return arr_out;
		},
		save: function()
		{
			return null;
		}
	});
})();