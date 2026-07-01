<?php 

namespace H5VP\Services;
// require_once(__DIR__.'/../Helper/Functions.php');
use H5VP\Helper\Functions as Utils;
use H5VP\Model\Video;
use H5VP\Model\View;

class BlockTemplate{
    protected static $uniqid;

    public static function html($data){
        // wp_enqueue_style('dashicons');
        self::enqueueFile($data['infos']);
        self::$uniqid = self::createId();
        // $data = self::getFinalData($data);
        // $classes = $data['template']['class'];
        // $provider = $data['infos']['provider'];
        // $video_source = $data['infos']['video']['source'];
        // $video_poster = $data['infos']['video']['poster'];

        // if($data['infos']['popup']){
        //     $classes .= ' h5vp_popup_wrapper popup_close';
        // }

        // if($data['template']['hideYoutubeUI']){
        //     $classes .= ' yes';
        // }

        // $settings = $data;
        // unset($settings['template']);
        ob_start();

            echo '<pre>';
            print_r( $data );
            echo '</pre>';
        return;


        echo Utils::trim(self::style($data));
        $video_id = self::getVideoId($video_source, $provider, self::i($data['template'], 'title'));

        if(!$video_poster){
            $thumb = Utils::getThumb($video_id);
            if(isset($thumb['thumb'])){
                $video_poster = $thumb['thumb'];
            }
        }

        ?>
        <div> 
        <div data-unique-id="<?php echo esc_attr(uniqid()) ?>" class="h5vp_player <?php echo esc_attr($classes); ?>" id="<?php echo esc_attr(self::$uniqid); ?>" data-settings='<?php echo esc_attr(wp_json_encode( $settings)) ?>' video-id="<?php echo esc_attr($video_id); ?>" 
                style="--plyr-color-main: <?php echo esc_attr($data['template']['brandColor']); ?>;">
            <div id="<?php echo esc_attr($data['template']['additionalID']) ?>" class="h5vp_wrapper">
			<!-- sticky close  -->
			<!-- <div class="sticky_close">&times;</div> -->
			<div class="popup_close_btn">&times;</div>
            <div class="h5vp_popup_overlay"></div>
			<?php 
            self::popup($data); 
            self::branding($data['template']);
            ?>

			<?php if(!$data['infos']['protected']): ?>
				<!-- for library  -->
				<?php if($provider == 'library'): ?>
				<video crossorigin data-poster="<?php echo esc_attr($video_poster); ?>"
					preload="<?php echo esc_attr($data['template']['preload']); ?>"
                    <?php echo $data['options']['autoplay'] ? 'autoplay' : ''; ?>
                    <?php echo $data['options']['muted'] ? 'muted' : ''; ?>
                    >
                    <source src="<?php echo esc_url($video_source) ?>" />
                  
				</video>

				<!-- for youtube -->
				<?php elseif($provider == 'youtube'): ?>
					<div class="notlibrary"  data-poster="<?php echo esc_attr($video_poster) ?>" data-plyr-provider="youtube" data-plyr-embed-id="<?php echo esc_attr($video_source); ?>"></div>
					<!-- for vimeo  -->
				<?php elseif($provider == 'vimeo'): ?>
					<div  class="notlibrary"  data-poster="<?php echo esc_attr($video_poster) ?>" data-plyr-provider="vimeo" data-plyr-embed-id="<?php echo esc_attr($video_source); ?>"></div>
				<?php endif; ?>
			<?php else: ?>

				<!-- this is for password protected video -->
				<div class="video_overlay"></div>
				<!-- Password Form Start -->
				<div class="password_form">
					<form action="#">
						<label for="password"><?php echo esc_html($data['template']['protected_text']); ?></label>
						<input type="password" id="password" placeholder="Password">
						<input type="submit" value="Access Video">
						<p class="notice"></p>
					</form>
				</div>
				<!-- Password From End -->

				<!-- for library  -->
				<?php if($provider == 'library'): ?>
				<video playsinline class="player"  data-poster="<?php echo esc_attr($video_poster) ?>" poster="<?php echo esc_attr($data['infos']['video']['poster']); ?>" preload="<?php echo esc_attr($data['template']['preload']); ?>">
          
                </video>
                
				<!-- for youtube  -->
				<?php elseif($provider == 'youtube'): ?>
					<div class="plyr__video-embed notlibrary" id="player"  data-poster="<?php echo esc_attr($video_poster) ?>">
						<iframe
							src="https://www.youtube.com/watch?v=XHOmBV4js_E&feature=youtu.be"
							allowfullscreen
							allowtransparency
							allow="autoplay"
						></iframe>
					</div>

					<!-- for vimeo  -->
				<?php elseif($provider == 'vimeo'): ?>
					<div class="plyr__video-embed notlibrary" id="player"  data-poster="<?php echo esc_attr($video_poster) ?>">
						<iframe
							src="https://www.youtube.com/watch?v=XHOmBV4js_E&feature=youtu.be"
							allowfullscreen
							allowtransparency
							allow="autoplay"
						></iframe>
					</div>
				<?php endif; ?>

			<!-- <div class="close">x</div> -->
			<?php endif; ?>
			</div></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function popup($data){
        $template = $data['template'];
        $template['popupBtnStyle']['padding'] = '0.4em 1.2em';
        if($template['popup'] && !$data['infos']['popupBtnExists']){ 
            if($template['popupType'] === 'poster'){ ?> 
                <div class="h5vp_popup">
                    <img src="<?php echo esc_attr($data['infos']['video']['poster']); ?>" alt="" />
                    <svg aria-hidden="true" focusable="false"><use xlink:href="#plyr-play"></use></svg>
                </div>
            <?php
            }else if($template['popupType'] === 'button'){ ?>
                    <button class="h5vp_popup_btn" data-style="<?php echo esc_attr(wp_json_encode($template['popupBtnStyle'])) ?>"><?php echo esc_html($template['popupBtnText']) ?></button>
            <?php }
        }
    }

    public static function branding($template){
         if($template['branding'] && !$template['popup']){
             ?>
                <div class="h5vp_video_overlay">
                    <?php if($template['branding_type'] === 'text'): ?>
                        <?php if($template['branding_link'] != ''): ?>
                            <p><a href="<?php echo esc_attr($template['branding_link']);  ?>" ><?php echo esc_attr($template['branding_text']); ?></a></p>
                        <?php else: ?>
                            <p><?php echo esc_html($template['branding_text']); ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if($template['branding_link'] != ''): ?>
                            <a href="<?php echo esc_attr($template['branding_link']); ?>" ><img src="<?php echo esc_attr($template['branding_logo']); ?>" alt="logo"></a>
                        <?php else: ?>
                            <img src="<?php echo esc_attr($template['branding_logo']); ?>" alt="logo">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
             <?php
         }
    }

    public static function endscreen($template){
         if($template['endscreen']){ ?>
            <div class="h5vp_endscreen">
                <div class="overlay"></div>
                <div class="endscreen_content">
                    <button><span>&#128065;</span> <?php esc_html_e('Watch Again') ?></button>
                    <p><a href="<?php echo esc_url($template['endscreen_text_link']); ?>"><?php echo esc_html($template['endscreen_text']); ?></a></p>
                </div>
            </div>
        <?php } 
    }

    public static function getFinalData($data){
        if($data['infos']['protected']){
            $quality = self::i($data['infos']['video'], 'quality');
            $newQuality = [];
            $data['infos']['propagans'] = Utils::scramble('encode', $data['infos']['propagans']);
            $data['infos']['video']['source'] = Utils::scramble('encode', $data['infos']['video']['source']);

            if($quality && is_array($quality) && count($quality) > 0){
                foreach($quality as $video){
                    $video['video_file'] = Utils::scramble('encode', $video['video_file']);
                    array_push($newQuality, $video);
                }
            }

            $data['infos']['video']['quality'] = $newQuality;
            return $data;
        }else {
            return $data;
        }
    }

    /**
     * create a unique id
     */
    public static function createId(){
        return "h5vp".uniqid();
    }

    /**
     * generate style 
     */
    public static function style($data){
        $id = "#".self::$uniqid;        
        $template = $data['template'];
        $option = $data['options'];
        ob_start();
        ?>
        <style>
            <?php echo esc_html("$id .h5vp_video_overlay") ?>{
            <?php if($template['branding_position']): ?>
            bottom: 50%;
            right: 50%;
            transform: translate(50%, 50%);
            <?php else: ?>
                <?php echo esc_html($template['branding_position_first_type'].":".$template['branding_position_first']) ?>;
                <?php echo esc_html($template['branding_position_second_type'].":".$template['branding_position_second']) ?>;
            <?php endif; ?>
        }
        <?php
            if(!$template['controlsShadow'] || count($option['controls']) < 3){
                self::renderStyle("$id .plyr__controls", 'background', 'transparent');
            }
            self::renderStyle("$id", 'border-radius', self::i($template, 'round'));
            self::renderStyle("$id", 'overflow', 'hidden');
            self::renderStyle("$id .h5vp_video_overlay p", 'color', self::i($template, 'branding_color'));
            self::renderStyle("$id .h5vp_video_overlay p", 'font-size', self::i($template, 'branding_font_size'));
            self::renderStyle($id, 'width', self::i($template, 'width'));
            self::renderStyle("$id .h5vp_popup svg", 'background', self::i($template, 'brandColor'));
            self::renderStyle("$id .h5vp_video_overlay p:hover", 'color', self::i($template, 'branding_hover_color'));
            
            if(self::i($template, 'FYT')){
                self::renderStyle("$id .plyr__video-embed .plyr__poster", 'background-image',"url(".self::i($template, 'thumbnail').") !important");
            }

            if(self::i($template, 'endscreen')){
                self::renderStyle("$id .h5vp_endscreen .endscreen_content p a", 'color', self::i($template, 'endscreen_text_color'));
                self::renderStyle("$id .h5vp_endscreen .endscreen_content p a:hover", 'color', self::i($template, 'endscreen_text_color_hover'));
            }
        ?>
        <?php echo esc_html(Utils::trim($template['additionalCSS'])); ?>
        </style>
        <?php
        return ob_get_clean();
    }

    /**
     * enqueue essential file
     */
    public static function enqueueFile($infos){
        $streaming = $infos['streaming'];
        $streamingType = $infos['streamingType'];
        wp_enqueue_script('html5-player-video-view-script');
        wp_enqueue_style('html5-player-video-style');
        if($streaming){
            if($streamingType == 'hls'){
                wp_enqueue_script('h5vp-plyrio-hls-js');
            }
            if($streamingType == 'dash'){
                wp_enqueue_script('h5vp-plyrio-dash-js');
            }
        }
    }

    /**
     * echo and ecape value if it isset
     */
    public static function e_i($array = [], $index = ''){
        if(isset($data[$index])){
            echo esc_html($data[$index]);
        }
        return false;
    }

    /**
     * return value if it isset
     */
    public static function i($array = [], $index = ''){
        if(isset($array[$index])){
            return $array[$index];
        }
        return false;
    }

    /**
     * render style
     */
    public static function renderStyle($selector, $property, $value){
        if($value){
            echo esc_html("$selector { $property:$value }");
        }
        return false;
    }

    public static function getVideoId($source = '', $provider = 'library', $title = ''){
        // return 'nothing';
        global $wpdb;
        $table_name = $wpdb->prefix.'h5vp_videos';
        // $select = $wpdb->get_row("SELECT * FROM $table_name WHERE src='$source'");
        // if(!$select){
            $video = new Video();
            $args = [
                'title' => $title,
                'user_id' => get_current_user_id(),
                'type' => $provider,
                'src' => $source
            ];

            $video_id = $video->create($args);
            return $video_id;
        // }
        // return $select->id ?? false;
    }

    
}