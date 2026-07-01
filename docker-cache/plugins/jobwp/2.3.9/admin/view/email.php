<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
//print_r( $jobwpEmailSettings );
foreach ( $jobwpEmailSettings as $option_name => $option_value ) {
    if ( isset( $jobwpEmailSettings[$option_name] ) ) {
        ${"" . $option_name} = $option_value;
    }
}
?>
<div id="wph-wrap-all" class="wrap jobwp-single-settings-page">

<div class="settings-banner">
    <h2><i class="fa fa-cogs" aria-hidden="true"></i>&nbsp;<?php 
_e( 'Email Settings', 'jobwp' );
?></h2>
</div>

<?php 
if ( $jobwpEmailMessage ) {
    $this->jobwp_display_notification( 'success', 'Your information updated successfully.' );
    echo '<br>';
}
?>
<div class="jobwp-wrap">

    <div class="jobwp_personal_wrap jobwp_personal_help" style="width: 76%; float: left;">
        
        <div class="tab-content">

            <?php 
?>
                <span><?php 
echo '<a href="' . job_fs()->get_upgrade_url() . '">' . __( 'Please Upgrade Now', 'jobwp' ) . '</a>';
?></span>
                <?php 
?>
        </div>
        
    </div>

    <?php 
include_once 'partial/admin-sidebar.php';
?>
</div>

</div>