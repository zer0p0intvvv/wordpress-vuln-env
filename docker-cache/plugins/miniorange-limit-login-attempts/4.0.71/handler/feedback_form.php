<?php
class Mo_lla_FeedbackHandler
{
    function __construct()
    {
        add_action('admin_init', array($this, 'mo_wpns_feedback_actions'));
    }

    function mo_wpns_feedback_actions()
    {

        global $moWpnsUtility, $mo_lla_dirName;
        if (current_user_can('manage_options') && isset($_POST['option'])) {


            switch ($_REQUEST['option']) {

                case "mo_wpns_skip_feedback_limit_login":
                  $this->wpns_handle_skip_feedback($_POST);						break;
                case "mo_wpns_feedback_limit_login":
                  $this->wpns_handle_feedback($_POST);				            break;

            }
        }
    }

    function wpns_handle_skip_feedback($postdata){



        if(MO2F_TEST_MODE_LIMIT_LOGIN_LIMIT_LOGIN){
            deactivate_plugins(dirname(dirname(__FILE__ ))."\\mo_limit_login_widget.php");
            return;
        }


        $user = wp_get_current_user();
        $message = 'Plugin Deactivated';
        $deactivate_reason_message = array_key_exists('wpns_query_feedback', $_POST) ? htmlspecialchars($_POST['wpns_query_feedback']) : false;

        $activation_date = get_site_option('limitlogin_activated_time');
        $current_date = time();
        $diff = $activation_date - $current_date;
        if($activation_date == false){
            $days = 'NA';
        }
        else{
            $days = abs(round($diff / 86400));
        }

        $feedback_option = $_POST['option'];
        if ($feedback_option != "mo_wpns_rating")
        {
            $message = "Plugin Deactivated V:[".LIMITLOGIN_VERSION."] - Feedback Skipped";
        }

        if ($feedback_option != "mo_wpns_rating")
        {
            $reply_required = '';
            if (isset($_POST['get_reply']))
                $reply_required = htmlspecialchars($_POST['get_reply']);

            if (empty($reply_required)) {
                $reply_required = "don't reply";
                $message .= ' &nbsp; [Reply:<b style="color:red";>' . $reply_required . '</b>,';
            } else {
                $reply_required = "yes";
                $message .= '[Reply:' . $reply_required . ',';
            }
        }

        else
        {
            $message ='[' ;
        }

        $message .= 'D:' . $days . ']';

        $message .= ', Feedback : ' . $deactivate_reason_message . '';

        $rate_value = "-";

        if (isset($_POST['rate']))
        {
                $rate_value = htmlspecialchars($_POST['rate']);
        }
        $message .= ', [Rating :' . $rate_value . ']';

        $phone = get_option('mo_wpns_admin_phone');
        $feedback_reasons = new Mo_lla_MocURL();
        $email = $user->user_email;




        global $moWpnsUtility;
        if (!is_null($feedback_reasons)) {
            if (!$moWpnsUtility->is_curl_installed()) {
                deactivate_plugins(dirname(dirname(__FILE__ ))."\\mo_limit_login_widget.php");
                do_action('wpns_show_message','Plugin Deactivated','SUCCESS');
                wp_redirect('plugins.php');
            } else {
                $subject = "Deactivate [Feedback Skipped] WordPress Limit Login Attempts Plugin";
               json_decode($feedback_reasons->send_email_alert($email, $phone, $message,$subject), true);
                deactivate_plugins(dirname(dirname(__FILE__ ))."\\mo_limit_login_widget.php");
                do_action('wpns_show_message','Plugin Deactivated','SUCCESS');
            }
        }
    }

    function wpns_handle_feedback($postdata)
    {
        if(MO2F_TEST_MODE_LIMIT_LOGIN_LIMIT_LOGIN){
            deactivate_plugins(dirname(dirname(__FILE__ ))."\\mo_limit_login_widget.php");
            return;
        }

        $user = wp_get_current_user();
        $message = 'Plugin Deactivated';
        $deactivate_reason_message = array_key_exists('wpns_query_feedback', $_POST) ? htmlspecialchars($_POST['wpns_query_feedback']) : false;

        $activation_date = get_site_option('limitlogin_activated_time');
        $current_date = time();
        $diff = $activation_date - $current_date;
        if($activation_date == false){
            $days = 'NA';
        }
        else{
            $days = abs(round($diff / 86400));
        }

        $feedback_option = $_POST['option'];
        if ($feedback_option != "mo_wpns_rating")
        {
            $message = "Plugin Deactivated V:[".LIMITLOGIN_VERSION."]";
        }

        if ($feedback_option != "mo_wpns_rating")
        {
            $reply_required = '';
            if (isset($_POST['get_reply']))
                $reply_required = htmlspecialchars($_POST['get_reply']);

            if (empty($reply_required)) {
                $reply_required = "don't reply";
                $message .= ' &nbsp; [Reply:<b style="color:red";>' . $reply_required . '</b>,';
            } else {
                $reply_required = "yes";
                $message .= '[Reply:' . $reply_required . ',';
            }
        }

        else
        {
            $message ='[' ;
        }
        $message .= 'D:' . $days . ',';


        $message .= 'Feedback : ' . $deactivate_reason_message . '';

        if (isset($_POST['rate']))
            $rate_value = htmlspecialchars($_POST['rate']);

        $message .= ', [Rating :' . $rate_value . ']';



        $email = $_POST['query_mail'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = get_option('mo_wpns_admin_email');
            if (empty($email))
                $email = $user->user_email;
        }
        $phone = get_option('mo_wpns_admin_phone');
        $feedback_reasons = new Mo_lla_MocURL();


        global $moWpnsUtility;
        if (!is_null($feedback_reasons)) {
            if (!$moWpnsUtility->is_curl_installed()) {
                deactivate_plugins(dirname(dirname(__FILE__ ))."\\mo_limit_login_widget.php");
                wp_redirect('plugins.php');
            } else {
                $subject = "Feedback : WordPress Limit Login Attempts Plugin";
                $submited = json_decode($feedback_reasons->send_email_alert($email, $phone, $message,$subject), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    if (is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR') {
                        do_action('wpns_show_message',$submited['message'],'ERROR');

                    } else {
                        if ($submited == false) {
                            do_action('wpns_show_message','Error while submitting the query.','ERROR');
                        }
                    }
                }

                deactivate_plugins(dirname(dirname(__FILE__ ))."\\mo_limit_login_widget.php");
                do_action('wpns_show_message','Thank you for the feedback.','SUCCESS');

            }
        }
    }

}new Mo_lla_FeedbackHandler();
