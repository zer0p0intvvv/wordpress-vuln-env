<div class="offcanvas <?php echo esc_html( wpb_append_class_base_on_rtl('offcanvas-end','offcanvas-start')) ?>" tabindex="-1" id="new-customer" data-bs-scroll="true"  data-bs-backdrop="true" aria-labelledby="new-customer-label">
                <div class="offcanvas-header">
                    <div class="d-flex align-items-center">
                        <h4 class="offcanvas-title" id="new-customer-label"><?php esc_html_e('Add New Customer', 'wpbookit'); ?> </h4>
                    </div>
                    <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body data-scrollbar">
                <form id="add-customer-form" class="add-customer-form" method="POST" enctype="multipart/form-data">
                    
                    <div class="text-center mb-4">
                        <img id="add-image-preview" src="<?php echo esc_html(IQWPB_PLUGIN_URL ."core/admin/assets/images/avatar.png");// phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" data-attr="<?php echo esc_html(IQWPB_PLUGIN_URL . "core/admin/assets/images/avatar.png"); ?>" class="rounded-pill img-fluid avatar-100" alt="profile-image">
                        <div class="d-flex align-items-center justify-content-center gap-2 mt-4">
                            <input type="file" class="form-control d-none" id="add-image" name="add-image" accept=".jpeg, .jpg, .png, .gif">
                            <label class="btn btn-danger-subtle" for="add-image"><?php esc_html_e('Upload','wpbookit'); ?></label>
                            <input type="button" id="remove-btn" class="btn btn-primary-subtle remove-btn d-none" name="remove" value="Remove">
                        </div>
                        <span id="image-preview-error" class="error-message"></span>
                    </div>
                    <div class="form-group mb-4">
                        <label for="name" class="form-label"><?php esc_html_e('First Name*','wpbookit'); ?></label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="first-name" id="first-name" placeholder="e.g. Kenny" aria-label="Username" aria-describedby="basic-addon1" required>
                            <span class="input-group-text" id="basic-addon1">
                            <img src="<?php echo esc_attr(IQWPB_PLUGIN_URL ."/core/admin/assets/images/userfield-icon.svg" ); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked">
                            </span>
                        </div>
                        <span id="first-name-error" class="error-message"></span>
                    </div>
                        
                    <div class="form-group mb-4">
                        <label for="name" class="form-label"><?php esc_html_e('Last Name*','wpbookit'); ?></label>
                        <div class="input-group">
                            <input type="text" class="form-control"  name="last-name"  id="last-name"placeholder="e.g. Williams" aria-label="Username" aria-describedby="basic-addon1" required>
                            <span class="input-group-text" id="basic-addon1">
                            <img src="<?php echo esc_attr( IQWPB_PLUGIN_URL ."/core/admin/assets/images/userfield-icon.svg"); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked">
                            </span>
                        </div>
                        <span id="last-name-error" class="error-message"></span>
                    </div>
                    <div class="form-group mb-4">
                            <label for="email" class="form-label"><?php esc_html_e('Email Address*','wpbookit'); ?></label>
                                <div class="input-group">
                                <input type="text" name="email" id="email" class="form-control" placeholder="e.g. Kenny@demo.com" aria-label="email" aria-describedby="basic-addon2" required>
                                <span class="input-group-text" id="basic-addon2">
                                <img src="<?php echo esc_attr(IQWPB_PLUGIN_URL ."/core/admin/assets/images/email-icon.svg" ); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked">
                                </span>
                            </div>
                            <span id="email-error" class="error-message"></span>
                        </div>
                        <div class="form-group mb-4">
                            <label for="phonenum" class="form-label"><?php esc_html_e('Phone Number','wpbookit'); ?></label>
                                <div class="input-group">
                                <input type="number" name="phone" id="phone" class="form-control" placeholder="e.g. 1234567890" aria-label="phonenum" aria-describedby="basic-addon3">
                                <span class="input-group-text" id="basic-addon3">
                                <img src="<?php echo esc_attr(IQWPB_PLUGIN_URL ."/core/admin/assets/images/phone-icon.svg"); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="checked">
                                </span>
                            </div>
                            <span id="phone-error" class="error-message"></span>
                        </div>
                        <div class="form-group wpb-gender-field mb-4">
                            <label for="gender" class="form-label d-block"><?php esc_html_e('Gender','wpbookit'); ?></label>
                            <div class="input-group">
                                <select class=" js-states form-select form-control" name="gender" id="gender" placeholder="Female">
                                    <option value="" disabled selected><?php esc_html_e('Select Gender','wpbookit'); ?></option>
                                    <option value="male"><?php esc_html_e('Male','wpbookit'); ?></option>
                                    <option value="female"><?php esc_html_e('Female','wpbookit'); ?></option>
                                    <option value="other"><?php esc_html_e('Other','wpbookit'); ?></option>
                                </select>
                                <span class="input-group-text" id="basic-addon4">
                                <img src="<?php echo esc_attr(IQWPB_PLUGIN_URL . "/core/admin/assets/images/gender-icon.svg");  // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked">
                                </span>
                            </div>
                            <span id="gender-error" class="error-message"></span>
                        </div>
                        <div class="form-group ">
                            <label for="time" class="form-label"><?php esc_html_e('Date of Birth','wpbookit'); ?></label>
                            <div class="input-group">
                                <input type="text" name="dob" id="wpb-range-flatpicker" class="form-control range_flatpicker flatpickr-input active" placeholder="e.g. YYYY-MM-DD"  aria-label="date" aria-describedby="basic-addon5">
                                <span class="input-group-text" id="basic-addon5">
                                <img src="<?php echo esc_attr(IQWPB_PLUGIN_URL ."/core/admin/assets/images/calender-icon.svg" );// phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" id="wpb-calendar-icon" alt="checked">
                                </span>
                            </div>
                            <span id="dob-error" class="error-message"></span>
                        </div>
                        <div class="form-group ">
                            <label for="time" class="form-label"><?php esc_html_e('Note','wpbookit'); ?> </label>
                            <div class="input-group">
                                
                                <textarea id="notes" name="notes" rows="4" cols="50" class="form-control" placeholder="<?php esc_html_e("Write Your Message Here", 'wpbookit') ?>" aria-describedby="basic-addon5" ></textarea>
                            </div>
                        </div>
                    <input type="hidden" id="edit-customer-id" name="edit-customer-id">
                    <button type="submit" class="submit-button btn btn-primary w-100 mt-5" id="submit-button">
                        <svg class="spinner d-none wpb-customer-submit-svg" height="20" width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z"/>
                        </svg>
                        <?php esc_html_e('Save', 'wpbookit' ); ?>
                    </button>
                    
                </form>
                </div>
            </div>
          