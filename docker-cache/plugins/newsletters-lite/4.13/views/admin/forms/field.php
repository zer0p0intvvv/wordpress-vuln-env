<div id="newsletters_forms_field_<?php echo $field -> id; ?>" class="postbox ">	
	<div class="postbox-header" >
		<div class="newsletters_delete_handle"><a href="" onclick="if (confirm('<?php echo esc_js(__('Are you sure you want to delete this field?', 'wp-mailinglist')); ?>')) { newsletters_forms_field_delete('<?php echo $field -> id; ?>'); } return false;"><i class="fa fa-trash fa-fw"></i></a></div>
		<div class="newsletters_edit_handle"><a href="" onclick="jQuery(this).closest('div.postbox').toggleClass('closed'); return false;"><i class="fa fa-pencil fa-fw"></i></a></div>
		<h2 class="hndle ui-sortable-handle" onclick="jQuery(this).parent().toggleClass('closed');"><i class="fa fa-bars fa-fw"></i> <span><?php echo __($field -> title) . ' <span class="newsletters_handle_more">' . $Html -> field_type($field -> type, $field -> slug) . '</span>' . ((!empty($field -> required) && $field -> required == "Y") ? ' <span class="newsletters_error"><i class="fa fa-asterisk fa-sm"></i></span>' : ''); ?></span></h2>
		<div class="handle-actions hide-if-no-js" style="    display: inline-flex;">
			<button type="button" class="handle-order-higher" aria-disabled="false" aria-describedby="newsletters_forms_field_<?php echo $field -> id; ?>-handle-order-higher-description">
				<span class="screen-reader-text"><?php echo esc_html_e('Move up', 'wp-mailinglist'); ?></span>
				<span class="order-higher-indicator" aria-hidden="true"></span>
			</button>
			<span class="hidden" id="newsletters_forms_field_<?php echo $field -> id; ?>-handle-order-higher-description"><?php echo esc_html_e('Move', 'wp-mailinglist'); ?> 
				<i class="fa fa-bars fa-fw"></i> <?php echo esc_html_e('Mailing List', 'wp-mailinglist'); ?>  <span class="newsletters_handle_more"><?php echo esc_html_e('Mailing List', 'wp-mailinglist'); ?></span> <span class="newsletters_error"><i class="fa fa-asterisk fa-sm"></i></span> <?php echo esc_html_e('box up', 'wp-mailinglist'); ?>
			</span>
			<button type="button" class="handle-order-lower" aria-disabled="false" aria-describedby="newsletters_forms_field_<?php echo $field -> id; ?>-handle-order-lower-description">
				<span class="screen-reader-text"><?php echo esc_html_e('Move Down', 'wp-mailinglist'); ?></span>
				<span class="order-lower-indicator" aria-hidden="true"></span>
			</button>
			<span class="hidden" id="newsletters_forms_field_<?php echo $field -> id; ?>-handle-order-lower-description"><?php echo esc_html_e('Move', 'wp-mailinglist'); ?> <i class="fa fa-bars fa-fw"></i> <?php echo esc_html_e('Mailing List', 'wp-mailinglist'); ?> <span class="newsletters_handle_more"><?php echo esc_html_e('Mailing List', 'wp-mailinglist'); ?></span> 
				<span class="newsletters_error"><i class="fa fa-asterisk fa-sm"></i></span> <?php echo esc_html_e('box down', 'wp-mailinglist'); ?>
			</span>
			<button type="button" class="handlediv" aria-expanded="false">
				<span class="screen-reader-text"><?php echo esc_html_e('Toggle panel:', 'wp-mailinglist'); ?>
					<i class="fa fa-bars fa-fw"></i> <?php echo esc_html_e('Mailing List', 'wp-mailinglist'); ?>
					<span class="newsletters_handle_more"><?php echo esc_html_e('Mailing List', 'wp-mailinglist'); ?></span> 
					<span class="newsletters_error">
						<i class="fa fa-asterisk fa-sm"></i>
					</span>
				</span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
		</div>
	</div>
	<div class="inside">	
		<?php echo wp_unslash($content); ?>
	</div>

</div>
