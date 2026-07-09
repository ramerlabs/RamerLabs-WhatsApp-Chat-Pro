( function ( blocks, blockEditor, components, i18n, element ) {
	var el = element.createElement;
	var InspectorControls = blockEditor.InspectorControls;
	var PanelBody = components.PanelBody;
	var TextControl = components.TextControl;
	var SelectControl = components.SelectControl;
	var ToggleControl = components.ToggleControl;

	blocks.registerBlockType( 'ramerlabs/whatsapp-chat-button', {
		edit: function ( props ) {
			var attributes = props.attributes;
			var setAttributes = props.setAttributes;

			return el(
				'div',
				{ className: 'rlwc-block-editor' },
				el( InspectorControls, {},
					el( PanelBody, { title: 'WhatsApp Chat', initialOpen: true },
						el( TextControl, {
							label: 'Button text',
							value: attributes.buttonText,
							onChange: function ( value ) { setAttributes( { buttonText: value } ); },
							help: 'Leave empty to use global widget text.'
						} ),
						el( SelectControl, {
							label: 'Department',
							value: attributes.department,
							options: [
								{ label: 'Auto (page routing)', value: '' },
								{ label: 'Sales', value: 'sales' },
								{ label: 'Support', value: 'support' },
								{ label: 'Billing', value: 'billing' },
								{ label: 'General', value: 'general' }
							],
							onChange: function ( value ) { setAttributes( { department: value } ); }
						} ),
						el( SelectControl, {
							label: 'Style',
							value: attributes.style,
							options: [
								{ label: 'Button', value: 'button' },
								{ label: 'Link', value: 'link' }
							],
							onChange: function ( value ) { setAttributes( { style: value } ); }
						} ),
						el( ToggleControl, {
							label: 'Show WhatsApp icon',
							checked: attributes.showIcon,
							onChange: function ( value ) { setAttributes( { showIcon: value } ); }
						} )
					)
				),
				el( 'div', { className: 'rlwc-block-preview' },
					el( 'span', { className: 'dashicons dashicons-whatsapp' } ),
					' ',
					attributes.buttonText || 'Chat on WhatsApp'
				)
			);
		}
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.i18n, window.wp.element );
