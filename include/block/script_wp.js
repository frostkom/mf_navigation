(function()
{
	var __ = wp.i18n.__,
    el = wp.element.createElement,
    registerBlockType = wp.blocks.registerBlockType,
    SelectControl = wp.components.SelectControl,
    TextControl = wp.components.TextControl,
    InspectorControls = wp.blockEditor.InspectorControls;

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
            },
			'navigation_mobile_ready':
			{
                'type': 'string',
                'default': ''
            },
			'navigation_link_color':
			{
                'type': 'string',
                'default': ''
            },
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
			return el(
				'div',
				{className: 'wp_mf_block_container'},
				[
					el(
						InspectorControls,
						'div',
						el(
							SelectControl,
							{
								label: __("Menu", 'lang_navigation'),
								value: props.attributes.navigation_id,
								options: convert_php_array_to_block_js(script_navigation_block_wp.arr_navigation),
								onChange: function(value)
								{
									props.setAttributes({navigation_id: value});
								}
							}
						),
						el(
							SelectControl,
							{
								label: __("Mobile Ready", 'lang_navigation'),
								value: props.attributes.navigation_mobile_ready,
								options: convert_php_array_to_block_js(script_navigation_block_wp.yes_no_for_select, false),
								onChange: function(value)
								{
									props.setAttributes({navigation_mobile_ready: value});
								}
							}
						),
						el(
							TextControl,
							{
								label: __("Link Color", 'lang_navigation'),
								type: 'text',
								value: props.attributes.navigation_link_color,
								onChange: function(value)
								{
									props.setAttributes({navigation_link_color: value});
								}
							}
						)
					),
					el(
						'strong',
						{className: props.className},
						wp.blocks.getBlockType(props.name).title
					)
				]
			);
		},
		save: function()
		{
			return null;
		}
	});
})();