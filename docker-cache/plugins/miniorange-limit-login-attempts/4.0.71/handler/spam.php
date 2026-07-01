<?php
	
	class Mo_lla_Spam
	{
		function __construct()
		{
			if(get_option('mo_wpns_enable_comment_spam_blocking') || get_option('mo_wpns_activate_recaptcha_for_comments'))
			{
				add_filter( 'preprocess_comment'		, array($this, 'comment_spam_check'			) );
				add_action( 'comment_form_after_fields' , array($this, 'comment_spam_custom_field'	) );
				//add_filter( 'comment_form_submit_button', array($this, 'captcha_on_submit'	) );
			}
		}

		/*function captcha_on_submit(){
		echo '<input type="hidden" name="mocomment" />';
			if(get_option('mo_wpns_activate_recaptcha_for_comments'))
			{
				echo '<script src="'.Mo_lla_MoWpnsConstants::RECAPTCHA_URL.'"></script>';
				echo '<div class="g-recaptcha" data-sitekey="'.get_option('mo_wpns_recaptcha_site_key').'"></div>  ';
			}
		echo '<input type="submit" value ="Post Comment">';
		
		}*/
		function comment_spam_check( $comment_data ) 
		{
			if(!is_user_logged_in()){
			global $moWpnsUtility;
			if( isset($_POST['mocomment']) && !empty($_POST['mocomment']))
				wp_die( __( 'You are not authorised to perform this action.'));
			else if(get_option('mo_wpns_activate_recaptcha_for_comments'))
			{
				if(is_wp_error($moWpnsUtility->verify_recaptcha($_POST['g-recaptcha-response'])))
					wp_die( __( 'Invalid captcha. Please verify captcha again.'));
			}
			return $comment_data;
		}
		else{
			return $comment_data;	
		}
		}

		function comment_spam_custom_field()
		{
			echo '<input type="hidden" name="mocomment" />';
			if(get_option('mo_wpns_activate_recaptcha_for_comments'))
			{
				echo '<script src="'.Mo_lla_MoWpnsConstants::RECAPTCHA_URL.'"></script>';
				echo '<div class="g-recaptcha" data-sitekey="'.get_option('mo_wpns_recaptcha_site_key').'"></div>';
			}
		}
	}
	new Mo_lla_Spam;