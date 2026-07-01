<?php
if (!defined('ABSPATH')) exit;
$filter_available = apply_filters('wpb_after_video_conference_check_hook', false);
?>

<div class="offcanvas <?php echo esc_html( wpb_append_class_base_on_rtl('offcanvas-end','offcanvas-start')) ?> add-booking-type booking-offcanvas w-40" tabindex="-1" id="add-booking-type" data-bs-scroll="true" data-bs-backdrop="true" aria-labelledby="add-booking-type-label">
    <div class="offcanvas-header">
        <div class="d-flex align-items-center">
            <h4 class="offcanvas-title" id="new-booking-label"></h4>
        </div>
        <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body data-scrollbar">
        <nav class="tab-bottom-bordered mb-3">
            <div class="mb-0 nav nav-tabs justify-content-around" id="nav-tab1" role="tablist">
                <button class="nav-link active" id="nav-newbooking-tab" data-bs-toggle="tab" data-bs-target="#nav-newbooking" type="button" role="tab" aria-controls="nav-newbooking" aria-selected="true"><?php esc_html_e("New booking", 'wpbookit') ?></button>
                <button class="nav-link" id="nav-advancebooking-tab" data-bs-toggle="tab" data-bs-target="#nav-advancebooking" type="button" role="tab" aria-controls="nav-advancebooking" aria-selected="false"><?php esc_html_e("Advanced booking", 'wpbookit') ?></button>
            </div>
        </nav>
        <form class="booking_type_form" novalidate>
            <input type="hidden" name="id" id="booking_type_id">
            <input type="hidden" name="cover_image_id" id="cover_image_id" value="">

            <div class="tab-content iq-tab-fade-up" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-newbooking" role="tabpanel" aria-labelledby="nav-newbooking-tab">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group ">
                                <label for="upload_cover_image" class="form-label"><?php esc_html_e('Background Image', 'wpbookit'); ?></label>
                                <?php wpb_render_pro_lable() ?>
                                <div class="position-relative custom-upload-container disabled-field">
                                    <input class="form-control" type="file" id="upload_cover_image" name="upload_cover_image" disabled>
                                    <div class="custom-upload">
                                        <div class="wpb-icon-wrapper">
                                            <?php echo wpb_render_filtered_svg('upload'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                            <div class="mt-3 title"><?php esc_html_e('Drag & Drop or choose file to upload', 'wpbookit'); ?></div>
                                        </div>
                                        <div id="cover_image_preview">
                                            <button type="button" id="cover_image_preview-btn" class="btn-close text-reset shadow-none btn-close-icon-white" style="display: none;"></button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group ">
                                <label for="" class="form-label "><?php esc_html_e('Replace image with color', 'wpbookit'); ?></label>
                                <?php wpb_render_pro_lable() ?>
                                <div class="position-relative custom-upload-container disabled-field" >
                                    <div class="custom-upload">
                                        <h6 class="small"><?php esc_html_e("No cover image? Use a background color!",'wpbookit'); ?></h6>
                                        <div class="d-flex align-items-center">
                                            <input type="color" id="wpb-booking-type-color" class="form-control-color" disabled value="#3745A4">
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4  col-lg-12">
                            <label for="title" class="form-label"><?php esc_html_e("Title", 'wpbookit') ?>*</label>
                            <div class="input-group">
                                <input type="text" name="title" id="title" class="form-control" placeholder="<?php esc_html_e("write your title here", 'wpbookit') ?>" aria-label="title">
                                <span class="input-group-text" id="basic-addon1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none">
                                        <path d="M10.2088 16.2H12.124M12.124 16.2H14.128M12.124 16.2V7.79999M12.124 7.79999H8.9999C8.66853 7.79999 8.3999 8.06862 8.3999 8.39999V9.28235M12.124 7.79999H14.9999C15.3313 7.79999 15.5999 8.06862 15.5999 8.39999V9.52941M4.7999 21.6H19.1999C20.5254 21.6 21.5999 20.5255 21.5999 19.2V4.79999C21.5999 3.47451 20.5254 2.39999 19.1999 2.39999H4.7999C3.47442 2.39999 2.3999 3.47451 2.3999 4.79999V19.2C2.3999 20.5255 3.47442 21.6 4.7999 21.6Z" stroke="#7e7e7e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                            <span class="error" id="title_error"></span>
                        </div>

                        <div class="form-group mb-4  col-lg-12">
                            <label for="slug" class="form-label"><?php esc_html_e("Slug", 'wpbookit') ?>*</label>
                            <div class="input-group">
                                <span class="input-group-text title-slug" id="basic-addon1">
                                    <?php echo esc_url(get_bookingtype_base_url()); ?>
                                </span>
                                <input type="text" name="slug" id="slug" class="form-control px-2" placeholder="<?php esc_html_e("write your slug here", 'wpbookit') ?>" aria-label="slug" aria-describedby="basic-addon1">
                            </div>
                            <span class="error" id="slug_error"></span>
                        </div>
                        <div class="form-group mb-4 col-lg-6 grid">
                            <label class="form-label"><?php esc_html_e("Duration", 'wpbookit') ?>*</label>
                            <div class="input-group">
                                <input type="number" name="duration" id="duration" class="form-control" min="5" placeholder="<?php esc_html_e("Enter duration", 'wpbookit') ?>" aria-label="duration" aria-describedby="basic-addon1">
                                <span class="input-group-text title-duration" id="basic-addon1">
                                    <?php esc_html_e("Minutes", 'wpbookit') ?>
                                </span>
                            </div>
                            <span class="error" id="duration_error"></span>
                        </div>
                        <input type="hidden" name="staff" id="staff" value="<?php echo esc_attr(get_current_user_id()) ?>" />

                        <?php do_action('wpb_after_location'); ?>

                        <div class="from-group mb-4 col-lg-12">
                            <label for="description" class="form-label"><?php esc_html_e("Description", 'wpbookit') ?></label>
                            <?php
                            $content = !empty($template_body) ? $template_body : '';
                            $editor_id = 'description';
                            $editor_name = 'description';
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
                            <span class="error" id="description_error"></span>
                        </div>
                        <div class="col-lg-12 mb-4">
                            <div class="d-flex align-items-center flex-wrap gap-3 mb-3 booking-inner">
                                <small class="title-text text-capitalize"><?php esc_html_e("When are you available for this booking?", 'wpbookit') ?></small>
                                <label for="weekly" class="btn bg-body d-inline-flex align-items-center gap-2">
                                    <input class="form-check-input btn-weekly mt-0" type="radio" name="booking_type" checked value="weekly" id="weekly">
                                    <span class="text-black form-check-label btn-weekly">
                                        <?php esc_html_e("Weekly", 'wpbookit') ?>
                                    </span>
                                </label>

                                <label for="sepcific_date" class="btn bg-body d-inline-flex align-items-center gap-2">
                                    <input class="form-check-input btn-sepcific mt-0" type="radio" name="booking_type" value="sepcific_date" id="sepcific_date">
                                    <span class="form-check-label btn-weekly">
                                        <?php esc_html_e("Specific Dates", 'wpbookit') ?>
                                    </span>
                                </label>


                            </div>
                            <div class="showspecific">
                                <small class="title-text text-capitalize"><?php esc_html_e("Define your specific dates availability below", 'wpbookit') ?>:</small>
                                <div class="table-responsive pt-2  mb-4">
                                    <table class="table border rounded mb-0">
                                        <tbody class="dateContainer">
                                            <tr class="new">
                                                <td>
                                                    <div>
                                                        <input class="form-control" type="date" name="specific_available_date[]" min="<?php echo esc_attr(date('Y-m-d')) // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0 d-flex align-items-center">
                                                        <input type="time" class="form-control bg-white title-text" placeholder="08:30 AM" name="specific_available_time_from[]" />
                                                        <svg class="mx-3" width="18" height="2" viewBox="0 0 6 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect width="18" height="2" fill="#0C112E" />
                                                        </svg>
                                                        <input type="time" class="form-control bg-white title-text" placeholder="04:20 PM" name="specific_available_time_to[]" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <span type="button" class="duplicate-row small"><?php esc_html_e("Duplicate", 'wpbookit') ?></span>
                                                </td>
                                                <td>
                                                    <span type="button" class="text-secondary remove-row small"><?php esc_html_e("Remove", 'wpbookit') ?></span>
                                                </td>
                                            </tr>

                                        </tbody>
                                        <tfoot>
                                            <tr class="w-100">
                                                <td class="w-100" collspan="3">
                                                    <span type="button" class="text-primary add-row w-100" id="addNewDate"><?php esc_html_e("Add New Date", 'wpbookit') ?></span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <span class="error" id="specific_error"></span>
                            </div>
                            <div class="showweekly">
                                <small class="title-text text-capitalize"><?php esc_html_e("Define your weekly availability below:", 'wpbookit') ?></small>

                                <div class="overflow-x-auto">
                                    <?php foreach ($all_weekdays as $weekday_slug  => $weekday_lable) : ?>
                                        <div class="p-3 border-bottom">
                                            <div class="row justify-content-between flex-nowrap">
                                                <div class="col-sm-3 col-5">
                                                    <div class="form-check my-2">
                                                        <input class="form-check-input weekly_day_checkbox" type="checkbox" id="<?php echo esc_html($weekday_slug); ?>" name="<?php echo  esc_html($weekday_slug); ?>" checked>
                                                        <label class="form-check-label title-text" for="<?php echo esc_html($weekday_slug); ?>"><?php echo  esc_html($weekday_lable); ?></label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-9 col-12 days_slots-container">
                                                    <div class="days_slots">
                                                        <div class="row align-items-center flex-nowrap days_slot-wrapper">
                                                            <div class="col-sm-9 col-12">
                                                                <div class="available_day available_<?php echo  esc_html($weekday_slug); ?> <?php echo  esc_html($weekday_slug); ?>_time_contiener" id="available_<?php echo  esc_html($weekday_slug); ?>" data-type="<?php echo  esc_html($weekday_slug); ?>">
                                                                    <div class="time_slot">
                                                                        <div class="form-group mb-0 d-flex align-items-center <?php echo  esc_html($weekday_slug); ?>_time_new">
                                                                            <input type="time" class="form-control bg-white title-text" value="09:00" placeholder="08:30 AM" name="<?php echo  esc_html($weekday_slug); ?>_time_to[]" />
                                                                            <svg class="mx-3" width="18" height="2" viewBox="0 0 6 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <rect width="18" height="2" fill="#0C112E" />
                                                                            </svg>
                                                                            <input type="time" class="form-control bg-white title-text" value="18:00" placeholder="04:20 PM" name="<?php echo esc_html($weekday_slug); ?>_time_from[]" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3 col-5 days-slot-action text-end">
                                                                <a type="button" id="<?php echo esc_html($weekday_slug); ?>_add_time" class="available_<?php echo  esc_html($weekday_slug); ?> text-secondary text-capitalize fw-bold add_new_time_slot small" data-type="<?php echo  esc_html($weekday_slug); ?>">+
                                                                    <?php esc_html_e("New Slot", 'wpbookit') ?></a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="unavailable_<?php echo  esc_html($weekday_slug); ?> unavailable_day my-2" id="unavailable_<?php echo  esc_html($weekday_slug); ?>" style="display:none;">
                                                        <span><?php esc_html_e("Unavailable", 'wpbookit') ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <span class="error" id="weekly_error"></span>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input btn-addAvailable" name="unavailable" type="checkbox" value="true" id="add_unvailable_date">
                                <label class="form-check-label title-text add_unvailable_date" for="add_unvailable_date">
                                    <?php esc_html_e("Add Unavailable Dates", 'wpbookit') ?>
                                </label>
                            </div>
                            <small class="title-text text-capitalize"><?php esc_html_e("Define specific dates that will be excluded from your weekly availability.", 'wpbookit') ?></small>
                            <div class="add_unvailable_date_div" style="display:none">
                                <div class="table-responsive pt-2 mb-4">
                                    <table class="table border rounded mb-0">
                                        <tbody id="undateContainer">
                                            <tr class="date-row">
                                                <td>
                                                    <div>
                                                        <input class="form-control" type="date" name="unavailable_date[]">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0 d-flex align-items-center">
                                                        <input type="time" class="form-control bg-white title-text" placeholder="08:30 AM" name="unavailable_time_to[]" />
                                                        <svg class="mx-3" width="18" height="2" viewBox="0 0 6 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect width="18" height="2" fill="#0C112E" />
                                                        </svg>
                                                        <input type="time" class="form-control bg-white title-text" placeholder="04:20 PM" name="unavailable_time_from[]" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <span type="button" class="duplicate-row small"><?php esc_html_e("Duplicate", 'wpbookit') ?></span>
                                                </td>
                                                <td>
                                                    <span type="button" style="display: none;" class="text-secondary remove-row small"><?php esc_html_e("Remove", 'wpbookit') ?></span>
                                                </td>
                                            </tr>

                                        </tbody>
                                        <tfoot>
                                            <tr class="w-100">
                                                <td class="w-100" collspan="3">
                                                    <span type="button" class="text-primary add-row w-100" id="addNewUnDate"><?php echo esc_html_x("Add New Date", 'Unavailable Date', 'wpbookit') ?></span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <span class="error" id="unavailable_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-advancebooking" role="tabpanel" aria-labelledby="nav-advancebooking-tab">
                    <h6 class="mb-5"><?php esc_html_e("Availability details", 'wpbookit') ?></h6>
                    <div class="row">
                        <div class="form-group col-lg-6 how_far">
                            <label class="form-label text-capitalize"><?php esc_html_e("How far out can users book?(Day ahead)", 'wpbookit') ?></label>
                            <input class="form-control" name="how_far" id="how_far" type="number" value="" min="1" placeholder="<?php esc_html_e("Enter days", 'wpbookit') ?>">
                            <span class="error" id="how_far_error"></span>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="maximum_buffer" class="form-label"><?php esc_html_e("Minimum meeting padding or buffer", 'wpbookit') ?></label>
                            <div class="input-group">
                                <input type="number" name="maximum_buffer" id="maximum_buffer" class="form-control px-2" value="" min="1" placeholder="<?php esc_html_e("Enter meeting buffer", 'wpbookit') ?>" aria-label="maximum_buffer" aria-describedby="basic-addon1">
                                <span class="input-group-text title-maximum_buffer" id="basic-addon1"><?php esc_html_e("Minutes of current time.", "wpbookit"); ?></span>
                                <span class="error" id="maximum_buffer_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <label class="form-label text-capitalize" for="booking_number"><?php esc_html_e("Enter Maximum Number of Bookings", 'wpbookit') ?></label>
                                    <div class="input-group" id="maximum_booking_number_container">
                                        <input class="form-control" placeholder="<?php esc_html_e("Enter maximum number of bookings", 'wpbookit') ?>" name="maximum_booking_number" type="number" id="booking_number" min="1">
                                        <span class="input-group-text">
                                            <select class="form-select" name="booking_number_by" id="booking_number_by">
                                                <option value="days"><?php esc_html_e("Days", 'wpbookit') ?></option>
                                                <option value="weeks"><?php esc_html_e("Weeks", 'wpbookit') ?></option>
                                                <option value="months"><?php esc_html_e("Months", 'wpbookit') ?></option>
                                            </select>
                                        </span>
                                    </div>
                                    <span class="error" id="maximum_booking_number_error"></span><br>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="booking_threshold" class="form-label"><?php esc_html_e("Bookers can't schedule within", 'wpbookit') ?></label>
                                    <div class="input-group">
                                        <input type="text" name="booking_threshold" id="booking_threshold" class="form-control px-2" placeholder="<?php esc_html_e("Enter booking threshold", 'wpbookit') ?>" aria-label="booking_threshold" aria-describedby="basic-addon1">
                                        <span class="input-group-text title-booking_threshold" id="basic-addon1"><?php esc_html_e("Minutes of current time.", "wpbookit"); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-1 col-lg-6">
                                <span class="ms-3">
                                    <?php wpb_render_pro_lable() ?>
                                </span>
                            </div>
                            <div class="form-check">

                                <input class="form-check-input" disabled type="checkbox"  value="true" id="enable_group_booking">
                                <label class="form-check-label title-text text-capitalize" id="enable_group_booking" for="enable_group_booking">
                                    <span class="">
                                        <?php esc_html_e("Enable Group Booking For More then One Attendee", 'wpbookit') ?>
                                    </span>
                                    
                                </label>
                            </div>
                            <div class="form-group-check" id="slots_per_booking_number_container">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label text-capitalize" for="slots_booking_number"><?php esc_html_e("Enter Number of Seats Per Booking", 'wpbookit') ?></label>
                                        <input class="form-control" name="slots_per_booking_number" type="number" id="slots_booking_number" min="2">
                                        <span class="error" id="group_booking_error"></span>
                                    </div>
                                </div>
                                <input class="form-check-input mt-3" type="checkbox" name="show_remaining_slot" value="true" id="show_remaining_slot">
                                <label class="form-label text-capitalize customer-space" for="show_remaining_slot"><?php esc_html_e("Show Remaining Seats?", 'wpbookit') ?></label>

                                <?php do_action("wpb_after_gropb_booking_fileds"); ?>

                            </div>
                            
                            <span class="error" id="slots_booking_number_error"></span>
                            <div class="border-top my-5"></div>
                            <h6 class="mt-2 mb-2"> <?php esc_html_e("Location", 'wpbookit') ?></h6>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="location" id="video_conference" value="online_video" checked>
                                <label class="form-check-label text-capitalize" for="video_conference"><?php esc_html_e("Online video conference", 'wpbookit') ?></label>
                            </div>
                            <?php 
                                if(!$filter_available){
                                    ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="location" id="physical_address" value="physical_address">
                                            <label class="form-check-label text-capitalize" for="physical_address"><?php esc_html_e("Physical address", 'wpbookit') ?></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="location" id="phone_number_checkbox" value="phone_number">
                                            <label class="form-check-label text-capitalize" for="phone_number_checkbox"><?php esc_html_e("Phone number", 'wpbookit') ?></label>
                                        </div>
                                    <?php
                                }
                            ?>
                            
                            <div class="form-check form-check-inline mb-4">
                                <input class="form-check-input" type="radio" name="location" id="no_location" value="no_location">
                                <label class="form-check-label" for="no_location"><?php esc_html_e("No Location", 'wpbookit') ?></label>
                            </div>
                            <div class="align-items-center mb-4 " id="static_url_field">
                                <label class="form-label text-capitalize me-2" for="meeting_link_type"><?php esc_html_e("Enter URL below", 'wpbookit') ?>*</label>
                                <?php wpb_render_pro_lable();?>
                                <div class="input-group align-items-center" id="link_type_field">
                                    <span class="input-group-text">
                                        <?php if( isset( $meeting_tools ) && !empty( $meeting_tools ) ) : ?>
                                            <select class="form-select" id="meeting_link_type" name="meeting_link_type" disabled>
                                                <?php foreach( $meeting_tools as $tool_key => $tool_label ) : ?>
                                                    <option value="<?php echo esc_attr( $tool_key ); ?>"><?php echo esc_attr( $tool_label ); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </span>
                                    <input type="url" disabled require name="online_video" id="static_url" class="form-control custom_link meeting-link-control " placeholder="<?php esc_html_e("https://example.com", 'wpbookit') ?>">
                                    <span class="zoom-meet-icon zoom meeting-link-control mx-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" height="26" viewBox="0 0 114 26" width="94px">
                                            <path d="m23.6977 25.2924h-20.10301c-1.32885 0-2.58954-.6978-3.202853-1.8892-.698493-1.3617-.4429462-2.9956.630343-4.068l13.98692-13.97375h-10.01743c-2.7599 0-4.99167-2.22968-4.99167-4.987h18.5186c1.3288 0 2.5895.69784 3.2028 1.88927.6986 1.36164.443 2.9956-.6303 4.0679l-13.98691 13.97378h11.60181c2.7599 0 4.9917 2.2297 4.9917 4.987zm79.5603-25.2924c-2.879 0-5.4691 1.24249-7.241 3.23389-1.7883-1.9914-4.3781-3.23389-7.2401-3.23389-5.3497 0-9.7108 4.56149-9.7108 9.88887v15.40353c2.7598 0 4.9915-2.2297 4.9915-4.987v-10.46757c0-2.5701 1.9933-4.74871 4.5487-4.85083 2.692-.10213 4.9237 2.05945 4.9237 4.73169v10.58671c0 2.7573 2.2317 4.987 4.9915 4.987v-15.45457c0-2.5701 1.9935-4.74871 4.5485-4.85083 2.692-.10213 4.924 2.05945 4.924 4.73169v10.58671c0 2.7573 2.232 4.987 4.991 4.987v-15.40353c-.017-5.32738-4.378-9.88887-9.727-9.88887zm-54.3805 12.8334c0 7.0806-5.7583 12.8335-12.8455 12.8335-7.0871 0-12.8454-5.7529-12.8454-12.8335 0-7.0805 5.7753-12.8334 12.8454-12.8334 7.0702 0 12.8455 5.7529 12.8455 12.8334zm-4.9917 0c0-4.32315-3.5265-7.8464-7.8538-7.8464-4.3272 0-7.8538 3.52325-7.8538 7.8464 0 4.3233 3.5266 7.8465 7.8538 7.8465 4.3273 0 7.8538-3.5232 7.8538-7.8465zm32.6758 0c0 7.0806-5.758 12.8335-12.8451 12.8335-7.0877 0-12.8458-5.7529-12.8458-12.8335 0-7.0805 5.7757-12.8334 12.8458-12.8334 7.0696 0 12.8451 5.7529 12.8451 12.8334zm-4.9915 0c0-4.32315-3.5264-7.8464-7.8536-7.8464-4.3273 0-7.8541 3.52325-7.8541 7.8464 0 4.3233 3.5268 7.8465 7.8541 7.8465 4.3272 0 7.8536-3.5232 7.8536-7.8465z" fill="#0b5cff" />
                                        </svg>
                                    </span>
                                    <?php do_action( 'wpbookit_booking_type_after_meeting_tools_dropdown' ); ?>
                                </div>
                                <span class="error" id="static_url_error"></span>
                            </div>
                            <div class="align-items-center mb-4 " id="address_input_field" style="display: none;">
                                <label class="form-label text-capitalize me-2"><?php esc_html_e("Enter Address", 'wpbookit') ?>*</label>
                                <textarea id="address_input" name="physical_address" class="form-control" rows="3" placeholder="<?php esc_html_e("Enter Physical Address", 'wpbookit') ?>"></textarea>
                                <span class="error" id="physical_address_error"></span>
                            </div>
                            <div class="align-items-center mb-4 " id="phone_number_field" style="display: none;">
                                <label class="form-label text-capitalize me-2"><?php esc_html_e("Enter Mobile Number *", 'wpbookit') ?></label>
                                <input type="number" id="phone_number" name="phone_number" class="form-control" placeholder="<?php esc_html_e("Enter Mobile Number", 'wpbookit') ?>">
                                <span class="error-message"></span>
                                <span class="error" id="meeting_phone_number_error"></span>
                            </div>

                            <!-- <div class="text-secondary fst-italic mt-2">*<?php esc_html_e("a new zoom link will be generated for this booking.", 'wpbookit') ?></div> -->
                            <div class="border-top my-5"></div>
                            <ul class="list-unstyled p-0 m-0">
                                <li class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="guest_invite" value="true" id="flexCheckDefault11" checked>
                                        <label class="form-check-label title-text text-capitalize" for="flexCheckDefault11">
                                            <?php esc_html_e("Enable Guest Booking", 'wpbookit') ?>
                                        </label>
                                    </div>
                                </li>
                               
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="offcanvas-footer" id="offcanvas-footer">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-secondary w-100 cancel_booking_type mx-2" id="cancel-booking-type"><?php esc_html_e("Cancel", 'wpbookit') ?></button>
            <button type="submit" class="btn btn-primary w-100 mx-2" id="wpb-save-booking-type">
                <svg class="spinner wpb-booking-type-submit-svg d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                </svg>
                <?php esc_html_e("Save booking type", 'wpbookit') ?>
            </button>
            <button type="button" class="btn btn-primary w-100 ms-3  wpb-booking-type-apply-advanced-booking" style="display: none;">
                <?php esc_html_e("Apply advanced booking", 'wpbookit') ?>
            </button>
        </div>
    </div>
    </form>
</div>