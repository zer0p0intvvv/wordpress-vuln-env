<?php

# Silence is golden.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="circulr-details-top">
    <div class="single-top-left">
        <<?php 
esc_attr_e( $jobwp_single_title_tag );
?> class="jobwp-job-title"><?php 
esc_html_e( $job_title );
?></<?php 
esc_attr_e( $jobwp_single_title_tag );
?>>
        <?php 
?>
    </div>
    <?php 
?>
</div>
<?php 
if ( null !== $resumeUploadMsg ) {
    ?>
    <div class="jobwp-application-message"><?php 
    esc_html_e( $resumeUploadMsg );
    ?></div>
    <?php 
}