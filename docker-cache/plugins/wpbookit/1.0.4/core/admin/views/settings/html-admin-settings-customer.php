
<div class="content-inner container-fluid pb-0" id="customer_inner">

	<div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-3" >
        <div class="d-flex flex-column">
            <h4 class="mb-0"><?php esc_html('Customers','wpbookit'); ?></h4>
        </div>
        <div class="d-flex justify-content-between align-items-center rounded flex-wrap gap-3">
				<div class="input-group flex-nowrap w-auto">
                    <span class="input-group-text bg-white" id="addon-wrapping">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <circle cx="11.7669" cy="11.7669" r="8.98856" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M18.0186 18.4854L21.5426 22.0002" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input type="text" class="form-control dt-search bg-white" placeholder="<?php esc_html_e("Search...", 'wpbookit') ?>" aria-label="Search" aria-describedby="addon-wrapping">
                </div>
        
		  <button type="button" class="btn btn-secondary lh-lg"  id="add-customer-click" data-bs-toggle="offcanvas" data-bs-target="#new-customer" role="button" aria-controls="new-customer">
		  <img src="<?php echo esc_url(IQWPB_PLUGIN_URL . '/core/admin/assets/images/plus-square.svg'); // phpcs:ignore  PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>"
                alt="icon" />
            <?php echo esc_attr_x('New', 'Customer', 'wpbookit'); ?>
          </button>
        </div>
    </div>

	<div class="row">
		<div class="col-lg-12">
			<div id="datatable_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
				<div class="table-wrapper rounded mb-4" id="table-main">
					<?php require_once "customers/customer-table.php"; ?>
				</div>
			</div>

		</div>
	</div>
</div>
<?php require_once IQWPB_PLUGIN_PATH. "core/admin/views/settings/customers/customer-import.php"; ?>
<?php require_once('customers/customer-form.php'); ?>