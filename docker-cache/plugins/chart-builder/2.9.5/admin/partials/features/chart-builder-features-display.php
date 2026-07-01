<div class="wrap">
    <div class="ays-chart-heading-box">
        <div class="ays-chart-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-chart-builder-plugin-user-manual" target="_blank" style="text-decoration: none;font-size: 13px;">
                <i class="ays_fa ays_fa_file_text" ></i> 
                <span style="margin-left: 3px;text-decoration: underline;">View Documentation</span>
            </a>
        </div>
    </div>
    <h1 class="wp-heading-inline">
        <?php echo __(esc_html(get_admin_page_title()), $this->plugin_name); ?>
    </h1>

    <div class="ays-chart-features-wrap">
        <div class="comparison">
            <table>
                <thead>
                    <tr>
                        <th class="tl tl2"></th>
                        <th class="product" style="background:#69C7F1; border-top-left-radius: 5px; border-left:0px;">
                            <span style="display: block"><?php echo __('Personal',$this->plugin_name)?></span>
                            <img src="<?php echo CHART_BUILDER_ADMIN_URL . '/images/avatars/personal_avatar.png'; ?>" alt="Free" title="Free" width="100" />
                        </th>
                        <th class="product" style="background:#69C7F1;">
                            <span style="display: block"><?php echo  __('Business',$this->plugin_name)?></span>
                            <img src="<?php echo CHART_BUILDER_ADMIN_URL . '/images/avatars/business_avatar.png'; ?>" alt="Business" title="Business" width="100" />
                        </th>
                        <th class="product" style="border-top-right-radius: 5px; border-right:0px; background:#69C7F1;">
                            <span style="display: block"><?php echo __('Developer',$this->plugin_name)?></span>
                            <img src="<?php echo CHART_BUILDER_ADMIN_URL . '/images/avatars/pro_avatar.png'; ?>" alt="Developer" title="Developer" width="100" />
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="price-info">
                            <div class="price-now">
                                <span><?php echo __('Free',$this->plugin_name)?></span>
                            </div>
                        </th>
                        <th class="price-info">
                            <!-- <div class="price-now"><span>$49</span></div> -->
                            <div class="price-now"><span style="text-decoration: line-through; color: red;">$75</span>
                            </div>
                            <div class="price-now"><span>$49</span>
                            </div> 
                            <!-- <div class="price-now"><span style="color: red; font-size: 12px;">Until December 31</span>
                            </div> -->
                            <div class="chart-builder-pracing-table-td-flex">
                                <a href="https://ays-pro.com/wordpress/chart-builder/" target="_blank" class="price-buy"><?php echo __('Buy now',$this->plugin_name)?><span class="hide-mobile"></span></a>
                                <span><?php echo __('(ONE-TIME PAYMENT)',$this->plugin_name)?></span>
                            </div>
                        </th>
                        <th class="price-info">
                            <!-- <div class="price-now"><span>$129</span></div> -->
                            <div class="price-now"><span span style="text-decoration: line-through; color: red;">$250</span>
                            </div>
                            <div class="price-now"><span>$129</span>
                            </div> 
                            <!-- <div class="price-now"><span style="color: red; font-size: 12px;">Until December 31</span>
                            </div>  -->
                            <div class="chart-builder-pracing-table-td-flex">
                                <a href="https://ays-pro.com/wordpress/chart-builder/" target="_blank" class="price-buy"><?php echo __('Buy now',$this->plugin_name)?><span class="hide-mobile"></span></a>
                                <span><?php echo __('(ONE-TIME PAYMENT)',$this->plugin_name)?></span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td colspan="4"><?php echo __('Support for',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Support for',$this->plugin_name)?></td>
                        <td><?php echo __('1 site',$this->plugin_name)?></td>
                        <td><?php echo __('5 site',$this->plugin_name)?></td>
                        <td><?php echo __('Unlimited sites',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="3"><?php echo __('Update for',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Update for',$this->plugin_name)?></td>
                        <td><?php echo __('1 months',$this->plugin_name)?></td>
                        <td><?php echo __('12 months',$this->plugin_name)?></td>
                        <td><?php echo __('Lifetime',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="4"><?php echo __('Support for',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Support for',$this->plugin_name)?></td>
                        <td><?php echo __('1 months',$this->plugin_name)?></td>
                        <td><?php echo __('12 months',$this->plugin_name)?></td>
                        <td><?php echo __('Lifetime',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Usage for',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Usage for',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Responsive design',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Responsive design',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>                   
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Line Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Line Chart',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Bar Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Bar Chart',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Pie Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Pie Chart',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Column Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Column Chart',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Donut Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Donut Chart',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Organizational Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Organizational Chart',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Source - Manual',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Source - Manual',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td> 
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Frontend Image Export',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Frontend Image Export',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td> 
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Permissions by user role',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Permissions by user role',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Access by user role',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Access by user role',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Geographical Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Geographical Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Histogram Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Histogram Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Area Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Area Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Gauge Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Gauge Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Combo Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Combo Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Stepped Area Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Stepped Area Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Bubble Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Bubble Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Scatter Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Scatter Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Table Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Table Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Timeline Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Timeline Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Candlestick Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Candlestick Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Gantt Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Gantt Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Sankey Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Sankey Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Treemap Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Treemap Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Word Tree Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Word Tree Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('3D Pie Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('3D Pie Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Google sheet integration',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Google sheet integration',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Source - Database',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Source - Database',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Source - External Database',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Source - External Database',$this->plugin_name)?></td>
                        <td><span>-</span></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Source - File',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Source - File',$this->plugin_name)?></td>
                        <td><span>-</span></td> 
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('WooCommerce Integration',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('WooCommerce Integration',$this->plugin_name)?></td>
                        <td><span>-</span></td> 
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Live Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Live Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td> 
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Frontend Print Chart',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Frontend Print Chart',$this->plugin_name)?></td>
                        <td><span>-</span></td> 
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Frontend XLSX Export',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Frontend XLSX Export',$this->plugin_name)?></td>
                        <td><span>-</span></td> 
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="4"><?php echo __('Frontend CSV Export',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Frontend CSV Export',$this->plugin_name)?></td>
                        <td><span>-</span></td> 
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                    <tr>
                        <td></td>
                        <!-- <td><a href="https://wordpress.org/plugins/chart-builder/" target="_blank" class="price-buy"><?php // echo __('Download',$this->plugin_name)?><span class="hide-mobile"></span></a></td> -->
                        <td></td>
                        <td>
                            <div class="chart-builder-pracing-table-td-flex">
                                <a href="https://ays-pro.com/wordpress/chart-builder/" target="_blank" class="price-buy"><?php echo __('Buy now',$this->plugin_name)?><span class="hide-mobile"></span></a>
                                <span style="line-height:1.5em;font-size:11px;color:#96a3bd;margin-top:5px;"><?php echo __('(ONE-TIME PAYMENT)',$this->plugin_name)?></span>
                            </div>
                        </td>
                        <td>
                            <div class="chart-builder-pracing-table-td-flex">
                                <a href="https://ays-pro.com/wordpress/chart-builder/" target="_blank" class="price-buy"><?php echo __('Buy now',$this->plugin_name)?><span class="hide-mobile"></span></a>
                                <span style="line-height:1.5em;font-size:11px;color:#96a3bd;margin-top:5px;"><?php echo __('(ONE-TIME PAYMENT)',$this->plugin_name)?></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="chart-builder-sm-content-row-sg">
        <div class="chart-builder-sm-guarantee-container-sg chart-builder-sm-center-box-sg">
            <img src="<?php echo CHART_BUILDER_ADMIN_URL ?>/images/money_back_logo.webp" alt="Best money-back guarantee logo">
            <div class="chart-builder-sm-guarantee-text-container-sg">
                <h3><?php echo __("30 day money back guarantee !!!", 'chart-builder'); ?></h3>
                <p>
                    <?php echo __("We're sure that you'll love our Chartify plugin, but, if for some reason, you're not
                    satisfied in the first 30 days of using our product, there is a money-back guarantee and
                    we'll issue a refund.", 'chart-builder'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

