<?php
/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2018-04-01
 * Time: 9:57 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class FormLift_Awards_Manager {
	const OPTION = 'formlift_awards';

	function __construct() {
		if ( ! get_option( static::OPTION, false ) ) {
			add_option( static::OPTION, array() );
		}

		if ( get_transient( 'formlift_awards_check' ) ) {
			return;
		} else {
			set_transient( 'formlift_awards_check', true, 7 * 24 * HOUR_IN_SECONDS );
			add_action( 'plugins_loaded', array( $this, 'run_awards_check' ) );
		}
	}

	function saveAward( $level ) {
		$option           = get_option( static::OPTION );
		$option[ $level ] = true;
		update_option( static::OPTION, $option );
	}

	function hasAward( $level ) {
		$option = get_option( static::OPTION );

		return ( isset( $option[ $level ] ) );
	}

	function run_awards_check() {
		$subs = formlift_get_all_submissions( date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ), current_time( 'mysql' ) );

		if ( $subs == 0 ) {
			return;
		} else if ( $subs > 10 && ! $this->hasAward( 10 ) ) {
			FormLift_Notice_Manager::add_success(
				'last-week-submissions',
				"Hey, last week you pulled in {$subs} new leads. Congratulations! Although I'm sure we could do better than that. If you need some help, feel free to ask for it by posting in the <a href='https://formlift.net/fb/'>facebook group</a> or <a href='mailto:info@formlift.net'>sending me an email</a> and sharing your ideas!"
			);
			$this->saveAward( 10 );
		} else if ( $subs > 50 && ! $this->hasAward( 50 ) ) {
			FormLift_Notice_Manager::add_success(
				'last-week-submissions',
				"Hey, last week you pulled in {$subs} new leads. Congratulations, that's way more than last week! Although I'm sure we could do better than that. I may have a tool to help you get some more leads in. <a href='https://formlift.net/store/downloads/proof/'>Check it out!</a>"
			);
			$this->saveAward( 50 );
		} else if ( $subs > 100 && ! $this->hasAward( 100 ) ) {
			FormLift_Notice_Manager::add_success(
				'last-week-submissions',
				"WOW! Last week you pulled in {$subs} new leads. You're just getting more and more every week! Maybe it's time you started considering turning those leads into payed customers, of course with a little help from this <a href='https://formlift.net/use-payments/'>awesome tool</a>."
			);
			$this->saveAward( 100 );
		} else if ( $subs > 500 && ! $this->hasAward( 500 ) ) {
			FormLift_Notice_Manager::add_success(
				'last-week-submissions',
				"You are on a roll! Last week you pulled in {$subs} new leads. I think that qualifies for a free t-shirt! Want yours? <a href='mailto:info@formlift.net'>Send me an email</a> with your shirt size and shipping information."
			);
			$this->saveAward( 500 );
		} else if ( $subs > 1000 && ! $this->hasAward( 1000 ) ) {
			FormLift_Notice_Manager::add_success(
				'last-week-submissions',
				"You are on a roll! Last week you pulled in {$subs} new leads. I was taught that whenever I experienced great success I had a duty and responsibility to share that with others. So, why don't you take a minute to pat yourself on the back and share how you were able to create so many leads in such a short period of time with the rest of the FormLift community on <a href='https://formlift.net/fb/'>Facebook</a>?"
			);
			$this->saveAward( 1000 );
		} else if ( $subs >= 1000 && ! $this->hasAward( 'BEST' ) ) {
			FormLift_Notice_Manager::add_success(
				'last-week-submissions',
				"You have created {$subs} new leads in less than 7 days. That's pretty amazing, and only a few FormLift users have ever accomplished that. You qualify to have your site featured as a super user on our site! Interested?  <a href='mailto:info@formlift.net'>Send me an email</a> to get started."
			);
			$this->saveAward( 'BEST' );
		}
	}
}

$FormLiftAwardsManager = new FormLift_Awards_Manager();