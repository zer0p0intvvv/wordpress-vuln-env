<?php

/**
 *
 * Class for sms notifications
 *
 * @author Sidney van de Stouwe <sidney@vandestouwe.com>
 * @package subscribe-to-category
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

require_once( STC_PLUGIN_PATH . '/vendor/autoload.php');

use TextMagic\Models\CreateListInputObject;
use TextMagic\Models\CreateContactInputObject;
use TextMagic\Models\SendMessageInputObject;
use TextMagic\Api\TextMagicApi;
use TextMagic\Configuration;

if ( class_exists( 'STC_SMSNotification' ) ) {
	$stc_SMSNotification = new STC_SMSNotification();
}

/**
 * This is our callback function that processes the callback from TextMagic when an SMS is received
 * First we will check if we know this number
 * Then we will act based on the SMS text and update the mobile phone status accordingly
 * 
 */
function prefix_get_endpoint_smsreceived( $data) {
        global $wpdb;

        // read the sms received calback parameters send to us by TextMagic via a POST REST API call
        $par = $data->get_json_params();

        //connect to the TextMagic API Interface
        $textmagic = STC_SMSNotification::get_instance();
        $api_textmagic = $textmagic->configTextMagic();
        try {
                // do we know the phone number
                $result = $wpdb->get_results("select * FROM {$wpdb->prefix}postmeta where `meta_key` = '_stc_subscriber_mobile_phone' and `meta_value` = '+".$par['sender']."'");
                foreach ($result as $record) {
                        $content = array();
                        if (file_exists(wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt")) {
                                $content = file_get_contents(wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt");
                                $content = explode("|", $content);
                        } else {
                                $content[] = '-';
                                $content[] = 'unknown id';
                        }
                        switch (strtoupper($par['text'])) {
                                case "JOIN" : case "SUBSCRIBE" : case "RESUBSCRIBE" : case "OPT IN" : case "OPT-IN" : case "OPTIN" : case "UNSTOP" :
                                        update_post_meta($record->post_id, '_stc_subscriber_mobile_phone_status', "joined");
                                        file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt" , "joined");                                        
                                        break;
                                case "STOP" : case "STOPP" : case "STOPPALL" : case "UNSUBSCRIBE" : case "END" : case "QUIT" :
                                        update_post_meta($record->post_id, '_stc_subscriber_mobile_phone_status', "stopped");
                                        file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt" , "stopped");                                        
                                        break;
                        }
                }
        } catch (Exception $e) {
                $out = "";
                foreach ($par as $i=>$arg) {
                    $out .= strval($i) . ":" . $arg . " ";
                }
                error_log("SMS Received error json parameters are: " . $out);
        }
}

/**
 * This is our callback function that processes the callback from TextMagic when an SMS is received
 * First we will check if we know this number
 * Then we will act based on the SMS text and update the mobile phone status accordingly
 * 
 */
function prefix_get_endpoint_smsdelivery( $data) {
        global $wpdb;

        // read the sms received calback parameters send to us by TextMagic via a POST REST API call
        $par = $data->get_json_params();
        $textmagic = STC_SMSNotification::get_instance();
        $api_textmagic = $textmagic->configTextMagic();
        try {
                // do we know the phone number
                $result = $wpdb->get_results("select * FROM {$wpdb->prefix}postmeta where `meta_key` = '_stc_subscriber_mobile_phone' and `meta_value` = '+".$par['receiver']."'");
                foreach ($result as $record) {
                        $content = array();
                        if (file_exists(wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt")) {
                                $content = file_get_contents(wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt");
                                $content = explode("|", $content);
                        } else {
                                $content[] = '-';
                                $content[] = 'unknown id';
                        }
                        if (get_post_meta($record->post_id, '_stc_subscriber_mobile_phone_status', true) === "new") {
                                switch ($par['status']) {
                                        case "d" :
                                                update_post_meta($record->post_id, '_stc_subscriber_mobile_phone_status', "pending");
                                                file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt" , "join pending" . "|" . $content[1]);                                        
                                                break;
                                        case "e" : case "f" : 
                                                update_post_meta($record->post_id, '_stc_subscriber_mobile_phone_status', "error");
                                                file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt" , "sms send error" . "|" . $content[1]);                                        
                                                break;
                                        case "j" :
                                                update_post_meta($record->post_id, '_stc_subscriber_mobile_phone_status', "rejected");
                                                file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt" , "sms rejected" . "|" . $content[1]);                                        
                                                break;
                                        case "u" :
                                                update_post_meta($record->post_id, '_stc_subscriber_mobile_phone_status', "unknown");
                                                file_put_contents( wp_get_upload_dir()['basedir']."/sms/" . $record->post_id . "-status.txt" , "unknown" . "|" . $content[1]);                                        
                                                break;
                                }
                        }
                }
        } catch (Exception $e) {
                $out = "";
                foreach ($par as $i=>$arg) {
                    $out .= strval($i) . ":" . $arg . " ";
                }
                error_log("SMS Delivery error json parameters are: " . $out);
        }
}

/**
 * This function is where we register our routes for our endpoints.
 */
function prefix_register_stc_routes() {
        // register_rest_route for the callback from TextMagic when it received an SMS Message.
        register_rest_route( 'textmagic/v1', '/smsreceived', array(
                // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
                'methods'  => 'POST',
                // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
                'callback' => 'prefix_get_endpoint_smsreceived',
                'permission_callback' => '__return_true',
        ) );
        // register_rest_route for the callback from TextMagic when it received an SMS Message.
        register_rest_route( 'textmagic/v1', '/smsdelivery', array(
                // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
                'methods'  => 'POST',
                // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
                'callback' => 'prefix_get_endpoint_smsdelivery',
                'permission_callback' => '__return_true',
        ) );
}
 
add_action( 'rest_api_init', 'prefix_register_stc_routes' );

/**
 *
 * STC Subscribe class
 */
class STC_SMSNotification {
        
        public static $textmagic_api;
        public static $instance;
        

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since  2.5.5
	 */
	public function __construct() {
        }

        /**
	 * Single configure and connect to TextMagic SMS API
	 *
	 * @since  2.5.5
	 */
	public static function configTextMagic() {
                // check if a file with SMS username / licenskey info is uploaded
                $sms_file = wp_upload_dir()['basedir']."/sms/smsinfo.txt";
                if (file_exists($sms_file)) {
                        $handle = fopen($sms_file, "r");
                        if ($handle) {
                            $line = fgets($handle);
                            $usrdata = explode(',', base64_decode($line));
                            fclose($handle);
                        }
                        // put your Username and API Key from https://my.textmagic.com/online/api/rest-api/keys page.
                        $config = Configuration::getDefaultConfiguration()
                            ->setUsername($usrdata[0])
                            ->setPassword($usrdata[1]);

                        self::$textmagic_api = new TextMagicApi( new GuzzleHttp\Client(), $config );
                        return self::$textmagic_api;
                }
        }
        
        /**
	 * Single instance of this class.
	 *
	 * @since  2.5.5
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
       }

       /**
	 * Get inbound sms traffic from TextMagic.
	 *
	 * @since  2.5.5
	 */
	public static function get_inbound_sms_traffic() {
                // Optional, you can pass them as null values to getAllInboundMessages method below, default values will be used
                $page = 1;
                $limit = 10;
                $orderBy = "id";
                $direction = "desc";
                try {
                        // GetAllInboundMessagesPaginatedResponse class object
                        $result = self::$textmagic_api->getAllInboundMessages($page, $limit, $orderBy, $direction);
                } catch (Exception $e) {
                        echo 'Exception when calling TextMagicApi->getAllInboundMessages: ', $e->getMessage(), PHP_EOL;
                }
                return $result;
       }
       
       /**
	 * Get inbound sms traffic from TextMagic.
	 *
	 * @since  2.5.5
	 */
	public static function sendSingleSMS($mobile_phone_number, $message) {
                global $wpdb;

                $input = new SendMessageInputObject();
                $input->setPhones($mobile_phone_number);
                
                // check if the message size does not exceed the total length of TextMagic sms messages (918)
                $msgIndex = 0;
                $sendText[$msgIndex] = "";
                if (strlen($message) > 900) {
                        // split the message up, use the LF character as seperator
                        $messages = explode("\n", $message);
                        $i = 1;
                        while ($i < count($messages)-1 ) {
                                while ((strlen($sendText[$msgIndex]) + strlen($messages[$i])) < 900 && $i < count($messages)-1) {
                                        $sendText[$msgIndex] .= $messages[$i++]."\n";
                                }
                                if ($i < count($messages)-1) $sendText[++$msgIndex] = "";
                        }
                }  else {
                        // message can be send in one go
                        $sendText[0] = $message;
                }
                
                // send the sms message part by part
                foreach ($sendText as $i=>$text) {
                        $input->setText($text);
                        try {
                                $result = self::$textmagic_api->sendMessage($input);
                                // sleep for 5 seconds to avoid problems with the sequnce of the messages
                                if ($i < count($sendText)-1) sleep(5);
                        } catch (Exception $e) {
                                echo 'Exception when calling TextMagicApi->sendMessage: ', $e->getMessage(), PHP_EOL;
                        }
                }
        }
       
        /**
	 * Obtains Carrier information.
	 *
	 * @since  2.5.5
	 */
        public static function CarrierLookup($mobile_phone_number){
                try {
                        // DoCarrierLookupResponse class object
                        $result = self::$textmagic_api->doCarrierLookup($mobile_phone_number);
                        return $result;
                } catch (Exception $e) {
                        echo 'Exception when calling TextMagicApi->doCarrierLookup: ', $e->getMessage(), PHP_EOL;
                }
        }

        /**
	 * Check if STC Contacts exists if not create the list.
         * Add M<obile Phone Number to Contact List
	 *
	 * @since  2.5.5
	 */
        public static function addPhoneToSTCContactList($data, $id){

                $page = 1; $limit = 10;
                $query = "STC Contacts";
                $onlyMine = 0; $onlyDefault = 0; $orderBy = "id"; $direction = "desc";

                try {
                        // first we need to find the id of the STC Contact list
                        $result = self::$textmagic_api->searchLists($page, $limit, $ids, $query, $onlyMine, $onlyDefault, $orderBy, $direction);
                        if (count($result['resources']) === 0) {
                                // STC Contacts List does not exists so we need to create the List.
                                $input = new CreateListInputObject();
                                // Required parameters
                                $input->setName("STC Contacts");
                                try {
                                        // create STC Contacts List
                                        $result = self::$textmagic_api->createList($input);
                                        // do the search lists again to get result to be the same as if the list was found
                                        $result = self::$textmagic_api->searchLists($page, $limit, $ids, $query, $onlyMine, $onlyDefault, $orderBy, $direction);
                                } catch (Exception $e) {
                                        echo 'Exception when calling TextMagicApi->createList: ', $e->getMessage(), PHP_EOL;
                                }                                
                        }
                        // there should be only one array for STC Contacts
                        foreach($result['resources'] as $resource) {
                                if ($resource['name'] === 'STC Contacts') {
                                       // now we can add the new mobile phone number to the conatct list
                                        $input = new CreateContactInputObject();
                                        $input->setPhone(substr($data['stc_mobile_phone'],1));
                                        $input->setEmail($data['email']);
                                        $input->setLists($resource['id']);
                                        try {
                                                $result = self::$textmagic_api->createContact($input);
                                                return $result;
                                        } catch (Exception $e) {
                                                echo 'Exception when calling TextMagicApi->createContact: ', $e->getMessage(), PHP_EOL;
                                        }
                                        break;
                                }
                        }
                } catch (Exception $e) {
                        echo 'Exception when calling TextMagicApi->searchLists: ', $e->getMessage(), PHP_EOL;
                }


        }
        
}
