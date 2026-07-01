<?php

final class WPB_Guest_Controller
{

    public function get_guest_list(WP_REST_Request $request)
    {
        $args =array();
        $args['offset'] = sanitize_text_field( $request->get_param('start') );
        if ($request->has_param('order')) :
            $args['order'] = strtoupper($request->get_param('order')[0]['dir'] ?? '');
            $args['order_by'] = $request->get_param('order')[0]['name'] ?? "";
        endif;

        if ($request->has_param('guest_search')) {
            $args['guest_name'] = $request->get_param('guest_search');
        }

        $guest_users = wpb_get_guest_users( $args );
        $data = array_map(function ($item) {
            return [
                'id'            => $item->get_id(),
                'guest_name'    => $item->get_guest_name(),
                'guest_email'   => $item->get_guest_email(),
                'guest_phone_number'   => $item->get_guest_phone_number()??'-',
            ];
        }, $guest_users->results);
        wp_send_json(
            array(
                "recordsTotal"      => $guest_users->total ?? 0,
                "recordsFiltered"   => $guest_users->total ?? 0,
                "data"              => $data
            )
        );

    }

    public function delete_guest_callback(WP_REST_Request $request)
    {
        $user_id = $request->get_param("guest_id");
        if (!wp_verify_nonce($request->get_param('_ajax_nonce'), 'ajax_post')) :
            if (!is_numeric($user_id)) :
                $response_data = [
                    "status"    => 'error',
                    "message"   => esc_html__('Invalid user ID', 'wpbookit'),
                ];
            endif;
            // Delete the user
            if (wpb_delete_guest_user($user_id)) :
                $response_data = [
                    "status" => 'success',
                    "message" => esc_html__("Guest User Deleted Successfully.", 'wpbookit'),
                ];
            else :
                $response_data = [
                    "status" => 'error',
                    "message" => esc_html__("Error Deleting Guest User.", 'wpbookit'),
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

}