<?php
// User is already logged in
if (is_user_logged_in()) {
    $logout_url = wp_logout_url(home_url());
    printf(
        '<a href="%s">%s</a>',
        esc_url( $logout_url ),
        esc_html('Logout', 'wpbookit')
    );
} else {
    ?>

    <div class="container wpb-login-shortcode">
        <div class="header">
            <a href="#" id="wpb_shortcode_login_tab" class="header-button active"><svg height="15" width="15"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path fill="currentColor"
                        d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z" />
                </svg> <?php esc_html_e('Sign In', 'wpbookit'); ?></a>
           
                <a href="#" id="wpb_shortcode_register_tab" class="header-button"><svg height="16" width="16"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path fill="currentColor"
                            d="M464 256A208 208 0 1 0 48 256a208 208 0 1 0 416 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm169.8-90.7c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
                    </svg> <?php esc_html_e('Register', 'wpbookit'); ?></a>
           
        </div>

        <div class="content" id="wpb_login_content">
            <form method="POST" id="wpb_shortcode_login_form">
                <div class="alert alert-danger d-none wpb-incorrect-password" role="alert">
                    <small><?php esc_html_e('Error: The username/password is not incorrect.', 'wpbookit'); ?></small>
                </div>
                <label class="label-name"><?php esc_html_e('Email Address', 'wpbookit'); ?><span style="color:red;">
                        *</span></label>
                <input type="email" name="email" id="email" class="input-box form-control"
                    placeholder="<?php esc_html_e('Email', 'wpbookit'); ?>" required>

                <label class="label-name"><?php esc_html_e('Password', 'wpbookit'); ?><span style="color:red;">
                        *</span></label>
                <div class="input-group-password toggle-password-container">
                    <input type="password" name="password" id="wpb-login-password" class="input-box form-control"
                        placeholder="<?php esc_html_e('Password', 'wpbookit'); ?>" required>
                    <span class="toggle-password show-password" id="wpb_toggle_login_password">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                            <path
                                d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z" />
                        </svg>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="wpb_remember_me" id="wpb_remember_me" class="form-check-input">
                        <label class="form-check-label"
                            for="wpb_remember_me"><?php esc_html_e('Remember Me', 'wpbookit'); ?></label>
                    </div>
                    <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" id="wpb_forgot_password_link"
                        class="forgot_password"><?php esc_html_e('Forgot Password?', 'wpbookit'); ?></a>
                </div>
                <button class="buttons" type="submit"
                    id="wpb_shortcode_login_btn"><?php esc_html_e('Log in', 'wpbookit'); ?></button>
            </form>
        </div>

        <div class="content" id="wpb_register_content" style="display: none;">
            <form method="post" id="wpb_shortcode_register_form">
                <label class="label-name"><?php esc_html_e('Email Address', 'wpbookit'); ?><span style="color:red;">
                        *</span></label>
                <input type="email" name="register_email" id="reset_email" class="input-box form-control"
                    placeholder="<?php esc_html_e('Email Address', 'wpbookit'); ?>" required>

                <label class="label-name"><?php esc_html_e('Password', 'wpbookit'); ?><span style="color:red;">
                        *</span></label>
                <div class="input-group-password toggle-password-container">
                    <input type="password" name="register_password" id="wpb-register-password"
                        class="input-box form-control" placeholder="<?php esc_html_e('Password', 'wpbookit'); ?>" required>
                    <span class="toggle-password show-password" id="wpb_toggle_register_password">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                            <path
                                d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z" />
                        </svg>
                    </span>
                </div>
                <button class="buttons" type="submit"
                    id="wpb_shortcode_register_btn"><?php esc_html_e('Register', 'wpbookit'); ?></button>
            </form>
        </div>

        <div class="content" id="forgot_password_content" style="display: none;">
            <form action="#">
                <label class="label-name"><?php esc_html_e('What is your email address?', 'wpbookit'); ?></label>
                <input type="text" name="reset_email" id="reset_email" class="input-box form-control" required>
                <button class="buttons"
                    id="reset_password_button"><?php esc_html_e('Reset my password', 'wpbookit'); ?></button>
            </form>
        </div>
    </div>
    <?php
}
?>