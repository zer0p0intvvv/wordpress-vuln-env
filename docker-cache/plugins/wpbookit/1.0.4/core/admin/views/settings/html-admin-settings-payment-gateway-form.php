<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) exit;
?>
    <form id="wpb-payment-gateway-form"  novalidate> 
<div class="offcanvas <?php echo esc_html( wpb_append_class_base_on_rtl('offcanvas-end','offcanvas-start')) ?> payment_gateway-options " tabindex="-1" id="payment_gateway-options" data-bs-scroll="true" data-bs-backdrop="true" aria-labelledby="add-booking-type-label">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center">
                <h4 class="offcanvas-title" id="add-offline-payment-mode">
                    <?php esc_html_e('Offline Payments', 'wpbookit'); ?>
                </h4>
                <h4 class="offcanvas-title d-none" id="edit-offline-payment-mode">
                    <?php esc_html_e('Edit Offline Payments', 'wpbookit'); ?>
                </h4>
            </div>
            <div class="d-flex gap-2 align-items-center">

            </div>
            <button type="button" class="btn-close add-btn-close text-reset shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body data-scrollbar">
            <div class="row mb-3">
                <div class="col-sm-12">
                    <label for="payment_mode_name" class="form-label"><?php esc_html_e("Method",'wpbookit') ?>*</label>
                    <input required type="text" class="form-control" name="payment_mode_name" id="payment_mode_name" placeholder="<?php esc_html_e("e.g. Cash, Cheque, Etc.",'wpbookit'); ?>">
                    <div class="invalid-feedback">
                        <?php esc_html_e("Method is required.",'wpbookit'); ?>
                    </div>
                </div>
            </div>
            <div class="row mb-3 reminder-div">
                <div class="col-sm-12">
                    <label for="payment_mode_desc" class="form-label"><?php esc_html_e("Descriptions",'wpbookit') ?>*</label>
                    <textarea required class="form-control" name="payment_mode_desc" placeholder="<?php esc_html_e("e.g. Via USD doller",'wpbookit') ?>" id="payment_mode_desc" rows="3"></textarea>
                    <div class="invalid-feedback">
                        <?php esc_html_e("Descriptions are required.",'wpbookit'); ?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="payment_gateway_id" id="payment_gateway_id" value="">
        </div>
        <div class="offcanvas-footer">
            <div class=" d-flex mb-3">
                <div class="col-sm-6 d-flex align-items-center mx-1">
                    <button id="cancel-update" class="btn btn-secondary w-100" type="button" data-bs-dismiss="offcanvas" aria-label="Close"><?php esc_html_e('Cancel', 'wpbookit'); ?></button>
                </div>
                <div class="col-sm-6 d-flex align-items-center mx-1">
                    <button id="wpb-submit-payment-mode" type="submit" name="wpb_payment_gateway_options_form" class="btn btn-primary w-100 payment_gateway_sub_button">
                        <svg class="spinner wpb-payment_gateway-submit-svg d-none" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z" />
                        </svg>
                        <span class="wpb-update-btn d-none">
                            <?php esc_html_e('Update', 'wpbookit'); ?>
                        </span>
                        <span class="wpb-add-btn">
                            <?php esc_html_e('Add', 'wpbookit'); ?>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>