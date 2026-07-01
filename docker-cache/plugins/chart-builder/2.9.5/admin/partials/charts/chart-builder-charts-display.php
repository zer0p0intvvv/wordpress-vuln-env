<?php
$items = $this->db_obj->get_items();
$all_items = CBFunctions()->get_all_charts_count();
$chart_count_per_page = count($items) > 0 ? $all_items/$this->db_obj->get_pagination_count() : 0;
$chart_paged = isset($_GET['paged']) && $_GET['paged'] != '' ? absint( sanitize_text_field($_GET['paged'])) : '';
$search = $this->db_obj->get_search_value();
$chart_max_id = Chart_Builder_Admin::get_max_id('charts');
$this->settings_obj = new Chart_Builder_Settings_DB_Actions($this->plugin_name);
$chart_title_length = $this->settings_obj->get_listtables_title_length();
$filter_by_type = (isset($_GET['filterbytype']) && $_GET['filterbytype'] != "") ? intval(sanitize_text_field($_GET['filterbytype'])) : '';
$filter_by_source = (isset($_GET['filterbysource']) && $_GET['filterbysource'] != "") ? intval(sanitize_text_field($_GET['filterbysource'])) : '';
$filter_by_chart_source = (isset($_GET['filterbychartsource']) && $_GET['filterbychartsource'] != "") ? intval(sanitize_text_field($_GET['filterbychartsource'])) : '';
$filter_by_date = (isset($_GET['filterbydate']) && $_GET['filterbydate'] != "") ? sanitize_text_field($_GET['filterbydate']) : '';
$order_by = (isset($_GET['orderby']) && $_GET['orderby'] != "") ? sanitize_text_field($_GET['orderby']) : '';
$order = (isset($_GET['order']) && $_GET['order'] != "") ? sanitize_text_field($_GET['order']) : 'desc';
$chart_types = array(
    "Line Chart",
    "Bar Chart",
    "Pie Chart",
    "Column Chart",
    "Org Chart",
    "Donut Chart",
);
$chart_sources = array(
    "Manual",
    "Quiz Maker",
    "File Import",
    "Google Sheet",
    "Database Query",
    "Woocommerce",
);
$chart_source_types = array(
    "Google Charts",
    "Chart.js",
);
$chart_dates = array(
    'today' => __("Today", "chart-builder"),
    'yesterday' => __("Yesterday", "chart-builder"),
    'last_week' => __("Last Week", "chart-builder"),
    'last_month' => __("Last Month", "chart-builder"),
    'last_year' => __("Last Year", "chart-builder"),
);
$order_by_values = array(
    'title' => __("Title", "chart-builder"),
    'date_created' => __("Date created", "chart-builder"),
    'date_modified' => __("Date modified", "chart-builder"),
);
$order_values = array(
    'asc' => __("Ascending", "chart-builder"),
    'desc' => __("Descending", "chart-builder"),
);
$filter_by_author_data = $this->db_obj->get_searched_author_info();

$plus_icon_svg = "<span class=''><img src='". CHART_BUILDER_ADMIN_URL ."/images/icons/plus=icon.svg'></span>";
$youtube_icon_svg = "<span class=''><img src='". CHART_BUILDER_ADMIN_URL ."/images/icons/youtube-video-icon.svg'></span>";

?>
<div class="wrap ays_charts_list_table">
    <div class="ays-chart-heading-box">
        <div class="ays-chart-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-chart-builder-plugin-user-manual" target="_blank" style="text-decoration: none;font-size: 13px;">
                <i class="ays_fa ays_fa_file_text" ></i> 
                <span style="margin-left: 3px;text-decoration: underline;">View Documentation</span>
            </a>
        </div>
    </div>
    <h1 class="wp-heading-inline">
        <?php
        echo __( esc_html( get_admin_page_title() ), "chart-builder" );
        ?>
    </h1>

    <div class="ays-chart-add-new-button-box" style="margin-top: 10px;">
        <?php
            echo sprintf( '<a href="?page=%s&action=%s" class="btn btn-primary chart-add-new-bttn chart-add-new-button-new-design"> %s ' . __( 'Add New', "chart-builder" ) . '</a>', esc_attr( $_REQUEST['page'] ), 'add', $plus_icon_svg);
        ?>
    </div>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable chart-list-table-container">
                    <form method="post">
                        <div class="ays-chart-table-actions-row">
                            <div class="ays-chart-table-actions-row-section">
                                <div class="ays-chart-delete-button">
                                    <button name="bulk_delete" id="ays-chart-bulk-delete" disabled><i class="ays_fa ays_fa_trash"></i><?php echo esc_html(__( 'Delete', "chart-builder" )); ?></button>
                                    <button type="submit" name="bulk_delete_confirm" id="ays-chart-bulk-delete-confirm" style="display: none;"></button>
                                </div>
                                <div class="ays-chart-filter-section">
                                    <select name="filterbytype" id="ays-chart-filter-select">
                                        <option value=""><?= __( "Select Type", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $chart_types as $k => $v ):
                                            $selected = ( $filter_by_type == ($k+1) ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k+1); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="filterbysource" id="ays-chart-filter-source">
                                        <option value=""><?= __( "Select Source", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $chart_sources as $k => $v ):
                                            $disabled = $k >= 2 ? 'disabled' : '' ;
                                            $selected = ( $filter_by_source == ($k+1) ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k+1); ?>" <?php echo esc_attr($selected); ?> <?php echo $disabled; ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="filterbychartsource" id="ays-chart-filter-chart-source">
                                        <option value=""><?= __( "Select Chart Source", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $chart_source_types as $k => $v ):
                                            $disabled = $k >= 2 ? 'disabled' : '' ;
                                            $selected = ( $filter_by_chart_source == ($k+1) ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k+1); ?>" <?php echo esc_attr($selected); ?> <?php echo $disabled; ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="filterbydate" id="ays-chart-filter-date">
                                        <option value=""><?= __( "Select Date", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $chart_dates as $k => $v ):
                                            $selected = ( $filter_by_date == $k ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="filterbyauthor" id="ays-chart-filter-author">
                                        <?php if (isset($filter_by_author_data) && !empty($filter_by_author_data)): ?>
                                            <option value="<?php echo esc_html($filter_by_author_data['ID'])?>" selected><?php echo esc_html($filter_by_author_data['display_name'])?></option>
                                        <?php endif; ?>
                                    </select>
                                    <select name="orderby" id="ays-chart-order-by">
                                        <option value=""><?= __( "Order by", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $order_by_values as $k => $v ):
                                            // $disabled = $k >= 2 ? 'disabled' : '' ;
                                            $selected = ( $order_by == $k ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="order" id="ays-chart-order">
                                        <?php
                                        foreach ( $order_values as $k => $v ):
                                            $selected = ( $order == $k ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <button type="submit" name="ays_chart_filter" id="ays-chart-filter"><?php echo esc_html(__( 'Filter', "chart-builder" )); ?></button>
                                    <button type="submit" name="ays_chart_filter_clear" id="ays-chart-filter-clear"><?php echo esc_html(__( 'Clear filters', "chart-builder" )); ?></button>
                                </div>
                            </div>
                            <div class="ays-chart-search-block">
                                <input type="text" name="s" id="ays-chart-search-input" value="<?php echo $search; ?>">
                                <button type="submit" name="ays_chart_search" id="ays-chart-search"><?php echo esc_html(__( 'Search', "chart-builder" )); ?></button>
                            </div>
                        </div>
                        <table class="chart-list-table table">
                            <thead>
                                <tr>
                                    <th class="column-cb">
                                        <input type="checkbox" class="form-check-input select-all" value="" />
                                    </th>
                                    <th class="column-title"><?php echo esc_html(__( 'Title', "chart-builder" )); ?></th>
                                    <th class="column-type"><?php echo esc_html(__( 'Type', "chart-builder" )); ?></th>
                                    <th class="column-source-type"><?php echo esc_html(__( 'Source', "chart-builder" )); ?></th>
                                    <th class="column-chart-source"><?php echo esc_html(__( 'Chart Source', "chart-builder" )); ?></th>
                                    <th class="column-shortcode"><?php echo esc_html(__( 'Shortcode', "chart-builder" )); ?></th>
                                    <th class="column-author"><?php echo esc_html(__( 'Author', "chart-builder" )); ?></th>
                                    <th class="column-status"><?php echo esc_html(__( 'Status', "chart-builder" )); ?></th>
                                    <th class="column-date"><?php echo esc_html(__( 'Date', "chart-builder" )); ?></th>
                                    <th class="column-id"><?php echo esc_html(__( 'ID', "chart-builder" )); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ( !empty( $items ) ): ?>
                            <?php foreach ( $items as $key => $item ): ?>
                                <tr>
                                    <td class="column-cb">
                                        <input type="checkbox" class="form-check-input check-current-row" name="bulk-delete[]" value="<?php echo esc_attr($item['id']) ?>" />
                                    </td>
                                    <td class="column-title"><?php
                                        if($item['status'] == 'trashed'){
                                            $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-item' );
                                        }else{
                                            $delete_nonce = wp_create_nonce( $this->plugin_name . '-trash-item' );
                                        }
                                        $publish_nonce = wp_create_nonce( $this->plugin_name . '-publish-item' );
                                        $unpublish_nonce = wp_create_nonce( $this->plugin_name . '-unpublish-item' );
                                        $duplicate_nonce = wp_create_nonce( $this->plugin_name . '-duplicate-item' );
                                        $chart_title = stripcslashes( $item['title'] );
                                        $q = esc_attr( $chart_title );

                                        $restitle = Chart_Builder_Admin::ays_restriction_string("word", $chart_title, $chart_title_length);

                                        $title = sprintf( '<a href="?page=%s&action=%s&id=%d" title="%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $q, $restitle);

                                        $actions = array();
                                        if($item['status'] == 'trashed'){
                                            $title = sprintf( '<strong><a>%s</a></strong>', $restitle );
                                            $actions['restore'] = sprintf( '<a href="?page=%s&action=%s&id=%d&_wpnonce=%s">'. __('Restore', "chart-builder") .'</a>', esc_attr( $_REQUEST['page'] ), 'restore', absint( $item['id'] ), $delete_nonce );
                                            $actions['delete'] = sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&id=%s&_wpnonce=%s">'. __('Delete Permanently', "chart-builder") .'</a>', $restitle, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce );
                                        }else{
                                            $actions['edit'] = sprintf( '<a class="btn btn-primary btn-sm" href="?page=%s&action=%s&id=%d" data-bs-toggle="tooltip" title="'. esc_html(__('Edit',"chart-builder")) .'"><i class="ays_fa ays_fa_pen_to_square"></i></a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) );
                                            
                                            $draft_text = '';
                                            if( $item['status'] == 'draft' && !( isset( $_GET['fstatus'] ) && $_GET['fstatus'] == 'draft' )){
                                                $draft_text = ' — ' . '<span class="post-state">' . __( "Draft", "chart-builder" ) . '</span>';
                                                $actions['publish'] = sprintf( '<a class="btn btn-primary btn-sm" href="?page=%s&action=%s&id=%s&_wpnonce=%s" data-bs-toggle="tooltip" title="'. esc_html(__('Publish',"chart-builder")) .'"><i class="ays_fa ays_fa_unpublished"></i></a>', esc_attr( $_REQUEST['page'] ), 'publish', absint( $item['id'] ), $publish_nonce );
                                            } else {
                                                $actions['unpublish'] = sprintf( '<a class="btn btn-primary btn-sm" href="?page=%s&action=%s&id=%s&_wpnonce=%s" data-bs-toggle="tooltip" title="'. esc_html(__('Unpublish',"chart-builder")) .'"><i class="ays_fa ays_fa_published"></i></a>', esc_attr( $_REQUEST['page'] ), 'unpublish', absint( $item['id'] ), $unpublish_nonce );
                                            }

                                            $title = sprintf( '<strong><a href="?page=%s&action=%s&id=%d" title="%s">%s</a>%s</strong>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $q, $restitle, $draft_text );

                                            $actions['duplicate'] = sprintf( '<a class="btn btn-primary btn-sm" href="?page=%s&action=%s&id=%s&_wpnonce=%s" data-bs-toggle="tooltip" title="'. esc_html(__('Duplicate',"chart-builder")) .'"><i class="ays_fa ays_fa_copy"></i></a>', esc_attr( $_REQUEST['page'] ), 'duplicate', absint( $item['id'] ), $duplicate_nonce );
                                            
                                            $actions['delete'] = sprintf( '<a class="ays_chart_delete_confirm btn btn-danger btn-sm" href="?page=%s&action=%s&id=%s&_wpnonce=%s" data-bs-toggle="tooltip" title="'. esc_html(__('Delete',"chart-builder")) .'"><i class="ays_fa ays_fa_trash_o"></i></a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce );
                                        }
                                        echo wp_kses_post($title);

                                        echo '<p class="chart-list-table-actions-row">';
                                        foreach ( $actions as $action => $action_html ){
                                            $link_class = '';
                                            // if( $action == 'delete' ){
                                            //     $link_class = 'link-danger';
                                            // }
                                            echo '<span class="chart-list-table-action-link ' . $link_class . '">' . $action_html . '</span>';
                                        }
                                        echo '</p>';
                                    ?></td>
                                    <td class="column-type"><?php
		                                switch ($item['source_chart_type']) {
                                            case 'line_chart':
				                                echo "<p><img src='" . CHART_BUILDER_ADMIN_URL  . "/images/icons/line-chart.png" . "' width='20px'>";
				                                echo "<span style='margin-left: 8px'>" . __('Line Chart', $this->plugin_name) . "</span></p>";
				                                break;

			                                case 'bar_chart':
				                                echo "<p><img src='" . esc_url(CHART_BUILDER_ADMIN_URL)  . "/images/icons/bar-chart.png" . "' width='20px'>";
				                                echo "<span style='margin-left: 8px'>" . __('Bar Chart', "chart-builder") . "</span></p>";
				                                break;

			                                case 'pie_chart':
				                                echo "<p><img src='" . esc_url(CHART_BUILDER_ADMIN_URL)  . "/images/icons/pie-chart.png" . "' width='20px'>";
				                                echo "<span style='margin-left: 8px'>" . __('Pie Chart', "chart-builder") . "</span></p>";
				                                break;

			                                case 'column_chart':
				                                echo "<p><img src='" . esc_url(CHART_BUILDER_ADMIN_URL)  . "/images/icons/column-chart.png" . "' width='20px'>";
				                                echo "<span style='margin-left: 8px'>" . __('Column Chart', "chart-builder") . "</span></p>";
				                                break;
		                                
                                            case 'donut_chart':
                                                echo "<p><img src='" . esc_url(CHART_BUILDER_ADMIN_URL)  . "/images/icons/donut-chart.png" . "' width='20px'>";
                                                echo "<span style='margin-left: 8px'>" . __('Donut Chart', "chart-builder") . "</span></p>";
                                                break;
                                                
                                            case 'org_chart':
                                                echo "<p><img src='" . CHART_BUILDER_ADMIN_URL  . "/images/icons/org-chart.png" . "' width='20px'>";
                                                echo "<span style='margin-left: 8px'>" . __('Org Chart', $this->plugin_name) . "</span></p>";
                                                break;
                                        }
                                    ?></td>
                                    <td class="column-source-type"><?php
		                                switch ($item['source_type']) {
                                            case 'quiz_maker':
				                                echo "<span style='margin-left: 8px'>" . __('Quiz Maker', $this->plugin_name) . "</span>";
				                                break;
                                            case 'manual':
                                            default:
				                                echo "<span style='margin-left: 8px'>" . __('Manual', $this->plugin_name) . "</span>";
				                                break;
		                                }
                                    ?></td>
                                    <td class="column-chart-source"><?php
		                                switch ($item['type']) {
                                            case 'chart-js':
				                                echo "<span style='margin-left: 8px'>" . __('Chart.js', $this->plugin_name) . "</span>";
				                                break;
                                            case 'google-charts':
                                            default:
                                                echo "<span style='margin-left: 8px'>" . __('Google Charts', $this->plugin_name) . "</span>";
                                            break;
		                                }
                                    ?></td>
                                    <td class="column-shortcode">
                                        <div class="ays-chart-shortcode-container">
                                            <div class="ays-chart-copy-image" data-bs-toggle="tooltip" title="<?php echo esc_html(__('Click to copy',"chart-builder"));?>">
                                                <img src='<?php echo esc_url(CHART_BUILDER_ADMIN_URL) . "/images/icons/copy-image.svg" ?>'>
                                            </div>
                                            <!-- <input type="text" class="ays-chart-shortcode-input" onClick="this.setSelectionRange(0, this.value.length)" readonly value="<//?= esc_attr('[ays_chart id="'. $item['id'] .'"]') ?>" /> -->
                                            <input type="text" class="ays-chart-shortcode-input" readonly value="<?php echo esc_attr('[ays_chart id="'. $item['id'] .'"]') ?>" />
                                        </div>
                                    </td>
                                    <td class="column-author"><?php
		                                $author = get_user_by("id", $item['author_id']);
                                        if( $author ){
                                            $author_name = $author->data->display_name;
                                            echo esc_html($author_name);
                                        } else {
                                            echo '<span style="color:red">'.__('Deleted user', 'chart-builder').'</span>';
                                        }
                                    ?></td>
                                    <td class="column-status"><?php
                                        $status = ucfirst( $item['status'] );
                                        $html = "<p style='font-size:14px;margin:0;'>" . $status . "</p>";
                                        
                                		echo wp_kses_post( $html );
                                    ?></td>
                                    <td class="column-date"><?php
                                        $date = date( 'Y/m/d', strtotime( $item['date_modified'] ) );
                                        $title_date = date( 'l jS \of F Y h:i:s A', strtotime( $item['date_modified'] ) );
                                        $html = "<p style='font-size:14px;margin:0;text-decoration: dotted underline;' title='" . $title_date . "'>" . $date . "</p>";
                                        
                                		echo wp_kses_post( $html );
                                    ?></td>
                                    <td class="column-id"><?php echo esc_attr($item['id']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8"><?php echo esc_html(__( 'There are no items yet.', "chart-builder" )); ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="ays-chart-table-actions-row">
                            <div class="ays-chart-table-actions-row-section">
                                <div class="ays-chart-delete-button">
                                    <button name="bulk_delete" id="ays-chart-bulk-delete-bottom" disabled><i class="ays_fa ays_fa_trash"></i><?php echo esc_html(__( 'Delete', "chart-builder" )); ?></button>
                                    <button type="submit" name="bulk_delete_confirm" id="ays-chart-bulk-delete-confirm-bottom" style="display: none;"></button>
                                </div>
                                <div class="ays-chart-filter-section">
                                    <select name="filterbytype" id="ays-chart-filter-select-bottom">
                                        <option value=""><?= __( "Select Type", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $chart_types as $k => $v ):
                                            $selected = ( $filter_by_type == ($k+1) ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k+1); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="filterbysource" id="ays-chart-filter-source-bottom">
                                        <option value=""><?= __( "Select Source", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $chart_sources as $k => $v ):
                                            $disabled = $k >= 2 ? 'disabled' : '' ;
                                            $selected = ( $filter_by_source == ($k+1) ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k+1); ?>" <?php echo esc_attr($selected); ?> <?php echo $disabled; ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="filterbychartsource" id="ays-chart-filter-chart-source-bottom">
                                        <option value=""><?= __( "Select Chart Source", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $chart_source_types as $k => $v ):
                                            $disabled = $k >= 2 ? 'disabled' : '' ;
                                            $selected = ( $filter_by_chart_source == ($k+1) ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k+1); ?>" <?php echo esc_attr($selected); ?> <?php echo $disabled; ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="filterbydate" id="ays-chart-filter-date-bottom">
                                        <option value=""><?= __( "Select Date", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $chart_dates as $k => $v ):
                                            $selected = ( $filter_by_date == $k ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="filterbyauthor" id="ays-chart-filter-author-bottom">
                                        <?php if (isset($filter_by_author_data) && !empty($filter_by_author_data)): ?>
                                            <option value="<?php echo esc_html($filter_by_author_data['ID'])?>" selected><?php echo esc_html($filter_by_author_data['display_name'])?></option>
                                        <?php endif; ?>
                                    </select>
                                    <select name="orderby" id="ays-chart-order-by-bottom">
                                        <option value=""><?= __( "Order by", "chart-builder" ) ?></option>
                                        <?php
                                        foreach ( $order_by_values as $k => $v ):
                                            // $disabled = $k >= 2 ? 'disabled' : '' ;
                                            $selected = ( $order_by == $k ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <select name="order" id="ays-chart-order-bottom">
                                        <?php
                                        foreach ( $order_values as $k => $v ):
                                            $selected = ( $order == $k ) ? 'selected' : '';
                                            ?>
                                            <option value="<?php echo esc_attr($k); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($v); ?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                    <button type="submit" name="ays_chart_filter" id="ays-chart-filter-bottom"><?php echo esc_html(__( 'Filter', "chart-builder" )); ?></button>
                                    <button type="submit" name="ays_chart_filter_clear" id="ays-chart-filter-clear-bottom"><?php echo esc_html(__( 'Clear filters', "chart-builder" )); ?></button>
                                </div>
                            </div>
                            <?php if($chart_count_per_page > 1):?>
                                <div class>
                                    <nav aria-label="Pagination" class="m-0">
                                        <ul class="pagination m-0 p-2">
                                        <?php for( $i = 0; $i < $chart_count_per_page; $i++ ):
                                            $url = esc_url_raw( remove_query_arg( false ) );
                                            $url = esc_url_raw( add_query_arg( array(
                                                'paged' => $i + 1
                                            ), $url ) );

                                            $active = '';
                                            if( $chart_paged != '' ){
                                                if( $chart_paged == $i + 1 ) {
                                                    $active = 'active';
                                                }
                                            } else {
                                                wp_safe_redirect($_SERVER['REQUEST_URI'] . "&paged=1");
                                            }
                                            ?>
                                            <li class="page-item <?php echo esc_attr($active) ?>">
                                                <a class="page-link" href="<?php echo esc_url($url) ?>"><?php echo esc_attr(absint($i)) + 1; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        </ul>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
        <div class="ays-chart-add-new-button-box">
            <?php
                echo sprintf( '<a href="?page=%s&action=%s" class="btn btn-primary chart-add-new-bttn chart-add-new-button-new-design"> %s ' . __( 'Add New', "chart-builder" ) . '</a>', esc_attr( $_REQUEST['page'] ), 'add', $plus_icon_svg);
            ?>
        </div>
        <?php if($chart_max_id <= 3): ?>
            <div class="ays-chart-create-chart-video-box" style="margin: 0 auto 30px;">
                <div class="ays-chart-create-chart-title">
                    <h4><?php echo __( "Create Your First Chart in Under One Minute", $this->plugin_name ); ?></h4>
                </div>
                <div class="ays-chart-create-chart-youtube-video">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/ysjUMK0HH3c" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" loading="lazy" allowfullscreen style="max-width: 100%;"></iframe>
                </div>
                <div class="ays_chart_small_hint_text_for_message_variables" style="text-align: center;">
                    <?php echo __( 'Please note that this video will disappear once you created 4 charts.', $this->plugin_name ); ?>
                </div>
                <div class="ays-chart-create-chart-youtube-video-button-box">
                    <?php echo sprintf( '<a href="?page=%s&action=%s" class="ays-chart-add-new-button-video chart-add-new-button-new-design"> %s ' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add', $plus_icon_svg);?>
                </div>
            </div>
        <?php else: ?>
            <div class="ays-chart-create-chart-video-box ays-chart-create-chart-video-box-only-link" style="margin: auto;">
                <div class="ays-chart-create-chart-youtube-video">
                    <?php echo $youtube_icon_svg; ?>
                    <a href="https://www.youtube.com/watch?v=ysjUMK0HH3c" target="_blank" style="color:#2271b1;text-decoration:none" title="YouTube video player"><?php echo __("How to create chart in one minute?", $this->plugin_name); ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
