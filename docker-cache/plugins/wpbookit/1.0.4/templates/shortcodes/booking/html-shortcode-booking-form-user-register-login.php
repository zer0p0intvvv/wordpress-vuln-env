<div class="col-12">
    <nav class="tab-bottom-bordered">
        <div class="nav nav-tabs" role="tablist" id="confirm-booking-tab">
            <button class="nav-link active" id="wpb-new-customer-tab" data-bs-toggle="tab" data-bs-target="#wpb-new-customer" type="button" role="tab" aria-controls="wpb-new-customer" aria-selected="true">New Customer</button>
            <button class="nav-link" id="wpb-already-customer-tab" data-bs-toggle="tab" data-bs-target="#wpb-already-customer" type="button" role="tab" aria-controls="wpb-already-customer" aria-selected="true"><?php esc_html_e("Already have an account?", 'wpbookit') ?></button>
        </div>
        <input type="hidden" name="wpb-user-booking-with" class="wpb-user-booking-with" value="wpb-register">
    </nav>
    <div class="tab-content" id="confirm-booking-content">
        <div class="tab-pane fade show active" id="wpb-new-customer">
            <div class="row">
               <?php do_action('wpb_booking_shortcode_user_name_fields',$args); ?>
                <div class="col-lg-6">
                    <div class="form-group mb-4">
                        <label for="#wpb_user_email" class="form-label"><?php esc_html_e("Email address", 'wpbookit') ?>*
                        </label>
                        <div class="input-group">
                            <input type="email" id="wpb_user_email" name="wpb_user_email" class="form-control" name="wpb_user_email" placeholder="e.g. kenny@demo.com" aria-label="wpb-user-first-name" aria-describedby="basic-addon1" required="required" >
                            <span class="input-group-text" id="basic-addon1">
                                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.4432 5.66309L7.85128 7.77069C7.36158 8.15919 6.67259 8.15919 6.18288 7.77069L3.56909 5.66309" stroke="#7E7E7E" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.86359 12.75C11.6377 12.7549 12.8334 11.2972 12.8334 9.50571V5.49917C12.8334 3.70765 11.6377 2.25 9.86359 2.25H4.13658C2.36246 2.25 1.16675 3.70765 1.16675 5.49917V9.50571C1.16675 11.2972 2.36246 12.7549 4.13658 12.75H9.86359Z" stroke="#7E7E7E" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        <span id="booking_shortcode_user_email_error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group mb-4">
                        <label for="#wpb_user_password" class="form-label">
                            <?php esc_html_e("Password", 'wpbookit') ?>*
                        </label>
                        <div class="input-group">
                            <input type="password" id="wpb_user_password" name="wpb_user_password" class="form-control" placeholder="<?php esc_html_e("Enter Password", 'wpbookit') ?>" aria-label="wpb-user-first-name" aria-describedby="basic-addon1" required="required" >
                            <span class="input-group-text" id="basic-addon1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 20" fill="none">
                                    <path d="M13.4228 7.44804V5.30104C13.4228 2.78804 11.3848 0.750045 8.87176 0.750045C6.35876 0.739045 4.31276 2.76704 4.30176 5.28104V5.30104V7.44804" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.683 19.25H5.042C2.948 19.25 1.25 17.553 1.25 15.458V11.169C1.25 9.07395 2.948 7.37695 5.042 7.37695H12.683C14.777 7.37695 16.475 9.07395 16.475 11.169V15.458C16.475 17.553 14.777 19.25 12.683 19.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.8623 12.2031V14.4241" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                        <span id="booking_shortcode_user_password_error" class="error-message"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="wpb-already-customer">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group mb-4">
                        <label for="#wpb_login_user_email" class="form-label"><?php esc_html_e("Email address", 'wpbookit') ?>*
                        </label>
                        <div class="input-group">
                            <input type="email" id="wpb_login_user_email" name="wpb_login_user_email" class="form-control" name="wpb_user_first_name" placeholder="e.g. kenny@demo.com" aria-label="wpb-user-first-name" aria-describedby="basic-addon1"  required="required" >
                            <span class="input-group-text" id="basic-addon1">
                                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.4432 5.66309L7.85128 7.77069C7.36158 8.15919 6.67259 8.15919 6.18288 7.77069L3.56909 5.66309" stroke="#7E7E7E" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.86359 12.75C11.6377 12.7549 12.8334 11.2972 12.8334 9.50571V5.49917C12.8334 3.70765 11.6377 2.25 9.86359 2.25H4.13658C2.36246 2.25 1.16675 3.70765 1.16675 5.49917V9.50571C1.16675 11.2972 2.36246 12.7549 4.13658 12.75H9.86359Z" stroke="#7E7E7E" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        <span id="wp_login_booking_shortcode_user_email_error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group mb-4">
                        <label for="#wpb_login_user_password" class="form-label"><?php esc_html_e("Password", 'wpbookit') ?>*
                        </label>
                        <div class="input-group">
                            <input type="password" id="wpb_login_user_password" name="wpb_login_user_password" class="form-control" name="wpb_login_user_password" placeholder="<?php esc_html_e("Enter Password", 'wpbookit') ?>" aria-label="wpb-user-first-name" aria-describedby="basic-addon1" required="required" >
                            <span class="input-group-text" id="basic-addon1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18 20" fill="none">
                                    <path d="M13.4228 7.44804V5.30104C13.4228 2.78804 11.3848 0.750045 8.87176 0.750045C6.35876 0.739045 4.31276 2.76704 4.30176 5.28104V5.30104V7.44804" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.683 19.25H5.042C2.948 19.25 1.25 17.553 1.25 15.458V11.169C1.25 9.07395 2.948 7.37695 5.042 7.37695H12.683C14.777 7.37695 16.475 9.07395 16.475 11.169V15.458C16.475 17.553 14.777 19.25 12.683 19.25Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.8623 12.2031V14.4241" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </div>
                        <span id="wpb_login_booking_shortcode_user_password_error" class="error-message"></span>
                        <div class="mt-2 text-end">
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" target="_blank" class="forgot_password small">
                                <?php esc_html_e('Forgot Password?', 'wpbookit'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
