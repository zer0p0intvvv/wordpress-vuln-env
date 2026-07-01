
<div class="position-relative ">
<!--Nav Start-->
    <nav class="nav navbar navbar-expand-xl navbar-light iq-navbar header-hover-menu left-border">
        <div class="container-fluid navbar-inner">
            <a href="<?php echo esc_url( admin_url()); ?>" class="wpb-wordpress-logo" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php esc_html_e( 'Go To WordPress', 'wpbookit' ); ?> ">
                <?php echo wpb_render_filtered_svg('wordpress-logo'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
            <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                <i class="icon d-flex">
                    <img src="<?php echo esc_attr( IQWPB_PLUGIN_URL.'core/admin/assets/images/sidebar-toggle.svg' );  // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked">
                </i>
            </div>
            <div class="d-flex align-items-center">
                <button id="navbar-toggle" class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                        <span class="navbar-toggler-bar bar1 mt-1"></span>
                        <span class="navbar-toggler-bar bar2"></span>
                        <span class="navbar-toggler-bar bar3"></span>
                    </span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto align-items-center navbar-list">
                    <?php do_action('wpb_add_header_right_before_list'); ?>
                    <li class="nav-item dropdown" id="itemdropdown1">
                        <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div >
                                <img class="avatar-40 rounded-pill" src="<?php echo esc_url( wpbookit_avatar( get_current_user_id(),'',true ) ); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="" loading="lazy" />
                            </div>
                            <span class="profile-alert"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <div class="dropdown-header bg-body py-2 rounded">
                                <div class="d-flex gap-2">
                                    <img class="avatar avatar-40 img-fluid rounded-pill" src="<?php echo esc_url( wpbookit_avatar( get_current_user_id(),'',true ) ); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>">
                                    <div class="d-flex flex-column align-items-start">
                                        <h6 class="m-0 text-primary"><?php echo esc_html( $current_user->user_login ); ?></h6>
                                        <small class="text-muted"><?php echo esc_html( $current_user->user_email ); ?></small>
                                    </div>
                                </div>
                            </div>
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center gap-1" href="<?php echo esc_attr(admin_url('admin.php?page=wpbookit-dashboard&tab=' . esc_attr('settings'))); ?>">
                                <?php esc_html_e( 'Settings ', 'wpbookit' ); ?> 
                                    <img src="<?php echo esc_attr( IQWPB_PLUGIN_URL.'/core/admin/assets/images/menu-icons/settings-icon.svg' ); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="checked">
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center gap-1" href="<?php echo esc_attr(admin_url()); ?>">
                                <?php esc_html_e( 'Go To WordPress', 'wpbookit' ); ?> 
                                    <img src="<?php echo esc_attr( IQWPB_PLUGIN_URL.'/core/admin/assets/images/menu-icons/wordpress-return.svg' );// phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked">
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center gap-1" href="<?php echo esc_url( wp_logout_url() ); ?>">
                                    <?php esc_html_e( 'Logout', 'wpbookit' ); ?> 
                                    <img src="<?php echo esc_attr( IQWPB_PLUGIN_URL.'core/admin/assets/images/menu-icons/logout.svg' ); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="checked">
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>