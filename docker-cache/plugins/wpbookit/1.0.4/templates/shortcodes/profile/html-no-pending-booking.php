<?php 

defined( 'ABSPATH' ) || exit;
?>

<div class="content-empty">
    <div class="mb-5">
        <img src="<?php echo esc_attr(IQWPB_PLUGIN_URL . "/core/admin/assets/images/unavailable-booking.png"); //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>"
        alt="checked" class="img-fluid">
    </div>
    <h5 class="mb-1">
        <?php esc_html_e("Oops! There is no Pending Booking.", "wpbookit"); ?>
    </h5>
</div>
</div>
</div>
</div>
</div>