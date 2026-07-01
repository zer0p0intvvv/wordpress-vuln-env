<?php
final class WPB_Setting_Controller
{
    public function add_general_setting(WP_REST_Request $request){
        $request_data = $request->get_params();
        update_option( 'wpb_general_setting_data', $request_data);
        $permalink_Handler = new Booking_Type_Handler();
        $permalink_Handler->add_booking_type_rewrite_rule([ ]);
        $response_data = array(
            "status"    => 'success',
            'message' => esc_html__("Settings Saved Successfully!", 'wpbookit')
        );
        wp_send_json_success($response_data);
    }
    
    public function save_custome_code(WP_REST_Request $request){
        $css_code= $request->get_param('css_code');
        $js_code= $request->get_param('js_code');
        update_option( 'wpb_custom_code_data', [  'css_code' => $css_code,  'js_code' => $js_code ]);
        flush_rewrite_rules();
 
        wp_send_json_success(array(
            "status"    => 'success',
            'message' => esc_html__("Custom Code Saved Successfully.", 'wpbookit')
        ));
    }
    public function get_payment_gateways_list(WP_REST_Request $request) {
        $data = get_option("wpb_offline_payment_modes", []);
        
        // Loop through the data and modify the description
        foreach ($data as $key => $value) {
            // Limit the description to 5 words
            $words = explode(' ', $value['desc']);
            if (count($words) > 5) {
                $data[$key]['desc'] = implode(' ', array_slice($words, 0, 5)) . '...';
            }
            // Assign an ID and actions
            $data[$key]['id'] = $key + 1;
            $data[$key]['actions'] = true;
        }
    
        // Sort data by ID in descending order
        usort($data, function($a, $b) {
            return $b['id'] <=> $a['id'];
        });
    
        // Send JSON response
        wp_send_json(array(
            "recordsTotal"    => count($data) ?? 0,
            "recordsFiltered" => count($data) ?? 0,
            "data"            => $data
        ));
    }
    

    public function image_upload_handle($tmp_file_path, $add_image_url)
    {
        $file_name = basename($add_image_url["name"]);

        $uploads_dir = wp_upload_dir();
        $destination_file_path = $uploads_dir["path"] . "/" . $file_name;

        // Move the uploaded file to the destination path
        if (move_uploaded_file($tmp_file_path, $destination_file_path)) : // phpcs:ignore  Generic.PHP.ForbiddenFunctions.Found 
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
    public function add_offline_payment_list   (WP_REST_Request $request)  {
        $name= $request->get_param('payment_mode_name');
        $desc= $request->get_param('payment_mode_desc');
        $payment_mode= get_option('wpb_offline_payment_modes',[]);
        if (empty($request->get_param('payment_gateway_id'))) {
            $payment_mode[] = ['name' => $name, 'desc' => $desc, 'status' => true];
        } else {
            if (isset($payment_mode[$request->get_param('payment_gateway_id') - 1])) {
                $payment_mode[$request->get_param('payment_gateway_id') - 1] = ['name' => $name, 'desc' => $desc, 'status' => true];
            } else {
                wp_send_json_error([
                    'status' => 'error',
                    'message' => esc_html__("Something Went Wrong", 'wpbookit'),
                ], 404);
            }
        }

        $result = update_option('wpb_offline_payment_modes', $payment_mode);

        wp_send_json_success([
            'status'=>'success',
            'message'=> esc_html__("Payment Mode Added Successfully",'wpbookit') ,
        ]);
    }
  
    public function update_payment_mode_status(WP_REST_Request $request)  {

        if(!empty($request->get_param('id')) || $request->get_param('id')== '0' ){
           
            $payment_mode= get_option('wpb_offline_payment_modes',[]);

            if(!isset($payment_mode[$request->get_param('id')])){
                wp_send_json_error([
                    'status'=>'error',
                    'message'=> esc_html__("Something Went Wrong",'wpbookit') ,
                ],404);
            }

            $payment_mode[$request->get_param('id')]['status'] = $request->get_param('value');
            $result= update_option('wpb_offline_payment_modes',$payment_mode);
            
            if($result){
                wp_send_json_success([
                    'status'=>'success',
                    'message'=> esc_html__("Payment Mode Status Updated Successfully",'wpbookit') ,
                ]);
            }
        }
        wp_send_json_error([
            'status'=>'error',
            'message'=> esc_html__("Something Went Wrong",'wpbookit') ,
        ],404);

     
    }
    public function delete_payment_mode(WP_REST_Request $request)  {

        if(!empty($request->get_param('id'))){
           
            $payment_mode= get_option('wpb_offline_payment_modes',[]);

            if(!isset($payment_mode[($request->get_param('id')-1) ] )){
                wp_send_json_error([
                    'status'=>'error',
                    'message'=> esc_html__("Something Went Wrong",'wpbookit') ,
                ],404);
            }

            unset($payment_mode[($request->get_param('id')-1)]);
            $result= update_option('wpb_offline_payment_modes',array_values($payment_mode));
            
            if($result){
                wp_send_json_success([
                    'status'=>'success',
                    'message'=> esc_html__("Payment Mode Delete Successfully",'wpbookit') ,
                ]);
            }
        }
        wp_send_json_error([
            'status'=>'error',
            'message'=> esc_html__("Something Went Wrong",'wpbookit') ,
        ],404);

     
    }
}
