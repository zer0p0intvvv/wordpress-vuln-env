<table class="table custome-table" id='wpb-booking-tbl'>
    <thead>
        <tr>
            <?php if ( ! empty( $columns ) && is_array( $columns ) ):
                foreach ( $columns as $column_id => $column_name ): ?>
                    <th scope="col" class="<?php echo esc_attr( $column_id ); ?>">
                        <span class="nobr"><?php echo esc_html( $column_name ); ?></span>
                    </th>
                <?php endforeach;
            endif; ?>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>