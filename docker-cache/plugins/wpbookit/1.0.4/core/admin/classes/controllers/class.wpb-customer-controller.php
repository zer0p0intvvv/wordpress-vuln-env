<?php

final class WPB_Customer_Controller
{

    public function get_customer_list(WP_REST_Request $request)
    {
        $args = array(
            'status'        => [],
            'number'      => $request->get_param('length'),
            'order'         => 'DESC',
            'order_by'      => 'user_registered',
            'role'          => WPBOOKIT()->helpers->get_customer_role(),
            'paged'         => intval($request->get_param('start') / $request->get_param('length')) + 1,
        );

        if ($request->has_param('customer_search')) {
            $args['search'] = '*' . esc_attr($request->get_param('customer_search')) . '*';
            $args['search_columns'] = array('display_name', 'user_email');
        }

        if ($request->has_param('order')) {
            if ($request->get_param('order')[0]['name'] == 'customer-dob') {
                $args['meta_key'] = 'date_of_birth';
                $args['order'] = strtoupper($request->get_param('order')[0]['dir'] ?? '');
                $args['orderby'] = 'meta_value';
            }elseif($request->get_param('order')[0]['name'] == 'customer-name'){
                $args['order'] = strtoupper($request->get_param('order')[0]['dir'] ?? '');   
                $args['orderby'] = 'email';
            }elseif($request->get_param('order')[0]['name'] == 'customer-id'){
                $args['order'] = strtoupper($request->get_param('order')[0]['dir'] ?? '');   
                $args['orderby'] = 'ID';
            } 
            else {
                $args['order'] = strtoupper($request->get_param('order')[0]['dir'] ?? '');
                $args['orderby'] = $request->get_param('order')[0]['name'] ?? "";
            }
        }

        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();
        $data = array_map(function ($item) {
            $user__profile_url =  wp_get_attachment_url(get_user_meta($item->ID, "wp_user_avatar", true) ?? 0);
            return [
                'id'        => $item->ID,
                'custom_note' => empty(get_user_meta($item->ID, "custom_note", true)) ? '' : get_user_meta($item->ID, "custom_note", true),
                'name'      => $item->display_name,
                'profile_img' => $user__profile_url === false ? get_avatar_url(0, ['size' => 50]) : $user__profile_url,
                'is_customer_image' => $user__profile_url === false ? false : true,
                'tddob'     => get_user_meta($item->ID, "date_of_birth", true),
                'dob'       => empty(get_user_meta($item->ID, "date_of_birth", true)) ? '-' : wpb_get_formated_date_time(get_user_meta($item->ID, "date_of_birth", true), ''),
                'phone'     => empty(get_user_meta($item->ID, "phone", true)) ? '-' : get_user_meta($item->ID, "phone", true),
                'email'     => $item->user_email,
                'gender'    => empty(get_user_meta($item->ID, "gender", true)) ?  '-' : get_user_meta($item->ID, "gender", true),
                'dialCode'    => empty(get_user_meta($item->ID, "dialCode", true)) ?  '' : get_user_meta($item->ID, "dialCode", true),
                'iso2'    => empty(get_user_meta($item->ID, "iso2", true)) ?  '' : get_user_meta($item->ID, "iso2", true),
                'user_registred' => wpb_get_formated_date_time($item->user_registered, ''),
                'actions'   => [
                    "edit"      => true,
                    "delete"    => true,
                ],
            ];
        }, $users);

        $user_count = count_users()['avail_roles'][WPBOOKIT()->helpers->get_customer_role()];
        wp_send_json(array(
            "recordsTotal" =>  $user_count ?? 0,
            "recordsFiltered" =>   $user_count ?? 0,
            "data" => $data
        ));
    }
    public function refresh_table_callback(WP_REST_Request $request)
    {
        $paged              = $request->get_param('paged') ?? 1;
        $customerObj        = new WPB_Settings_Customer();
        $columns            = $customerObj->get_table_column();
        $customerObj->paged = $paged;
        $customer           = $customerObj->get_customers($paged);
        $customers          = $customer->get_results();
        $total_users         = $customer->get_total();
        $customer_page_total = count($customers);
        $total_pages         = ceil($total_users / $customerObj->per_page);
        $customer_total_page = ($customer_page_total < $customerObj->per_page) ? $customer_page_total : $customerObj->per_page;
        $pagination_output  = wpb_get_pagination($total_pages, $customerObj->paged, 'customer');



        ob_start();
        require_once IQWPB_PLUGIN_PATH . "core/admin/views/settings/html-admin-settings-customer.php";
        $html_content = ob_get_clean();

        wp_send_json_success(
            array(
                "html_content" => $html_content,
                "paged" => $paged,
            )
        );
    }

    public function delete_customer_callback(WP_REST_Request $request)
    {
        $user_id = $request->get_param("delete_user_id");
        if (!wp_verify_nonce($request->get_param('_ajax_nonce'), 'ajax_post')) :
            if (!is_numeric($user_id)) :
                $response_data = [
                    "status"    => 'error',
                    "message"   => esc_html__('Invalid user ID', 'wpbookit'),
                ];
            endif;
            // Delete the user
            if (wp_delete_user($user_id)) :
                $response_data = [
                    "status" => 'success',
                    "message" => esc_html__("Customer Deleted Successfully.", 'wpbookit'),
                ];
            else :
                $response_data = [
                    "status" => 'error',
                    "message" => esc_html__("Error Deleting Customer.", 'wpbookit'),
                ];
            endif;
        else :
            $response_data = [
                "status"    => 'error',
                "message"   => esc_html__("Token Verification Failed.", 'wpbookit'),
            ];
        endif;
        wp_send_json($response_data);
    }


    public function edit_newdata_customer_callback(WP_REST_Request $request)
    {
        $data = $request->get_params();
        $image_url = $request->get_file_params();

        // Verify nonce first
            // Check user authorization
            if (!is_user_logged_in()) {
                wp_send_json([
                    "status"    => 'error',
                    "message"   => esc_html__("You must be logged in to edit customer data.", 'wpbookit'),
                ]);
                return;
            }

            $first_name     = isset($data["first-name"]) ? sanitize_text_field($data["first-name"]) : '';
            $last_name      = isset($data["last-name"]) ? sanitize_text_field($data["last-name"]) : '';
            $display_name   = trim($first_name . ' ' . $last_name);
            $edit_email     = sanitize_email($data["email"]);
            $edit_gender    = isset($data["gender"]) ? sanitize_text_field($data["gender"]) : '';
            $edit_dob       = isset($data["dob"]) ? sanitize_text_field($data["dob"]) : '';
            $edit_phone     = isset($data["phone"]) ? sanitize_text_field($data["phone"]) : '';
            $edit_notes     = isset($data["notes"]) ? sanitize_text_field($data["notes"]) : '';
            $user_id        = isset($data["edit-customer-id"]) ? absint($data["edit-customer-id"]) : null;
            $avatar         = isset($image_url["add-image"]) ? $image_url["add-image"] : null;
            $default_img_url = IQWPB_PLUGIN_URL . "core/admin/assets/images/avatar.png";
            $iso2           = isset($data["iso2"]) ? sanitize_text_field($data["iso2"]) : '';
            $dialCode       = isset($data["dialCode"]) ? sanitize_text_field($data["dialCode"]) : '';

            // Security check: Only allow admins or the customer themselves to update their profile
            $current_user_id = get_current_user_id();
            if (!current_user_can('edit_users') && $current_user_id !== $user_id) {
                wp_send_json([
                    "status"    => 'error',
                    "message"   => esc_html__("You don't have permission to edit this customer.", 'wpbookit'),
                ]);
                return;
            }

            if (is_null($user_id)) :
                $response_data = [
                    "status"    => 'error',
                    "message"   => esc_html__("Missing Customer ID", 'wpbookit'),
                ];

            else :
                $tmp_file_path = $avatar["tmp_name"];
                if ("" === $tmp_file_path) :
                    $response = wp_update_user(
                        apply_filters(
                            "wpb_update_customer",
                            [
                                "ID"            => $user_id,
                                "display_name"  => $display_name,
                                "user_email"    => $edit_email,
                                "first_name"    => $first_name,
                                "last_name"     => $last_name,
                                "meta_input"    => [
                                    "phone"         => $edit_phone,
                                    "custom_note"   => $edit_notes,
                                    "gender"        => $edit_gender,
                                    "date_of_birth" => $edit_dob,
                                    'iso2' => $iso2,
                                    'dialCode' => $dialCode,
                                    "wp_user_avatar" => $default_img_url
                                ],
                            ]
                        )
                    );

                    if (is_wp_error($response)) :
                        $response_data = [
                            "status" => 'error',
                            "message" => esc_html__("Failed to update user.", 'wpbookit'),
                        ];

                    elseif (!$response) :
                        $response_data = [
                            "status"    => 'error',
                            "message"   => esc_html__("Customer update failed.", 'wpbookit'),
                        ];

                    else :
                        $response_data = [
                            "status"    => 'success',
                            "message"   =>  esc_html__("Customer Updated Successfully.", 'wpbookit'),
                        ];

                    endif;

                else :
                    $avatar_id = $this->image_upload_handle($user_id, $tmp_file_path, $avatar);
                    if (!$avatar_id) :
                        $response_data = [
                            "status"    => 'error',
                            "message"   => esc_html__('Error sideloading image', 'wpbookit'),
                        ];

                    else :
                        $response = wp_update_user([
                            "ID"            => $user_id,
                            "display_name"  => $display_name,
                            "user_email"    => $edit_email,
                            "first_name"    => $first_name,
                            "last_name"     => $last_name,
                            "meta_input"    => [
                                "phone"         => $edit_phone,
                                "custom_note"   => $edit_notes,
                                "gender"        => $edit_gender,
                                "date_of_birth" => $edit_dob,
                                'iso2' => $iso2,
                                'dialCode' => $dialCode,
                            ],
                        ]);

                        if (is_wp_error($response)) :
                            $response_data = [
                                "status"    => 'error',
                                "message"   => esc_html__("Failed to update user.", 'wpbookit'),
                            ];

                        elseif (!$response) :
                            $response_data = [
                                "status" => 'error',
                                "message" => esc_html__("User Update Failed.", 'wpbookit'),
                            ];

                        else :

                            $response_data = [
                                "status" => 'success',
                                "message" => esc_html__("User Updated Successfully.", 'wpbookit'),
                            ];

                        endif;
                    endif;
                endif;
            endif;
       
        wp_send_json($response_data);
    }
    public function add_newdata_customer_callback(WP_REST_Request $request)
    {

        $data               = $request->get_params();
        $image_url          = $request->get_file_params();

        $first_name         = sanitize_text_field($data["first-name"]);
        $last_name          = sanitize_text_field($data["last-name"]);
        $email              = strtolower(sanitize_email($data["email"]));
        $phone              = sanitize_text_field($data["phone"]);
        $iso2              = sanitize_text_field($data["iso2"]);
        $dialCode              = sanitize_text_field($data["dialCode"]);
        $gender             = sanitize_text_field($data["gender"]);
        $dob                = sanitize_text_field($data["dob"]);
        $notes              = sanitize_text_field($data["notes"]);
        $avatar             = isset($image_url["add-image"]) ? $image_url["add-image"] : null;
        // $user_login         = sanitize_user(strtolower($first_name . "_" . $last_name));
        $password           = wp_generate_password();
        // $hashed_password    = wp_hash_password($password);

        $user_data = apply_filters(
            "wpb_insert_customer",
            [
                "user_login"    => $email, // The username for the new user
                "user_email"    => $email, // The email address of the new user
                "user_pass"     => $password, // The password for the new user
                "first_name"    => $first_name, // The first name of the new user
                "last_name"     => $last_name, // The last name of the new user
                "role"          => WPBOOKIT()->helpers->get_customer_role(),
                "meta_input"    => [
                    "phone"         => $phone,
                    "gender"        => $gender,
                    "date_of_birth" => $dob,
                    "custom_note"   => $notes,
                    'iso2' => $iso2    ,
                    'dialCode' => $dialCode    ,
                ],
                "user_registered" => date("Y-m-d H:i:s"), // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date 
            ]
        );

        do_action('wpb_insert_customer', $user_data);

        $user_id = wp_insert_user($user_data);

        if (is_wp_error($user_id)) :
            wp_send_json([
                "status" => 'error',
                "message" => str_replace("username", "email", $user_id->get_error_message()),
            ]);
        endif;
        $wp_user_instance = get_user_by('ID', $user_id);

        do_action('wpb_customer_registration', $wp_user_instance,$password);

        $tmp_file_path = $avatar["tmp_name"];
        if ("" === $tmp_file_path) :
            $response_data = [
                "status"        => 'success',
                "message"       => esc_html__("Customer Added Successfully.", 'wpbookit'),
            ];


        else :
            $avatar_id = $this->image_upload_handle($user_id, $tmp_file_path, $avatar);
            if (!$avatar_id) :
                $response_data = [
                    "status"    => 'error',
                    "message"   => esc_html__("Error image Uploading", 'wpbookit'),
                ];

            else :
                $response_data = [
                    "status"        => 'success',
                    "message"       => esc_html__("Customer Added Successfully.", 'wpbookit'),
                ];

            endif;
        endif;
        wp_send_json($response_data);
    }

    public function image_upload_handle($user_id, $tmp_file_path, $image)
    {
        $file_name             = basename($image["name"]);
        $uploads_dir           = wp_upload_dir();
        $destination_file_path = $uploads_dir["path"] . "/" . $file_name;

        // Move the uploaded file to the destination path
        if (move_uploaded_file($tmp_file_path, $destination_file_path)) :  // phpcs:ignore  Generic.PHP.ForbiddenFunctions.Found 
            $file_url = $uploads_dir["url"] . "/" . $file_name;

            // Attempt to sideload the image
            $attachment_id  = media_sideload_image($file_url, "", "avatar", "id");
            $attachment_url = wp_get_attachment_url($attachment_id);

            // Check if sideloading was successful
            if (!is_wp_error($attachment_id) && $attachment_id) :
                // Image successfully sideloaded, update user meta with attachment ID
                update_user_meta($user_id, "wp_user_avatar", $attachment_id);
                return $attachment_url; // Return the attachment ID
            else :
                // Sideload failed, handle the error
                return false; // Return an error message
            endif;
        endif;
        // Move uploaded file failed, handle the error
        return false;
    }

    public function login_customer(WP_REST_Request $request)
    {
        // Retrieve email and password from request parameters
        $email = sanitize_text_field($request->get_param('email'));
        $password = sanitize_text_field($request->get_param('password'));
       
        // Attempt authentication
        $auth_success = wp_authenticate($email,$password);
    
        // Check if authentication failed
        if (is_wp_error($auth_success)) {
            // Return error response with specific message
            $response_data = [
                "status"    => 'error',
                "redirect"  => '',
                "message"   => esc_html__("Login Failed. Incorrect email or password.", 'wpbookit'),
            ];
        } else {
            // Get user data
            $user_id = $auth_success->data->ID;
            $user_data = get_userdata($user_id);

            // Set current user and authenticate cookie
            wp_set_current_user($user_id, $auth_success->data->user_login);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $auth_success->data->user_login, $auth_success);

            // Get redirect URL
            $page_slug = wpb_get_general_settings()['login_redirect'];
            $page = get_page_by_path($page_slug);
            $redirect_url = $page ? get_permalink($page->ID) : get_home_url();

            // Success response
            $response_data = [
                "status"    => 'success',
                "redirect"  => $redirect_url,
                "message"   => esc_html__("Logged in successfully.", 'wpbookit'),
            ];
        }

        // Send JSON response
        wp_send_json($response_data);
    }

    public function register_customer(WP_REST_Request $request)
    {
        $register_username = sanitize_text_field($request->get_param('register_email'));
        $register_email = sanitize_text_field($request->get_param('register_email'));
        $register_password = sanitize_text_field($request->get_param('register_password'));

        $exists = email_exists($register_email);
        if ($exists) {
            wp_send_json([
                "status"        => 'error',
                "message"       => esc_html__("That E-mail is registered kindly login.", 'wpbookit')
            ]);
        }

        $user_data = apply_filters(
            "wpb_register_customer",
            [
                "user_login"    => $register_username, // The username for the new user
                "user_email"    => $register_email, // The email address of the new user
                "user_pass"     => $register_password, // The password for the new user
                "role"          => WPBOOKIT()->helpers->get_customer_role(),
                "user_registered" => date("Y-m-d H:i:s"), // phpcs:ignore  WordPress.DateTime.RestrictedFunctions.date_date 
            ]
        );

        do_action('wpb_insert_customer', $user_data);
        $user_id = wp_insert_user($user_data);

        // Attempt authentication
        $auth_success = wp_authenticate($register_email, $register_password);
        $user_id = $auth_success->data->ID;

        // Set current user and authenticate cookie
        wp_set_current_user($user_id, $auth_success->data->user_login);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $auth_success->data->user_login, $auth_success);

        $redirect_url_link = '';
        $page = wpb_get_general_settings()['login_redirect'];

        $post_id = get_page_by_path($page)->ID; // Get the post ID by post name

        $redirect_url_link = ($page !== 'same-page') ? get_permalink($post_id) : '';

        $redirect_url = apply_filters(
            'wpb_booking_confirmation_email',
            ['redirect_url_link' => $redirect_url_link]
        );

        $wp_user_instance = get_user_by('ID', $user_id);

        do_action('wpb_customer_registration', $wp_user_instance,$register_password);

        if (is_wp_error($user_id)) {
            $error_message = $user_id->get_error_message();
            $response_data = [
                "status" => 'error',
                "redirect"  => '',
                "message" => $error_message,
            ];
        } else {
            $response_data = [
                "status"        => 'success',
                "redirect"      => $redirect_url,
                "message"       => esc_html__("Registaration Successfull.", 'wpbookit'),
            ];
        }
        wp_send_json($response_data);
    }
}
