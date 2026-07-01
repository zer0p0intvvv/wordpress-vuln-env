<?php

/**
 * WPBookit PayPal Payment Integration
 *
 */

use Omnipay\Omnipay;

defined('ABSPATH') || exit;

class WPB_CSV_Import extends  WPB_Abstract_Import {

    public  function init() {
        $this->import_moduel = 'csv';
        $this->import_moduel_label =  esc_html__("CSV",'wpbookit');
        $this->required_fields = get_require_csv_fields();
    }

    public function get_content($file,$type) {
        if (($handle = fopen($file, "r")) !== false) { //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
            if (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $this->file_columns = apply_filters("wpb_booking_csv_import_field_cols",array_map(function($el){
                   return trim($el);
                },$data));
            }
            $req_col= $this->check_has_require_column($type);
            if( is_wp_error($req_col)){
                return $req_col;
            }
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $this->file_data[] = array_combine($this->file_columns, $data);
            }
            fclose($handle); //phpcs:ignore  WordPress.WP.AlternativeFunctions.file_system_operations_fclose 
            return $this->file_data;
        } 
    }
}

new WPB_CSV_Import ();