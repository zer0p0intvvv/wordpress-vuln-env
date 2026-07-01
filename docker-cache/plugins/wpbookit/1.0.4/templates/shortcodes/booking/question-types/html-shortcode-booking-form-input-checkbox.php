<?php defined('ABSPATH') || exit; ?>

<div class="col-12">
    <div class="mb-3">
        <div class="row">
            <div class="col-12">
                <h6 class="form-label"><?php echo esc_html(wpb_unicode_to_utf8($question['question'])); ?></h6>
            </div>
            <div class="col-12">
                <div class="row">
                    <?php foreach ($question['options']  as $key => $option) : ?>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="<?php echo esc_html($guest_index_class);?>[<?php echo esc_attr($question['questionId']) ?>]" id="<?php echo esc_html($guest_index_class);?>-<?php echo esc_attr($key.strtolower(str_replace(' ','-',$question['question']))) ?>" value="<?php echo esc_html($option) ?>">
                                <label class="form-check-label" for="<?php echo esc_html($guest_index_class);?>-<?php echo esc_attr($key.strtolower(str_replace(' ','-',$question['question']))) ?>">
                                    <?php echo esc_attr($option) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>