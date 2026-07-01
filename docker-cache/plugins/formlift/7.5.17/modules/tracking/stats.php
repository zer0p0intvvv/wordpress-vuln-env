<?php

/*

This class runs independently of any operations you do and does not hinder the user experience.
Sends usage stats once daily.

*/

class FormLift_Stats_Collector {

	const DEST = 'https://formlift.net/';
	const TRANSIENT = 'formlift_stats_loop';
	const PASSWORD = 'formlift_stats_pw';

	function send() {
		$url  = get_site_url();
		$imps = formlift_get_all_impressions( date( 'Y-m-d H:i:s', strtotime( '-1 days' ) ), current_time( 'mysql' ) );
		$subs = formlift_get_all_submissions( date( 'Y-m-d H:i:s', strtotime( '-1 days' ) ), current_time( 'mysql' ) );

		$response        = wp_remote_get( 'https://formlift.net/?formlift_action=get_encryption_data' );
		$response        = wp_remote_retrieve_body( $response );
		$encryption_data = json_decode( $response );

		//wp_die( print_r( $encryption_data, true ) );

		$output = openssl_encrypt( $url, $encryption_data->method, $encryption_data->key, 0, $encryption_data->IV );

		$args = array(
			'url'         => $output,
			'impressions' => intval( $imps ),
			'submissions' => intval( $subs )
		);

		$destination = add_query_arg( array( 'formlift_action' => 'log_usage' ), static::DEST );

		wp_remote_post( $destination, array( 'body' => $args, 'sslverify' => true ) );
	}

	function loop() {
		if ( get_formlift_setting( 'opt_out_of_usage_stats', false ) ) {
			return;
		}

		if ( get_transient( self::TRANSIENT ) ) {
			return;
		}

		$this->send();

		set_transient( self::TRANSIENT, true, 24 * HOUR_IN_SECONDS );

		$args = $defaults = array(
			'is_dismissable' => true,
			'show_once'      => true,
			'is_premium'     => 'both',
			'is_specific'    => false,
			'type'           => 'notice-info',
			'html'           => 'ATTENTION: This is a notice to inform you of our new user statistics collection.

We are currently anonymously collecting information about your website regarding the usage of FormLift. 
<b>Specifically</b>, we are collecting the following two statistics: The daily # of submission & impression totals of your forms. 
We are NOT collecting any personal information related to you, your site, your IP address, or your customers.
Again, we are only collecting the NUMBERS. We are using these numbers only for one simple purpose at the moment, the live stats counters which can be found on our site https://formlift.net.
This usage collection does not impede service, site speed, or have any otherwise negative affect on user experience.
Even though we collect no personal identifiable information, you may choose to opt out of this service by visiting the FormLift Settings -> "Admin Settings" and enabling "Opt out of usage stats collection."
We appreciate your support of FormLift by allowing us to use this information to expand the FormLift user base around the world. Should you decide not to want to share this anonymous information with us, well, we hope you continue to enjoy the product.'
		);

		FormLift_Notice_Manager::add_notice( 'stats-collection', $args );
	}
}
//$FormLiftStatsCollector = new FormLift_Stats_Collector();
//add_action( 'init', array( $FormLiftStatsCollector, 'loop' ) );