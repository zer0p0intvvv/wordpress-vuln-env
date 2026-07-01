<?php


class WPB_Routes_Handler extends WPB_Routes
{
	private  $controller_path;

	public function __construct() {
		$this->controller_path = IQWPB_PLUGIN_PATH . 'core/admin/classes/controllers/';
		parent::__construct();
		$this->event_Handler();	
	}

	public function event_Handler() {
		add_action( "wp_ajax_wpb_ajax_post", [ $this, 'wpb_ajax_post' ] );
		add_action( "wp_ajax_nopriv_wpb_ajax_post", [ $this, 'wpb_ajax_post' ] );

		add_action( "wp_ajax_wpb_ajax_get", [ $this, 'wpb_ajax_get' ] );
		add_action( "wp_ajax_nopriv_wpb_ajax_get", [ $this, 'wpb_ajax_get' ] );
	}

	public function wpb_ajax_post() {
		try {
			//check if request method is post method
			if ( strtolower( sanitize_textarea_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) !== 'post' ) :
				$error = __( 'Method is not allowed', 'wpbookit');
				throw new Exception($error, 405);
			endif;

            $this->routes();

			//check if request route key exists in route array
			if ( $this->has_route( $_REQUEST['route_name'] ) ) :
				//get request route value from route array
				$route = $this->get_route($_REQUEST['route_name']);

				//check if request route method is same as required method
				if (strtolower($route['method']) !== 'post') :
					$error = __( 'Method is not allowed', 'wpbookit' );
					throw new Exception($error, 405);
				endif;

				//check route value have nonce if not set nonce to 100
				if (!isset($route['nonce'])) :
					$route['nonce'] = 1;
				endif;

				if ($route['nonce'] === 1) :
					//verify request nonce
					if (isset($_REQUEST['_ajax_nonce']) && !wp_verify_nonce($_REQUEST['_ajax_nonce'], 'wpb_ajax_nonce')) :
						$error = __('Invalid nonce in request', 'wpbookit');
						throw new Exception($error, 419);
					endif;
				endif;

				//call function
				$this->call($route);
			else :
				$error = __('Route not found', 'wpbookit');
				throw new Exception($error, 404);
			endif;
		} catch (Exception $e) {

			$code    = $e->getCode();
			$message = $e->getMessage();

			header("Status: $code $message");

			wp_send_json(
				array(
					'status'  => false,
					'message' => $e->getMessage()
				)
			);
		}
		wp_die();
	}

	public function wpb_ajax_get() {
		if ($_REQUEST === '') {
			$_REQUEST = json_decode(file_get_contents("php://input"), true);
		}

		try {
			//check if request method is get method
			if ( strtolower(sanitize_textarea_field(wp_unslash($_SERVER['REQUEST_METHOD']))) !== 'get' ) :
				$error =  __('Method is not allowed', 'wpbookit');
				throw new Exception($error, 405);
			endif;

            $this->routes();

			//check if request route key exists in route array
			if ($this->has_route($_REQUEST['route_name'])) :
				//get request route value from route array
				$route = $this->get_route($_REQUEST['route_name']);

				//check if request route method is same as required method
				if (strtolower($route['method']) !== 'get') :
					$error = __('Method is not allowed', 'wpbookit');
					throw new Exception($error, 405);
				endif;

				//call function
				$this->call($route);
			else :
				$error = __('Route not found', 'wpbookit');
				throw new Exception($error, 404);
			endif;
		} catch (Exception $e) {

			$code    = $e->getCode();
			$message = $e->getMessage();

			header("Status: $code $message");

			wp_send_json([
				'status'  => false,
				'message' => $e->getMessage()
			]);
		}
	}

	public function call($route) {
	
		list($class, $method) = explode('@', $route['action']);
		
		require_once IQWPB_PLUGIN_PATH . 'core/includes/classes/class.wpb-cache-handler.php';
		if( isset( $route['dependency'] ) && !empty( $route['dependency'] ) ) :
			$dependencys = apply_filters( 'wpb_include_dependency_file_' . ($route['module']??''), ($route['dependency']??''), $route );
	
			foreach( $dependencys as $dependency ) :
				if( file_exists( $dependency ) ) :
					require_once $dependency;
				else :
					wp_send_json(
						array( 
							'success'    => false,
							'message'    => sprintf( 
								// translators: Dependency Name placeholder:0
								__( '%s dependency file not found at the desired location ', 'wpbookit' ),
								$dependency
							),
						)
					);    
				endif;
			endforeach;
		endif;
		
		if( isset( $route['module'] ) ) :
			require_once apply_filters( 'wpb_include_controller_file',$this->controller_path .'class.wpb-'. $route['module'].'.php',$route['module']) ;
		endif;
	
		$request    = new WP_REST_Request( $_SERVER['REQUEST_METHOD'],$method );
		$req_method    = $_SERVER['REQUEST_METHOD'] ==='GET' ? 'set_query_params' : 'set_body_params';
	
		$request->$req_method($_REQUEST);
		if( "set_body_params" === $req_method ):
			$request->set_file_params($_FILES);
		endif;

        if( class_exists( $class ) ) {
            (new $class)->$method($request);
        }else{
            wpb_error_log( 'Class '. $class .' not found' );
            $response = array(
                'status'  => 'error',
                'message' => __('Internal server error.', 'wpbookit'),
                'data'     => array(),
            );
            wp_send_json($response, 503 );
        }
	}
}
