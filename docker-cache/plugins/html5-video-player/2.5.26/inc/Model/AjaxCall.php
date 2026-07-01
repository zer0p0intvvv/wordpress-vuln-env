<?php
namespace H5VP\Model;

use H5VP\Model\ImportData;
use H5VP\Helper\Uploader;
use H5VP\Helper\Functions as Utils;
use H5VP\Model\Video;


class AjaxCall{

    protected static $_instance = null;
    public function __construct(){
        add_action('wp_ajax_nopriv_video_played', [$this, 'upateTotalViews']);
        add_action('wp_ajax_video_played', [$this, 'upateTotalViews']);
        add_action('wp_ajax_h5vp_import_data_ajax', [$this, 'importData']);
        add_action('wp_ajax_h5vp_export_data', [$this, 'h5vp_export_data']);
        add_action('wp_ajax_h5vp_store_thumb', [$this, 'storeThumb']);
        add_action('wp_ajax_createVideo', [$this, 'createVideo']);
        add_action('wp_ajax_getThumb', [$this, 'getThumb']);
    }

  
    public static function instance(){
        if(self::$_instance === null){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function upateTotalViews(){
        $data = $_POST['data'];
        $data = \sanitize_text_field( $data );
        $id = $_POST['id'];
        $id = \sanitize_text_field( $id );
        $total_viewes = \metadata_exists( 'post', $id, 'h5vp_total_views' ) ? \get_post_meta($id, 'h5vp_total_views', true): null;
        $total_viewes = $total_viewes + 1;
        \update_post_meta( $id, 'h5vp_total_views', $total_viewes );
        echo \esc_html($total_viewes);
        die();
    }

    public function importData(){
        ImportData::importMeta();
        echo \wp_json_encode(array(
            'success' => true,
        ));
        die();
    }

    public function h5vp_export_data(){
        $id = sanitize_text_field( $_POST['id'] );
        $output['id'] = $id;
        if(!$id) die();

        $post_type = get_post_type( $id );

        if(in_array($post_type, ['videoplayer', 'h5vpplaylist'])){
            $meta = get_post_meta($id);
            $post = get_post($id);
            unset($meta['_edit_last']);
            unset($meta['_edit_lock']);
            unset($meta['h5vp_total_views']);

            foreach($meta as $key => $value){
                $output[$key] = maybe_unserialize( $value[0] );
            }
            $output['body'] = $post->post_content;
            echo wp_json_encode($output);
        }

        die();

    }


    public function storeThumb(){
        $thumb = sanitize_text_field( $_POST['thumb'] );
        $filename = sanitize_text_field( $_POST['filename'] );
        $uploader = new Uploader('h5vp');
        $res = $uploader->createFile("$filename", wp_json_encode( ['thumb' => $thumb] ));
        echo wp_json_encode( ['success' => $res]);
        die();
    }

    public function createVideo(){
        $source = sanitize_text_field( $_POST['source'] );
        $title = sanitize_text_field( $_POST['title'] );
        $video = new Video();
        $args = [
            'title' => $title,
            'user_id' => get_current_user_id(),
            'type' => 'library',
            'src' => $source
        ];

        $video_id = $video->create($args);
        echo wp_json_encode( ['videoId' => (int) $video_id] );
        die();
    }

    public function getThumb(){
        $video_id = isset($_POST['videoId']) ? sanitize_text_field( $_POST['videoId'] ) : '0';
        echo wp_json_encode(['thumb' => Utils::getThumb($video_id), 'videoId' => $video_id]);
        die();
    }
}

AjaxCall::instance();