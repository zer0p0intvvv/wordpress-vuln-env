<?php 

defined( 'ABSPATH' ) || exit;
?>
<?php  $format =  get_option( 'date_format' );
    $translated_date = date_i18n( $format, strtotime( $args['date_formated'] ) );
?>
<li class="item">
    <div class="btn-primary-subtle btn rounded avaliable-slot-btn">
        <div class="date-time-slot" data-time_slot="<?php echo esc_html($args['time_display'])?>" 
        <?php // translators: Date format placeholder:0, Time slot placeholder:1 ?>
        data-date_formated="<?php echo esc_attr(sprintf(__("%1\$s At %2\$s",'wpbookit'),$translated_date,$args['time_slot'])) ?>"> 
            <?php echo esc_html($args['time_slot'])?>
        </div>
        <?php if($args['remain_lable']): ?>
        <div class="remain_table" data-remain-slot-label="<?php echo esc_html($args['remain_slot_label'])?>" data-remain-slot="<?php echo esc_html($args['remain_slot'])?>">
            <?php echo esc_html($args['remain_lable'])?>
        </div>
        <?php endif;?>
    </div>
</li>