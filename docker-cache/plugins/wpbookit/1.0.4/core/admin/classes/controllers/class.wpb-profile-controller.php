<?php
final class WPB_Profile_controller
{
    public function edit_profile_data(WP_REST_Request $request)
    {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            $this->send_error_response("You must be logged in to edit profile data.");
            return;
        }

        // Verify nonce
        $data = $request->get_params();

        $user_id = isset($data["edit-profile-submit"]) ? absint($data["edit-profile-submit"]) : null;
        $current_user_id = get_current_user_id();

        // Check if user is trying to modify someone else's profile without permission
        if ($user_id !== $current_user_id && !current_user_can('edit_users')) {
            $this->send_error_response("You don't have permission to edit this profile.");
            return;
        }

        if (is_null($user_id)) {
            $this->send_error_response("Missing User ID");
        }

        $first_name = isset($data["first_name"]) ? sanitize_text_field($data["first_name"]) : '';
        $last_name = isset($data["last_name"]) ? sanitize_text_field($data["last_name"]) : '';
        $display_name = trim($first_name . ' ' . $last_name);
        $edit_email = isset($data["email"]) ? sanitize_email($data["email"]) : '';
        $edit_dob = isset($data["date_of_birth"]) ? sanitize_text_field($data["date_of_birth"]) : '';
        $phone = isset($data["phone"]) ? sanitize_text_field($data["phone"]) : '';
        $gender = isset($data["gender"]) ? sanitize_text_field($data["gender"]) : '';
        $pass1 = isset($data["pass1"]) ? sanitize_text_field($data["pass1"]) : '';
        $iso2 = isset($data["iso2"]) ? sanitize_text_field($data["iso2"]) : '';
        $dialCode = isset($data["dialCode"]) ? sanitize_text_field($data["dialCode"]) : '';

        $avatar = $request->get_file_params()['avatar'] ?? null;
        $tmp_file_path = $avatar["tmp_name"] ?? '';

        if (!empty($tmp_file_path)) {
            $avatar_id = $this->handle_image_upload($user_id, $tmp_file_path, $avatar);
            if (!$avatar_id) {
                $this->send_error_response('Error sideloading image');
            }
        }

        $user_data = [
            "ID" => $user_id,
            "display_name" => $display_name,
            "user_email" => $edit_email,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "meta_input" => [
                "date_of_birth" => $edit_dob,
                "phone" => $phone,
                "gender" => $gender,
                'iso2' => $iso2,
                'dialCode' => $dialCode,
            ]
        ];

        if (!empty($pass1)) {
            $user_data["user_pass"] = $pass1;
        }

        $response = wp_update_user($user_data);

        if (is_wp_error($response)) {
            $this->send_error_response("Failed to update user.");
        } elseif (!$response) {
            $this->send_error_response("User Update Failed.");
        }

        $this->send_success_response("User Updated Successfully.");
    }

    public function handle_image_upload($user_id, $tmp_file_path, $image)
    {
        $file_name = basename($image["name"]);
        $uploads_dir = wp_upload_dir();
        $destination_file_path = $uploads_dir["path"] . "/" . $file_name;

        if (move_uploaded_file($tmp_file_path, $destination_file_path)) { // phpcs:ignore  Generic.PHP.ForbiddenFunctions.Found 
            $file_url = $uploads_dir["url"] . "/" . $file_name;

            $attachment_id = media_sideload_image($file_url, "", "avatar", "id");
            $attachment_url = wp_get_attachment_url($attachment_id);

            if (!is_wp_error($attachment_id) && $attachment_id) {
                update_user_meta($user_id, "wp_user_avatar", $attachment_id);
                return $attachment_url;
            } else {
                return false;
            }
        }
        return false;
    }

    private function send_error_response($message)
    {
        wp_send_json_error([
            "status" => esc_html__('Error', 'wpbookit'),
            "message" => esc_html($message),
        ]);
    }

    private function send_success_response($message)
    {
        wp_send_json_success([
            "status" => esc_html__('Success', 'wpbookit'),
            "message" => esc_html($message),
        ]);
    }
}
