<h3><?php esc_html_e('Custom Fields', 'wpbookit'); ?></h3>
<table class="form-table">
    <tr>
        <th><label for="gender"><?php esc_html_e('Gender', 'wpbookit'); ?></label></th>
        <td>
            <select name="gender" id="gender">
                <option value="male" <?php selected(get_user_meta($user->ID, 'gender', true), 'male'); ?>><?php esc_html_e('Male', 'wpbookit'); ?></option>
                <option value="female" <?php selected(get_user_meta($user->ID, 'gender', true), 'female'); ?>><?php esc_html_e('Female', 'wpbookit'); ?></option>
                <option value="other" <?php selected(get_user_meta($user->ID, 'gender', true), 'other'); ?>><?php esc_html_e('Other', 'wpbookit'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th><label for="phone"><?php esc_html_e('Phone Number', 'wpbookit'); ?></label></th>
        <td>
            <input type="text" name="phone" id="phone" value="<?php echo esc_attr(get_user_meta($user->ID, 'phone', true)); ?>" class="regular-text">
            <p class="description"><?php esc_html_e('Enter your phone number here.', 'wpbookit'); ?>
            <p>
        </td>
    </tr>
    <tr>
        <th><label for="custom_note"><?php esc_html_e('User Note', 'wpbookit'); ?></label></th>
        <td>
            <textarea name="custom_note" id="custom_note" rows="5" cols="30"><?php echo esc_html(get_user_meta($user->ID, 'custom_note', true)); ?></textarea>
            <p class="description"><?php esc_html_e('Enter your user note here.', 'wpbookit'); ?></p>
        </td>
    </tr>
    <tr>
        <th><label for="date_of_birth"><?php esc_html_e('Date of Birth', 'wpbookit'); ?></label></th>
        <td>
            <input type="text" name="date_of_birth" id="date_of_birth" value="<?php echo esc_attr(get_user_meta($user->ID, 'date_of_birth', true)); ?>" class="regular-text datepicker">
            <p class="description"><?php esc_html_e('Enter your date of birth here.', 'wpbookit'); ?></p>
        </td>
    </tr>
</table>