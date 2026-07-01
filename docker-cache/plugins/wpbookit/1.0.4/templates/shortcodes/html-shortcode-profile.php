<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/wpbookit/html-shotcode-profile.php.
 *
 * HOWEVER, on occasion WPBookit will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package WPBookit\Templates
 * @version 1.0.4
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="wpb-profile-shortcode  pb-0" data-bs-theme="light" id="profile-shortcode">
    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class=" card">
                        <div class="p-3 bg-primary rounded-top d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <?php echo wp_kses_post(wpbookit_avatar(get_current_user_id(  ), 'avatar-40 circle-image')); ?>
                                <p class="mb-0 text-white"><?php echo esc_html($args['username']); ?></p>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M11.5134 5.1579V4.3804C11.5134 2.68457 10.1384 1.30957 8.44256 1.30957H4.38006C2.68506 1.30957 1.31006 2.68457 1.31006 4.3804V13.6554C1.31006 15.3512 2.68506 16.7262 4.38006 16.7262H8.45089C10.1417 16.7262 11.5134 15.3554 11.5134 13.6646V12.8787"
                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M17.1747 9.01774H7.1405" stroke="white" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M14.7344 6.58838L17.1744 9.01755L14.7344 11.4475" stroke="white"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mb-0 text-white"><a class="text-white"
                                        href="<?php echo esc_url(wp_logout_url( home_url())); ?>"><?php esc_html_e('Sign-out','wpbookit'); ?></a></p>
                            </div>
                        </div>
                        <div class="bg-body px-3 pt-3">
                            <nav class="tab-bottom-bordered">
                                <div class="mb-0 bg-body nav nav-tabs " id="nav-tab1" role="tablist">
                                    <button class="nav-link d-inline-flex align-items-center gap-2 active" id="upcoming-bookings-tab" data-bs-toggle="tab"
                                        data-bs-target="#upcoming-bookings" type="button" role="tab"
                                        aria-controls="upcoming-bookings" aria-selected="true"><svg class="icon-20"
                                            width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.57715 7.83688H17.4304" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M13.7017 11.0913H13.7094" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M10.004 11.0913H10.0117" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M6.29816 11.0913H6.30588" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M13.7017 14.33H13.7094" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M10.004 14.33H10.0117" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M6.29816 14.33H6.30588" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M13.3699 1.6665V4.40882" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M6.63795 1.6665V4.40882" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M13.5319 2.98242H6.4758C4.02856 2.98242 2.5 4.3457 2.5 6.85161V14.393C2.5 16.9383 4.02856 18.3331 6.4758 18.3331H13.5242C15.9791 18.3331 17.5 16.9619 17.5 14.456V6.85161C17.5077 4.3457 15.9868 2.98242 13.5319 2.98242Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                        <?php esc_html_e('Upcoming bookings','wpbookit'); ?></button>
                                    <button class="nav-link d-inline-flex align-items-center gap-2" id="pending-bookings-tab" data-bs-toggle="tab"
                                        data-bs-target="#pending-bookings" type="button" role="tab"
                                        aria-controls="pending-bookings" aria-selected="false"><svg
                                            class="icon-20" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M17.7082 10.0003C17.7082 14.2578 14.2573 17.7087 9.99984 17.7087C5.74234 17.7087 2.2915 14.2578 2.2915 10.0003C2.2915 5.74283 5.74234 2.29199 9.99984 2.29199C14.2573 2.29199 17.7082 5.74283 17.7082 10.0003Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M12.8594 12.4524L9.71777 10.5782V6.53906" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <?php esc_html_e('Pending bookings','wpbookit'); ?>
                                        <?php if (isset($args['count_pending_booking']) && $args['count_pending_booking'] !== 0 && $args['count_pending_booking'] < 9) { ?>
                                                <span class="counter-badge">
                                                    <?php echo esc_html($args['count_pending_booking']);
                                                ?></span>
                                            <?php } elseif (isset($args['count_pending_booking'])&& $args['count_pending_booking'] >= 9) { ?>
                                                <span class="counter-badge">
                                                    <?php  esc_html_e("9+",'wpbookit');
                                                ?></span>
                                            <?php  }  ?>
                                    </button>
                                    <button class="nav-link d-inline-flex align-items-center gap-2" id="bookings-history-tab" data-bs-toggle="tab"
                                        data-bs-target="#booking-history" type="button" role="tab"
                                        aria-controls="booking-history" aria-selected="false"><svg
                                            class="icon-20" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12.2814 2.30129H6.73728C5.00394 2.29463 3.58311 3.67629 3.54227 5.40879V14.3363C3.50394 16.0971 4.89978 17.5563 6.66061 17.5955C6.68644 17.5955 6.71144 17.5963 6.73728 17.5955H13.3948C15.1398 17.5246 16.5148 16.083 16.5023 14.3363V6.69796L12.2814 2.30129Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M12.0625 2.2915V4.71567C12.0625 5.899 13.0192 6.85817 14.2025 6.8615H16.4983"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M11.9067 12.7985H7.40674" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M10.2025 9.67155H7.40588" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <?php esc_html_e('Bookings history','wpbookit'); ?></button>
                                    <button class="nav-link d-inline-flex align-items-center gap-2" id="edit-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#edit-profile" type="button" role="tab"
                                        aria-controls="edit-profile" aria-selected="false"><svg class="icon-20"
                                            width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.4562 17.0355H17.5" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M10.65 3.16233C11.2964 2.38982 12.4583 2.27655 13.2469 2.90978C13.2905 2.94413 14.6912 4.03232 14.6912 4.03232C15.5575 4.55599 15.8266 5.66925 15.2912 6.51882C15.2627 6.56432 7.34329 16.4704 7.34329 16.4704C7.07981 16.7991 6.67986 16.9931 6.25242 16.9978L3.21961 17.0358L2.53628 14.1436C2.44055 13.7369 2.53628 13.3098 2.79975 12.9811L10.65 3.16233Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M9.18396 5.00098L13.7275 8.49025" stroke="currentColor"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg><?php esc_html_e('Edit profile','wpbookit'); ?></button>
                                </div>
                            </nav>
                        </div>
                        <div class="tab-content iq-tab-fade-up" id="nav-tabContent">
                            <?php do_action('wpb_upcoming_bookings_hook',$args); ?>
                            <?php do_action('wpb_pending_bookings_hook',$args); ?>
                            <?php do_action('wpb_bookings_history_hook',$args); ?>
                            <?php do_action('wpb_edit_profile_hook',$args); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php do_action('wpb_after_profile_container'); ?>
</div>