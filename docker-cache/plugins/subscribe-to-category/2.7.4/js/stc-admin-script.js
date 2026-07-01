/* global action, ajax_object */

jQuery(function($){

    $('div.stc-email-notification-checkboxes input[type=checkbox]').click( function() {
        $currentCheckBox = $(this);
        $state = $currentCheckBox.prop('checked');
        $('div.stc-email-notification-checkboxes input[type=checkbox]').each(function () {
            $(this).prop('checked', false);
        });
        if ($state) $currentCheckBox.prop("checked", true);
    });

    $(document).ready(function() {

        $('#stc-resend').change( function() {

          if( $(this).is(':checked') ) {
            $('#stc-resend-info').show();
          } else {
            $('#stc-resend-info').hide();
          }

        });


        // make sure that we prevent double events by unbinding and then binding the click event to the button with ID stc-force-run
		$('#stc-force-run').off('çlick').on('click', function(){
			var trigger_btn = $(this);
            
            // we are troubled with double hits
            if (trigger_btn.attr('disabled') === 'disabled') {
                alert ("Problem calling: force_run\nCode: " + this.status + "\nException: " + this.statusText);
                return false;
            }
			trigger_btn.attr('disabled', 'disabled'); // disable button during ajax call
			$('#message').remove(); // remove any previous message

            var data = {
              action: 'force_run',
              nonce: ajax_object.ajax_nonce
            };

            $.post( ajax_object.ajaxurl, data, function(response) {
                setTimeout(function(){
                    $('#stc-posts-in-que').text('0'); // clear posts in que
                    $('.wrap h2').after('<div id="message"></div>'); // add message element
                    $('#message').addClass('updated').html('<p><strong>' + response + '</strong></p>'); // get text from ajax call
                    trigger_btn.attr('disabled', false); // enable button
                }, 1500);
            }).error(function(){
                alert ("Problem calling: " + data.action + "\nCode: " + this.status + "\nException: " + this.statusText);
            });
            return false;
        });
		

	});

} )

