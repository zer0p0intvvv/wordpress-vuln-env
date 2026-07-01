<?php
// phpcs:ignoreFile

if (!class_exists('wpmlFormHelper')) {
class wpmlFormHelper extends wpMailPlugin
{
	var $name = 'Form';
	
	function __construct() {
		return true;
	}
	
	function file($name = null, $options = array()) {
		global $Html;
		
		$defaults = array(
			'error' 		=> 	true,
			'class'			=>	"widefat",
			'width'			=>	"auto",
		);

		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start(); ?><input class="<?php echo esc_html( $class); ?>" style="width:<?php echo esc_html( $width); ?>;" type="file" name="<?php echo esc_html($name); ?>" id="<?php echo esc_html($Html -> field_id($name)); ?>" /><?php
		
		if ($error == true) {
			echo  $Html -> field_error($name);
		}
		
		$file = ob_get_clean();
		return $file;
	}
	
	function hidden($name = null, $options = array())
    {
		global $Html;
		
		$defaults = array();
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start(); ?><input type="hidden" name="<?php echo esc_html($name); ?>" value="<?php echo esc_attr(wp_unslash($Html -> field_value($name))); ?>" /><?php
		
		$hidden = ob_get_clean();
		return $hidden;
	}
	
	function text($name = null, $options = array())
    {
		global $Html;
		
		$defaults = array(
			'width' 		=> 	"100%", 
			'class'			=>	"widefat",
			'error' 		=> 	true, 
			'id' 			=> 	$Html -> field_id($name),
			'autocomplete'	=>	"on",
			'tabindex'		=>	false,
			'placeholder'	=>	false,
		);
		
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		if ($Html -> has_field_error($name)) {			
			$class .= ' newsletters_fielderror';
		}
		
		ob_start(); ?><input placeholder="<?php echo esc_attr(wp_unslash($placeholder)); ?>" autocomplete="<?php echo esc_html( $autocomplete); ?>" <?php echo (empty($tabindex)) ? '' : 'tabindex="' . esc_attr(wp_unslash($tabindex)) . '"'; ?> class="<?php echo esc_html( $class); ?>" style="width:<?php echo esc_html( $width); ?>;" id="<?php echo esc_html($id); ?>" type="text" name="<?php echo esc_html($name); ?>" value="<?php echo esc_attr(wp_unslash($Html -> field_value($name))); ?>" /><?php
		
		if ($error != false) {
			echo  $Html -> field_error($name);
		}
		
		$text = ob_get_clean();
		return $text;
	}
	
	function textarea($name = null, $options = array())
    {
		global $Html;
		
		$defaults = array('error' => true, 'width' => "100%", 'cols' => "100%", 'class' => "widefat", 'rows' => 5);
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start(); ?><textarea style="width:<?php echo esc_html( $width); ?>;" class="<?php echo esc_html( $class); ?>" name="<?php echo esc_html($name); ?>" id="<?php echo esc_html($Html -> field_id($name)); ?>" cols="<?php echo esc_html( $cols); ?>" rows="<?php echo esc_html( $rows); ?>"><?php echo wp_kses_post( wp_unslash($Html -> field_value($name))) ?></textarea><?php
		
		if ($error != false) {
			echo  $Html -> field_error($name);
		}
		
		$textarea = ob_get_clean();
		return $textarea;
	}
	
	public function radio( $name = null, $buttons = array(), $options = array() ) {
		global $Html;
	
		$defaults = array(
			'error'     => true,
			'onclick'   => 'return;',
			'separator' => '<br/>',
			'default'   => null,
		);
		$r = wp_parse_args( $options, $defaults );
		extract( $r, EXTR_SKIP );
	
		$current = $Html->field_value( $name );
	
		ob_start();
	
		if ( ! empty( $buttons ) ) {
			foreach ( $buttons as $value => $label ) {
	
				$is_checked = ( ( $current !== '' && $current == $value )
								|| ( $current === '' && $default !== null && $value == $default ) );
				?>
				<label>
					<input
						type="radio"
						id="<?php echo $Html->field_id( $name ); ?>_<?php echo esc_attr( $value ); ?>"
						name="<?php echo $name; ?>"
						value="<?php echo esc_attr( $value ); ?>"
						onclick="<?php echo $onclick; ?>"
						<?php echo $is_checked ? 'checked="checked"' : ''; ?>
					/>
					<?php echo esc_html__( $label, 'wp-mailinglist' ); ?>
				</label><?php echo $separator; ?>
				<?php
			}
		}
	
		if ( $error ) {
			echo $Html->field_error( $name );
		}
	
		return ob_get_clean();
	}
	
	function checkbox($name = null, $boxes = array(), $options = array()) {
		global $Html;
		
		$defaults = array('separator' => PHP_EOL);
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start(); ?>
		<?php $language =  $this -> language_default(); ?>

		
		<?php if (!empty($boxes)) : ?>
			<?php foreach ( $boxes as $value => $label ) :
				?>
				<label>
					<input type="checkbox"
						   id="<?php echo $Html->field_id( $name ); ?>check_<?php echo $value; ?>"
						   name="<?php echo $name; ?>"
						   value="<?php echo esc_attr( $value ); ?>"
						   <?php checked( in_array( $value, (array) $Html->field_value( $name ), true ) ); ?>
					/>
					<?php echo esc_html__( $label, 'wp-mailinglist' ); ?>
				</label><?php echo $separator; ?>
				<?php
			endforeach; ?>
		<?php endif; ?>
		
		<?php
		
		$checkbox = ob_get_clean();
		return $checkbox;
	}
	
	function select($name = null, $selects = array(), $options = array())
    {
		global $Html;
		
		$defaults = array('error' => true, 'class' => "widefat", 'width' => "auto", 'onchange' => 'return;');
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start(); ?>
		
		<select class="<?php echo esc_html( $class); ?>" style="width:<?php echo esc_html( $width); ?>;" onchange="<?php echo wp_kses_post($onclick); ?>" class="<?php echo esc_html( $class); ?>" id="<?php echo esc_html($Html -> field_id($name)); ?>" name="<?php echo esc_html($name); ?>">
			<option value=""><?php esc_html_e('- Select -', 'wp-mailinglist'); ?></option>
			<?php if (!empty($selects)) : ?>
				<?php foreach ($selects as $skey => $sval) : ?>
					<option <?php echo ($Html -> field_value($name) == $skey) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr(wp_unslash($skey)); ?>"><?php echo esc_html($this -> language_useordefault($sval)); ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		
		<?php
		
		if ($error != false) { 
			echo  $Html -> field_error($name);
		}
		
		$select = ob_get_clean();
		return $select;
	}
	
	function submit($name = null, $options = array())
    {
		global $Html;
		
		$defaults = array('class' => "button-primary");
		$r = wp_parse_args($options, $defaults);
		extract($r, EXTR_SKIP);
		
		ob_start(); ?>
		
		<button type="submit" name="<?php echo esc_html( $Html -> sanitize($name)); ?>" value="1" class="<?php echo esc_attr(wp_unslash($class)); ?>">
			<?php echo esc_attr(wp_unslash(esc_html($name))); ?>	
		</button>
		
		<?php
		
		$submit = ob_get_clean();
		return $submit;
	}
}
}

?>
