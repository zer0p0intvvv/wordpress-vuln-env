<div class="content-inner container-fluid pb-0" id="page_layout">
    <div class="row">
        <div class="col-lg-3 col-md-4">
            <div class="card">
                <div class="card-body">
                <div class="nav flex-column setting-tabs gap-3" id="general-tab" role="tablist" aria-orientation="vertical">
                <?php
                $active_tab_target = 'general-home';
                foreach ($nav_menu as $nav_menu_key => $nav_menu_value) :
                    $is_selected = isset($nav_menu_value['is_selected']) ? $nav_menu_value['is_selected'] : 'false';
                    
                    // Check if 'target' and 'text' keys exist
                    if (isset($nav_menu_value['target']) && isset($nav_menu_value['text'])) {
                        // Set active tab target if selected
                        if ($is_selected == 'true') {
                            $active_tab_target = $nav_menu_value['target'];
                        }

                        ?>
                        <a class="nav-link <?php echo esc_attr($is_selected == 'true' ? 'active' : ''); ?>" id="<?php echo esc_attr($nav_menu_key); ?>" data-bs-toggle="pill" data-bs-target="<?php echo esc_attr('#' . $nav_menu_value['target']); ?>" type="button" role="tab" aria-controls="<?php echo esc_attr($nav_menu_value['target']); ?>" aria-selected="<?php echo esc_attr($is_selected); ?>">
                            <?php echo esc_html($nav_menu_value['text']); ?>
                        </a>
                    <?php
                    }
                endforeach; ?>
            </div>

                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content iq-tab-fade-up" id="general-tabContent">
                        <?php if (current_user_can('administrator')) : ?>
                            <div class="tab-pane fade <?php echo esc_attr( $active_tab_target === 'general-home' ? ' show active ' : '' );?>" id="general-home" role="tabpanel" aria-labelledby="general-tab">
                                <form id="general_setting_form">
                                    <div class="form-group">
                                        <div class="mb-4 require-guest-email-address-field">
                                            <label class="form-label d-block"><?php esc_html_e('Guest Booking Options', 'wpbookit'); ?></label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="true" <?php echo esc_html($general_setting_data['require_guest_email_address'] ?? 'false' == 'true' ? "checked" : ''); ?> id="require_guest_email_address" name="require_guest_email_address" />
                                                <label class="form-check-label" for="require_guest_email_address">
                                                    <?php esc_html_e('Require Email Address — Require your guests to enter their email address.', 'wpbookit'); ?>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="true" <?php echo esc_html($general_setting_data['require_guest_phone_number'] ?? 'false' == 'true' ? "checked" : ''); ?> id="require_guest_phone_number" name="require_guest_phone_number" />
                                                <label class="form-check-label" for="require_guest_phone_number">
                                                    <?php esc_html_e('Require Phone Number — Ensure guests provide their phone number', 'wpbookit'); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                  

                                    <div class="form-group mb-4">
                                        <label class="form-label d-block"><?php esc_html_e('Booking Options', 'wpbookit'); ?></label>
                                        <?php foreach ($booking_options as $booking_options_key => $booking_options_value) : ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="booking_options" id="<?php echo esc_attr($booking_options_key); ?>" value="<?php echo esc_attr($booking_options_value['value']); ?>" <?php checked($general_setting_data['booking_options'], $booking_options_value['value']); ?>>

                                                <label class="form-check-label" for="<?php echo esc_attr($booking_options_key); ?>"><?php echo esc_html($booking_options_value['text']); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label"><?php esc_html_e('Currency', 'wpbookit'); ?></label>
                                        <select class="select2-basic-single js-states form-select form-control" name="currency">
                                            <?php foreach ($currencies as $curr_code => $curr_label) : ?>
                                                <option value="<?php echo esc_attr($curr_code); ?>" <?php selected($general_setting_data['currency'], $curr_code); ?>>
                                                    <?php echo esc_html ($curr_label . ' — ' . $curr_code); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group mb-4 ">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label"><?php esc_html_e('Currency Prefix', 'wpbookit'); ?></label>
                                                    <input class="form-control" type="text" value="<?php echo esc_html($general_setting_data['prefix'] ?? ""); ?>" name="prefix" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label"><?php esc_html_e('CurrencyPostfix', 'wpbookit'); ?></label>
                                                    <input class="form-control" type="text" value="<?php echo esc_html($general_setting_data['postfix'] ?? ""); ?>" name="postfix" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    <div class="form-group mb-4">
                                        <label class="form-label"><?php esc_html_e('Default Status For New Booking', 'wpbookit'); ?></label>
                                        <select class="select2-basic-single js-states form-select form-control" name="booking_status">
                                            <?php foreach ($booking_status as $booking_status_key => $booking_status_value) : ?>
                                                <option value="<?php echo esc_attr($booking_status_key); ?>" <?php selected($general_setting_data['booking_status'], $booking_status_key); ?>><?php echo esc_html($booking_status_value); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label"><?php esc_html_e('Pending Booking Limit For Registered Users', 'wpbookit'); ?></label>
                                        <select class="select2-basic-single js-states form-select form-control" name="booking_limit">
                                            <?php foreach ($booking_limit as $booking_limit_key => $booking_limit_value) : ?>
                                                <option value="<?php echo esc_attr($booking_limit_key); ?>" <?php selected($general_setting_data['booking_limit'], $booking_limit_key); ?>><?php echo esc_html($booking_limit_value); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-4 d-none">
                                        <label class="form-label"><?php esc_html_e('Cancellation Buffer', 'wpbookit'); ?></label>
                                        <select class="select2-basic-single js-states form-select form-control" name="cancellation_buffer">
                                            <?php foreach ($cancellation_buffer as $cancellation_buffer_key => $cancellation_buffer_value) : ?>
                                                <option value="<?php echo esc_attr($cancellation_buffer_key); ?>" <?php selected($general_setting_data['cancellation_buffer'], $cancellation_buffer_key); ?>>
                                                    <?php echo esc_html($cancellation_buffer_value); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label d-flex">
                                            <div class="disabled-field">
                                                <?php esc_html_e("Login Redirect",'wpbookit'); ?>                                         
                                            </div>
                                            <?php wpb_render_pro_lable();?>
                                        </label>
                                        <select class="select2-basic-single js-states form-select form-control" disabled >
                                            <option  selected >
                                                <?php esc_html_e("Please Upgrade Pro Version to use this",'wpbookit'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label class="form-label d-flex">
                                            <div class="disabled-field">
                                                <?php esc_html_e("Booking Redirect",'wpbookit'); ?>                                         
                                            </div>
                                             <?php wpb_render_pro_lable();?>
                                        </label>
                                        <?php foreach ($booking_redirect as $booking_redirect_key => $booking_redirect_value) : ?>
                                            <div class="form-check">
                                                <input disabled class="form-check-input booking_redirect" type="radio" id="<?php echo esc_attr($booking_redirect_key??''); ?>" value="<?php echo esc_attr($booking_redirect_value['value']??''); ?>" <?php checked($general_setting_data['booking_redirect']??'', $booking_redirect_value['value']??''); ?>>
                                                <label class="form-check-label" for="<?php echo esc_attr($booking_redirect_key??''); ?>"><?php echo esc_html($booking_redirect_value['text']??''); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                        <div id="booking_redirect_section" class="d-none">
                                            <select  class="select2-basic-single js-states form-select form-control" >
                                                <?php foreach ($login_redirect as $booking_redirect_url_key => $booking_redirect_url_value) : ?>
                                                    <option value="<?php echo esc_attr($booking_redirect_url_key); ?>" <?php selected($general_setting_data['booking_redirect_url'], $booking_redirect_url_key); ?>>
                                                        <?php echo esc_html($booking_redirect_url_value); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mb-0 mb-3">
                                        <label class="form-label d-flex">
                                            <div class="disabled-field">
                                                <?php esc_html_e("Base URL Slug",'wpbookit'); ?>                                         
                                            </div>
                                             <?php wpb_render_pro_lable();?>
                                        </label>
                                        <input disabled class="form-control form-control" type=text value="<?php echo esc_html($general_setting_data['permalink_strcture'] ?? 'booking'); ?>" placeholder="<?php esc_html_e("Enter Slug", 'wpbookit') ?>" />
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label d-block disabled-field"><?php esc_html_e('Hide Header or Footer on Booking Share URL Page', 'wpbookit'); ?>
                                        <?php wpb_render_pro_lable();?>
                                    </label>
                                        <?php foreach ($this->get_booking_header_footer_options() as $booking_options_key => $booking_options_value) : ?>
                                            <div class="form-check">
                                                <input disabled class="form-check-input" type="checkbox"  id="<?php echo esc_attr($booking_options_key); ?>" value="<?php echo esc_attr($booking_options_value['value']); ?>" <?php checked($general_setting_data[$booking_options_key] ?? false, $booking_options_value['value']); ?>>
                                                <label class="form-check-label" for="<?php echo esc_attr($booking_options_key); ?>"><?php echo esc_html($booking_options_value['text']); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary general_setting_sub_button">
                                            <svg class="spinner wpb-general-settings-submit-svg d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                                            </svg>
                                            <?php esc_html_e("Save", 'wpbookit') ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php if (current_user_can('administrator')) : ?>
                            <div class="tab-pane fade" id="theme-settings" role="tabpanel" aria-labelledby="theme-settings">
                                <form id="theme_settings_form" enctype="multipart/form-data">
                                    <div class="form-group mb-4 wpb-site_logo">
                                        <span class="error small" id="site_logo_error"></span>
                                        <div class="d-flex align-items-center gap-2">
                                            <label for="site_logo" class="mb-0 form-label disabled-field">
                                                <?php esc_html_e('Upload Site Logo:', 'wpbookit'); ?>
                                                <span type="button" disabled class="btn btn btn-primary-subtle">
                                                    <?php esc_html_e('Upload', 'wpbookit'); ?>
                                                </span>
                                            </label>
                                             <?php wpb_render_pro_lable();?>
                                            <input class="file-upload" type="file" name="site_logo" id="site_logo" accept="image/*" disabled>
                                        </div>

                                        <?php
                                        $old_site_logo = 0;
                                        $show_remove_btn = 'block';
                                        if (!empty($theme_setting_data['site_logo'])) {
                                            $site_logo_url = wp_get_attachment_url($theme_setting_data['site_logo']);
                                            $old_site_logo = $theme_setting_data['site_logo'];
                                        } else {
                                            $site_logo_url = IQWPB_PLUGIN_URL . '/core/admin/assets/images/logo.png';
                                            $show_remove_btn = 'none';
                                        }
                                        ?>
                                        <input type="hidden" name="old_site_logo" id="old_site_logo" value="<?php echo esc_attr($old_site_logo); ?>">

                                        <?php wp_nonce_field('site_logo_upload', 'site_logo_nonce'); ?>
                                        <div id="site_logo_image_preview" class="disabled-field">
                                            <div class="booking-cover-image">
                                                <img src="<?php echo esc_attr($site_logo_url); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" class="site_logo_preview" />
                                            </div>
                                            <button type="button" id="site_logo_preview_btn" class="btn-close text-reset shadow-none btn-close-icon-white" style="display: <?php echo esc_attr($show_remove_btn); ?>;"></button>
                                        </div>
                                    </div>
                                    <div class="form-group mb-0 mb-3">
                                        <label class="form-label disabled-field">
                                            <?php esc_html_e('Dashboard Name', 'wpbookit'); ?>
                                        </label>
                                         <?php wpb_render_pro_lable();?>
                                        <input class="form-control" type="text" disabled
                                            value="<?php echo isset($theme_setting_data['dashboard_name']) ? esc_html($theme_setting_data['dashboard_name']) : 'WPBookit'; ?>" 
                                            name="dashboard_name" />
                                    </div>
                                    <div class="form-group mb-0 mb-3">
                                        <label class="form-label disabled-field">
                                            <?php esc_html_e('Copyright Text', 'wpbookit'); ?>
                                        </label>
                                         <?php wpb_render_pro_lable();?>
                                        <input class="form-control " disabled type="text"
                                            value="<?php echo isset($theme_setting_data['copyright_text']) ? esc_html($theme_setting_data['copyright_text']) : ''; ?>" 
                                            name="copyright_text" />
                                    </div>
                                  

                                    <div class=" d-flex ">
                                        <div class="col-sm-3 d-flex align-items-center mx-1">
                                            <button disabled type="submit" class="btn btn-primary theme_settings_sub_button">
                                                <svg class="spinner wpb-theme-settings-submit-svg d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                                                </svg>
                                                <?php esc_html_e("Save", 'wpbookit') ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php if (current_user_can('administrator')) : ?>
                            <div class="tab-pane fade" id="emails" role="tabpanel" aria-labelledby="emails-tab">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <div class="table-wrapper rounded mb-4">
                                                <table class="table custome-table" id="setting-emails-table">
                                                    <thead>
                                                        <tr>
                                                            <?php
                                                            if (!empty($this->get_email_options()) && is_array($this->get_email_options())) :
                                                                foreach ($this->get_email_options() as $column_id => $column_name) : ?>
                                                                    <th scope="col" class="<?php echo esc_attr($column_id); ?>"><span class="nobr"><?php echo esc_html($column_name); ?></span>
                                                                    </th>
                                                            <?php endforeach;
                                                            endif; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $emails = wpb_get_email(); ?>
                                                        <?php foreach ($emails as $email) : ?>
                                                            <tr data-id="<?php echo esc_attr($email['id']); ?>">
                                                                <td>
                                                                    <div class="media-support-info">
                                                                        <h6 class="iq-sub-label" name="heading">
                                                                            <?php
                                                                            echo sprintf("%s", esc_html($email['emails_title']));
                                                                            ?>

                                                                        </h6>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="media-support-info">
                                                                        <div name="role">
                                                                            <?php echo esc_html($email['role']); ?>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="media-support-info">
                                                                        <h6 class="iq-sub-label" name="status">
                                                                            <div class="form-check form-switch form-status">
                                                                                <input class="form-check-input" name="status" data-id="<?php echo esc_attr($email['id']); ?>" id="data-status" type="checkbox" <?php echo (($email['status'] == '1') ? 'checked' : ''); ?>>
                                                                            </div>
                                                                        </h6>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <a class="edit-booking-button" name="advance-email-options" data-bs-toggle="offcanvas" data-bs-target="#email-options" role="button" aria-controls="email-options" data-id="<?php echo esc_attr($email['id']); ?>">
                                                                        <img src="<?php echo esc_attr(IQWPB_PLUGIN_URL . 'core/admin/assets/images/edit-icon.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked">
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="tab-pane fade <?php echo esc_attr(!in_array('administrator', wp_get_current_user()->roles) ? 'show active' : ''); ?>" id="calender" role="tabpanel" aria-labelledby="calender-tab">
                            <div class="row disabled-field">
                                <div class="col-lg-12">
                                    <h4 class="mb-3">
                                        <span class="me-1">
                                            <svg class="align-bottom" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M10.7999 17.6998L13.1999 6.2998M5.9999 15.8998L2.3999 12.2998L5.9999 8.69981M17.9999 8.6998L21.5999 12.2998L17.9999 15.8998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <?php esc_html_e('Calendar Feeds', 'wpbookit'); ?>
                                         <?php wpb_render_pro_lable();?>

                                    </h4>
                                    <p class="mb-4">
                                        <?php esc_html_e('Use the following URLs to either download a static feed (not auto-updating) or paste the URL into your favorite calendar app (Google Calendar, Apple Calendar, etc.) as a subscription to load a read-only auto-updating booking feed.', 'wpbookit'); ?>
                                    </p>
                                    <div class="row">
                                        <div class="col-lg-7 mb-3">
                                            <h6><?php esc_html_e('All Bookings', 'wpbookit'); ?></h6>
                                            <div class="d-flex align-items-center">
                                                <input type="text" class="form-control" value="<?php echo esc_url(get_site_url()); ?>?wpbookit_ical" readonly />
                                                <span class="mx-2 d-inline-block">
                                                    <a href="javascript: void(0);" class="text-body wpb-copy-button" data-toggle="tooltip" title="<?php esc_attr_e('Copied', 'wpbookit'); ?>">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17" fill="none">
                                                            <path d="M1.33399 9.4375L1.33399 4.33333C1.33399 2.49238 2.82637 0.999999 4.66732 1L9.77149 1M6.33399 16L11.959 16C12.9945 16 13.834 15.1605 13.834 14.125L13.834 6C13.834 4.96447 12.9945 4.125 11.959 4.125L6.33399 4.125C5.29845 4.125 4.45899 4.96447 4.45899 6L4.45899 14.125C4.45898 15.1605 5.29845 16 6.33399 16Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" />
                                                        </svg>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>

                                        <?php foreach ($booking_types as $booking_type) : ?>
                                            <div class="col-lg-7 mb-3">
                                                <h6><?php echo esc_html($booking_type->get_name()); ?></h6>
                                                <div class="d-flex align-items-center">
                                                    <input disabled type="text" class="form-control" value="<?php echo esc_url(get_site_url()); ?>?wpbookit_ical&booking_type=<?php echo esc_html($booking_type->get_id()) ?>" readonly />
                                                    <span class="mx-2 d-inline-block">
                                                        <a href="javascript: void(0);" class="text-body" data-toggle="tooltip" title="<?php esc_attr_e('Copied', 'wpbookit'); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17" fill="none">
                                                                <path d="M1.33399 9.4375L1.33399 4.33333C1.33399 2.49238 2.82637 0.999999 4.66732 1L9.77149 1M6.33399 16L11.959 16C12.9945 16 13.834 15.1605 13.834 14.125L13.834 6C13.834 4.96447 12.9945 4.125 11.959 4.125L6.33399 4.125C5.29845 4.125 4.45899 4.96447 4.45899 6L4.45899 14.125C4.45898 15.1605 5.29845 16 6.33399 16Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" />
                                                            </svg>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (current_user_can('administrator')) : ?>
                            <div class="tab-pane fade" id="shortcode" role="tabpanel" aria-labelledby="shortcode-tab">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <h6><?php esc_html_e('Display the Login / Register Form', 'wpbookit'); ?></h6>
                                        <p>
                                            <?php esc_html_e('If the Registration tab doesn\'t show up, be sure to allow registrations from the Settings > General page.', 'wpbookit'); ?>
                                        </p>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="d-flex align-items-center">
                                                    <input type="text" class="form-control w-auto" value="[wpb-login]" disabled />
                                                    <span class="mx-2 d-inline-block">
                                                        <a href="javascript: void(0);" class="text-body wpb-copy-button align-middle" data-toggle="tooltip" title="<?php esc_attr_e('Copied', 'wpbookit'); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17" fill="none">
                                                                <path d="M1.33399 9.4375L1.33399 4.33333C1.33399 2.49238 2.82637 0.999999 4.66732 1L9.77149 1M6.33399 16L11.959 16C12.9945 16 13.834 15.1605 13.834 14.125L13.834 6C13.834 4.96447 12.9945 4.125 11.959 4.125L6.33399 4.125C5.29845 4.125 4.45899 4.96447 4.45899 6L4.45899 14.125C4.45898 15.1605 5.29845 16 6.33399 16Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" />
                                                            </svg>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h6><?php esc_html_e('Display User Profile', 'wpbookit'); ?></h6>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="d-flex align-items-center">
                                                    <input type="text" class="form-control w-auto" value="[wpb-profile]" disabled />
                                                    <span class="mx-2 d-inline-block">
                                                        <a href="javascript: void(0);" class="text-body wpb-copy-button align-middle" data-toggle="tooltip" title="<?php esc_attr_e('Copied', 'wpbookit'); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17" fill="none">
                                                                <path d="M1.33399 9.4375L1.33399 4.33333C1.33399 2.49238 2.82637 0.999999 4.66732 1L9.77149 1M6.33399 16L11.959 16C12.9945 16 13.834 15.1605 13.834 14.125L13.834 6C13.834 4.96447 12.9945 4.125 11.959 4.125L6.33399 4.125C5.29845 4.125 4.45899 4.96447 4.45899 6L4.45899 14.125C4.45898 15.1605 5.29845 16 6.33399 16Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" />
                                                            </svg>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-12 disabled-field">
                                        <h6><?php esc_html_e('Display Booking Type List', 'wpbookit'); ?> <?php wpb_render_pro_lable();?></h6>
                                        <p>
                                        </p>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="d-flex align-items-center">
                                                    <input type="text" class="form-control w-auto" value="[wpb-booking-types]" disabled />
                                                    <span class="mx-2 d-inline-block">
                                                        <a href="javascript: void(0);" class="text-body wpb-copy-button align-middle" data-toggle="tooltip" title="<?php esc_attr_e('Copied', 'wpbookit'); ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17" fill="none">
                                                                <path d="M1.33399 9.4375L1.33399 4.33333C1.33399 2.49238 2.82637 0.999999 4.66732 1L9.77149 1M6.33399 16L11.959 16C12.9945 16 13.834 15.1605 13.834 14.125L13.834 6C13.834 4.96447 12.9945 4.125 11.959 4.125L6.33399 4.125C5.29845 4.125 4.45899 4.96447 4.45899 6L4.45899 14.125C4.45898 15.1605 5.29845 16 6.33399 16Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" />
                                                            </svg>
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <?php do_action('wpb_after_bookingtypelist_shorcode') ?>
                            </div>
                        <?php endif; ?>

                        <div class="tab-pane fade" id="telemed" role="tabpanel" aria-labelledby="telemed-tab">
                            <div class="row disabled-field">
                                <div class="col-md-6">
                                    <form id="wpb_zoom_setting_form" class="wpb_zoom_setting_form" novalidate method="post">
                                        <h5 class="d-block"><?php esc_html_e('Zoom Integration', 'wpbookit'); ?> <?php wpb_render_pro_lable();?></h5>
                                        <?php if(!in_array(WPBOOKIT()->helpers->get_staff_role(),wp_get_current_user( )->roles)):?>
                                        <div class="form-group mb-4">
                                            <div class="wpb-zoom-info">
                                                <h6 class="wp-sub-label" name="status">
                                                    <label class="form-label"><?php esc_html_e('Enabled / Disabled', 'wpbookit'); ?></label>
                                                    <div class="form-check form-switch form-status">
                                                        <input class="form-check-input" name="wpb_zoom_status" type="checkbox" <?php echo esc_attr((($wpb_zoom_status??'off') == 'on') ? 'checked' : ''); ?> />
                                                    </div>
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label class="form-label"><?php esc_html_e('Client ID', 'wpbookit'); ?>*</label>
                                            <input class="form-control " required type="text" value="<?php echo esc_attr($wpb_zoom_client_id??""); ?>" name="wpb_zoom_client_id" aria-describedby="zoom-client-id-error" />
                                            <div id="zoom-client-id-error" class="invalid-feedback">
                                                <?php esc_html_e("Please Enter Client ID", 'wpbookit') ?>
                                            </div>
                                        </div>
                                        <div class="form-group mb-4">
                                            <label class="form-label"><?php esc_html_e('Client Secret Key', 'wpbookit'); ?>*</label>
                                            <input class="form-control " required type="text" value="<?php echo esc_attr($wpb_zoom_client_secret??""); ?>" name="wpb_zoom_client_secret" aria-describedby="zoom-client-secret-error" />
                                            <div id="zoom-client-secret-error" class="invalid-feedback">
                                                <?php esc_html_e("Please Enter Client Secret Key", 'wpbookit') ?>
                                            </div>
                                        </div>
                                        <div class="form-group mb-4">
                                            <div class="d-flex">
                                                <label class="form-label"><?php esc_html_e('Authorized Redirect URI', 'wpbookit'); ?></label>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="d-flex align-items-center">
                                                        <input type="text" class="form-control" value="<?php echo esc_attr($wpb_zoom_client_redirect_uri); ?>" name="wpb_zoom_client_redirect_uri" readonly />
                                                        <span class="mx-2 d-inline-block">
                                                            <a href="javascript: void(0);" class="text-body wpb-copy-button align-middle" data-toggle="tooltip" title="<?php esc_attr_e('Copied', 'wpbookit'); ?>">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17" fill="none">
                                                                    <path d="M1.33399 9.4375L1.33399 4.33333C1.33399 2.49238 2.82637 0.999999 4.66732 1L9.77149 1M6.33399 16L11.959 16C12.9945 16 13.834 15.1605 13.834 14.125L13.834 6C13.834 4.96447 12.9945 4.125 11.959 4.125L6.33399 4.125C5.29845 4.125 4.45899 4.96447 4.45899 6L4.45899 14.125C4.45898 15.1605 5.29845 16 6.33399 16Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" />
                                                                </svg>
                                                            </a>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if(!in_array(WPBOOKIT()->helpers->get_staff_role(),wp_get_current_user( )->roles)):?>
                                        <div class="d-flex">
                                            <div class="col-sm-3 d-flex align-items-center mx-1">
                                                <button type="submit" class="btn btn-primary w-100 wpb_zoom_setting_sub_button">
                                                    <svg class="spinner wpb-zoom-settings-submit-spinner d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                        <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                                                    </svg>
                                                    <?php esc_html_e("Save", 'wpbookit') ?>
                                                </button>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <?php if(!in_array(WPBOOKIT()->helpers->get_staff_role(),wp_get_current_user( )->roles)):?>
                                <div class="col-md-6 mt-5 mt-md-0">
                                    <h5 data-v-2a6e6c1c="" class=" mb-3">Guide to setup Zoom Meet.</h5>
                                    <ul class="list-inline m-0 p-5 bg-body">
                                        <li class="mb-3">
                                            <h6 class="mb-2"><?php esc_html_e("Step 1: Sign in to the Zoom Developer Website",'wpbookit') ?></h6>
                                            <p><?php esc_html_e("Sign in to the Zoom Developer Website - ",'wpbookit') ?><a href="https://marketplace.zoom.us" target="_blank"><?php esc_html_e("https://marketplace.zoom.us",'wpbookit') ?></a></p>
                                        </li>
                                        <li class="mb-3">
                                            <h6 class="mb-2"><?php esc_html_e("Step 2: Build App",'wpbookit') ?></h6>
                                            <p><?php esc_html_e("In the header, select \"Build App\" and choose the OAuth type",'wpbookit') ?></p>
                                        </li>
                                        <li class="mb-3">
                                            <h6 class="mb-2"><?php esc_html_e("Step 3: Fill in Application Information",'wpbookit') ?></h6>
                                            <p><?php esc_html_e("Fill in your application information",'wpbookit') ?></p>
                                        </li>
                                        <li class="mb-3">
                                            <h6 class="mb-2"><?php esc_html_e("Step 4: Retrieve Developer Keys",'wpbookit') ?></h6>
                                            <p><?php esc_html_e("Retrieve your developer keys: Client ID and Client Secret",'wpbookit') ?></p>
                                        </li>
                                        <li class="mb-3">
                                            <h6 class="mb-2"><?php esc_html_e("Step 5: Select Scope",'wpbookit') ?></h6>
                                            <p><?php esc_html_e("Select the scope \"Create meeting scope\" from the scope section",'wpbookit') ?></p>
                                        </li>
                                        <li class="mb-3">
                                            <h6 class="mb-2"><?php esc_html_e("Step 6: Paste Credentials",'wpbookit') ?></h6>
                                            <p><?php esc_html_e("Paste the Client ID and Client Secret in the WPBookit Telemed section",'wpbookit') ?></p>
                                        </li>
                                        <li class="mb-3">
                                            <h6 class="mb-2"><?php esc_html_e("Step 7: Authorize",'wpbookit') ?></h6>
                                            <p><?php esc_html_e("Click on the authorize button",'wpbookit') ?></p>
                                        </li>
                                        <li class="mb-3">
                                            <h6 class="mb-2"><?php esc_html_e("That's it!",'wpbookit') ?></h6>
                                        </li>
                                    </ul>

                                </div>
                                <?php endif; ?>
                            </div>
                            <hr>
                            <div class="row mt-5">
                                <div class='col-md-6'>
                                    <?php do_action('wpb_add_navbar_menu_google_meet_html'); ?>
                                </div>
                                <div class='col-md-6'>
                                    <?php do_action('wpb_add_navbar_menu_google_meet_html_guide'); ?>
                                </div>
                            </div>
                            <?php do_action('wpb_add_navbar_menu_microsoft_team_meet_html'); ?>
                        </div>
                        <?php do_action('wpb_add_twilio_menu_custom_html');  ?>
                        <?php do_action('wpb_add_twilio_sms_wa_custom_html'); ?>
                        <?php do_action('wpb_add_whatsapp_notification_menu_html');  ?>
                        <?php do_action('wpb_add_ratings_menu_custom_html') ;  ?>
                        <?php do_action('wpb_add_ratings_tem_wa_custom_html'); ?>
                        <?php do_action('wpb_add_whatsapp_notification_template_html');  ?>
                        <?php if (current_user_can('administrator')) : ?>
                            <div class="tab-pane fade" id="custom-code" role="tabpanel" aria-labelledby="custom-code-tab">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form id="wpb-custom-code">
                                            <h6><?php esc_html_e('Custom Code', 'wpbookit'); ?></h6>
                                            <p><?php esc_html_e('Custom CSS Code', 'wpbookit'); ?></p>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="d-flex custom-code-block">
                                                        <?php
                                                        $content = stripslashes($this->get_custom_code()['css_code']);
                                                        $editor_id = 'css_code';
                                                        $editor_name = 'css_code';
                                                        $settings = array(
                                                            'textarea_rows' => 8,
                                                            'media_buttons' => false,
                                                            'quicktags' => array(),
                                                            'tinymce' => false,
                                                            'editor_name' => $editor_name,
                                                        );
                                                        wp_editor($content, $editor_id, $settings);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <p><?php esc_html_e('Custom JS Code', 'wpbookit'); ?></p>
                                            <div class="row mb-4">
                                                <div class="col-lg-12">
                                                    <div class="d-flex custom-code-block">
                                                        <?php
                                                        $content = stripslashes($this->get_custom_code()['js_code']);
                                                        $editor_id = 'js_code';
                                                        $editor_name = 'js_code';
                                                        $settings = array(
                                                            'textarea_rows' => 8,
                                                            'media_buttons' => false,
                                                            'quicktags' => array(),
                                                            'tinymce' => false,
                                                            'editor_name' => $editor_name,
                                                        );
                                                        wp_editor($content, $editor_id, $settings);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary " type="submit">
                                                <svg class="spinner d-none wpb-customer-submit-svg" height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                                                </svg>
                                                <?php esc_html_e("Save", 'wpbookit') ?>
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="offline-payments" role="tabpanel" aria-labelledby="offline-payments-tab">
                                <div class="row">
                                    <div class="col-lg-12 mb-4">
                                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                            <h4 class="mb-0"><?php esc_html_e("Offline Payment Mode", 'wpbookit') ?></h4>
                                            <a class="btn btn-secondary lh-lg" id="payment_gateway" data-bs-toggle="offcanvas" data-bs-target="#payment_gateway-options" role="button" aria-controls="payment_gateway-options">
                                                <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/plus-square.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="icon" />
                                                <span class="align-middle">
                                                    <?php esc_html_e("New",'wpbookit') ?>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table custome-table" id='offline-payments-table'>
                                                <thead>
                                                    <tr>
                                                        <?php if (!empty($this->get_offline_payment_modes_cols()) && is_array($this->get_offline_payment_modes_cols())) :
                                                            foreach ($this->get_offline_payment_modes_cols() as $column_id => $column_name) : ?>
                                                                <th scope="col" class="<?php echo esc_attr($column_id); ?>">
                                                                    <span class="nobr"><?php echo esc_html($column_name); ?></span>
                                                                </th>
                                                        <?php endforeach;
                                                        endif; ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane fade " id="online-payments" role="tabpanel" aria-labelledby="online-payments-tab">
                                <div class="row disabled-field">
                                    <div class="col-lg-12">
                                        <h4 class="mb-0">
                                            <?php esc_html_e("Online Payment Mode", 'wpbookit') ?>
                                            <span payment="" gatewayan="" class="upgrade-pro-label"><?php esc_html_e("Upgrade Pro", 'wpbookit') ?></span>
                                        </h4>
                                    </div>
                                    <div class="col-lg-12 ">
                                        <hr>
                                        <div class="card p-3">
                                            <form id="save-woo-payment-gateway-name">

                                                <h5 class="mb-3"><?php esc_html_e("WooCommerce payment gateway", 'wpbookit') ?></h5>
                                                <div class="form-check form-switch mb-2">
                                                    <input disabled name="wpb_woocommerce_payment_gateway_status" class="form-check-input" <?php echo isset($active_payment_gateways['wpb_wc_payment_gateway']['status']) && $active_payment_gateways['wpb_wc_payment_gateway']['status'] == "true" ? 'checked' : ''  ?> <?php echo !is_plugin_active('woocommerce/woocommerce.php') ? 'disabled' : '' ?> type="checkbox" id="wpb_woocommerce_payment_gateway_status">
                                                    <label class="form-check-label" for="wpb_woocommerce_payment_gateway_status"><?php esc_html_e("Enable WooCommerce payment", 'wpbookit') ?></label>
                                                </div>
                                                <p class="small"><?php esc_html_e("Note: If you enable Woocommerce payment. This action may redirect appointments for payment on the default woocommerce cart page with selected appointment services. The appointment will be canceled automatically in case of an unsuccessful payment. (woocommerce redirection is for the patient role only) ", 'wpbookit') ?></p>
                                                <div class="col-lg-4">
                                                    <label class="form-label" for="wpb_change_woocommerce_payment_gateway_label"><?php esc_html_e('WooCommerce payment gateway Label', 'wpbookit') ?>:</label>
                                                    <input disabled type="text" value="<?php echo esc_html(isset($active_payment_gateways['wpb_wc_payment_gateway']['label']) && !empty($active_payment_gateways['wpb_wc_payment_gateway']['label']) ? $active_payment_gateways['wpb_wc_payment_gateway']['label'] :  __("WooCommerce payment gateway", 'wpbookit')) ?>" class="form-control" id="wpb_paypal_client_id" name="wpb_change_woocommerce_payment_gateway_label">
                                                </div>
                                                <div class="col-12 text-end mt-3">
                                                    <button disabled class="btn btn-primary" type="submit">
                                                        <svg class="spinner wpb-email-submit-svg d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                            <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                                                        </svg>
                                                        <?php esc_html_e('Save', 'wpbookit') ?>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card p-3">
                                            <h5 class="mb-3"><?php esc_html_e("PayPal Payment Gateway", 'wpbookit') ?></h5>
                                            <form class="wpb-payment-gateway-setting">
                                                <input disabled type="hidden" name="wpb_payment_gateway" value="paypal">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-check form-switch mb-2">
                                                            <input  disabled class="form-check-input" name="status" value="true" <?php echo isset($active_payment_gateways['paypal']['status']) && $active_payment_gateways['paypal']['status'] == "true" ? 'checked' : ''  ?> type="checkbox" id="wpb_paypal_payment_gateway_status">
                                                            <label class="form-check-label" for="wpb_paypal_payment_gateway_status"><?php esc_html_e("Enable PayPal payment", 'wpbookit') ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <label class="form-label" for="wpb_paypal_client_secret"> <?php esc_html_e('Mode', 'wpbookit') ?>: </label>
                                                        <select disabled class="form-select" name="wpb_paypal_payment_mode">
                                                            <option <?php selected($active_payment_gateways['paypal']['wpb_paypal_payment_mode'] ?? "", 'sandbox') ?> value="sandbox"><?php esc_html_e("Sandbox", 'wpbookit') ?></option>
                                                            <option <?php selected($active_payment_gateways['paypal']['wpb_paypal_payment_mode'] ?? "", 'live') ?> value="live"><?php esc_html_e("Live", 'wpbookit') ?></option>
                                                        </select>
                                                    </div>

                                                    <div class="col-lg-4 mt-lg-0 mt-2">
                                                        <label class="form-label" for="wpb_paypal_client_id"><?php esc_html_e('Client ID', 'wpbookit') ?>:</label>
                                                        <input disabled type="text" value="<?php echo esc_html($active_payment_gateways['paypal']['wpb_paypal_payment_client_id'] ?? "") ?>" class="form-control" id="wpb_paypal_client_id" name="wpb_paypal_payment_client_id">
                                                    </div>
                                                    <div class="col-lg-4 mt-lg-0 mt-2">
                                                        <label class="form-label" for="wpb_paypal_client_secret"><?php esc_html_e('Client Secret', 'wpbookit') ?>:</label>
                                                        <input type="password" class="form-control" value="<?php echo esc_html($active_payment_gateways['paypal']['wpb_paypal_payment_client_secret'] ?? "") ?>" name="wpb_paypal_payment_client_secret" id="wpb_paypal_client_secret">
                                                    </div>

                                                    <div class="col-12 mt-2">
                                                        <p class="small"><?php esc_html_e("Note: The PayPal currency must be the same as the service price currency", 'wpbookit') ?></p>
                                                    </div>
                                                    <div class="col-12 text-end mt-3">
                                                        <button disabled class="btn btn-primary payment_sub_button" type="submit">
                                                            <svg class="spinner wpb-payment-submit-svg d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                                                            </svg>
                                                            <?php esc_html_e('Save', 'wpbookit') ?></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <?php do_action('wpb_integrate_payment_gateway' , $active_payment_gateways) ; ?>
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="import" role="tabpanel" aria-labelledby="import-tab">
                                <div class="row disabled-field">
                                    <div class="col-12">
                                        <h4><?php esc_html_e("Import Booking From WPBooked CSV",'wpbookit') ?>  <?php wpb_render_pro_lable();?></h4>
                                    </div>
                                    <div class="col-12">
                                    <form id="wpb-import-booking">
                                        <input type="hidden" name="wpb_file" value="csv">
                                        <input type="hidden" name="wp_import_module" value="booking_from_old_wpbookit_plugin">
                                        <div class="mb-3">
                                            <label for="wpb_upload_file" class="form-label"><?php esc_html_e("Upload a File", 'wpbookit') ?>*</label>
                                            <input disabled class="form-control" type="file" id="wpb_upload_file" name="wpb_import_file" required>
                                            <div class="invalid-feedback">
                                                <?php esc_html_e('Please upload a valid CSV file', 'wpbookit') ?>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <button disabled class="btn btn-primary payment_sub_button" type="submit">
                                                <svg class="spinner loader d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                                                </svg>
                                                <span><?php esc_html_e('Import', 'wpbookit') ?></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                </div>
                            </div>
                        <?php endif; ?>
                        <?php do_action('wpb_add_navbar_menu_custom_html'); // Add custom HTML here 
                        ?>
                        <?php foreach ($nav_menu as $nav_menu_key => $nav_menu_value) :
                            $is_selected = $nav_menu_value['is_selected']; 

                            $menu_id = isset($nav_menu_value['target']) ? $nav_menu_value['target'] : '';

                            $text = isset($nav_menu_value['text']) ? $nav_menu_value['text'] : '';

                            $menu_data = [
                                'text' => $text,
                                'menu_id' => $menu_id,
                                'is_selected' => $is_selected,
                            ];
                            
                            do_action("wpb_add_{$menu_id}_html_content", $menu_data); ?>
                        <?php endforeach; ?>


                        <div class="d-flex justify-content-between align-items-center rounded flex-wrap gap-3">
                            <div class="offcanvas <?php echo esc_html( wpb_append_class_base_on_rtl('offcanvas-end','offcanvas-start')) ?> email-options " tabindex="-1" id="email-options" data-bs-scroll="true" data-bs-backdrop="true" aria-labelledby="add-booking-type-label">
                                <div class="offcanvas-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="offcanvas-title" id="add-booking-type-label">
                                            <?php esc_html_e('Edit Email', 'wpbookit'); ?>
                                        </h4>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">

                                    </div>
                                    <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body data-scrollbar">
                                    <form class="wpb_email_options_form" name="wpb_email_options_form">
                                        <div class="row mb-3">
                                            <div class="col-sm-12">
                                                <label for="url" class="h6 m-0 small"><?php esc_html_e('Subject', 'wpbookit'); ?><span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="email_subject" name="email_subject" placeholder="<?php esc_html_e('write subject name here..', 'wpbookit'); ?>">
                                            </div>
                                        </div>
                                        <div class="row mb-3 reminder-div">
                                            <div class="col-sm-12">
                                                <label for="url" class="h6 m-0 small"><?php esc_html_e('Set Reminder', 'wpbookit'); ?></label>
                                                <select name="email_reminder" id="email_reminder">
                                                    <?php foreach ($get_remainder_interval as $interval => $label) : ?>
                                                        <option value="<?php echo esc_attr($interval) ?>"><?php echo esc_attr($label) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="email_id" value="">
                                        <div class="row mb-3">
                                            <div class="col-sm-12">
                                                <div class="from-group mb-4 col-lg-12">
                                                    <label for="emails_content" class="form-label"><?php esc_html_e("Email Content", 'wpbookit') ?></label>
                                                    <div id="email_dynamic_keys">
                                                    </div>
                                                    <div id="email_content_editor">
                                                        <?php
                                                        $content = '';
                                                        $editor_id = 'emails_content';
                                                        $editor_name = 'emails_content';
                                                        $settings = array(
                                                            'textarea_rows' => 8,
                                                            'media_buttons' => false,
                                                            'quicktags' => array('buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code'),
                                                            'tinymce' => true,
                                                            'editor_name' => $editor_name,
                                                        );
                                                        $headers = array('Content-Type: text/html; charset=UTF-8');
                                                        wp_editor($content, $editor_id, $settings, $headers);
                                                        ?>
                                                    </div>
                                                    <span class="error" id="description_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <div class="offcanvas-footer">
                                    <div class=" d-flex mb-3">
                                        <div class="col-sm-6 d-flex align-items-center mx-1">
                                            <button id="cancel-update" class="btn btn-secondary w-100" type="button" data-bs-dismiss="offcanvas" aria-label="Close"><?php esc_html_e('Cancel', 'wpbookit'); ?></button>
                                        </div>
                                        <div class="col-sm-6 d-flex align-items-center mx-1">
                                            <button type="submit" name="wpb_email_options_form" class="btn btn-primary w-100 email_sub_button">
                                                <svg class="spinner wpb-email-submit-svg d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                                                </svg>
                                                <?php esc_html_e('Update', 'wpbookit'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                        <?php include IQWPB_PLUGIN_PATH . '/core/admin/views/settings/html-admin-settings-payment-gateway-form.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>