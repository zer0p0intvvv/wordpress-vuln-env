<?php
    require_once( CHART_BUILDER_ADMIN_PATH . "/partials/charts/actions/chart-builder-charts-actions-options.php" );

    $form_id = "";
    if (!($id === 0 && !isset($_GET['type']) && !isset($_GET['source']))) {
        $form_id = "ays-charts-form-".stripslashes($chart_source_type);
    }
        
?>
<div class="wrap">
    <div class="ays-chart-heading-box">
        <div class="ays-chart-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-chart-builder-plugin-user-manual" target="_blank" style="text-decoration: none;font-size: 13px;">
                <i class="ays_fa ays_fa_file_text" ></i> 
                <span style="margin-left: 3px;text-decoration: underline;">View Documentation</span>
            </a>
        </div>
    </div>
    <div class="container-fluid">
        <form class="ays-charts-form" id="<?php echo $form_id; ?>" method="post">
            <input type="hidden" name="ays_chart_tab" value="<?php echo $ays_chart_tab; ?>">
            <h1 class="wp-heading-inline">
                <?php
                echo __( esc_html( esc_attr($heading) ), "chart-builder" );
                ?>
            </h1>
            <div class="ays-chart-add-new-button-box" style="margin-top: 10px;">
                <input type="submit" name="ays_submit_top" value="<?php echo esc_html(__('Save and close', "chart-builder")) ?>" class="button button-primary ays-button ays-chart-loader-banner" id="ays-button-top-save">
                <input type="submit" name="ays_apply_top" value="<?php echo esc_html(__('Save', "chart-builder")) ?>" class="button button-secondary ays-button ays-chart-loader-banner" id="ays-button-top-apply">
            </div>
            <div>
                <?php if ($id !== null && $id !== 0): ?>
                    <div class="ays-chart-subtitle-main-box">
                        <p class="ays-subtitle">
                            <?php if(isset($id) && count($get_all_charts) > 1):?>
                                <i class="ays_fa ays_fa_arrow_down ays-subtitle-inner-charts-page ays-chart-open-charts-list"></i>   
                                <strong class="ays_chart_title_in_top"><?php echo esc_attr( stripslashes( $object['title'] ) ); ?></strong>
                            <?php endif; ?>
                        </p>
                        <?php if(isset($id) && count($get_all_charts) > 1):?>
                            <div class="ays-chart-charts-data">
                                <?php $var_counter = 0; foreach($get_all_charts as $var => $var_name): if( intval($var_name['id']) == $id ){continue;} $var_counter++; ?>
                                    <?php ?>
                                    <label class="ays-chart-message-vars-each-data-label">
                                        <input type="radio" class="ays-chart-charts-each-data-checker" hidden id="ays_chart_message_var_count_<?php echo esc_attr($var_counter)?>" name="ays_chart_message_var_count">
                                        <div class="ays-chart-charts-each-data">
                                            <input type="hidden" class="ays-chart-charts-each-var" value="<?php echo esc_attr($var); ?>">
                                            <a href="?page=chart-builder&action=edit&id=<?php echo esc_attr($var_name['id']); ?>" target="_blank" class="ays-chart-go-to-charts"><span><?php echo stripslashes(esc_attr($var_name['title'])); ?></span></a>
                                        </div>
                                    </label>              
                                <?php endforeach ?>
                            </div>                        
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p style="font-size:14px; font-style:italic;">
                                <?php echo __("To make your chart live, copy shortcode", "chart-builder"); ?>
                                <strong class="ays-chart-shortcode-box" title="<?php echo __('Click to copy',"chart-builder");?>" onClick="selectElementContents(this)" style="font-size:16px; font-style:normal;" data-bs-toggle="tooltip"><?php echo '[ays_chart id="'.$id.'"]'; ?></strong>
                                <?php echo " " . __( "and paste it into your desired Page or Post.", "chart-builder"); ?>
                            </p>
                        </div>
                    </div>
                <?php endif;?>
            </div>
            <hr />

            <?php
                if (!($id === 0 && !isset($_GET['type']) && !isset($_GET['source']))) {
                    require_once( CHART_BUILDER_ADMIN_PATH . "/partials/charts/actions/partials/chart-builder-charts-actions-".stripslashes($chart_source_type).".php" );
                } else {
                    require_once( CHART_BUILDER_ADMIN_PATH . "/partials/charts/chart-builder-charts-display.php" );
                }
            ?>

            <input type="hidden" name="<?php echo esc_attr($html_name_prefix); ?>date_created" value="<?php echo esc_attr($date_created); ?>">
            <input type="hidden" name="<?php echo esc_attr($html_name_prefix); ?>date_modified" value="<?php echo esc_attr($date_modified); ?>">
            <hr />
            <?php
                wp_nonce_field('chart_builder_action', 'chart_builder_action');
            ?>
            <input type="submit" name="ays_submit" value="<?php echo esc_html(__('Save and close', "chart-builder")) ?>" class="button button-primary ays-button ays-chart-loader-banner" id="ays-button-save">
            <input type="submit" name="ays_apply" value="<?php echo esc_html(__('Save', "chart-builder")) ?>" class="button button-secondary ays-button ays-chart-loader-banner" id="ays-button-apply">
            <?php 
                if($id === 0 && !isset($_GET['type']) && !isset($_GET['source'])){
                    require_once( CHART_BUILDER_ADMIN_PATH . "/partials/charts/actions/partials/chart-builder-charts-add-new-layer-page.php" );
                }
            ?>
        </form>

        <div class="ays-modal" id="ays-chart-db-query-results">
            <div class="ays-modal-content">
                <div class="ays-preloader">
                    <img class="loader" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/loaders/tail-spin-result.svg" alt="" width="100">
                </div>

                <!-- Modal Header -->
                <div class="ays-modal-header">
                    <span class="ays-close">&times;</span>
                    <h2><?php echo esc_html(__('Database query results', "chart-builder")); ?></h2>
                </div>

                <!-- Modal body -->
                <div class="ays-modal-body">
                    <div class="db-wizard-results"></div>
                </div>

                <!-- Modal footer -->
            </div>
        </div>
    </div>
</div>
