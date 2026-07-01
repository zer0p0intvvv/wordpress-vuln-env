<div class="offcanvas <?php echo esc_html(wpb_append_class_base_on_rtl('offcanvas-end', 'offcanvas-start')) ?> wpb-customer-import" tabindex="-1" id="wpb-customer-import" data-bs-scroll="false" data-bs-backdrop="true" aria-labelledby="wpb-customer-import-label">
    <form action="" class="offcanvas-form" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="wp_import_module" value="customer">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center">
                <h4 class="offcanvas-title" id="wpb-customer-import-label"><?php esc_html_e('Customer import', 'wpbookit'); ?></h4>
            </div>
            <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body data-scrollbar">
            <div class="form-group">
                <label class="form-label"><?php esc_html_e('Select File Type', 'wpbookit'); ?></label>
                <select class="form-control" name="wpb_file" style="width: 100%;">
                    <?php foreach (wpb_available_import_file_type() as $import_file) : ?>
                        <option value="<?php echo esc_attr($import_file['key']); ?>">
                            <?php echo esc_html($import_file['label']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="wpb_import_file" class="form-label"><?php esc_html_e('Upload a File', 'wpbookit'); ?></label>
                <div class="position-relative custom-upload-container">
                    <input class="form-control" type="file" id="wpb_import_file" name="wpb_import_file">
                    <div class="custom-upload">
                        <?php echo wpb_render_filtered_svg('upload'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <div class="mt-3 title"><?php esc_html_e('Drag & Drop or choose file to upload', 'wpbookit'); ?></div>
                    </div>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="mb-5">
                <a href="<?php echo esc_url(IQWPB_PLUGIN_URL."sample-data/sample-customer.csv")?>" target="_blank" class="text-primary small" rel="noopener noreferrer"><?php esc_html_e("Click here to download sample file", 'wpbookit') ?></a>
            </div>
            <div class="mb-3 small">
                <p class=" text-black"><?php esc_html_e("Following fields is required in CSV file", 'wpbookit') ?></p>
                <dl>
                    <?php foreach (get_require_csv_fields()['customer'] as $field) : ?>
                        <dd><span class="me-1"><?php echo wpb_render_filtered_svg('double-check') // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span> <?php echo esc_html($field); ?></dd>
                    <?php endforeach; ?>
                </dl>
            </div>
            <div class="mb-3">
                <div class="form-control import-data-log d-none">
                    <h6><?php esc_html_e('Customer Import', 'wpbookit'); ?></h6>
                    <div class="row">
                        <div class="col"><?php esc_html_e('Total rows:', 'wpbookit'); ?><span class="text-black" id="total_rows">0</span></div>
                        <div class="col"><?php esc_html_e('Total rows inserted:', 'wpbookit'); ?><span class="text-black" id="total_imported_rows">0</span></div>
                        <div class="w-100"></div>
                        <div class="col"><?php esc_html_e('Email not found:', 'wpbookit'); ?><span class="text-black" id="email_not_found">0</span></div>
                        <div class="col"><?php esc_html_e('Name not found:', 'wpbookit'); ?><span class="text-black" id="name_not_found">0</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="row">
                <div class="col-6">
                    <button id="wpb_apply_booking_reset" type="button" class="btn btn-secondary  w-100 mt-5">
                        <?php esc_html_e('Cancle', 'wpbookit'); ?>
                    </button>
                </div>
                <div class="col-6">
                    <button id="wpb_apply_booking_filters" type="submit" class="btn btn-primary  w-100 mt-5">
                        <span class="loader d-none">
                            <?php echo wpb_render_filtered_svg('spinner') // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </span>
                        <?php esc_html_e('Import', 'wpbookit'); ?>
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>