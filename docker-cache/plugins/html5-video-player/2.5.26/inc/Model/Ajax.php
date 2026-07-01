<?php
namespace H5VP\Model;

class Ajax{
 
    protected static $_instance = null;
    private $params = [];
    private $requestType; 
    private $requestMethod;
    private $requestModel;
    private $namespace = "H5VP\Model\\";
    private $model;

    public function __construct(){
    }

    public function register(){
        add_action('wp_ajax_h5vp_ajax_handler', [$this, 'prepareAjax']);
        add_action('wp_ajax_nopriv_h5vp_ajax_handler', [$this, 'prepareAjax']);
    }

    public static function instance(){
        if(!self::$_instance){
            self::$_instance = new self();
        }
        return self::$_instance;
    }   

    public function isset($array, $key, $default = false){
        if(isset($array[$key])){
            return $array[$key];
        }
        return $default;
    }

    public function prepareAjax(){
        if(!wp_verify_nonce(sanitize_text_field( $_POST['nonce'] ), 'wp_ajax' )){
            wp_send_json_error('invalid request');
        }
        
        $this->params = $_POST;
        $this->requestType = 'POST';
        $this->proceedRequest();
    }

    public function proceedRequest(){
        $data = $this->params;

        $this->requestModel = $this->isset($data, 'model', 'Model');
        $this->requestMethod = $this->isset($data, 'method', 'invalid');
        $this->model = $this->namespace.$this->requestModel;

        if(!class_exists($this->model)){
            wp_send_json_error('request destination failed!');
        }

        $model = new $this->model();

        if(method_exists($model, $this->requestMethod)){
            unset($this->params['method']);
            unset($this->params['action']);
            unset($this->params['nonce']);
            unset($this->params['model']);
            return $model->{$this->requestMethod}($this->params);
        }else {
           wp_send_json_error('request destination failed!');
        }
    }

    public function invalid(){
        wp_send_json_error('request destination failed!');
    }

}
