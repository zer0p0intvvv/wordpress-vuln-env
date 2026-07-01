<?php

defined('ABSPATH') || exit;

?>

<div class="col-12">
    <div class="row ">
        <?php if (!empty($shortcode_instance->booking_type->get_meta('questions') ?? [])) : ?>
            <h6 class="mb-3"> <?php esc_html_e("Questions:", 'wpbookit') ?> </h6>
            <?php
            $questions = array_map(function ($item) {
                $item['question'] = wpb_unicode_to_utf8($item['question']);
                if (!empty($item['options']) && is_array($item['options'])) {
                    $item['options'] = array_map(function ($option) {
                        return wpb_unicode_to_utf8($option);
                    }, $item['options']);
                }
                return $item;
            }, json_decode($shortcode_instance->booking_type->get_meta('questions') ?? [], true));

            foreach ($questions as $index => $question) {
                do_action('wpb_booking_shortcode_form_question_type', ['question' => $question]);
            }

            ?>
        <?php endif; ?>
    </div>
</div>
