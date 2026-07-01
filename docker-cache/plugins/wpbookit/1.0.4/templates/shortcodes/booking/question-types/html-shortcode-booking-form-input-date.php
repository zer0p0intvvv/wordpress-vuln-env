<?php defined('ABSPATH') || exit;


?>

<div class="col-12">
    <div class="mb-3">
        <label for="<?php echo esc_html($guest_index_class);?>-<?php echo esc_attr(str_replace(' ', '-', strtolower($question['question']))) ?>" class="form-label">
        <?php echo esc_html(wpb_unicode_to_utf8($question['question'])) ?>
    </label>
        <input type="date" class="form-control" name="<?php echo esc_html($guest_index_class);?>[<?php echo esc_attr($question['questionId']) ?>]" id="<?php echo esc_html($guest_index_class);?>-<?php echo esc_attr(str_replace(' ', '-', strtolower($question['question']))) ?>" />
    </div>
</div>