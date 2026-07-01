// Shoutbox 1.00 - admin
function Shoutbox_clean_private(){
    jQuery.post(Shoutbox_admin.ajaxurl, {
        action: 'shoutbox-ajax-clean-private'},
        function(data) {
            alert(Shoutbox_admin.i18n.clean_private_done)
        });
}

jQuery(window).load(function(){
    jQuery("a#Shoutbox_clean_private").on('click', function(e) {
        e.preventDefault();

        if (confirm(Shoutbox_admin.i18n.clean_private_confirm)){
            Shoutbox_clean_private();
        }
    });

    jQuery("a.Shoutbox_show_hide").on('click', function(e) {
        e.preventDefault();
        if(jQuery(this).text() == 'Show'){
            jQuery(this).text('Hide').siblings('textarea').slideDown('slow');
        }else {
            jQuery(this).text('Show').siblings('textarea').slideUp('slow');
        }
    });
});