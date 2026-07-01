<?php
/**
 * Admin View: Settings
 */

if ( ! defined( 'ABSPATH' ) ) :
	exit;
endif;

$tab_exists        = isset( $tabs[ $current_tab ] ) || has_action( 'wpb_sections_' . $current_tab ) || has_action( 'wpb_settings_' . $current_tab ) || has_action( 'wpb_settings_tabs_' . $current_tab );
$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : '';

if(!empty(wpb_get_theme_settings('site_logo'))){
    $site_logo_url = wp_get_attachment_url (wpb_get_theme_settings('site_logo'));
}else{
    $site_logo_url = IQWPB_PLUGIN_URL.'/core/admin/assets/images/logo.png';
}
$dashboard_name =  !empty(wpb_get_theme_settings('dashboard_name')) ? wpb_get_theme_settings('dashboard_name') : "WPBookit";

$admin_url = admin_url(); ?>

<aside class="sidebar sidebar-base " id="first-tour" data-toggle="main-sidebar" data-sidebar="responsive">
    <?php do_action( 'wpb_before_settings_' . $current_tab ); ?>
    <div class="sidebar-header d-flex align-items-center justify-content-center">
        <a href="#" class="navbar-brand">
            <!--Logo start-->
            <div class="logo-main">
                <div class="logo-normal"> <img Width="48.19px" height="46px" src="<?php echo esc_attr( $site_logo_url ); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage  ?>" alt="checked"></div>
                <div class="logo-mini"></div>
            </div>
            <!--logo End-->
            <h4 class="logo-title" data-setting="app_name"><?php echo  esc_html($dashboard_name)?></h4>
        </a>
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg class="icon-20" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
        </div>
    </div>
    <div class="sidebar-body pt-0 data-scrollbar" data-scrollbar="true" tabindex="-1"
        style="overflow: auto; outline: none;">
        <div class="scroll-content">
            <div class="sidebar-list">
                <!-- Sidebar Menu Start -->
                <ul class="navbar-nav iq-main-menu" id="sidebar-menu">
                  
                        <?php
                        do_action( 'wpb_before_settings_tabs' );
                        foreach ( $tabs as $tab_cat_slug => $tab_cat ) :
                            echo wp_kses_post(sprintf(
                                '  <li class="nav-item static-item">
                                    <a href="#" class="nav-link nav-link static-item disabled text-start ">
                                        <span class="default-icon">%1$s</span>
                                    </a>
                                 </li>',
                                 $tab_cat_slug ,
                            ));
                            foreach ($tab_cat as $slug => $tab) :
                                echo (sprintf( 
                                    '  <li class="nav-item"><a href="%2$s" class="nav-link%3$s">
                                        <i class="icon" data-bs-toggle="tooltip" title="%4$s" aria-label="%4$s" data-bs-original-title="%4$s">
                                            %1$s
                                        </i>
                                        <span class="item-name">%4$s</span>
                                    </a> </li>',
                                     $tab['icon'] ,//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                                    esc_html(admin_url('admin.php?page=wpbookit-dashboard&tab=' . esc_attr($slug))),
                                    ($current_tab === $slug ? ' active' : '' ),
                                    esc_html( $tab['label'] )
                                ));
                            endforeach;
                        endforeach;
                        do_action( 'wpb_after_settings_tabs' ); ?>
                   
                </ul>

                <!-- Sidebar Menu End -->
            </div>
        </div>

		<?php do_action( 'wpb_settings_tabs_' . $current_tab ); ?>
    </div>
    <div class="sidebar-footer"></div>
</aside>