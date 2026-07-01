<?php

defined('ABSPATH') || exit;

?>
<div class="tab-pane show fade" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
    <div class="card-body">
        <h5 class="mb-4"><?php esc_html_e('Edit profile', 'wpbookit'); ?></h5>
        <form method="POST" enctype="multipart/form-data" id="wpb-edit-profile-form">
            <div class="d-flex align-items-center mb-0 position-relative flex-wrap gap-5">
                <div class="position-relative">
                    <?php echo wp_kses_post(wpbookit_avatar($args['user_id'], 'rounded-pill img-fluid avatar-80 profile-image-preview')); ?>
                    <div class="upload-icone bg-primary">
                        <label for="avatar" class="avatar-label">
                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.02683 0.701C9.70016 0.969 9.90616 1.90233 10.1815 2.20233C10.4568 2.50233 10.8508 2.60433 11.0688 2.60433C12.2275 2.60433 13.1668 3.54367 13.1668 4.70167V8.565C13.1668 10.1183 11.9068 11.3783 10.3535 11.3783H3.64683C2.09283 11.3783 0.833496 10.1183 0.833496 8.565V4.70167C0.833496 3.54367 1.77283 2.60433 2.9315 2.60433C3.14883 2.60433 3.54283 2.50233 3.81883 2.20233C4.09416 1.90233 4.2995 0.969 4.97283 0.701C5.64683 0.433 8.3535 0.433 9.02683 0.701Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M10.6637 4.33333H10.6697" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.11928 6.75215C9.11928 5.58148 8.17062 4.63281 6.99995 4.63281C5.82928 4.63281 4.88062 5.58148 4.88062 6.75215C4.88062 7.92281 5.82928 8.87148 6.99995 8.87148C8.17062 8.87148 9.11928 7.92281 9.11928 6.75215Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </label>
                        <input class="file-upload" name="avatar" id="avatar" type="file" accept="image/*">
                    </div>
                    <?php wp_nonce_field('avatar_upload', 'avatar_nonce'); ?>
                </div>
                <div>
                    <h5 class="mb-0"><?php echo esc_html($args['username']); ?></h5>
                    <small><?php the_author_meta('user_email', $args['user_id']); ?></small>
                </div>
            </div>
            <div class="my-lg-5 my-3 border"></div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group mb-4">
                        <label for="email" class="form-label"><?php esc_html_e('First Name*', 'wpbookit'); ?></label>
                        <div class="input-group">
                            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="<?php echo esc_html__("Enter First Name", 'wpbookit'); ?>" aria-label="email" aria-describedby="basic-addon1" value="<?php the_author_meta('first_name', $args['user_id']); ?>">
                            <span class="input-group-text" id="basic-addon1"><svg class="icon-16" width="10" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.99113 8.45166C2.73502 8.45166 0.80835 8.79277 0.80835 10.1589C0.80835 11.525 2.72279 11.8783 4.99113 11.8783C7.24724 11.8783 9.17335 11.5367 9.17335 10.1711C9.17335 8.80555 7.25946 8.45166 4.99113 8.45166Z" stroke="#7E7E7E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.99119 6.50327C6.47174 6.50327 7.67174 5.30271 7.67174 3.82216C7.67174 2.3416 6.47174 1.1416 4.99119 1.1416C3.51063 1.1416 2.31007 2.3416 2.31007 3.82216C2.30507 5.29771 3.4973 6.49827 4.9723 6.50327H4.99119Z" stroke="#7E7E7E" stroke-width="1.42857" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        <span id="first-name-error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group mb-4">
                        <label for="name" class="form-label"><?php esc_html_e('Last Name*', 'wpbookit'); ?></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="<?php echo esc_html__("Enter Last Name", 'wpbookit'); ?>" aria-label="name" aria-describedby="basic-addon5" value="<?php the_author_meta('last_name', $args['user_id']); ?>">
                            <span class="input-group-text" id="basic-addon5"><svg class="icon-16" width="10" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.99113 8.45166C2.73502 8.45166 0.80835 8.79277 0.80835 10.1589C0.80835 11.525 2.72279 11.8783 4.99113 11.8783C7.24724 11.8783 9.17335 11.5367 9.17335 10.1711C9.17335 8.80555 7.25946 8.45166 4.99113 8.45166Z" stroke="#7E7E7E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.99119 6.50327C6.47174 6.50327 7.67174 5.30271 7.67174 3.82216C7.67174 2.3416 6.47174 1.1416 4.99119 1.1416C3.51063 1.1416 2.31007 2.3416 2.31007 3.82216C2.30507 5.29771 3.4973 6.49827 4.9723 6.50327H4.99119Z" stroke="#7E7E7E" stroke-width="1.42857" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        <span id="last-name-error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group mb-4">
                        <label for="email" class="form-label"><?php esc_html_e('Email Address*', 'wpbookit'); ?></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="email_address" name="email" placeholder="Kelvin0210@demo.com" value="<?php the_author_meta('user_email', $args['user_id']); ?>" aria-label="email" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="basic-addon2"><svg class="icon-16" width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.4432 5.66321L7.85128 7.77081C7.36158 8.15932 6.67259 8.15932 6.18288 7.77081L3.56909 5.66321" stroke="#7E7E7E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.86359 12.75C11.6377 12.7549 12.8334 11.2972 12.8334 9.50571V5.49917C12.8334 3.70765 11.6377 2.25 9.86359 2.25H4.13658C2.36246 2.25 1.16675 3.70765 1.16675 5.49917V9.50571C1.16675 11.2972 2.36246 12.7549 4.13658 12.75H9.86359Z" stroke="#7E7E7E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        <span id="email-address-error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group mb-4">
                        <label for="time" class="form-label"><?php esc_html_e('Date of birth', 'wpbookit'); ?></label>
                        <div class="input-group">
                            <input 
                                type="text" 
                                class="form-control date_flatpicker flatpickr-input active" 
                                id="wpb-profile-flatpickr" 
                                name="date_of_birth" 
                                value="<?php echo esc_attr(get_user_meta($args['user_id'], 'date_of_birth', true)); ?>" 
                                placeholder="<?php esc_html_e("select your dob", 'wpbookit') ?>" 
                                aria-label="date" 
                                aria-describedby="basic-addon5" 
                                max="<?php echo esc_attr(date('Y-m-d')); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date ?>">
                            <span class="input-group-text" id="basic-addon5"><svg class="icon-16" width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.80396 5.98601H12.2013" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.59123 8.26409H9.59663" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M7.00261 8.26409H7.00801" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M4.40886 8.26409H4.41426" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.59123 10.5312H9.59663" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M7.00261 10.5312H7.00801" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M4.40886 10.5312H4.41426" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.35882 1.66675V3.58637" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M4.64666 1.66675V3.58637" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path id="wpb-profile-flatpickr" fill-rule="evenodd" clip-rule="evenodd" d="M9.47232 2.58789H4.53306C2.81999 2.58789 1.75 3.54219 1.75 5.29632V10.5753C1.75 12.357 2.81999 13.3334 4.53306 13.3334H9.46692C11.1854 13.3334 12.25 12.3735 12.25 10.6194V5.29632C12.2554 3.54219 11.1908 2.58789 9.47232 2.58789Z" stroke="#7E7E7E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group mb-4">
                        <label for="name" class="form-label"><?php esc_html_e('Phone Number', 'wpbookit'); ?></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="phone" name="phone" placeholder="<?php esc_html_e("Enter Phone Number", 'wpbookit') ?>" aria-label="phonenum" aria-describedby="basic-addon5" data-iso2="<?php the_author_meta('iso2', $args['user_id']) ?? ""; ?>" value="<?php the_author_meta('phone', $args['user_id']); ?>">
                            <span class="input-group-text" id="basic-addon3">
                                <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . 'core/admin/assets/images/phone-icon.svg');  // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="phone">
                            </span>
                        </div>
                        <span id="phone-error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group mb-4">
                        <label for="name" class="form-label"><?php esc_html_e('Gender', 'wpbookit'); ?></label>
                        <div class="input-group">
                            <select class=" js-states form-select form-control" name="gender" id="gender">
                                <option value="" disabled selected><?php echo esc_html(empty(get_the_author_meta('gender', $args['user_id'])) ? 'Select Gender' : htmlspecialchars(ucfirst(get_the_author_meta('gender', $args['user_id'])))); ?></option>
                                <option value="male"><?php esc_html_e('Male', 'wpbookit'); ?></option>
                                <option value="female"><?php esc_html_e('Female', 'wpbookit'); ?></option>
                                <option value="other"><?php esc_html_e('Other', 'wpbookit'); ?></option>
                            </select>
                            <span class="input-group-text" id="basic-addon4">
                                <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . 'core/admin/assets/images/gender-icon.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="gender">
                            </span>
                        </div>
                        <span id="gender-error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="email" class="form-label"><?php esc_html_e('Old Password', 'wpbookit'); ?></label>
                        <div class="input-group">
                            <input type="password" name="old_password" id="old_password" class="form-control" placeholder="<?php esc_attr_e("Enter Old Password", 'wpbookit') ?>" aria-label="old-password" aria-describedby="basic-addon6">
                            <span class="input-group-text show-pass-toggle" id="togglePassword">
                                <svg class="icon-16 hide-pass" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_936_3182)">
                                        <path d="M3.44874 2.39326C3.41032 2.34995 3.36369 2.3147 3.31155 2.28954C3.2594 2.26439 3.20278 2.24984 3.14497 2.24673C3.08716 2.24362 3.02931 2.25201 2.97476 2.27142C2.92022 2.29084 2.87007 2.32088 2.82723 2.35982C2.78438 2.39876 2.74969 2.44581 2.72517 2.49825C2.70064 2.5507 2.68677 2.60749 2.68435 2.66533C2.68193 2.72317 2.69102 2.78092 2.71109 2.83523C2.73115 2.88954 2.7618 2.93932 2.80124 2.98169L3.85343 4.13943C1.86718 5.35841 1.01296 7.23748 0.975229 7.32279C0.950354 7.37874 0.9375 7.43929 0.9375 7.50052C0.9375 7.56175 0.950354 7.6223 0.975229 7.67826C0.99437 7.72146 1.45757 8.74849 2.48734 9.77826C3.85945 11.1498 5.5925 11.875 7.5 11.875C8.48033 11.8806 9.45076 11.6787 10.3476 11.2827L11.5507 12.6067C11.5891 12.65 11.6358 12.6853 11.6879 12.7104C11.74 12.7356 11.7967 12.7501 11.8545 12.7532C11.9123 12.7563 11.9701 12.7479 12.0247 12.7285C12.0792 12.7091 12.1294 12.6791 12.1722 12.6401C12.2151 12.6012 12.2498 12.5541 12.2743 12.5017C12.2988 12.4493 12.3127 12.3925 12.3151 12.3346C12.3175 12.2768 12.3084 12.219 12.2884 12.1647C12.2683 12.1104 12.2376 12.0606 12.1982 12.0183L3.44874 2.39326ZM6.0371 6.54076L8.31593 9.04818C7.97277 9.22871 7.57936 9.29016 7.19748 9.22287C6.81561 9.15558 6.46689 8.96337 6.2061 8.67642C5.94531 8.38946 5.7872 8.02402 5.75661 7.63747C5.72603 7.25092 5.82469 6.86516 6.0371 6.54076ZM7.5 11C5.81671 11 4.34617 10.388 3.12882 9.18162C2.62915 8.68504 2.20418 8.1186 1.86718 7.49998C2.12367 7.01927 2.94234 5.67396 4.45664 4.79951L5.44101 5.87959C5.05991 6.36767 4.86364 6.97478 4.88688 7.59359C4.91012 8.21239 5.15138 8.80306 5.56801 9.26119C5.98465 9.71932 6.54983 10.0154 7.16366 10.0971C7.77749 10.1788 8.40045 10.0409 8.92242 9.70771L9.72796 10.5936C9.01702 10.8664 8.26145 11.0042 7.5 11ZM7.82812 5.78115C7.71412 5.75939 7.61343 5.69324 7.5482 5.59724C7.48297 5.50125 7.45855 5.38327 7.48031 5.26927C7.50206 5.15527 7.56822 5.05458 7.66421 4.98935C7.76021 4.92413 7.87818 4.8997 7.99218 4.92146C8.54978 5.02956 9.05749 5.31502 9.43959 5.73526C9.82169 6.1555 10.0577 6.688 10.1124 7.25333C10.1232 7.36886 10.0877 7.48394 10.0136 7.57327C9.9396 7.6626 9.8331 7.71885 9.71757 7.72966C9.70391 7.73047 9.69022 7.73047 9.67656 7.72966C9.56721 7.73013 9.46165 7.68963 9.38067 7.61615C9.29969 7.54266 9.24917 7.44151 9.23906 7.33263C9.20223 6.9566 9.04503 6.60249 8.79082 6.32296C8.53661 6.04343 8.19898 5.85341 7.82812 5.78115ZM14.0231 7.67826C14.0002 7.72966 13.4462 8.9563 12.1987 10.0736C12.1561 10.113 12.1061 10.1435 12.0516 10.1633C11.997 10.1832 11.9391 10.192 11.8811 10.1893C11.8231 10.1865 11.7663 10.1723 11.7139 10.1474C11.6615 10.1224 11.6145 10.0873 11.5758 10.0441C11.5371 10.0008 11.5074 9.95031 11.4884 9.89547C11.4694 9.84062 11.4615 9.78255 11.4652 9.72463C11.4688 9.66671 11.484 9.61009 11.5098 9.55808C11.5355 9.50607 11.5714 9.45971 11.6152 9.42169C12.2272 8.87188 12.7413 8.22205 13.1355 7.49998C12.7978 6.88079 12.3719 6.31396 11.8712 5.81724C10.6538 4.61193 9.18328 3.99998 7.5 3.99998C7.14532 3.99954 6.79121 4.02826 6.44125 4.08583C6.38433 4.0959 6.32599 4.09459 6.26959 4.08198C6.21319 4.06937 6.15984 4.0457 6.11264 4.01236C6.06543 3.97901 6.0253 3.93665 5.99457 3.8877C5.96383 3.83876 5.94309 3.78421 5.93355 3.72721C5.92402 3.67021 5.92587 3.61188 5.939 3.5556C5.95214 3.49932 5.97629 3.44619 6.01007 3.3993C6.04386 3.35241 6.08659 3.31267 6.13582 3.28239C6.18504 3.2521 6.23978 3.23187 6.29687 3.22287C6.69454 3.15721 7.09694 3.12446 7.5 3.12498C9.4075 3.12498 11.1405 3.85013 12.5127 5.22224C13.5424 6.25201 14.0056 7.27959 14.0248 7.32279C14.0496 7.37874 14.0625 7.43929 14.0625 7.50052C14.0625 7.56175 14.0496 7.6223 14.0248 7.67826H14.0231Z" fill="#6E7990" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_936_3182">
                                            <rect width="14" height="14" fill="white" transform="translate(0.5 0.5)" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <svg class="icon-16 show-pass" style="display: none;" enable-background="new 0 0 128 128" height="512" viewBox="0 0 128 128" width="512" xmlns="http://www.w3.org/2000/svg">
                                    <path id="Show" d="m64 104c-41.873 0-62.633-36.504-63.496-38.057-.672-1.209-.672-2.678 0-3.887.863-1.552 21.623-38.056 63.496-38.056s62.633 36.504 63.496 38.057c.672 1.209.672 2.678 0 3.887-.863 1.552-21.623 38.056-63.496 38.056zm-55.293-40.006c4.758 7.211 23.439 32.006 55.293 32.006 31.955 0 50.553-24.775 55.293-31.994-4.758-7.211-23.439-32.006-55.293-32.006-31.955 0-50.553 24.775-55.293 31.994zm55.293 24.006c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z" fill="#6E7990"></path>
                                </svg>
                            </span>
                        </div>
                        <span id="pass-old-error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="email" class="form-label"><?php esc_html_e('Change Password', 'wpbookit'); ?></label>
                        <div class="input-group">
                            <input type="password" name="pass1" id="pass1" class="form-control" placeholder="<?php esc_attr_e("Enter New Password", 'wpbookit') ?>" aria-label="email" aria-describedby="basic-addon6">
                            <span class="input-group-text show-pass-toggle" id="togglePassword">
                                <svg class="icon-16 hide-pass" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_936_3182)">
                                        <path d="M3.44874 2.39326C3.41032 2.34995 3.36369 2.3147 3.31155 2.28954C3.2594 2.26439 3.20278 2.24984 3.14497 2.24673C3.08716 2.24362 3.02931 2.25201 2.97476 2.27142C2.92022 2.29084 2.87007 2.32088 2.82723 2.35982C2.78438 2.39876 2.74969 2.44581 2.72517 2.49825C2.70064 2.5507 2.68677 2.60749 2.68435 2.66533C2.68193 2.72317 2.69102 2.78092 2.71109 2.83523C2.73115 2.88954 2.7618 2.93932 2.80124 2.98169L3.85343 4.13943C1.86718 5.35841 1.01296 7.23748 0.975229 7.32279C0.950354 7.37874 0.9375 7.43929 0.9375 7.50052C0.9375 7.56175 0.950354 7.6223 0.975229 7.67826C0.99437 7.72146 1.45757 8.74849 2.48734 9.77826C3.85945 11.1498 5.5925 11.875 7.5 11.875C8.48033 11.8806 9.45076 11.6787 10.3476 11.2827L11.5507 12.6067C11.5891 12.65 11.6358 12.6853 11.6879 12.7104C11.74 12.7356 11.7967 12.7501 11.8545 12.7532C11.9123 12.7563 11.9701 12.7479 12.0247 12.7285C12.0792 12.7091 12.1294 12.6791 12.1722 12.6401C12.2151 12.6012 12.2498 12.5541 12.2743 12.5017C12.2988 12.4493 12.3127 12.3925 12.3151 12.3346C12.3175 12.2768 12.3084 12.219 12.2884 12.1647C12.2683 12.1104 12.2376 12.0606 12.1982 12.0183L3.44874 2.39326ZM6.0371 6.54076L8.31593 9.04818C7.97277 9.22871 7.57936 9.29016 7.19748 9.22287C6.81561 9.15558 6.46689 8.96337 6.2061 8.67642C5.94531 8.38946 5.7872 8.02402 5.75661 7.63747C5.72603 7.25092 5.82469 6.86516 6.0371 6.54076ZM7.5 11C5.81671 11 4.34617 10.388 3.12882 9.18162C2.62915 8.68504 2.20418 8.1186 1.86718 7.49998C2.12367 7.01927 2.94234 5.67396 4.45664 4.79951L5.44101 5.87959C5.05991 6.36767 4.86364 6.97478 4.88688 7.59359C4.91012 8.21239 5.15138 8.80306 5.56801 9.26119C5.98465 9.71932 6.54983 10.0154 7.16366 10.0971C7.77749 10.1788 8.40045 10.0409 8.92242 9.70771L9.72796 10.5936C9.01702 10.8664 8.26145 11.0042 7.5 11ZM7.82812 5.78115C7.71412 5.75939 7.61343 5.69324 7.5482 5.59724C7.48297 5.50125 7.45855 5.38327 7.48031 5.26927C7.50206 5.15527 7.56822 5.05458 7.66421 4.98935C7.76021 4.92413 7.87818 4.8997 7.99218 4.92146C8.54978 5.02956 9.05749 5.31502 9.43959 5.73526C9.82169 6.1555 10.0577 6.688 10.1124 7.25333C10.1232 7.36886 10.0877 7.48394 10.0136 7.57327C9.9396 7.6626 9.8331 7.71885 9.71757 7.72966C9.70391 7.73047 9.69022 7.73047 9.67656 7.72966C9.56721 7.73013 9.46165 7.68963 9.38067 7.61615C9.29969 7.54266 9.24917 7.44151 9.23906 7.33263C9.20223 6.9566 9.04503 6.60249 8.79082 6.32296C8.53661 6.04343 8.19898 5.85341 7.82812 5.78115ZM14.0231 7.67826C14.0002 7.72966 13.4462 8.9563 12.1987 10.0736C12.1561 10.113 12.1061 10.1435 12.0516 10.1633C11.997 10.1832 11.9391 10.192 11.8811 10.1893C11.8231 10.1865 11.7663 10.1723 11.7139 10.1474C11.6615 10.1224 11.6145 10.0873 11.5758 10.0441C11.5371 10.0008 11.5074 9.95031 11.4884 9.89547C11.4694 9.84062 11.4615 9.78255 11.4652 9.72463C11.4688 9.66671 11.484 9.61009 11.5098 9.55808C11.5355 9.50607 11.5714 9.45971 11.6152 9.42169C12.2272 8.87188 12.7413 8.22205 13.1355 7.49998C12.7978 6.88079 12.3719 6.31396 11.8712 5.81724C10.6538 4.61193 9.18328 3.99998 7.5 3.99998C7.14532 3.99954 6.79121 4.02826 6.44125 4.08583C6.38433 4.0959 6.32599 4.09459 6.26959 4.08198C6.21319 4.06937 6.15984 4.0457 6.11264 4.01236C6.06543 3.97901 6.0253 3.93665 5.99457 3.8877C5.96383 3.83876 5.94309 3.78421 5.93355 3.72721C5.92402 3.67021 5.92587 3.61188 5.939 3.5556C5.95214 3.49932 5.97629 3.44619 6.01007 3.3993C6.04386 3.35241 6.08659 3.31267 6.13582 3.28239C6.18504 3.2521 6.23978 3.23187 6.29687 3.22287C6.69454 3.15721 7.09694 3.12446 7.5 3.12498C9.4075 3.12498 11.1405 3.85013 12.5127 5.22224C13.5424 6.25201 14.0056 7.27959 14.0248 7.32279C14.0496 7.37874 14.0625 7.43929 14.0625 7.50052C14.0625 7.56175 14.0496 7.6223 14.0248 7.67826H14.0231Z" fill="#6E7990" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_936_3182">
                                            <rect width="14" height="14" fill="white" transform="translate(0.5 0.5)" />
                                        </clipPath>
                                    </defs>
                                </svg>

                                <svg class="icon-16 show-pass" style="display: none;" enable-background="new 0 0 128 128" height="512" viewBox="0 0 128 128" width="512" xmlns="http://www.w3.org/2000/svg">
                                    <path id="Show" d="m64 104c-41.873 0-62.633-36.504-63.496-38.057-.672-1.209-.672-2.678 0-3.887.863-1.552 21.623-38.056 63.496-38.056s62.633 36.504 63.496 38.057c.672 1.209.672 2.678 0 3.887-.863 1.552-21.623 38.056-63.496 38.056zm-55.293-40.006c4.758 7.211 23.439 32.006 55.293 32.006 31.955 0 50.553-24.775 55.293-31.994-4.758-7.211-23.439-32.006-55.293-32.006-31.955 0-50.553 24.775-55.293 31.994zm55.293 24.006c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z" fill="#6E7990"></path>
                                </svg>
                            </span>
                        </div>
                        <span id="pass-1-error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="email" class="form-label"><?php esc_html_e('Confirm Password', 'wpbookit'); ?></label>
                        <div class="input-group">
                            <input type="password" name="pass2" id="pass2" class="form-control" placeholder="<?php esc_attr_e("Enter Confirm Password", 'wpbookit') ?>" aria-label="email" aria-describedby="basic-addon7">
                            <span class="input-group-text show-pass-toggle" id="togglePassword2">
                                <svg class="icon-16 hide-pass" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_936_3182)">
                                        <path d="M3.44874 2.39326C3.41032 2.34995 3.36369 2.3147 3.31155 2.28954C3.2594 2.26439 3.20278 2.24984 3.14497 2.24673C3.08716 2.24362 3.02931 2.25201 2.97476 2.27142C2.92022 2.29084 2.87007 2.32088 2.82723 2.35982C2.78438 2.39876 2.74969 2.44581 2.72517 2.49825C2.70064 2.5507 2.68677 2.60749 2.68435 2.66533C2.68193 2.72317 2.69102 2.78092 2.71109 2.83523C2.73115 2.88954 2.7618 2.93932 2.80124 2.98169L3.85343 4.13943C1.86718 5.35841 1.01296 7.23748 0.975229 7.32279C0.950354 7.37874 0.9375 7.43929 0.9375 7.50052C0.9375 7.56175 0.950354 7.6223 0.975229 7.67826C0.99437 7.72146 1.45757 8.74849 2.48734 9.77826C3.85945 11.1498 5.5925 11.875 7.5 11.875C8.48033 11.8806 9.45076 11.6787 10.3476 11.2827L11.5507 12.6067C11.5891 12.65 11.6358 12.6853 11.6879 12.7104C11.74 12.7356 11.7967 12.7501 11.8545 12.7532C11.9123 12.7563 11.9701 12.7479 12.0247 12.7285C12.0792 12.7091 12.1294 12.6791 12.1722 12.6401C12.2151 12.6012 12.2498 12.5541 12.2743 12.5017C12.2988 12.4493 12.3127 12.3925 12.3151 12.3346C12.3175 12.2768 12.3084 12.219 12.2884 12.1647C12.2683 12.1104 12.2376 12.0606 12.1982 12.0183L3.44874 2.39326ZM6.0371 6.54076L8.31593 9.04818C7.97277 9.22871 7.57936 9.29016 7.19748 9.22287C6.81561 9.15558 6.46689 8.96337 6.2061 8.67642C5.94531 8.38946 5.7872 8.02402 5.75661 7.63747C5.72603 7.25092 5.82469 6.86516 6.0371 6.54076ZM7.5 11C5.81671 11 4.34617 10.388 3.12882 9.18162C2.62915 8.68504 2.20418 8.1186 1.86718 7.49998C2.12367 7.01927 2.94234 5.67396 4.45664 4.79951L5.44101 5.87959C5.05991 6.36767 4.86364 6.97478 4.88688 7.59359C4.91012 8.21239 5.15138 8.80306 5.56801 9.26119C5.98465 9.71932 6.54983 10.0154 7.16366 10.0971C7.77749 10.1788 8.40045 10.0409 8.92242 9.70771L9.72796 10.5936C9.01702 10.8664 8.26145 11.0042 7.5 11ZM7.82812 5.78115C7.71412 5.75939 7.61343 5.69324 7.5482 5.59724C7.48297 5.50125 7.45855 5.38327 7.48031 5.26927C7.50206 5.15527 7.56822 5.05458 7.66421 4.98935C7.76021 4.92413 7.87818 4.8997 7.99218 4.92146C8.54978 5.02956 9.05749 5.31502 9.43959 5.73526C9.82169 6.1555 10.0577 6.688 10.1124 7.25333C10.1232 7.36886 10.0877 7.48394 10.0136 7.57327C9.9396 7.6626 9.8331 7.71885 9.71757 7.72966C9.70391 7.73047 9.69022 7.73047 9.67656 7.72966C9.56721 7.73013 9.46165 7.68963 9.38067 7.61615C9.29969 7.54266 9.24917 7.44151 9.23906 7.33263C9.20223 6.9566 9.04503 6.60249 8.79082 6.32296C8.53661 6.04343 8.19898 5.85341 7.82812 5.78115ZM14.0231 7.67826C14.0002 7.72966 13.4462 8.9563 12.1987 10.0736C12.1561 10.113 12.1061 10.1435 12.0516 10.1633C11.997 10.1832 11.9391 10.192 11.8811 10.1893C11.8231 10.1865 11.7663 10.1723 11.7139 10.1474C11.6615 10.1224 11.6145 10.0873 11.5758 10.0441C11.5371 10.0008 11.5074 9.95031 11.4884 9.89547C11.4694 9.84062 11.4615 9.78255 11.4652 9.72463C11.4688 9.66671 11.484 9.61009 11.5098 9.55808C11.5355 9.50607 11.5714 9.45971 11.6152 9.42169C12.2272 8.87188 12.7413 8.22205 13.1355 7.49998C12.7978 6.88079 12.3719 6.31396 11.8712 5.81724C10.6538 4.61193 9.18328 3.99998 7.5 3.99998C7.14532 3.99954 6.79121 4.02826 6.44125 4.08583C6.38433 4.0959 6.32599 4.09459 6.26959 4.08198C6.21319 4.06937 6.15984 4.0457 6.11264 4.01236C6.06543 3.97901 6.0253 3.93665 5.99457 3.8877C5.96383 3.83876 5.94309 3.78421 5.93355 3.72721C5.92402 3.67021 5.92587 3.61188 5.939 3.5556C5.95214 3.49932 5.97629 3.44619 6.01007 3.3993C6.04386 3.35241 6.08659 3.31267 6.13582 3.28239C6.18504 3.2521 6.23978 3.23187 6.29687 3.22287C6.69454 3.15721 7.09694 3.12446 7.5 3.12498C9.4075 3.12498 11.1405 3.85013 12.5127 5.22224C13.5424 6.25201 14.0056 7.27959 14.0248 7.32279C14.0496 7.37874 14.0625 7.43929 14.0625 7.50052C14.0625 7.56175 14.0496 7.6223 14.0248 7.67826H14.0231Z" fill="#6E7990" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_936_3182">
                                            <rect width="14" height="14" fill="white" transform="translate(0.5 0.5)" />
                                        </clipPath>
                                    </defs>
                                </svg>
                              
                                <svg class="icon-16 show-pass" style="display: none;" enable-background="new 0 0 128 128" height="512" viewBox="0 0 128 128" width="512" xmlns="http://www.w3.org/2000/svg">
                                    <path id="Show" d="m64 104c-41.873 0-62.633-36.504-63.496-38.057-.672-1.209-.672-2.678 0-3.887.863-1.552 21.623-38.056 63.496-38.056s62.633 36.504 63.496 38.057c.672 1.209.672 2.678 0 3.887-.863 1.552-21.623 38.056-63.496 38.056zm-55.293-40.006c4.758 7.211 23.439 32.006 55.293 32.006 31.955 0 50.553-24.775 55.293-31.994-4.758-7.211-23.439-32.006-55.293-32.006-31.955 0-50.553 24.775-55.293 31.994zm55.293 24.006c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z" fill="#6E7990"></path>
                                </svg>

                            </span>
                        </div>
                        <span id="pass-2-error" class="error-message"></span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <input name="edit-profile-submit" type="hidden" id="edit-profile-submit" value="<?php echo esc_html($args['user_id']) ?>">
                        <button type="submit" id="submit-button" class="btn btn-primary">
                            <svg class="spinner d-none wpb-customer-submit-svg" height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                            </svg>
                            <?php esc_html_e('Save', 'wpbookit'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>