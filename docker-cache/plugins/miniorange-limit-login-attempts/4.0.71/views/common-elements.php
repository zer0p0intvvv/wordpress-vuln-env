<?php
	

	//Function to show Error message if user is not registered
   
	function Mo_lla_is_customer_valid()
	{
		global $moWpnsUtility;
		$url 	=	add_query_arg( array('page' => 'wpnsaccount'), $_SERVER['REQUEST_URI'] );
		if (!$moWpnsUtility->icr())
			echo '<div class="warning_div">Please <a href="'.$url.'">Register or Login with miniOrange</a> to configure the Limit Login Attempts Plugin.</div>';
	}


	//Function to show Login Transactions
	function Mo_lla_showLoginTransactions($usertranscations)
	{
		 foreach($usertranscations as $usertranscation)
        {
        		echo "<tr><td>".$usertranscation->ip_address."</td><td>".$usertranscation->username."</td><td>";
				if($usertranscation->status==Mo_lla_MoWpnsConstants::FAILED || $usertranscation->status==Mo_lla_MoWpnsConstants::PAST_FAILED)
					echo "<span style=color:red>".Mo_lla_MoWpnsConstants::FAILED."</span>";
				else if($usertranscation->status==Mo_lla_MoWpnsConstants::SUCCESS)
					echo "<span style=color:green>".Mo_lla_MoWpnsConstants::SUCCESS."</span>";
				else
					echo "N/A";
				echo "</td><td>".$usertranscation->type."</td><td>".date("M j, Y, g:i:s a",$usertranscation->created_timestamp)."</td></tr>";
		}
	}

	//Function to show 404 and 403 Reports
	function Mo_lla_showErrorTransactions($usertransactions)
	{
		foreach($usertransactions as $usertranscation)
        {
    		echo "<tr><td>".$usertranscation->ip_address."</td><td>".$usertranscation->username."</td>";
			echo "<td>".$usertranscation->url."</td><td>".$usertranscation->type."</td>";
			echo "</td><td>".date("M j, Y, g:i:s a",$usertranscation->created_timestamp)."</td></tr>";
		}
	}


	//Function to show google recaptcha form
	function Mo_lla_show_google_recaptcha_form()
	{
		echo'
			<link rel="stylesheet" type="text/css" media="all" href="'.site_url().'/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load%5B%5D=dashicons,admin-bar,common,forms,admin-menu,dashboard,list-tables,edit,revisions,media,themes,about,nav-menus,widgets,site-icon,&amp;load%5B%5D=l10n,buttons,wp-auth-check&amp;ver=4.5.2"/>
			<style> .button.button-large { height: 30px; line-height: 28px; padding: 0 12px 2px; } .button-primary { background: #0085ba; border-color: #0073aa #006799 #006799; -webkit-box-shadow: 0 1px 0 #006799; box-shadow: 0 1px 0 #006799; color: #fff; text-decoration: none; text-shadow: 0 -1px 1px #006799,1px 0 1px #006799,0 1px 1px #006799,-1px 0 1px #006799; border-radius: 3px; cursor: pointer; border-width: 1px; border-style: solid; font-size: 15px; width: 300px; } </style>
			<script src="'.Mo_lla_MoWpnsConstants::RECAPTCHA_URL.'"></script>
			<div style="font-family:\'Open Sans\',sans-serif;margin:0px auto;width:303px;text-align:center;">
				<br><br><h2>Test google reCAPTCHA keys</h2>
				<form method="post">
					<div class="g-recaptcha" data-sitekey="'.get_option('mo_wpns_recaptcha_site_key').'"></div>
					<br><input class="button button-primary button-large" type="submit" value="Test Keys" class="button button-primary button-large">
				</form>
			</div>';

	}
        function mo_lla_show_google_recaptcha_form_v3_login()
        {

            $site_k=get_option('mo_wpns_recaptcha_site_key_v3');
            ?>
            <script src='https://www.google.com/recaptcha/api.js?render=<?php echo get_option("mo_wpns_recaptcha_site_key_v3");?>'></script>
            <?php
            echo'
                <div class="g-recaptcha-response" data-sitekey="'.$site_k.'"></div>
                <input type="hidden"  name="g-recaptcha-response" id="g-recaptcha-response">
            ';?>

            <script>

            grecaptcha.ready(function() {

                var sitek = "<?php echo $site_k;?>";
                grecaptcha.execute(  sitek, {action:"homepage"}).
                then(function(token) {
                    document.getElementById("g-recaptcha-response").value=token;
              });
            });

            </script>
            <?php
        }

        function mo_lla_show_google_recaptcha_form_v2_login()
       	{
       		echo "<script src='".Mo_lla_MoWpnsConstants::RECAPTCHA_URL."'></script>";
    		echo '<div class="g-recaptcha" data-sitekey="'.get_option("mo_wpns_recaptcha_site_key").'"></div>';
    		echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#registerform{padding-bottom:20px;}</style>';
    	}


    	//Function to show google recaptcha v3 form

    	function mo_lla_show_google_recaptcha_form_v3()
    	{
    		$site_k=get_option('mo_wpns_recaptcha_site_key_v3');
    		echo'
    			<link rel="stylesheet" type="text/css" media="all" href="'.site_url().'/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load%5B%5D=dashicons,admin-bar,common,forms,admin-menu,dashboard,list-tables,edit,revisions,media,themes,about,nav-menus,widgets,site-icon,&amp;load%5B%5D=l10n,buttons,wp-auth-check&amp;ver=4.5.2"/>
    			<style> .button.button-large { height: 30px; line-height: 28px; padding: 0 12px 2px; } .button-primary { background: #0085ba; border-color: #0073aa #006799 #006799; -webkit-box-shadow: 0 1px 0 #006799; box-shadow: 0 1px 0 #006799; color: #fff; text-decoration: none; text-shadow: 0 -1px 1px #006799,1px 0 1px #006799,0 1px 1px #006799,-1px 0 1px #006799; border-radius: 3px; cursor: pointer; border-width: 1px; border-style: solid; font-size: 15px; width: 300px; } </style>';
    			?>

    			<script src='https://www.google.com/recaptcha/api.js?render=<?php echo get_option("mo_wpns_recaptcha_site_key_v3");?>'></script>

    			<?php
    			echo'
    			    <div style="font-family:\'Open Sans\',sans-serif;margin:0px auto;width:303px;text-align:center;">
    				<br><br><h2>Test google reCAPTCHA keys</h2>
                    <form id="f1" method="post">
                        <div class="g-recaptcha-response" data-sitekey="'.$site_k.'"></div>
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                        <br><input class="button button-primary button-large" type="submit" value="Test Keys" class="button button-primary button-large">
                    </form>
                    </div>
                </div>';
    			?>

    	    <script>

            grecaptcha.ready(function() {
            	var sitek = ""+"<?php echo get_option('mo_wpns_recaptcha_site_key_v3');?>";
            	grecaptcha.execute(sitek, {action:"homepage"}).
                then(function(token) {
                    document.getElementById("g-recaptcha-response").value=token;
              });
            });

            </script>
            <?php

    	}