<?php

$isSerialKeyValid       = false;                       // default: Lite
$serial_validation_data = $this->ci_serial_valid();    // run core check

if ( ! is_array( $serial_validation_data ) && $serial_validation_data ) {
    $isSerialKeyValid = true;                          // Pro licence active
}


$captcha_type = $this->get_option( 'captcha_type' );
?>
<style type="text/css">
    /* ── Select2 dropdown: show full list, no inner scrollbar ─────────── */
    .select2-container #select2-captcha_type_select-results.select2-results__options{
        max-height:300px !important;   /* remove the 200 px cap        */
    }

</style>
<table class="form-table">
    <tbody>
    <tr>
        <th>
            <label for="captcha_type_select"><?php _e( 'CAPTCHA Type', 'wp-mailinglist' ); ?></label>
            <?php echo $Html->help( __( 'Choose which CAPTCHA system you want to use. Otherwise, select “None”.', 'wp-mailinglist' ) ); ?>
        </th>

        <td class="captcha_select_custom_height">
            <select name="captcha_type" id="captcha_type_select"   class="captcha-type-select"
                    onchange="newsletters_captcha_change( this.value );">
                <option value="none"       <?php selected( $captcha_type, 'none' ); ?>><?php _e( 'None', 'wp-mailinglist' ); ?></option>
                <!-- Disabled until plugin available -->
                <option value="hcaptcha"
                        id="captcha_type_hcaptcha"
                    <?php selected( $captcha_type, 'hcaptcha' ); ?>
                    <?php disabled ( ! $isSerialKeyValid || ! $this->is_plugin_active( 'hcaptcha-for-forms-and-more' ) ); ?>
                ><?php _e( 'hCaptcha', 'wp-mailinglist' ); echo $isSerialKeyValid ? '' : ' (PRO)'; ?></option>
                <option value="recaptcha"  <?php selected( $captcha_type, 'recaptcha' ); ?>><?php _e( 'Google reCAPTCHA v2', 'wp-mailinglist' ); ?></option>
                <option value="recaptcha3"
                    <?php selected( $captcha_type, 'recaptcha3' ); ?>
                    <?php disabled ( ! $isSerialKeyValid ); ?>
                ><?php _e( 'Google reCAPTCHA v3', 'wp-mailinglist' ); echo $isSerialKeyValid ? '' : ' (PRO)'; ?></option>
                <!-- Disabled until plugin available -->
                <option value="rsc"
                        id="captcha_type_rsc"
                    <?php selected( $captcha_type, 'rsc' ); ?>
                    <?php disabled ( ! $this->is_plugin_active( 'captcha' ) ); ?>
                ><?php _e( 'Really Simple CAPTCHA', 'wp-mailinglist' ); ?></option>

                <option value="turnstile"
                    <?php selected( $captcha_type, 'turnstile' ); ?>
                    <?php disabled ( ! $isSerialKeyValid ); ?>
                ><?php _e( 'Cloudflare Turnstile', 'wp-mailinglist' ); echo $isSerialKeyValid ? '' : ' (PRO)'; ?></option>




            </select>
            
            <?php
            $rsc_path      = 'really-simple-captcha/really-simple-captcha.php';
            $hcaptcha_path = 'hcaptcha-for-forms-and-more/hcaptcha.php';
            ?>

            <!-- Install / Activate Really Simple CAPTCHA -->
            <span class="plugin-install-rsc">
				<?php if ( ! $this->is_plugin_active( 'captcha', true ) ) : ?>
                    <button type="button" class="install-now button button-secondary"
                            href="<?php echo wp_nonce_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=really-simple-captcha&TB_iframe=true&width=600&height=550' ) ); ?>">
						<i class="fa fa-download"></i> <?php _e( 'Install Really Simple CAPTCHA', 'wp-mailinglist' ); ?>
					</button>
                <?php elseif ( ! $this->is_plugin_active( 'captcha', false ) ) : ?>
                    <a class="button button-secondary"
                       href="<?php echo wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $rsc_path ), 'activate-plugin_' . $rsc_path ); ?>">
						<i class="fa fa-check"></i> <?php _e( 'Activate Really Simple CAPTCHA', 'wp-mailinglist' ); ?>
					</a>
                <?php endif; ?>
			</span>

            <!-- Install / Activate hCaptcha -->
            <span class="plugin-install-hcaptcha">
				<?php if ( ! $this->is_plugin_active( 'hcaptcha-for-forms-and-more', true ) ) : ?>
                    <button type="button" class="install-now button button-secondary"
                            href="<?php echo wp_nonce_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=hcaptcha-for-forms-and-more&TB_iframe=true&width=600&height=550' ) ); ?>">
						<i class="fa fa-download"></i> <?php _e( 'Install hCaptcha for WP', 'wp-mailinglist' ); ?>
					</button>
                <?php elseif ( ! $this->is_plugin_active( 'hcaptcha-for-forms-and-more', false ) ) : ?>
                    <a class="button button-secondary"
                       href="<?php echo wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $hcaptcha_path ), 'activate-plugin_' . $hcaptcha_path ); ?>">
						<i class="fa fa-check"></i> <?php _e( 'Activate hCaptcha for WP', 'wp-mailinglist' ); ?>
					</a>
                <?php endif; ?>
			</span>

            <span class="howto">
				<?php _e( 'Select a provider, then complete its settings below.', 'wp-mailinglist' ); ?>
			</span>
            <?php if (!$isSerialKeyValid) { ?>
                <span class="howto">
                    <?php echo  sprintf(__('Items marked with (PRO) require you to %s.', 'wp-mailinglist'),  '<a href="' . admin_url('admin.php?page=' . $this->sections->lite_upgrade) . '" target="_blank">upgrade to PRO</a>'); ?>
                </span>
            <?php } ?>

        </td>
    </tr>
    </tbody>
</table>
<script type="text/javascript">
    function newsletters_captcha_change( type ){
        const $ = jQuery;
        const panels = ['#recaptcha_div','#recaptcha3_div','#turnstile_div','#rsc_div','#hcaptcha_div'];
        $( panels.join() ).hide();
        switch( type ){
            case 'recaptcha'  : $('#recaptcha_div').show();   break;
            case 'recaptcha3' : $('#recaptcha3_div').show();  break;
            case 'turnstile'  : $('#turnstile_div').show();   break;
            case 'rsc'        : $('#rsc_div').show();         break;
            case 'hcaptcha'   : $('#hcaptcha_div').show();    break;
            // 'none' leaves all hidden
        }
    }
    jQuery(function(){ newsletters_captcha_change( jQuery('#captcha_type_select').val() ); });

</script>

<!-- reCAPTCHA Settings -->
<?php

$recaptcha_publickey = $this -> get_option('recaptcha_publickey');
$recaptcha_privatekey = $this -> get_option('recaptcha_privatekey');
$recaptcha_type = $this -> get_option('recaptcha_type');
$recaptcha_language = $this -> get_option('recaptcha_language');
$recaptcha_theme = $this -> get_option('recaptcha_theme');
$recaptcha_customcss = $this -> get_option('recaptcha_customcss');

?>

<div class="newsletters_indented" id="recaptcha_div" style="display:<?php echo (!empty($captcha_type) && $captcha_type == "recaptcha") ? 'block' : 'none'; ?>;">
    <table class="form-table">
        <tbody>
        <tr>
            <th></th>
            <td>
                <p><?php echo sprintf(__('In order to use Google reCAPTCHA, the public and private keys below are required.<br/>Go to the %sreCAPTCHA signup page%s and %screate a set of keys%s for this domain.', 'wp-mailinglist'),'<a href="https://www.google.com/recaptcha/" target="_blank">', '</a>',  '<a href="https://www.google.com/recaptcha/admin/create" target="_blank">', '</a>'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="recaptcha_type"><?php _e('reCAPTCHA Type', 'wp-mailinglist'); ?></label></th>
            <td>
                <label><input onclick="jQuery('#recaptcha_type_div_robot').show();" <?php echo (!empty($recaptcha_type) && $recaptcha_type == "robot") ? 'checked="checked"' : ''; ?> type="radio" name="recaptcha_type" value="robot" id="recaptcha_type_robot" /><?php echo esc_html_e("I'm not a robot", 'wp-mailinglist'); ?></label>
                <label><input onclick="jQuery('#recaptcha_type_div_robot').hide();" <?php echo (empty($recaptcha_type) || $recaptcha_type == "invisible") ? 'checked="checked"' : ''; ?> type="radio" name="recaptcha_type" value="invisible" id="recaptcha_type_invisible" /> <?php _e('Invisible', 'wp-mailinglist'); ?></label>
                <span class="howto"><?php _e('Choose the reCAPTCHA integration to use, make sure your keys are valid for that integration.', 'wp-mailinglist'); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="recaptcha_publickey"><?php _e('Site Key', 'wp-mailinglist'); ?></label></th>
            <td>
                <input type="text" class="widefat" name="recaptcha_publickey" value="<?php echo esc_attr(wp_unslash($recaptcha_publickey)); ?>" id="recaptcha_publickey" />
                <span class="howto"><?php _e('Site key provided by reCAPTCHA upon signing up.', 'wp-mailinglist'); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="recaptcha_privatekey"><?php _e('Secret Key', 'wp-mailinglist'); ?></label></th>
            <td>
                <input type="text" class="widefat" name="recaptcha_privatekey" value="<?php echo wp_kses_post(($recaptcha_privatekey)); ?>" id="recaptcha_privatekey" />
                <span class="howto"><?php _e('Secret key provided by reCAPTCHA upon signing up.', 'wp-mailinglist'); ?></span>
            </td>
        </tr>
        <tr class="advanced-setting">
            <th><label for="recaptcha_language"><?php _e('Language', 'wp-mailinglist'); ?></label></th>
            <td>
                <input type="text" class="widefat" style="width:65px;" name="recaptcha_language" value="<?php echo esc_attr(wp_unslash($recaptcha_language)); ?>" id="recaptcha_language" />
                <span class="howto"><?php echo sprintf(__('Language in which to display the CAPTCHA. Two-letter code, e.g., “en”. See the %s', 'wp-mailinglist'), '<a href="https://developers.google.com/recaptcha/docs/language" target="_blank">' . __('language codes', 'wp-mailinglist') . '</a>'); ?></span>
            </td>
        </tr>
        <tr class="advanced-setting">
            <th><label for="recaptcha_theme"><?php _e('Theme', 'wp-mailinglist'); ?></label>
                <?php echo $Html -> help(__('Choose the reCAPTCHA theme to show to your users. Some premade themes by reCAPTCHA are available or you can use the Custom theme and style it according to your needs.', 'wp-mailinglist')); ?></th>
            <td>
                <?php $themes = array('light' => __('Light', 'wp-mailinglist'), 'dark' => __('Dark', 'wp-mailinglist')); ?>
                <select name="recaptcha_theme" id="recaptcha_theme">
                    <option value=""><?php _e('- Select -', 'wp-mailinglist'); ?></option>
                    <?php foreach ($themes as $theme_key => $theme_value) : ?>
                        <option <?php echo (!empty($recaptcha_theme) && $recaptcha_theme == $theme_key) ? 'selected="selected"' : ''; ?> value="<?php echo $theme_key; ?>"><?php echo $theme_value; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="howto"><?php _e('Pick the reCAPTCHA theme that you want to use.', 'wp-mailinglist'); ?></span>
            </td>
        </tr>
        </tbody>
    </table>

    <div id="recaptcha_type_div_robot" style="display:<?php echo (!empty($recaptcha_type) && $recaptcha_type == "robot") ? 'block' : 'none'; ?>;">

    </div>
</div>

<!-- reCAPTCHA v3 Settings -->
<?php
// === get the v3-specific keys ===
$recaptcha3_public  = $this->get_option( 'recaptcha3_publickey'  );
$recaptcha3_secret  = $this->get_option( 'recaptcha3_privatekey' );
?>

<div class="newsletters_indented" id="recaptcha3_div"
     style="display:<?php echo ( $captcha_type === 'recaptcha3' ) ? 'block' : 'none'; ?>;">
    <table class="form-table">
        <tbody>
        <tr><th></th><td>
                <p><?php printf(
                        __( 'To use reCAPTCHA v3, you need a dedicated set of keys generated for v3 in the %sGoogle reCAPTCHA console%s. If you don\'t have a Google reCAPTCHA account, you can %ssign up for an account%s.', 'wp-mailinglist' ),
                        '<a href="https://www.google.com/recaptcha/admin/create" target="_blank">','</a>', '<a href="https://www.google.com/recaptcha/" target="_blank">','</a>'
                    ); ?></p>
            </td></tr>

        <tr>
            <th><label for="recaptcha3_publickey"><?php _e( 'Site Key', 'wp-mailinglist' ); ?></label></th>
            <td>
                <input type="text" class="widefat" id="recaptcha3_publickey"
                       name="recaptcha3_publickey"
                       value="<?php echo esc_attr( $recaptcha3_public ); ?>" />
            </td>
        </tr>

        <tr>
            <th><label for="recaptcha3_privatekey"><?php _e( 'Secret Key', 'wp-mailinglist' ); ?></label></th>
            <td>
                <input type="text" class="widefat" id="recaptcha3_privatekey"
                       name="recaptcha3_privatekey"
                       value="<?php echo esc_attr( $recaptcha3_secret ); ?>" />
            </td>
        </tr>

        <tr class="advanced-setting">
            <th><label for="recaptcha3_language"><?php _e( 'Language', 'wp-mailinglist' ); ?></label></th>
            <td>
                <input type="text" class="widefat" style="width:65px"
                       id="recaptcha3_language" name="recaptcha_language"
                       value="<?php echo esc_attr( $recaptcha_language ); ?>" />
                <span class="howto"><?php printf(
                        __( 'Language in which to display the CAPTCHA. Two-letter code, e.g., “en”. See the %slanguage codes%s.', 'wp-mailinglist' ),
                        '<a href="https://developers.google.com/recaptcha/docs/language" target="_blank">','</a>'
                    ); ?></span>
            </td>
        </tr>

        <tr class="advanced-setting">
            <th><label for="recaptcha3_score"><?php _e( 'Score Threshold', 'wp-mailinglist' ); ?></label></th>
            <td>
                <?php $recaptcha3_score = $this->get_option( 'recaptcha3_score', '0.5' ); ?>
                <input type="number" class="widefat" style="width:65px"
                       id="recaptcha3_score" name="recaptcha3_score"
                       step="0.1" min="0" max="1"
                       value="<?php echo esc_attr( $recaptcha3_score ); ?>" />
                <span class="howto"><?php _e( 'Default 0.5 (0 = bot, 1 = human).', 'wp-mailinglist' ); ?></span>
            </td>
        </tr>
        </tbody>
    </table>
</div>




<!-- RSC Settings -->
<?php if ($this->is_plugin_active('captcha')) : ?>
    <div class="newsletters_indented" id="rsc_div" style="display:<?php echo (!empty($captcha_type) && $captcha_type == "rsc") ? 'block' : 'none'; ?>;">


        <!-- Preview of Really Simple CAPTCHA -->
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

        <!-- Really Simple CAPTCHA Settings -->
        <table class="form-table">
            <tbody>
            <tr>
                <th><label for=""><?php _e('Preview', 'wp-mailinglist'); ?></label></th>
                <td>
                    <div id="newsletters-captcha-preview">
                        <img src="<?php echo $captcha_file; ?>" alt="captcha" />
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="captcha_bg"><?php _e('Background Color', 'wp-mailinglist'); ?></label>
                    <?php echo $Html -> help(__('The background color of the CAPTCHA image in hex code, e.g., #FFFFFF', 'wp-mailinglist')); ?></th>
                <td>
                    <input type="text" name="captcha_bg" id="captcha_bg" value="<?php echo $this -> get_option('captcha_bg'); ?>" class="color-picker" />
                    <span class="howto"><?php _e('Set the background color of the CAPTCHA image', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="captcha_fg"><?php _e('Text Color', 'wp-mailinglist'); ?></label>
                    <?php echo $Html -> help(__('The foreground/text color of the text on the CAPTCHA image.', 'wp-mailinglist')); ?></th>
                <td>
                    <input type="text" name="captcha_fg" id="captcha_fg" value="<?php echo $this -> get_option('captcha_fg'); ?>" class="color-picker" />
                    <span class="howto"><?php _e('Set the foreground/text color of the CAPTCHA image', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="captcha_size_w"><?php _e('Image Size', 'wp-mailinglist'); ?></label>
                    <?php echo $Html -> help(__('Choose the size of the CAPTCHA image as it will display to your users. Fill in the width and the height of the image in pixels (px). The default is 72 by 24px, which is optimal.', 'wp-mailinglist')); ?></th>
                <td>
                    <?php $captcha_size = $this -> get_option('captcha_size'); ?>
                    <input type="text" class="widefat" style="width:45px;" name="captcha_size[w]" value="<?php echo $captcha_size['w']; ?>" id="captcha_size_w" /> <?php _e('by', 'wp-mailinglist'); ?>
                    <input type="text" class="widefat" style="width:45px;" name="captcha_size[h]" value="<?php echo $captcha_size['h']; ?>" id="captcha_size_h" /> <?php _e('px', 'wp-mailinglist'); ?>
                    <span class="howto"><?php _e('Choose your preferred size for the CAPTCHA image.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="captcha_chars"><?php _e('Number of Characters', 'wp-mailinglist'); ?></label>
                    <?php echo $Html -> help(__('You can increase the number of characters to show in the CAPTCHA image to increase the security. Too many characters will make it difficult for your users though. The default is 4.', 'wp-mailinglist')); ?></th>
                <td>
                    <input type="text" name="captcha_chars" value="<?php echo $this -> get_option('captcha_chars'); ?>" id="captcha_chars" class="widefat" style="width:45px;" /> <?php _e('characters', 'wp-mailinglist'); ?>
                    <span class="howto"><?php _e('The number of characters to show in the CAPTCHA image.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            <tr>
                <th><label for="captcha_font"><?php _e('Font Size', 'wp-mailinglist'); ?></label>
                    <?php echo $Html -> help(__('A larger font will make the characters easier to read for your users. The default is 14 pixels.', 'wp-mailinglist')); ?></th>
                <td>
                    <input type="text" name="captcha_font" value="<?php echo $this -> get_option('captcha_font'); ?>" id="captcha_font" class="widefat" style="width:45px;" /> <?php _e('px', 'wp-mailinglist'); ?>
                    <span class="howto"><?php _e('Choose the font size of the characters on the CAPTCHA image.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            <tr class="advanced-setting">
                <th><label for="captchainterval"><?php _e('Cleanup Interval', 'wp-mailinglist'); ?></label>
                    <?php echo $Html -> help(__('To keep your server clean from old, unused CAPTCHA images a schedule will run at the interval specified to clean up old images. Set this to hourly or less as a recommended setting.', 'wp-mailinglist')); ?></th>
                <td>
                    <?php $captchainterval = $this -> get_option('captchainterval'); ?>
                    <select class="widefat" style="width:auto;" id="captchainterval" name="captchainterval">
                        <option value=""><?php _e('- Select Interval -', 'wp-mailinglist'); ?></option>
                        <?php $schedules = array(
                            "1minutes" => array(
                                "interval" => 60,
                                "display" => "Every Minute"
                            ),
                            "2minutes" => array(
                                "interval" => 120,
                                "display" => "Every 2 Minutes"
                            ),
                            "5minutes" => array(
                                "interval" => 300,
                                "display" => "Every 5 Minutes"
                            ),
                            "10minutes" => array(
                                "interval" => 600,
                                "display" => "Every 10 Minutes"
                            ),
                            "20minutes" => array(
                                "interval" => 1200,
                                "display" => "Every 20 Minutes"
                            ),
                            "30minutes" => array(
                                "interval" => 1800,
                                "display" => "Every 30 Minutes"
                            ),
                            "40minutes" => array(
                                "interval" => 2400,
                                "display" => "Every 40 Minutes"
                            ),
                            "50minutes" => array(
                                "interval" => 3000,
                                "display" => "Every 50 minutes"
                            ),
                            "hourly" => array(
                                "interval" => 3600,
                                "display" => "Once Hourly"
                            ),
                            "twicedaily" => array(
                                "interval" => 43200,
                                "display" => "Twice Daily"
                            ),
                            "daily" => array(
                                "interval" => 86400,
                                "display" => "Once Daily"
                            ),
                            "weekly" => array(
                                "interval" => 604800,
                                "display" => "Once Weekly"
                            ),
                            "monthly" => array(
                                "interval" => 2664000,
                                "display" => "Once Monthly"
                            )

                        ); ?>
                        <?php if (!empty($schedules)) : ?>
                            <?php foreach ($schedules as $key => $val) :
                                if (preg_match('/wp_|every_minute/', $key)) continue;
                                ?>
                                <?php $sel = ($captchainterval == $key) ? 'selected="selected"' : ''; ?>
                                <option <?php echo $sel; ?> value="<?php echo $key ?>"><?php echo $val['display']; ?> (<?php echo $val['interval'] ?> <?php _e('seconds', 'wp-mailinglist'); ?>)</option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <span class="howto"><?php _e('The interval at which old CAPTCHA images will be removed from the server.', 'wp-mailinglist'); ?></span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php endif; ?>



<!-- hCaptcha Settings -->
<div class="newsletters_indented" id="hcaptcha_div" style="display:<?php echo (!empty($captcha_type) && $captcha_type == "hcaptcha") ? 'block' : 'none'; ?>;">
    <table class="form-table">
        <tbody>
        <tr>
            <th></th>
            <td>
                <p><?php echo sprintf(__('hCaptcha is managed by the plugin %shCaptcha for WP%s. Install and configure it to use hCaptcha here.', 'wp-mailinglist'), '<a href="https://wordpress.org/plugins/hcaptcha-for-forms-and-more/" target="_blank">', '</a>'); ?></p>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<!-- Turnstile Settings -->
<?php
$turnstile_sitekey  = $this->get_option( 'turnstile_sitekey' );
$turnstile_secret   = $this->get_option( 'turnstile_secret' );
?>
<div class="newsletters_indented" id="turnstile_div"
     style="display:<?php echo ( $captcha_type === 'turnstile' ) ? 'block' : 'none'; ?>;">
    <table class="form-table">
        <tbody>
        <tr>
            <th></th>
            <td>
                <p><?php printf(
                        __( 'Register your domain for free in the %sCloudflare Turnstile dashboard%s to get your API keys. Once acquired, copy them below.', 'wp-mailinglist' ),
                        '<a href="https://dash.cloudflare.com/" target="_blank">', '</a>'
                    ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="turnstile_sitekey"><?php _e( 'Site Key', 'wp-mailinglist' ); ?></label></th>
            <td>
                <input type="text" class="widefat"
                       name="turnstile_sitekey" id="turnstile_sitekey"
                       value="<?php echo esc_attr( $turnstile_sitekey ); ?>"/>
            </td>
        </tr>
        <tr>
            <th><label for="turnstile_secret"><?php _e( 'Secret Key', 'wp-mailinglist' ); ?></label></th>
            <td>
                <input type="text" class="widefat"
                       name="turnstile_secret" id="turnstile_secret"
                       value="<?php echo esc_attr( $turnstile_secret ); ?>"/>
            </td>
        </tr>
        </tbody>
    </table>
</div>


<script type="text/javascript">
    (function ($) {
        const $doc  = $(document);
        const $rsc  = $('.plugin-install-rsc');
        const $hcpt = $('.plugin-install-hcaptcha');

        /* RSC ---------------------------------------------------------------- */
        $rsc.on('click', '.install-now', function () {
            tb_show(
                '<?php _e( "Install Really Simple CAPTCHA Plugin", "wp-mailinglist" ); ?>',
                $(this).attr('href'),
                false
            );
            return false;
        });
        $rsc.on('click', '.activate-now', function () { window.location = $(this).attr('href'); });

        $doc.on('wp-plugin-installing', function (e, args) {
            if (args.slug === 'really-simple-captcha') {
                $rsc.find('.install-now')
                    .html('<i class="fa fa-refresh fa-spin fa-fw"></i> <?php echo esc_js( __( "Installing", "wp-mailinglist" ) ); ?>')
                    .prop('disabled', true);
            }
        });
        $doc.on('wp-plugin-install-success', function (e, r) {
            if (r.slug === 'really-simple-captcha') {
                $rsc.find('.install-now')
                    .html('<i class="fa fa-check fa-fw"></i> <?php _e( "Activate Really Simple CAPTCHA", "wp-mailinglist" ); ?>')
                    .attr('href', r.activateUrl)
                    .prop('disabled', false)
                    .removeClass('install-now')
                    .addClass('activate-now');
                $('#captcha_type_rsc').prop('disabled', false);
            }
        });
        $doc.on('wp-plugin-install-error', function (e, r) {
            if (r.slug === 'really-simple-captcha') {
                alert('<?php echo esc_js( __( "An error occurred, please try again.", "wp-mailinglist" ) ); ?>');
                $rsc.find('.install-now')
                    .html('<i class="fa fa-download"></i> <?php _e( "Install Really Simple CAPTCHA", "wp-mailinglist" ); ?>')
                    .prop('disabled', false);
            }
        });

    })(jQuery);
</script>