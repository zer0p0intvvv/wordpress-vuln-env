<?php defined('ABSPATH') || exit; ?>

<div class="col-12">
    <div class="mb-3">
        <div class="row">
            <div class="col-12">
                <h6 class="form-label"><?php echo esc_html(wpb_unicode_to_utf8($question['question'])); ?></h6>
            </div>
            <div class="col-12">
            <select class="form-control" name="<?php echo esc_html($guest_index_class);?>[<?php echo esc_attr($question['questionId']) ?>]">
                <?php foreach ($question['options']  as $key => $option) : ?>
                    <option value="<?php echo esc_html($option) ?>"> <?php echo esc_attr($option) ?></option>
                <?php endforeach; ?>
            </select>
            </div>
        </div>
    </div>
</div>