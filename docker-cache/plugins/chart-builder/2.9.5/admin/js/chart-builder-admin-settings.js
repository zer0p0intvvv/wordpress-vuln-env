(function( $ ) {
	'use strict';
	$(document).ready(function () {

		$(document).find('#ays-category').select2({
			placeholder: 'Select category'
		});

		$(document).find('#ays-status').select2({
			placeholder: 'Select status'
		});

		//
		$(document).find('.nav-tab-wrapper a.nav-tab').on('click', function (e) {
		    if(! $(this).hasClass('no-js')){
		        var elemenetID = $(this).attr('href');
		        var active_tab = $(this).attr('data-tab');
		        $(document).find('.nav-tab-wrapper a.nav-tab').each(function () {
		            if ($(this).hasClass('nav-tab-active')) {
		                $(this).removeClass('nav-tab-active');
		            }
		        });
		        $(this).addClass('nav-tab-active');
		        $(document).find('.ays-tab-content').each(function () {
		            $(this).css('display', 'none');
		        });
		        $(document).find("[name='ays_tab']").val(active_tab);
		        $('.ays-tab-content' + elemenetID).css('display', 'block');
		        e.preventDefault();
		    }
		});


		$(document).find('#ays_select_forms').select2({
			allowClear: true,
			placeholder: false
		});

		$(document).find('#ays_user_roles').select2({
			allowClear: true,
			placeholder: aysChartBuilderAdminSettings.selectUserRoles
		});

		$(document).find('#ays_user_roles_to_change_plugin').select2({
			allowClear: true,
			placeholder: aysChartBuilderAdminSettings.selectUserRoles
		});

		$(document).find('#ays_form_default_type').select2({
			allowClear: true,
			placeholder: aysChartBuilderAdminSettings.selectQuestionDefaultType
		});

		// ===============================================================
		// =====================   Integrations  =========================
		// ===============================================================

		$(document).find("#googleInstructionsPopOver").popover({
			content: $(document).find("#googleInstructions").html(),
			html: true,
		});

		$(document).find("#googleInstructionsPopOver").on("click" , function(){
			$(document).find(".popover").addClass("ays-google-sheet-popover");
		});



	});

})( jQuery );
