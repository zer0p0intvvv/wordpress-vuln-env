<?php

defined('ABSPATH') || exit;

?>
    <div class="row">
        <?php do_action('wpb_booking_shortcode_before_payment_tab_content', $args); 

        if ((int)($shortcode_instance->booking_type->get_meta('price')) > 0) : ?>
            <div class="col-lg-12 mb-2 pb-2">
                <div class="mt-2 pt-2 border-top">
                    <label class="form-label d-block "><?php esc_html_e('Booking Summary', 'wpbookit'); ?></label>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tax-module-table-wrapper" id="iqwpb-booking-summary">
                                <div class="table-responsive">
                                    <table  class="table mb-0 tax-module-table table-borderless" data-toggle="data-table" aria-describedby="datatable_info">
                                        <tbody>
                                            <?php foreach ($shortcode_instance->booking_totals_and_html['tax_values'] as $tax_data_key => $tax_data_value) : ;
                                                $display_key = ucwords(str_replace('_', ' ', $tax_data_key)); ?>
                                                <tr class="">
                                                    <th scope="col" class="sorting" tabindex="0">
                                                        <span class="nobr small"><?php echo esc_html($display_key); ?></span>
                                                    </th>
                                                    <td scope="col" class="sorting" tabindex="0" id="<?php echo esc_attr($tax_data_key); ?>">
                                                        <span class="nobr small fw-bolder">
                                                            <?php
                                                            if ($tax_data_value === esc_html__("Free", 'wpbookit')) {
                                                                echo esc_html($tax_data_value);
                                                            } else {
                                                                echo esc_html(wpb_get_prefix_postfix_price($tax_data_value));
                                                            }
                                                            ?>
                                                        </span>
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
            </div>
        <?php endif; ?>
    </div>
    <?php
    if (($shortcode_instance->booking_type->get_meta('price')) > 0 && count($shortcode_instance->payment) > 0) : ?>
        <div class="row">
            <div class="col-lg-12">
                <label class="form-label"><?php esc_html_e("Payments", 'wpbookit') ?></label>
                <?php foreach ($shortcode_instance->payment as $key => $value) : ?>
                    <div class="form-check">
                        <input class="form-check-input" required type="radio" name="wpb_payments_gateways" value="<?php echo esc_attr($key) ?>" id="<?php echo esc_html($key) ?>">
                        <label class="form-check-label" for="<?php echo esc_html($key) ?>"> <?php echo esc_html(wpb_get_payment_gateway_name($key)) ?> </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>