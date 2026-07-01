!function(e){var c=e(document.body);c.on("click","tbody > tr > .check-column :checkbox",function(c){if("undefined"==c.shiftKey)return!0;if(c.shiftKey){if(!b)return!0;s=e(b).closest("form").find(":checkbox").filter(":visible:enabled"),a=s.index(b),r=s.index(this),l=e(this).prop("checked"),0<a&&0<r&&a!=r&&(a<r?s.slice(a,r):s.slice(r,a)).prop("checked",function(){return!!e(this).closest("tr").is(":visible")&&l})}var i=e(b=this).closest("tbody").find(":checkbox").filter(":visible:enabled").not(":checked");return e(this).closest("table").children("thead, tfoot").find(":checkbox").prop("checked",function(){return 0===i.length}),!0}),c.on("click.wp-toggle-checkboxes","thead .check-column :checkbox, tfoot .check-column :checkbox",function(c){var i=e(this),t=i.closest("table"),n=i.prop("checked"),o=c.shiftKey||i.data("wp-toggle");t.children("tbody").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked",function(){return!e(this).is(":hidden,:disabled")&&(o?!e(this).prop("checked"):!!n)}),t.children("thead,  tfoot").filter(":visible").children().children(".check-column").find(":checkbox").prop("checked",function(){return!o&&!!n})})}(jQuery);
    var $body = jQuery( document.body );
	/**
	 * Collapses the admin menu.
	 *
	 * @return {void}
	 */
     jQuery( '#collapse-button' ).on( 'click.collapse-menu', function() {
		var viewportWidth = getViewportWidth() || 961;

		// Reset any compensation for submenus near the bottom of the screen.
		jQuery('#adminmenu div.wp-submenu').css('margin-top', '');

		if ( viewportWidth < 960 ) {
			if ( $body.hasClass('auto-fold') ) {
				$body.removeClass('auto-fold').removeClass('folded');
				setUserSetting('unfold', 1);
				setUserSetting('mfold', 'o');
				menuState = 'open';
			} else {
				$body.addClass('auto-fold');
				setUserSetting('unfold', 0);
				menuState = 'folded';
			}
		} else {
			if ( $body.hasClass('folded') ) {
				$body.removeClass('folded');
				setUserSetting('mfold', 'o');
				menuState = 'open';
			} else {
				$body.addClass('folded');
				setUserSetting('mfold', 'f');
				menuState = 'folded';
			}
		}

		$document.trigger( 'wp-collapse-menu', { state: menuState } );
	});

    
	/**
	 * Get the viewport width.
	 *
	 * @since 4.7.0
	 *
	 * @return {number|boolean} The current viewport width or false if the
	 *                          browser doesn't support innerWidth (IE < 9).
	 */
	function getViewportWidth() {
		var viewportWidth = false;

		if ( window.innerWidth ) {
			// On phones, window.innerWidth is affected by zooming.
			viewportWidth = Math.max( window.innerWidth, document.documentElement.clientWidth );
		}

		return viewportWidth;
	}