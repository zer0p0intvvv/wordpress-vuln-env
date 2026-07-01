<?php
final class WPB_Booking_type_Controller
{
  
    public function add_booking_type(WP_REST_Request $request)
    {
        global $wpdb;
        $request_data = $request->get_params();
        $file_params = $request->get_file_params();
        $edit_image_url = isset($file_params["cover_image_img"]) ? $file_params["cover_image_img"] : null;
        $wpb_booking_type_table = $wpdb->wpb_booking_type;
        $wpb_booking_typemeta_table = $wpdb->wpb_booking_typemeta;
        $booking_type_id = 0;
        $request_data['weekly_data'] = json_decode(stripslashes($request_data['weekly_data']), true);
        $request_data['questions'] = json_decode(stripslashes($request_data['questions']), true);
        $request_data['specific_available_dates'] = json_decode(stripslashes($request_data['specific_available_dates']), true);
        $request_data['unavailable_dates'] = json_decode(stripslashes($request_data['unavailable_dates']), true);
    
        $uniqueArray = [];
        foreach ($request_data['questions'] as $item) {
            $questionId = $item['questionId'];
            if (!isset($uniqueArray[$questionId])) {
                $uniqueArray[$questionId] = $item;
            }
        }
    
        $request_data['description'] = $request_data['description'] ?? ' ';
    
        try {
            if (empty($request_data['title']) || empty($request_data['booking_type'])) {
                throw new Exception('Required fields are missing.');
            }

            if (!empty($edit_image_url["tmp_name"])) {
                $image_upload = $this->image_upload_handle($edit_image_url["tmp_name"], $edit_image_url);
                if (is_wp_error($image_upload)) {
                    wp_send_json_error([
                        'status' => 'error',
                        'message' => __('Fail To Upload Media', 'wpbookit')
                    ]);
                }
            }
    
            if (empty($request_data["id"])) {
                $slug = sanitize_title($request_data['slug']);
            
                $existing_slug = $wpdb->get_var($wpdb->prepare(
                    "SELECT slug FROM $wpb_booking_type_table WHERE slug = %s",
                    $slug
                ));
            
               
                if ($existing_slug) {
                    $original_slug = $slug;
                    $counter = 1;
            
                    do {
                        $slug = $original_slug . '-' . $counter;
            
                        $existing_slug = $wpdb->get_var($wpdb->prepare(
                            "SELECT slug FROM $wpb_booking_type_table WHERE slug = %s",
                            $slug
                        ));
            
                        $counter++;
                    } while ($existing_slug); 
                }
            
                $request_data['slug'] = $slug;
            
                $bokking_type_data = [
                    'name' => $request_data['title'] ?? '',
                    'slug' => $request_data['slug'] ?? '',
                    'description' => wpautop(wp_unslash($request_data['description'])) ?? '',
                    'type' => !empty($request_data['booking_type']) ? $request_data['booking_type'] : "",
                    'unavailable' => (!empty($request_data['unavailable']) && $request_data['unavailable']) ? "1" : "0",
                    'duration' => (!empty($request_data['duration'])) ? $request_data['duration'] : '',
                    'url' => (!empty($request_data['url'])) ? $request_data['url'] : '',
                    'status' => 1,
                ];
            
                $inserted = $wpdb->insert(
                    $wpb_booking_type_table,
                    $bokking_type_data,
                    [
                        '%s', // name
                        '%s', // slug
                        '%s', // description
                        '%s', // type
                        '%d', // unavailable
                        '%d', // duration
                        '%s', // url
                        '%d'  // status
                    ]
                );
            
                // Get the inserted booking type ID
                $booking_type_id = $wpdb->insert_id;
            
                if (!$inserted) {
                    throw new Exception('Failed to insert booking type data.');
                }
            } else {
                // Update existing record
                $booking_type_id = $request_data["id"];
                $wpb_edit_booking_type_data = [
                    'name' => $request_data['title'] ?? '',
                    'slug' => $request_data['slug'] ?? '',
                    'description' => wpautop( stripslashes($request_data['description'] ?? '') ),
                    'type' => !empty($request_data['booking_type']) ? $request_data['booking_type'] : "",
                    'unavailable' => (!empty($request_data['unavailable']) && $request_data['unavailable']) ? "1" : "0",
                    'duration' => (!empty($request_data['duration'])) ? $request_data['duration'] : '',
                    'url' => (!empty($request_data['url'])) ? $request_data['url'] : '',
                ];
                $updated = $wpdb->update(
                    $wpb_booking_type_table,
                    $wpb_edit_booking_type_data,
                    ['id' => $booking_type_id]
                );
                if ($updated === false) {
                    throw new Exception('Failed to update booking type data.');
                } elseif ($updated === 0) {
                }
            }

            if ($booking_type_id) {
                // Save custom meta data of booking type
                $unique_days = [];
                $new_data = [];

                foreach ($request_data['weekly_data'] as $weekly_data_item) {
                    $day_name = $weekly_data_item['weekly_available_dates'];
                    if (!in_array($day_name, $unique_days)) {
                        $unique_days[] = $day_name;
                    }
                    if (!isset($new_data[$day_name])) {
                        $new_data[$day_name] = [];
                    }
                    foreach ($weekly_data_item['dayTimeRanges'] as $slot) {
                        $new_data[$day_name][] = $slot;
                    }
                }
                
                // Convert the array to a Laravel collection
                $new_data = collect($new_data)->map(function($day){
                    return collect($day)->sortBy('timeFrom')->values();
                })->toArray();


                
                $new_unavailable_dates = array();
                if (!empty($request_data['unavailable_dates'])) {
                    foreach ($request_data['unavailable_dates'] as $entry) {
                        $date = $entry['date'];
                        $from = $entry['from'];
                        $to = $entry['to'];
                        $new_unavailable_dates[] = array(
                            'date' => $date,
                            'from' => $from,
                            'to' => $to,
                        );
                    }
                }
                

                $new_specific_available_dates = array();
                foreach ($request_data['specific_available_dates'] as $entry) {
                    $date = $entry['date'];
                    $from = $entry['from'];
                    $to = $entry['to'];
                    $new_specific_available_dates[] = array('date'=> $date,'from' => $from, 'to' => $to);
                }

                // Define the meta data to be saved
                $meta_data = array(
                    'how_far' => $request_data['how_far'] ?? '',
                    'booking_threshold' => $request_data['booking_threshold'] ?? '',
                    'maximum_buffer' => $request_data['maximum_buffer'] ?? '',
                    'maximum_booking' => $request_data['maximum_booking'] === 'true' ? 'true' : 'false',
                    'slots_per_booking' => $request_data['slots_per_booking'] === 'true' ? 'true' : 'false',
                    'location' => $request_data['location'] ?? '',
                    'guest_invite' => $request_data['guest_invite'] === 'true' ? 'true' : 'false',
                    'show_remaining_slot' => !empty($request_data['show_remaining_slot']) && $request_data['show_remaining_slot'] === 'true' ? 'true' : 'false',
                    'email_reminder' => $request_data['email_reminder'] === 'true' ? 'true' : 'false',
                    'redirection' => $request_data['redirection'] === 'true' ? 'true' : 'false',
                    'private_mode' => $request_data['private_mode'] === 'true' ? 'true' : 'false',
                    'weekly_days' => wp_json_encode($unique_days),
                    'weekly_time_slots' => wp_json_encode($new_data),
                    'unavailable_dates' => wp_json_encode($new_unavailable_dates),
                    'specific_dates' => wp_json_encode($new_specific_available_dates),
                    'questions'     => !empty($uniqueArray) ? wp_json_encode(array_values($uniqueArray)) : "",
                    'staff'         => get_current_user_id() ,
                    'background_color'         => !empty($request_data['background_color']) ? $request_data['background_color'] : '',
                    'meeting_link_type'         => !empty($request_data['meeting_link_type']) ? $request_data['meeting_link_type'] : '',
                    'demo_with_whom_label'         => !empty($request_data['demo_with_whom_label']) ? $request_data['demo_with_whom_label'] : '',
                );
                
                // print_r($meta_data);
                
                if(empty($request_data['cover_image_id']) ){
                    $meta_data ['cover_image_id'] = null;
                }
                if(!empty($edit_image_url["tmp_name"])){
                    $meta_data ['cover_image_id'] = $image_upload;
                }
               
                if ($request_data['location']) {
                    $meta_data["location_source"] = $request_data[$request_data['location']];
                    $meta_data["link_type"] = $request_data['link_type'];
                }

                $meta_data["maximum_booking"] = !empty($request_data['maximum_booking_number']) ? $request_data['maximum_booking_number'] : null;
                $meta_data["booking_number_by"] = !empty($request_data['booking_number_by']) ? $request_data['booking_number_by'] : 'days';
                
                $meta_data["slots_per_booking_number"]=null;
                $meta_data['enable_group_booking']=$request_data['enable_group_booking'];
                if($request_data['enable_group_booking']=='true'){
                    $meta_data["slots_per_booking_number"] = !empty($request_data['slots_per_booking_number']) ? $request_data['slots_per_booking_number'] : null;
                }

                if ($request_data['charge'] === 'true') {
                    $meta_data["price"] = !empty($request_data['price']) ? $request_data['price'] : 0;
                }else{
                    $meta_data["price"] = 0;
                }
                
                if ($request_data['redirection'] === 'true') {
                    $meta_data["redirection_url"] = !empty($request_data['redirection_url']) ? $request_data['redirection_url'] : "";
                }

                if (!empty($request_data['email_reminder']) && $request_data['email_reminder']) {
                    $meta_data["email_content_editor"] = !empty($request_data['email_content_editor']) ? $request_data['email_content_editor'] : "";
                }

                if (!empty($request_data['booking_type'])) {
                    if ($request_data['booking_type'] == 'weekly') {
                        $request_data['weekly_time_slots'] = wp_json_encode([]);
                        $request_data['weekly_days'] = wp_json_encode([]);
                    } elseif ($request_data['booking_type'] == 'specific_date') {
                        $request_data['specific_dates'] = wp_json_encode([]);
                    }
                }
                
                foreach (apply_filters('wpb_update_booking_type_meta',$meta_data,$request_data) as $meta_key => $meta_value) {
                    update_metadata('wpb_booking_type', $booking_type_id, $meta_key, $meta_value);
                }
                do_action('wpb_after_booking_type_background_color', $booking_type_id, $request_data['booking_type_location']);
                $message = !empty($request_data["id"]) ? __("Booking Type Updated Successfully", "wpbookit") : __("Booking Type Created Successfully", "wpbookit");
                wp_send_json_success(
                    array(
                        'status'  => 'success',
                        'message' => $message
                    )
                );
            } else {
                wp_send_json_error(
                    array(
                        'status'  => 'error',
                        'message' => __('Booking Type Creation Failed', 'wpbookit')
                    )
                );
            }
        } catch (Exception $e) {
            wp_send_json_error(
                array(
                    'status'  => 'error',
                    'message' => $e->getMessage()
                )
            );
        }
    }

    public function delete_booking_type(WP_REST_Request $request)
    {
        global $wpdb;
        $request_data = $request->get_params();
        $wpb_booking_type_table = $wpdb->prefix . "wpb_booking_type";
        $wpb_booking_typemeta_table = $wpdb->prefix . "wpb_booking_typemeta";

        try {
            // Validate request data
            if (empty($request_data['id'])) {
                throw new Exception('Booking Type ID Missing.');
            }

            $booking_type_id = $request_data['id'];
            $shortcode_page_id  = get_metadata( 'wpb_booking_type', $booking_type_id, 'booking_type_page_id', true );
            if( $shortcode_page_id ) :
                wp_delete_post( $shortcode_page_id, true);
            endif;

            $wpdb->delete(
                $wpb_booking_typemeta_table,
                array('wpb_booking_type_id' => $booking_type_id),
                array('%d')
            );
            $wpdb->delete(
                $wpb_booking_type_table,
                array('id' => $booking_type_id),
                array('%d')
            );

            $deleted_rows = $wpdb->rows_affected;
            if ($deleted_rows > 0) {
                wp_send_json_success(
                    array(
                        'status'  => 'success',
                        'message' => __('Booking Type Deleted Successfully.', 'wpbookit')
                    )
                );
            } else {
                wp_send_json_error(
                    array(
                        'status'  => 'error',
                        'message' => __('No Booking Type Found.', 'wpbookit')
                    )
                );
            }
        } catch (Exception $e) {
            wp_send_json_error(
                array(
                    'status'  => 'error',
                    'message' => $e->getMessage()
                )
            );
        }
    }

    public function clone_booking_type(WP_REST_Request $request)
    {
        global $wpdb;
        $request_data = $request->get_params();

        try {
            // Validate request data
            if (empty($request_data['id'])) {
                throw new Exception('Booking Type ID Missing.');
            }

            $original_booking_type_id = $request_data['id'];

          
            // Retrieve the original booking type data
            $original_booking_type_data = wpb_get_booking_type((int)$original_booking_type_id, ["name", "slug", "description", "type", "unavailable", "duration", "url", "status"]);

            $old_booking= $original_booking_type_data;
            $counter = 1;
            while (true ) {
                $old_booking= wpb_get_booking_type($original_booking_type_data['slug'].'-'.$counter, ['id']);
                if($old_booking == null){
                    $original_booking_type_data['name']=  $original_booking_type_data['name'].'-copy';
                    $original_booking_type_data['slug']=  $original_booking_type_data['slug'].'-'.$counter;
                    break;
                }
                $counter ++;
            }
            

            // Insert the cloned booking type data
            $inserted = $wpdb->insert($wpdb->wpb_booking_type, $original_booking_type_data);
            $new_booking_type_id = $wpdb->insert_id;

            if ($inserted) {
                // Retrieve and clone the meta data associated with the original booking type
                $original_meta_data = wpb_get_booking_types_meta_data($original_booking_type_id);

                foreach ($original_meta_data as $meta_row) {
                    $meta_row['wpb_booking_type_id'] = $new_booking_type_id; // Modify the booking type ID
                    unset($meta_row['meta_id']);
                    $meta_inserted = $wpdb->insert($wpdb->wpb_booking_typemeta, $meta_row);

                    if (!$meta_inserted) {
                        wp_send_json_error(
                            array(
                                'status'  => 'error',
                                'message' => __('Failed to clone meta data for booking type.', 'wpbookit')
                            )
                        );
                    }
                }

                wp_send_json_success(
                    array(
                        'status'  => 'success',
                        'message' => __('Booking Type Cloned Successfully.', 'wpbookit')
                    )
                );
            } else {
                wp_send_json_error(
                    array(
                        'status'  => 'error',
                        'message' => __('Failed to clone Booking Type.', 'wpbookit')
                    )
                );
            }
        } catch (Exception $e) {
            wp_send_json_error(
                array(
                    'status'  => 'error',
                    'message' => $e->getMessage()
                )
            );
        }
    }

    public function get_booking_type(WP_REST_Request $request)
    {
        global $wpdb;
        $request_data = $request->get_params();
        $wpb_booking_type_table = $wpdb->wpb_booking_type;
        $wpb_booking_typemeta_table = $wpdb->wpb_booking_type;

        try {
            if (empty($request_data['id'])) {
                throw new Exception('Booking Type ID Missing.');
            }

            $booking_type_id = $request_data['id'];

            $bookint_type_data = wpb_get_booking_type( (int)$booking_type_id, ["id", "name", "slug", "description", "type", "unavailable", "duration", "url", "status"], true);
                
            // Convert the array to a Laravel collection
            $bookint_type_data['meta']['weekly_time_slots'] = collect(json_decode($bookint_type_data['meta']['weekly_time_slots'],true))->map(function($day){
                return collect($day)->reverse()->values();
            })->toJson();

            
            $bookint_type_data['meta']['questions'] = array_map(function($item){
                $item['question']=wpb_unicode_to_utf8($item['question']);
                if (!empty($item['options']) && is_array($item['options'])) {
                    $item['options'] = array_map(function ($option) {
                        return wpb_unicode_to_utf8($option);
                    }, $item['options']);
                }
                return $item;
            },$bookint_type_data['meta']['questions']??[]);


            if (!$bookint_type_data) {
                throw new Exception('Failed to retrieve booking type data.');
            }
            wp_send_json_success($bookint_type_data);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
    public function image_upload_handle($tmp_file_path, $add_image_url)
    {
        $file_name = basename($add_image_url["name"]);

        $uploads_dir = wp_upload_dir();
        $destination_file_path = $uploads_dir["path"] . "/" . $file_name;

        // Move the uploaded file to the destination path
        if (move_uploaded_file($tmp_file_path, $destination_file_path)) :  //phpcs:ignore Generic.PHP.ForbiddenFunctions.Found 
            $file_url = $uploads_dir["url"] . "/" . $file_name;

            // Attempt to sideload the image
            $return_type = "id";
            $attachment_id = media_sideload_image($file_url, "", "avatar", $return_type);

            return $attachment_id;
        else :
            // Move uploaded file failed, handle the error
            return new WP_Error('fail');
        endif;
    }
    public function update_booking_type_status(WP_REST_Request $request){
        global $wpdb ;


        if(!$request->has_param('status') && !$request->has_param('id')){
            wp_send_json_error([
                'status' => 'error',
                'message' => esc_html__("SomeThing Went Wrong",'wpbookit')
            ]);
        }

        $current_user=get_user_by('ID',get_current_user_id()) ;

        $booking_id = (int) $request->get_param('id');

        $status = $request->get_param('status')==='true' ? 1 : 0;
        
        $result = $wpdb->update($wpdb->wpb_booking_type, ['status'=>$status] ,['id'=> $booking_id]);

        do_action( 'wpb_after_booking_status_update', $booking_id );

        if($result===1){
            wp_send_json_success([
                'status' => 'success',
                'message' => esc_html__("Booking Type Status Update Successfully.",'wpbookit'),
            ]);
        }
        wp_send_json_error([
            'status' => 'error',
            'message' => esc_html__("SomeThing Went Wrong",'wpbookit'),
        ]);

    }

    private function slugExists($post_name, $post_id) {
        global $wpdb;
        // Check the slug against the posts table
        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM $wpdb->wpb_booking_type WHERE slug = %s AND id != %d LIMIT 1",
                $post_name,
                $post_id
            )
        );
        return $existing;
    }
    
    public function is_slug_unique(WP_REST_Request $request) {
        $slug           = $request->get_param('slug');
        $post_id        = ! empty( $request->get_param('post_id') ) ? $request->get_param('post_id') : 0;
        $post_name      = sanitize_text_field($slug);
        $originalSlug   = $post_name;
        $counter        = 1;
    
        while ( $this->slugExists( $post_name, $post_id ) ) {
            $post_name = $originalSlug . '-' . $counter;
            $counter++;
        }
    
        wp_send_json(
            array(
                'slug'   => $post_name
            )
        );  
    }
    
}