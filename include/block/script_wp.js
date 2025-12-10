(function()
{
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl,
		InspectorControls = wp.blockEditor.InspectorControls;

	registerBlockType('mf/navigation',
	{
		title: script_navigation_block_wp.block_title,
		description: script_navigation_block_wp.block_description,
		icon: 'menu',
		category: 'widgets',
		'attributes':
		{
			'align':
			{
				'type': 'string',
				'default': ''
			},
			'navigation_is_in_header':
			{
				'type': 'string',
				'default': ''
			},
			'navigation_id':
			{
				'type': 'string',
				'default': ''
			},
			'navigation_id_logged_in':
			{
				'type': 'string',
				'default': ''
			},
			'navigation_id_logged_in_cookie':
			{
				'type': 'string',
				'default': ''
			},
			'navigation_mobile_ready':
			{
				'type': 'string',
				'default': ''
			},
			'navigation_orientation':
			{
				'type': 'string',
				'default': ''
			},
			'navigation_search':
			{
				'type': 'string',
				'default': 'no'
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
								label: script_navigation_block_wp.navigation_is_in_header_label,
								value: props.attributes.navigation_is_in_header,
								options: convert_php_array_to_block_js(script_navigation_block_wp.yes_no_for_select, false),
								onChange: function(value)
								{
									props.setAttributes({navigation_is_in_header: value});
								}
							}
						),
						el(
							SelectControl,
							{
								label: script_navigation_block_wp.navigation_id_label,
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
								label: script_navigation_block_wp.navigation_id_logged_in_label,
								value: props.attributes.navigation_id_logged_in,
								options: convert_php_array_to_block_js(script_navigation_block_wp.arr_navigation),
								onChange: function(value)
								{
									props.setAttributes({navigation_id_logged_in: value});
								}
							}
						),
						el(
							TextControl,
							{
								label: script_navigation_block_wp.navigation_id_logged_in_cookie_label,
								type: 'text',
								value: props.attributes.navigation_id_logged_in_cookie,
								onChange: function(value)
								{
									props.setAttributes({navigation_id_logged_in_cookie: value});
								},
								placeholder: 'wp-settings-time'
							}
						),
						el(
							SelectControl,
							{
								label: script_navigation_block_wp.navigation_mobile_ready_label,
								value: props.attributes.navigation_mobile_ready,
								options: convert_php_array_to_block_js(script_navigation_block_wp.yes_no_for_select, false),
								onChange: function(value)
								{
									props.setAttributes({navigation_mobile_ready: value});
								}
							}
						),
						el(
							SelectControl,
							{
								label: script_navigation_block_wp.navigation_orientation_label,
								value: props.attributes.navigation_orientation,
								options: convert_php_array_to_block_js(script_navigation_block_wp.navigation_orientation_for_select, false),
								onChange: function(value)
								{
									props.setAttributes({navigation_orientation: value});
								}
							}
						),
						el(
							SelectControl,
							{
								label: script_navigation_block_wp.navigation_search_label,
								value: props.attributes.navigation_search,
								options: convert_php_array_to_block_js(script_navigation_block_wp.yes_no_for_select, false),
								onChange: function(value)
								{
									props.setAttributes({navigation_search: value});
								}
							}
						)
					),
					el(
						'strong',
						{className: props.className},
						script_navigation_block_wp.block_title
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