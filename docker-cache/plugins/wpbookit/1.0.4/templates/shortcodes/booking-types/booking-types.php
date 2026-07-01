<div class="container">
    <div class="row gy-5">
        <?php foreach ($args['wpb_booking_type_list'] as $key => $value) :
            $staff = $value->get_booking_type_staff();
            if(  $staff ) :
                $staff_profile_url = wp_get_attachment_url(get_user_meta($staff->ID, "wp_user_avatar", true) ?? 0);
                $staff_profile_img = $staff_profile_url === false ? get_avatar_url(0, ['size' => 50]) : $staff_profile_url; 
            endif; ?>
            
            <div class="col-lg-4 col-md-6">
                <div class="booking-type-profile-card card-has-gredient">
                  
                    <div class="profile-card-height" style="background-color: <?php echo esc_html($value->get_meta('background_color')??"#3745A4") ?> " >
                        <?php if ($value->get_meta('cover_image_id')) : ?>
                        <img class="img-fluid rounded" src="<?php echo esc_url(wp_get_attachment_url($value->get_meta('cover_image_id'))); //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <div class="booking-type-profile-content">
                        <?php if( $staff ) : ?>
                            <div class="staff-info">
                                <div class="booking-user-image">
                                    <img class="rounded-pill img-fluid border border-white border-2 profile-card-img" src="<?php echo esc_url($staff_profile_img);  //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="" loading="lazy">
                                </div>
                                <small class="name"><?php echo esc_html($staff->data->display_name); ?></small>
                            </div>
                        <?php endif; ?>
                        <div class="staff-meta mt-4">
                            <div class="staff-content">
                            <?php do_action('wpb_before_bookingtype_title', $value); ?>
                                <h6 class="mb-2"><?php echo esc_html($value->get_name()); ?></h6>
                                <div class="overflow-auto overflow-content" style="height: 150px;">
                                    <p class="mb-0"><?php echo wp_kses_post( $value->get_description() ); ?></p>
                                </div>
                                <div class="contain-gredient"></div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-6 col-md-6">
                                    <div class="d-flex gap-2">
                                        <small class="title-text">
                                            <svg class="icon-20" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 8V11.5M15 8V11.5M3.5 15H16.5C17.3284 15 18 14.3284 18 13.5V6.5C18 5.67157 17.3284 5 16.5 5H3.5C2.67157 5 2 5.67157 2 6.5V13.5C2 14.3284 2.67157 15 3.5 15ZM12 10C12 11.1046 11.1046 12 10 12C8.89543 12 8 11.1046 8 10C8 8.89543 8.89543 8 10 8C11.1046 8 12 8.89543 12 10Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </small>
                                        <span class="booking-type-card-info">
                                            <?php echo esc_html( $value->get_meta('price') ? wpb_get_prefix_postfix_price( $value->get_meta('price')) : __("Free", 'wpbookit')); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 mt-md-0 mt-2">
                                    <div class="d-flex gap-2 justify-content-custom-end">
                                        <small class="title-text">
                                            <svg class="icon-20" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M13.0711 12.3493C13.333 12.4367 13.6162 12.2951 13.7035 12.0331C13.7908 11.7711 13.6493 11.488 13.3873 11.4007L13.0711 12.3493ZM10.4167 10.9375H9.91667C9.91667 11.1527 10.0544 11.3438 10.2586 11.4118L10.4167 10.9375ZM10.9167 7.01739C10.9167 6.74125 10.6928 6.51739 10.4167 6.51739C10.1405 6.51739 9.91667 6.74125 9.91667 7.01739H10.9167ZM13.3873 11.4007L10.5748 10.4632L10.2586 11.4118L13.0711 12.3493L13.3873 11.4007ZM10.9167 10.9375V7.01739H9.91667V10.9375H10.9167ZM17.4167 10C17.4167 13.866 14.2827 17 10.4167 17V18C14.8349 18 18.4167 14.4183 18.4167 10H17.4167ZM10.4167 17C6.55068 17 3.41667 13.866 3.41667 10H2.41667C2.41667 14.4183 5.99839 18 10.4167 18V17ZM3.41667 10C3.41667 6.13401 6.55068 3 10.4167 3V2C5.99839 2 2.41667 5.58172 2.41667 10H3.41667ZM10.4167 3C14.2827 3 17.4167 6.13401 17.4167 10H18.4167C18.4167 5.58172 14.8349 2 10.4167 2V3Z" fill="currentColor"></path>
                                            </svg>
                                        </small>
                                        <span class="booking-type-card-info"><?php echo esc_html(sprintf("%d %s", $value->get_duration(), _n("minute", "minutes", $value->get_duration(), 'wpbookit'))); ?></span>
                                    </div>
                                </div>
                                <?php if (!empty($value->get_booking_type_meta('location_source'))) : ?>
                                <div class="col-lg-12 mt-2">
                                    <div class="d-flex gap-2">
                                        <small class="title-text">
                                            <?php echo wpb_render_filtered_svg('globe-03') //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        </small>
                                        <span class="booking-type-card-info"><?php echo esc_html(wpb_get_country_name_from_timezone(wpb_get_timezone())); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="col-lg-12 mt-5">
                                 <a href="<?php echo esc_url( $value->get_bookingtype_permalink() ); ?>" class="btn btn-primary text-decoration-none w-100"><?php esc_html_e("Book Now",'wpbookit') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
