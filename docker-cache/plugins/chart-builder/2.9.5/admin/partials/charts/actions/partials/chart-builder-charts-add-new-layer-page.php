<div class="<?php echo esc_attr($html_class_prefix); ?>layer_container">
    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_content">
        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box">
            <div class="<?php echo esc_attr($html_class_prefix); ?>close-type">
                <a href="?page=chart-builder">
                    <img src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/cross.png">
                </a>
            </div>
            <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_source_type">
                <select class="<?php echo esc_attr($html_class_prefix); ?>layer_box_source_type_select">
                    <option value="google-charts"><?php echo __('Google Charts', 'chart-builder'); ?></option>
                    <option value="chart-js"><?php echo __('Chart.js', 'chart-builder'); ?></option>
                </select>
            </div>
            <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_blocks" source-type="google-charts">
                <?php foreach ($google_charts as $type => $data) : ?>
                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_each_block">
                        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_layer_block <?php echo $data['pro'] ? 'only_pro' : ''; ?>">
                            <?php if ($data['pro']) : ?>
                                <div class="pro_features">
                                    <div>
                                        <a href="https://ays-pro.com/wordpress/chart-builder/" target="_blank" title="PRO feature">
                                            <div class="<?php echo esc_attr($html_class_prefix); ?>pro-features-icon" style="background-image: url(<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg)"></div>
                                            <div class="<?php echo esc_attr($html_class_prefix); ?>pro-features-text"><?php echo __("Upgrade", "chart-builder"); ?></div>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <label class='<?php echo esc_attr($html_class_prefix); ?>dblclick-layer'>
                                <input type="radio" name="<?php echo esc_attr($this->plugin_name); ?>[modal_content]" class="<?php echo esc_attr($html_class_prefix); ?>choose-source" value="<?php echo $type; ?>">
                                <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item">
                                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo">
                                        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo_overlay">
                                            <img class="<?php echo esc_attr($html_class_prefix); ?>layer_icons" src="<?= CHART_BUILDER_ADMIN_URL; ?>/images/icons/<?php echo esc_attr($data['icon']); ?>">
                                        </div>
                                        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_checked">
                                            <img src="<?= CHART_BUILDER_ADMIN_URL; ?>/images/icons/check.svg">
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_separate_title">
                            <span><?php echo __(esc_attr($data['name']), "chart-builder") ?></span>
                        </div>
                        <?php if ($data['demo']) : ?>
                            <div class="<?php echo esc_attr($html_class_prefix); ?>view_demo_content">
                                <a href="<?php echo esc_url($data['demo']); ?>" target="_blank"><?php echo __('View demo', "chart-builder") ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_blocks" source-type="chart-js" style="display:none;">
                <?php foreach ($chartjs_charts as $type => $data) : ?>
                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_each_block">
                        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_layer_block <?php echo $data['pro'] ? 'only_pro' : ''; ?>">
                            <?php if ($data['pro']) : ?>
                                <div class="pro_features">
                                    <div>
                                        <a href="https://ays-pro.com/wordpress/chart-builder/" target="_blank" title="PRO feature">
                                            <div class="<?php echo esc_attr($html_class_prefix); ?>pro-features-icon" style="background-image: url(<?php echo esc_attr(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pro-features-icons/Locked_24x24.svg)"></div>
                                            <div class="<?php echo esc_attr($html_class_prefix); ?>pro-features-text"><?php echo __("Upgrade", "chart-builder"); ?></div>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <label class='<?php echo esc_attr($html_class_prefix); ?>dblclick-layer'>
                                <input type="radio" name="<?php echo esc_attr($this->plugin_name); ?>[modal_content]" class="<?php echo esc_attr($html_class_prefix); ?>choose-source" value="<?php echo $type; ?>">
                                <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item">
                                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo">
                                        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo_overlay">
                                            <img class="<?php echo esc_attr($html_class_prefix); ?>layer_icons" src="<?= CHART_BUILDER_ADMIN_URL; ?>/images/icons/<?php echo esc_attr($data['icon']); ?>">
                                        </div>
                                        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_checked">
                                            <img src="<?= CHART_BUILDER_ADMIN_URL; ?>/images/icons/check.svg">
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_separate_title">
                        <span><?php echo __(esc_attr($data['name']), "chart-builder") ?></span>
                        </div>
                        <?php if ($data['demo']) : ?>
                            <div class="<?php echo esc_attr($html_class_prefix); ?>view_demo_content">
                                <a href="<?php echo esc_url($data['demo']); ?>" target="_blank"><?php echo __('View demo', "chart-builder") ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_link">
                <a target="_blank" href="https://www.youtube.com/watch?v=CiZ-w9t9yoo"><?php echo __('All Chart Types', 'chart-builder'); ?></a>
            </div>
            <!-- <div class="<?php // echo esc_attr($html_class_prefix); ?>select_button_layer">
                <div class="<?php // echo esc_attr($html_class_prefix); ?>select_button_item">
                    <input type="button" class="<?php // echo esc_attr($html_class_prefix); ?>layer_button" name="" value="Next >" disabled>
                </div>
            </div> -->
        </div>
    </div>
    <div class="<?php echo esc_attr($html_class_prefix); ?>select_button_layer">
        <div class="<?php echo esc_attr($html_class_prefix); ?>select_button_item">
            <input type="button" class="<?php echo esc_attr($html_class_prefix); ?>layer_button" name="" value="Next" disabled>
        </div>
    </div>
</div>