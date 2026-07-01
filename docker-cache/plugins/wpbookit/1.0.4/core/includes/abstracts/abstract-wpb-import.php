<?php
if (!defined('ABSPATH')) {
    exit;
}

abstract class WPB_Abstract_Import
{
    public $import_moduel;
    public $import_moduel_label;

    public $file_columns;
    public $file_data = [];

    public $required_fields = [];


    public function __construct()
    {

        add_action('init',[$this,'init']);

        add_filter('wpb_available_import_files', [$this, 'add_import_module']);
        add_filter("wpb_get_{$this->import_moduel}_content", [$this, 'get_content'],10,2);
    }

    final public function add_import_module($available_import_files){
        $available_import_files[] = [
            'key' => $this->import_moduel,
            'label' => $this->import_moduel_label,
        ];
        return $available_import_files;
    }
    public function init(){
    }
    public function get_content($file,$type){
        return [];
    }
    final function check_has_require_column($type){
        if (count(array_intersect($this->file_columns, array_keys($this->required_fields[$type]))) == count($this->required_fields[$type])) {
            return true;
        }
        return new WP_Error('missing_require_field', esc_html__("Missing required field in CSV.",'wpbookit') , array_diff(array_keys($this->required_fields[$type]),array_intersect($this->file_columns, array_keys($this->required_fields[$type]))));
    }
}
