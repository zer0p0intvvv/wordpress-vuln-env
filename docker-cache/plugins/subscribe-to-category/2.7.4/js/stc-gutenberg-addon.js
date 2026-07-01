/* global oobject_name, wp */

( function( plugins, editPost, element, components, data, compose) {
    
	const el = element.createElement;
	const { Fragment } = element;
	const { registerPlugin } = plugins;
    var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel; 
	const { PanelBody, TextControl, TextareaControl, CheckboxControl } = components;
	const { withSelect, withDispatch } = data;
    const __ = wp.i18n.__;
    
	const MetaTextControl = compose.compose(
/*          Disabled to prevent users to changes these value 
            withDispatch( function( dispatch, props ) {
			return {
				setMetaValue: function( metaValue ) {
					dispatch( 'core/editor' ).editPost(
						{ meta: { [ props.metaKey ]: metaValue } }
					);
				}
			}
		} ), */
		withSelect( function( select, props ) {
			return {
				metaValue: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ props.metaKey ]
			};
		} ) )( function( props ) {
			return el( TextControl, {
				label: props.title,
				value: props.metaValue,
				onChange: function( content ) {
					props.setMetaValue( content );
				}
			});
		}
	);

	const MetaCheckboxControl = compose.compose(
		withDispatch( function( dispatch, props ) {
			return {
			    setMetaValue: function( metaValue ) {
				dispatch( 'core/editor' ).editPost(
				    { meta: { [ props.metaKey ]: metaValue } }
				);
			    }
			};
		} ),
		withSelect( function( select, props ) {
			return {
				metaValue: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ props.metaKey ]					
			};
		} ) )( function( props ) {
			return el( CheckboxControl, {
				label: props.title,
				value: props.metaValue,
				checked: props.metaValue,
				onChange: function( content ) {
                    if (props.metaKey === '_stc_notifier_prevent') {
                        if (wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ '_stc_notifier_request' ] === false) {
                            props.setMetaValue( content );
                            checked: props.metaValue;
                        }
					} 
                    if (props.metaKey === '_stc_notifier_request') {
                        if (wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ '_stc_notifier_prevent' ] === false) {
                            if (wp.data.select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ '_stc_notifier_status' ].localeCompare('outbox')!=0) {
                                props.setMetaValue( content );
                                checked: props.metaValue;
                            }
                        }
                    }
				}
			});
		}
	);

    registerPlugin( 'stc-gutenberg-addon-sidebar', {
        render: function() {
            return el( Fragment, {},
                el( PluginDocumentSettingPanel,
                    {
                        name:  'stc-gutenberg-addon-sidebar',
                        icon:  'images-alt2',
                        title: __('Mail Post to Subscribers', 'subscribe-to-category')
                    },
                    // Field 1
                    el( 'div',{ className: 'stc-gutenberg-addon-fields' },
                        el( TextControl,
                            {
                                label: __('Post state', 'subscribe-to-category'),
                                value: wp.data.select( 'core/editor' ).getCurrentPost().status
                            }
                        )
                    ),
                    // Field 2
                    el( 'div',{ className: 'stc-gutenberg-addon-fields' },
                        el( MetaTextControl,
                            {
                                metaKey: '_stc_notifier_status',
                                title : __('Mail status', 'subscribe-to-category')
                            }
                        )
                    ),
                    // Field 3
                    el( 'div',{ className: 'stc-gutenberg-addon-fields' },
                        el( MetaTextControl,
                            {
                                metaKey: '_stc_notifier_sent_time',
                                title : __('Send on date', 'subscribe-to-category')
                            }
                        )
                    ),
                    // Field 4
                    el( 'div',{ className: 'stc-gutenberg-addon-checkbox-update' },
                        el( MetaCheckboxControl,
                            {
                                metaKey: '_stc_notifier_request',
                                title : __('Mail this post again', 'subscribe-to-category')
                            }
                        )
                    ),
                    // Field 5
                    el( 'div',{ className: 'stc-gutenberg-addon-checkbox-prevent' },
                        el( MetaCheckboxControl,
                            {
                                metaKey: '_stc_notifier_prevent',
                                title : __('Never mail this post', 'subscribe-to-category')
                            }
                        )
                    )
                )
            );
        }
    } );
} )(
	window.wp.plugins,
	window.wp.editPost,
	window.wp.element,
	window.wp.components,
	window.wp.data,
	window.wp.compose,
        jQuery
);