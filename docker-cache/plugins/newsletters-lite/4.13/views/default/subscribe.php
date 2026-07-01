<?php // phpcs:ignoreFile ?>
<!-- Subscribe Form -->
<div id="newsletters-<?php echo $form -> id; ?>-form-wrapper">

<?php do_action('newsletters_subscribe_before_form', $instance); ?>
<?php
$allowed_html = array(

    'address'    => array(),
    'a'          => array(
        'href'     => true,
        'rel'      => true,
        'rev'      => true,
        'name'     => true,
        'target'   => true,
        'download' => array(
            'valueless' => 'y',
        ),
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'abbr'       => array(),
    'acronym'    => array(),
    'area'       => array(
        'alt'    => true,
        'coords' => true,
        'href'   => true,
        'nohref' => true,
        'shape'  => true,
        'target' => true,
    ),
    'article'    => array(
        'align' => true,
    ),
    'aside'      => array(
        'align' => true,
    ),
    'audio'      => array(
        'autoplay' => true,
        'controls' => true,
        'loop'     => true,
        'muted'    => true,
        'preload'  => true,
        'src'      => true,
    ),
    'b'          => array(),
    'bdo'        => array(),
    'big'        => array(),
    'blockquote' => array(
        'cite' => true,
    ),
    'br'         => array(),
    'button'     => array(
        'disabled' => true,
        'name'     => true,
        'type'     => true,
        'value'    => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'caption'    => array(
        'align' => true,
    ),
    'cite'       => array(),
    'code'       => array(),
    'col'        => array(
        'align'   => true,
        'char'    => true,
        'charoff' => true,
        'span'    => true,
        'valign'  => true,
        'width'   => true,
    ),
    'colgroup'   => array(
        'align'   => true,
        'char'    => true,
        'charoff' => true,
        'span'    => true,
        'valign'  => true,
        'width'   => true,
    ),
    'del'        => array(
        'datetime' => true,
    ),
    'dd'         => array(),
    'dfn'        => array(),
    'details'    => array(
        'align' => true,
        'open'  => true,
    ),
    'div'        => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'dl'         => array(),
    'dt'         => array(),
    'em'         => array(),
    'fieldset'   => array(),
    'figure'     => array(
        'align' => true,
    ),
    'figcaption' => array(
        'align' => true,
    ),
    'font'       => array(
        'color' => true,
        'face'  => true,
        'size'  => true,
    ),
    'footer'     => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'head' => array(),

    'h1'         => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'h2'         => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'h3'         => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'h4'         => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'h5'         => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'h6'         => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'header'     => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'hgroup'     => array(
        'align' => true,
    ),
    'hr'         => array(
        'align'   => true,
        'noshade' => true,
        'size'    => true,
        'width'   => true,
    ),
    'i'          => array(),
    'img'        => array(
        'alt'      => true,
        'align'    => true,
        'border'   => true,
        'height'   => true,
        'hspace'   => true,
        'loading'  => true,
        'longdesc' => true,
        'vspace'   => true,
        'src'      => true,
        'usemap'   => true,
        'width'    => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'ins'        => array(
        'datetime' => true,
        'cite'     => true,
    ),
    'kbd'        => array(),
    'label'      => array(
        'for' => true,
        'id' => true,
        'class' => true,
    ),
    'legend'     => array(
        'align' => true,
    ),
    'li'         => array(
        'align' => true,
        'value' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'main'       => array(
        'align' => true,
    ),
    'map'        => array(
        'name' => true,
    ),
    'mark'       => array(),
    'menu'       => array(
        'type' => true,
    ),
    'nav'        => array(
        'align' => true,
    ),
    'object'     => array(
        'data' => array(
            'required'       => true,
            'value_callback' => '_wp_kses_allow_pdf_objects',
        ),
        'type' => array(
            'required' => true,
            'values'   => array( 'application/pdf' ),
        ),
    ),
    'style'      => array (
        'type' =>    true,
    ),
    'p'          => array(
        'align' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'pre'        => array(
        'width' => true,
    ),
    'q'          => array(
        'cite' => true,
    ),
    'rb'         => array(),
    'rp'         => array(),
    'rt'         => array(),
    'rtc'        => array(),
    'ruby'       => array(),
    's'          => array(),
    'samp'       => array(),
    'span'       => array(
        'align' => true,
    ),
    'section'    => array(
        'align' => true,
        'id' => true,
        'class' => true,
    ),
    'small'      => array(),
    'strike'     => array(),
    'strong'     => array(),
    'sub'        => array(),
    'summary'    => array(
        'align' => true,
    ),
    'sup'        => array(),
    'table'      => array(
        'align'       => true,
        'bgcolor'     => true,
        'border'      => true,
        'cellpadding' => true,
        'cellspacing' => true,
        'rules'       => true,
        'summary'     => true,
        'width'       => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'tbody'      => array(
        'align'   => true,
        'char'    => true,
        'charoff' => true,
        'valign'  => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'td'         => array(
        'abbr'    => true,
        'align'   => true,
        'axis'    => true,
        'bgcolor' => true,
        'char'    => true,
        'charoff' => true,
        'colspan' => true,
        'headers' => true,
        'height'  => true,
        'nowrap'  => true,
        'rowspan' => true,
        'scope'   => true,
        'valign'  => true,
        'width'   => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'textarea'   => array(
        'cols'     => true,
        'rows'     => true,
        'disabled' => true,
        'name'     => true,
        'readonly' => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'tfoot'      => array(
        'align'   => true,
        'char'    => true,
        'charoff' => true,
        'valign'  => true,
    ),
    'th'         => array(
        'abbr'    => true,
        'align'   => true,
        'axis'    => true,
        'bgcolor' => true,
        'char'    => true,
        'charoff' => true,
        'colspan' => true,
        'headers' => true,
        'height'  => true,
        'nowrap'  => true,
        'rowspan' => true,
        'scope'   => true,
        'valign'  => true,
        'width'   => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'thead'      => array(
        'align'   => true,
        'char'    => true,
        'charoff' => true,
        'valign'  => true,
    ),
    'title'      => array(),
    'tr'         => array(
        'align'   => true,
        'bgcolor' => true,
        'char'    => true,
        'charoff' => true,
        'valign'  => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    ),
    'track'      => array(
        'default' => true,
        'kind'    => true,
        'label'   => true,
        'src'     => true,
        'srclang' => true,
    ),
    'tt'         => array(),
    'u'          => array(),
    'ul'         => array(
        'type' => true,
    ),
    'ol'         => array(
        'start'    => true,
        'type'     => true,
        'reversed' => true,
    ),
    'var'        => array(),
    'video'      => array(
        'autoplay'    => true,
        'controls'    => true,
        'height'      => true,
        'loop'        => true,
        'muted'       => true,
        'playsinline' => true,
        'poster'      => true,
        'preload'     => true,
        'src'         => true,
        'width'       => true,
        'id' => true,
        'class' => true,
        'style' =>  true,
    )
);

$allowedProtocols = [ 'http', 'https' ];
?>
<?php if(!empty ($form -> styling_beforeform))
{
    ?>
        <div class="newsletters-form-styling_beforeform" ><?php
            $styling_beforeform = $form -> styling_beforeform;
            if ( $this->language_do() && !empty($styling_beforeform)) {

                $styling_beforeform = $this->language_useordefault(  $styling_beforeform  ) ;
            }

            echo wpautop(wp_kses(wp_unslash($styling_beforeform), $allowed_html, $allowedProtocols));  ?>
        </div>
    <?php
}

if(!empty($form-> buttontext)) {

    if (WPMAIL() -> language_do()) {
        $language_live =   WPMAIL() -> language_current();
        $form-> buttontext = WPMAIL() -> language_use($language_live, $form-> buttontext);
    }
}
  
  ?>
    <?php if (!empty($form)) { ?>
    <?php $form_styling = maybe_unserialize($form -> styling); ?>
    <!-- Subscribe Form Custom CSS -->
    <style type="text/css">
				#newsletters-<?php echo esc_html($form -> id); ?>-form-wrapper {
					<?php echo (!empty($form_styling['background'])) ? 'background-color: ' . esc_html($form_styling['background']) . ';' : ''; ?>
					<?php echo (!empty($form_styling['formpadding'])) ? 'padding: ' . esc_html($form_styling['formpadding']) . 'px;' : ''; ?>
					<?php echo (!empty($form_styling['formtextcolor'])) ? 'color: ' . esc_html($form_styling['formtextcolor']) . ';' : ''; ?>
                    <?php echo (!empty($form_styling['formborderradius'])) ? 'border-radius: ' . $form_styling['formborderradius'] . 'px;' : ''; ?>
				}
				
                #newsletters-<?php echo esc_html($form -> id); ?>-form .control-label, #newsletters-<?php echo esc_html($form -> id); ?>-form .control-label .text-muted {
                    <?php echo (!empty($form_styling['fieldlabelcolor'])) ? 'color: ' . $form_styling['fieldlabelcolor'] . ' !important; ' : ''; ?>
                }
				
				
				#newsletters-<?php echo esc_html($form -> id); ?>-form .has-error .control-label,
				#newsletters-<?php echo esc_html($form -> id); ?>-form .has-error .form-control,
				#newsletters-<?php echo esc_html($form -> id); ?>-form .has-error .alert,
				#newsletters-<?php echo esc_html($form -> id); ?>-form .has-error .help-block {
					<?php echo (!empty($form_styling['fielderrorcolor'])) ? 'color: ' . esc_html($form_styling['fielderrorcolor']) . ' !important; border-color: ' . esc_html($form_styling['fielderrorcolor']) . ';' : ''; ?>
				}
				
                #newsletters-<?php echo $form->id; ?>-form input::placeholder,
				#newsletters-<?php echo $form->id; ?>-form textarea::placeholder {
					<?php echo (!empty($form_styling['fieldplaceholdertextcolor'])) ? 'color: ' . $form_styling['fieldplaceholdertextcolor'] . ' !important;' : ''; ?>
					    opacity: 1; 

				}


				#newsletters-<?php echo $form->id; ?>-form input::-ms-input-placeholder,
				#newsletters-<?php echo $form->id; ?>-form textarea::-ms-input-placeholder,
				#newsletters-<?php echo $form->id; ?>-form input::-ms-input-placeholder,
				#newsletters-<?php echo $form->id; ?>-form textarea::-ms-input-placeholder,
				#newsletters-<?php echo $form->id; ?>-form input::-webkit-input-placeholder,
				#newsletters-<?php echo $form->id; ?>-form textarea::-webkit-input-placeholder,
				#newsletters-<?php echo $form->id; ?>-form input::-moz-placeholder,
				#newsletters-<?php echo $form->id; ?>-form textarea::-moz-placeholder {
					<?php echo (!empty($form_styling['fieldplaceholdertextcolor'])) ? 'color: ' . $form_styling['fieldplaceholdertextcolor'] . ' !important;' : ''; ?>
					    opacity: 1; 

				}


				#newsletters-<?php echo esc_html($form -> id); ?>-form .form-control {
					<?php echo (!empty($form_styling['fieldcolor'])) ? 'background-color: ' . esc_html($form_styling['fieldcolor']) . ';' : ''; ?>
					<?php echo (!empty($form_styling['fieldtextcolor'])) ? 'color: ' . esc_html($form_styling['fieldtextcolor']) . ';' : ''; ?>
					<?php echo (!empty($form_styling['fieldborderradius']) || $form_styling['fieldborderradius'] == 0) ? 'border-radius: ' . esc_html($form_styling['fieldborderradius']) . 'px;' : ''; ?>
				}

                #newsletters-<?php echo esc_html($form -> id); ?>-form input.form-control , #newsletters-<?php echo $form -> id; ?>-form textarea.form-control  {
                    <?php echo (!empty($form_styling['fieldbackgroundcolor'])) ? 'background-color: ' . esc_html($form_styling['fieldbackgroundcolor']) . ';' : ''; ?>
                    <?php echo (!empty($form_styling['fieldpadding'])) ? 'padding: ' . esc_html($form_styling['fieldpadding']) . '  !important;' : ''; ?>

				}

				
				#newsletters-<?php echo esc_html($form -> id); ?>-form .help-block {
					<?php echo (!empty($form_styling['fieldcaptioncolor'])) ? 'color: ' . esc_html($form_styling['fieldcaptioncolor']) . ';' : ''; ?>
				}
				
				#newsletters-<?php echo esc_html($form -> id); ?>-form .btn {
                    border-style: solid;
					<?php echo (!empty($form_styling['buttonbordersize']) || $form_styling['buttonbordersize'] == 0) ? 'border-width: ' . esc_html($form_styling['buttonbordersize']) . 'px;' : ''; ?>
					<?php echo (!empty($form_styling['buttonborderradius']) || $form_styling['buttonborderradius'] == 0) ? 'border-radius: ' . esc_html($form_styling['buttonborderradius']) . 'px;' : ''; ?>
                    <?php echo ( isset($form_styling['buttonpadding']) && !empty($form_styling['buttonpadding']) ) ? 'padding: ' . esc_html($form_styling['buttonpadding']) . ';' : ''; ?>
                    <?php echo ( isset($form_styling['buttonfullwidth']) && !empty($form_styling['buttonfullwidth']) &&  $form_styling['buttonfullwidth'] == 1) ? 'width: 100%;' : ''; ?>
                    
                    <?php echo (!empty($form_styling['buttontextcolor']) || $form_styling['buttontextcolor'] == 0) ? 'color: ' . $form_styling['buttontextcolor'] . ';' : ''; ?>

                    transition: 0.3s all;
				}


				#newsletters-<?php echo $form -> id; ?>-form .btn:hover {
					<?php echo (!empty($form_styling['buttontexthovercolor']) || $form_styling['buttontexthovercolor'] == 0) ? 'color: ' . $form_styling['buttontexthovercolor'] . ' !important;' : ''; ?>
                    transition: 0.3s all;

				}


				#newsletters-<?php echo $form -> id; ?>-form .btn:hover * {
					<?php echo (!empty($form_styling['buttontexthovercolor']) || $form_styling['buttontexthovercolor'] == 0) ? 'color: ' . $form_styling['buttontexthovercolor'] . ' !important;' : ''; ?>
             

				}



                #newsletters-form-<?php echo $form -> id; ?>-submit  {
                    <?php echo (!empty($form_styling['button_positions']) || $form_styling['button_positions'] == 0) ? 'text-align: ' . $form_styling['button_positions'] . ' !important;' : ''; ?>
                    transition: 0.3s all;

                }

				
				#newsletters-<?php echo esc_html($form -> id); ?>-form .btn-primary {
					<?php echo (!empty($form_styling['buttoncolor'])) ? 'background-color: ' . esc_html($form_styling['buttoncolor']) . ';' : ''; ?>
					<?php echo (!empty($form_styling['buttonbordercolor'])) ? 'border-color: ' . esc_html($form_styling['buttonbordercolor']) . ';' : ''; ?>
				}
				
				#newsletters-<?php echo esc_html($form -> id); ?>-form .btn-primary.active,
				#newsletters-<?php echo esc_html($form -> id); ?>-form .btn-primary.focus,
				#newsletters-<?php echo esc_html($form -> id); ?>-form .btn-primary:active,
				#newsletters-<?php echo esc_html($form -> id); ?>-form .btn-primary:focus,
				#newsletters-<?php echo esc_html($form -> id); ?>-form .btn-primary:hover {
					<?php echo (!empty($form_styling['buttonhovercolor'])) ? 'background-color: ' . esc_html($form_styling['buttonhovercolor']) . ';' : ''; ?>
					<?php echo (!empty($form_styling['buttonhoverbordercolor'])) ? 'border-color: ' . esc_html($form_styling['buttonhoverbordercolor']) . ';' : ''; ?>
				}
				
				#newsletters-<?php echo esc_html($form -> id); ?>-form i.newsletters-loading-icon {
					<?php echo (!empty($form_styling['loadingcolor'])) ? 'color: ' . esc_html($form_styling['loadingcolor']) . ' !important;' : ''; ?>
				}
				
				<?php if (!empty($form -> styling_customcss)) : ?>
					<?php echo wp_kses_post($this -> Subscribeform() -> customcss($form)); ?>
				<?php endif; ?>
				</style>
    <?php    } ?>

  <div class="newsletters newsletters-form-wrapper" >
  <?php $currentusersubscribed = $this -> get_option('currentusersubscribed'); ?>
	<?php if (!empty($currentusersubscribed)) : ?>
		<?php if (is_user_logged_in()) : ?>
			<?php $current_user = wp_get_current_user(); ?>
			<?php global $wpdb; ?>
			<?php if ($wpdb -> get_row("SELECT * FROM " . $wpdb -> prefix . $Subscriber -> table . " WHERE `email` = '" . $current_user -> user_email . "' AND `user_id` = '" . $current_user -> ID . "'")) : ?>
				<div class="alert alert-success">
					<i class="fa fa-check"></i> <?php echo esc_html(sprintf(__('You are already subscribed with email address %s. Go to the %s page to manage your subscription.', 'wp-mailinglist'), '<strong>' . esc_html($current_user -> user_email) . '</strong>', '<a href="' . esc_url_raw($this -> get_managementpost(true)) . '">' . __('manage subscriptions', 'wp-mailinglist') . '</a>')); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
		
	<?php if (!empty($form)) : ?>
		<?php $form_styling_unsafe = maybe_unserialize($form -> styling); ?>

        <?php
        $form_styling = [];

        foreach ($form_styling_unsafe as $k => $v) {
            $form_styling[$k] = esc_attr($v);
        }

        ?>

		<?php if (!empty($form -> form_fields)) : ?>
			<form class="<?php echo (empty($form_styling['formlayout']) || $form_styling['formlayout'] == "normal") ? ((!empty($form_styling['twocolumns'])) ? 'form-twocolumns' : 'form-onecolumn') : (($form_styling['formlayout'] == "inline") ? 'form-inline' : 'form-horizontal'); ?> newsletters-subscribe-form <?php echo (!empty($form -> ajax)) ? 'newsletters-subscribe-form-ajax' : 'newsletters-subscribe-form-regular'; ?>" action="<?php echo esc_url_raw($Html -> retainquery($this -> pre . 'method=optin')); ?>" method="post" id="newsletters-<?php echo esc_html($form -> id); ?>-form" enctype="multipart/form-data">
				<input type="hidden" name="form_id" value="<?php echo esc_attr(wp_unslash($form -> id)); ?>" />
				<input type="hidden" name="scroll" value="<?php echo esc_attr($form -> scroll); ?>" />
				
				<?php do_action('newsletters_subscribe_inside_form_top', $instance); ?>
				
				<?php foreach ($form -> form_fields as $field) : ?>
					<?php $this -> render_field($field -> field_id, true, $form -> id, false, false, false, false, $errors, $form -> id, $field); ?>
				<?php endforeach; ?>
                <?php
                // Only skip CAPTCHA rendering when actively editing in Beaver Builder
                $skip_captcha = (class_exists('FLBuilderModel') && method_exists('FLBuilderModel', 'is_builder_active') && FLBuilderModel::is_builder_active());
                if (!$skip_captcha) {
                    ?>
                    <?php if (!empty($form -> captcha)) : ?>
                        <?php if ($captcha_type = $this -> use_captcha()) : ?>		
                            <?php if ($captcha_type == "rsc") : ?>
                                <div class="form-group<?php echo (!empty($errors['captcha_code'])) ? ' has-error' : ''; ?> newsletters-fieldholder newsletters-captcha newsletters-captcha-wrapper">
                                    <?php 
                                    
                                    $captcha = new ReallySimpleCaptcha();
                                    $captcha -> bg = $Html -> hex2rgb($this -> get_option('captcha_bg')); 
                                    $captcha -> fg = $Html -> hex2rgb($this -> get_option('captcha_fg'));
                                    $captcha_size = $this -> get_option('captcha_size');
                                    $captcha -> img_size = array($captcha_size['w'], $captcha_size['h']);
                                    $captcha -> char_length = $this -> get_option('captcha_chars');
                                    $captcha -> font_size = $this -> get_option('captcha_font');
                                    $captcha_word = $captcha -> generate_random_word();
                                    $captcha_prefix = mt_rand();
                                    $captcha_filename = $captcha -> generate_image($captcha_prefix, $captcha_word);
                                    $captcha_file = plugins_url() . '/really-simple-captcha/tmp/' . $captcha_filename; 
                                    
                                    ?>
                                    <?php if (!empty($form_styling['fieldlabels'])) : ?>
                                        <label class="control-label" for="<?php echo esc_html($this -> pre); ?>captcha_code"><?php esc_html_e('Please fill in the code below:', 'wp-mailinglist'); ?></label>
                                    <?php endif; ?>
                                    <img class="newsletters-captcha-image" src="<?php echo esc_url_raw($captcha_file); ?>" alt="captcha" />
                                    <input size="<?php echo esc_attr(wp_unslash($captcha -> char_length)); ?>" <?php echo esc_attr($Html -> tabindex('newsletters-' . $form -> id)); ?> class="form-control <?php echo esc_html($this -> pre); ?>captchacode <?php echo esc_html($this -> pre); ?>text <?php echo (!empty($errors['captcha_code'])) ? 'newsletters_fielderror' : ''; ?>" type="text" name="captcha_code" id="<?php echo esc_html($this -> pre); ?>captcha_code" value="" />
                                    <input type="hidden" name="captcha_prefix" value="<?php echo esc_html( $captcha_prefix); ?>" />
                                    
                                    <?php if (!empty($errors['captcha_code']) && !empty($form_styling['fielderrors'])) : ?>
                                        <div id="newsletters-<?php echo esc_attr($number); ?>-captcha-error" class="newsletters-field-error alert alert-danger">
                                            <i class="fa fa-exclamation-triangle"></i> <?php echo esc_html($errors['captcha_code']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($captcha_type == "recaptcha") : ?>
                                <?php $recaptcha_type = $this -> get_option('recaptcha_type'); ?>
                                <div class="newsletters-captcha-wrapper form-group newsletters-fieldholder <?php echo ($recaptcha_type == "invisible") ? 'newsletters-fieldholder-hidden hidden' : ''; ?>">
                                    <div id="newsletters-<?php echo esc_html($form -> id); ?>-recaptcha-challenge" class="newsletters-recaptcha-challenge"></div>
                                </div>
                            <?php elseif ($captcha_type == "recaptcha3") : ?>
                                    <input type="hidden" name="g-recaptcha-response" id="newsletters-<?php echo $form->id; ?>-recaptcha-response" value="" />
                            <?php elseif ($captcha_type == "hcaptcha") : ?>
                                <?php if (function_exists('HCaptcha\Helpers\HCaptcha::form_display')) : ?>
                                    <div class="form-group newsletters-fieldholder newsletters-captcha-wrapper">
                                        <?php
                                        $args = [
                                            'action' => 'hcaptcha_wpmailinglist',
                                            'name'   => 'hcaptcha_wpmailinglist_nonce',
                                            'id'     => [
                                                'source'  => ['wp-mailinglist'],
                                                'form_id' => $form->id,
                                            ],
                                        ];
                                        HCaptcha\Helpers\HCaptcha::form_display($args);
                                        ?>
                                        <?php if (!empty($errors['captcha_code'])) : ?>
                                            <div id="newsletters-<?php echo $form->id; ?>-captcha-error" class="newsletters-field-error alert alert-danger">
                                                <i class="fa fa-exclamation-triangle"></i> <?php echo wp_unslash($errors['captcha_code']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php elseif ( $captcha_type == 'turnstile' ) : ?>
                                    <div
                                        id="newsletters-<?php echo $form->id; ?>-turnstile-challenge"
                                        class="newsletters-turnstile-challenge"
                                        data-sitekey="<?php echo esc_attr( $this->get_option( 'turnstile_sitekey' ) ); ?>"
                                        data-theme="light"
                                        data-size="normal"

                                    ></div>

                                    <input type="hidden"
                                        name="cf-turnstile-response"
                                        id="newsletters-<?php echo $form->id; ?>-turnstile-response"
                                        value=""/>

                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
				<?php } ?>
				<div class="newslettername-wrapper" style="display:none;">
			    	<input type="text" name="newslettername" value="" id="newsletters-<?php echo esc_html($form -> id); ?>newslettername" class="newslettername" />
			    </div>
				
				<?php if (empty($form_styling['formlayout']) || $form_styling['formlayout'] == "normal") : ?>
					<div class="clearfix"></div>
				<?php endif; ?>
				
				<div id="newsletters-form-<?php echo esc_html($form -> id); ?>-submit" class="form-group newsletters-fieldholder newsletters_submit">
					<span class="newsletters_buttonwrap">
						<button value="1" type="submit" name="subscribe" id="newsletters-<?php echo esc_html($form -> id); ?>-button" class="btn btn-primary button newsletters-button">
							<?php if (!empty($form_styling['loadingindicator'])) : ?>
								<span id="newsletters-<?php echo esc_html($form -> id); ?>-loading" class="newsletters-loading-wrapper" style="display:none;">
									<?php if (!empty($form_styling['loadingicon'])) : ?>
										<i class="<?php echo esc_html($Html -> get_loading_icon($form_styling['loadingicon'])); ?> newsletters-loading-icon"></i>
									<?php else : ?>
										<i class="fa fa-refresh fa-spin fa-fw newsletters-loading-icon"></i>
									<?php endif; ?>
								</span>
							<?php endif; ?>
							<span class="newsletters-button-label"><?php echo esc_attr(wp_unslash(esc_html($form -> buttontext))); ?></span>
						</button>
					</span>
				</div>
				<div class="row">				
					<div class="newsletters-progress" style="display:none;">
						<div class="progress">
							<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
								<span class="sr-only">0% Complete</span>
							</div>
						</div>
					</div>
				</div>
				
				
			</form>
			
			<?php do_action('newsletters_subscribe_after_form', $instance); ?>
            <?php if ($captcha_type = $this->use_captcha()) : ?>
                <?php if ($captcha_type == "recaptcha3") : ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            $('#newsletters-<?php echo $form->id; ?>-form').on('submit', function(e) {
                                e.preventDefault();
                                var $form = $(this);

                                grecaptcha.ready(function() {
                                    grecaptcha.execute('<?php echo esc_js($this->get_option('recaptcha_publickey')); ?>', {action: 'subscribe'}).then(function(token) {
                                        $('#newsletters-<?php echo $form->id; ?>-recaptcha-response').val(token);
                                        $form.off('submit').submit(); // Remove handler and submit form
                                    });
                                });
                            });
                        });
                    </script>
                <?php endif; ?>
            <?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
</div>
<?php if(!empty ($form -> styling_afterform))
{
    ?>
    <div class="newsletters-form-styling_afterform" >
    <?php
        $styling_afterform = $form -> styling_afterform;
        if ( $this->language_do() && !empty($styling_afterform)) {

            $styling_afterform = $this->language_useordefault(  $styling_afterform  ) ;
        }
        echo wpautop(wp_kses(wp_unslash($styling_afterform),  $allowed_html, $allowedProtocols)); ?>
    </div>
<?php
} ?>
</div>

