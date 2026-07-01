<?php
final class WPB_Email_Controller
{
    public function edit_email_details(WP_REST_Request $request)
    {

        $data = $request->get_body_params();

        //update email data
        $success = wpb_update_email($data);

        // Check if the update was successful
        if ($success) {
            $response_data = [
                "status"   => 'success',
                "data"     => $data,
                "message"  => esc_html__("Email Updated Successfully.",'wpbookit'),
            ];
        } else {
            $response_data = [
                "status"   => 'error',
                "message"  => esc_html__("Failed to update email details.",'wpbookit'),
            ];
        }
        
        wp_send_json($response_data);
    }

    public function get_email_details(WP_REST_Request $request)
    {
        $data = $request->get_body_params();
        $id = $data['id'];
        if (!empty($id)) {
            $result = wpb_get_email($id);

            $slug = $result[0]['emails_title'];
            $path = $this->generateSlug($slug);
            $email_key = str_replace(' ', '_', strtolower($slug));

            $email_dynamic_keys = wpb_get_email_template_dynamic_keys($email_key);

            $template_path = $this->wpb_get_email_template_path($path);
            $result[0]['template_path'] = $template_path;
            $result[0]['email_dynamic_keys'] = $email_dynamic_keys;
            
            wp_send_json_success($result);
            exit;
        } else {
            wp_send_json_error('email id is not defind');
            exit;
        }

    }

    public function email_status_update(WP_REST_Request $request)
    {
        $data = $request->get_body_params();
        $success = wpb_update_email($data);
        if ($success) {
            $response_data = [
                "status"   => 'success',
                "message"  => esc_html__("Email Status Updated Successfully.",'wpbookit'),
            ];
            wp_send_json($response_data);
            exit;
        } else {
            $response_data = [
                "status"   => 'error',
                "message"  => esc_html__("Failed to update email status.",'wpbookit'),
            ];
            wp_send_json($response_data);
            exit;
        }
    }

    public function generateSlug($title)
    {
        $slug = strtolower($title);
        $slug = str_replace(' ', '-', $slug);
        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);
        $slug .= '.php';
        return $slug;
    }

    public function wpb_get_email_template_path($template_name)
    {

        $default_path = IQWPB_PLUGIN_PATH . 'templates/emails/';
        $template = locate_template(
            array(
                trailingslashit('wpbookit') . 'emails/'.$template_name,
                'emails/'.$template_name,
            )
        );
        if (!file_exists($template))
            $template = $default_path . $template_name;

        $relative_path = str_replace(ABSPATH, '', $template);

        if (!file_exists($template)) {
            return sprintf(
                // translators: template path placeholder:0
                __('%s does not exist.','wpbookit'),
                '<code>' . $relative_path . '</code>'
            );
        }


        return $relative_path;
    }

}
