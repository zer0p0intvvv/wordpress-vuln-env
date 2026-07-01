// Shoutbox 1.00 - power
var Shoutbox = jQuery.extend(Shoutbox || {}, {
    toggle: false,
    delete_messages: function(chat_id, to_delete_ids){
        var room_name = Shoutbox.data[chat_id]['room_name'];
        jQuery.post(Shoutbox.ajaxurl, {
            action: 'shoutbox-ajax-delete',
            to_delete_ids: to_delete_ids,
            to_delete_room_name: room_name},
            function(data) {
                if(data.rows_affected == to_delete_ids.length){
                    for(var current_chat_id in Shoutbox.data){
                        if(Shoutbox.data[current_chat_id]['room_name'] == room_name){
                            for (var i=0;typeof(to_delete_ids[i]) != 'undefined';i++){
                                jQuery('div[data-shoutbox-id='+current_chat_id+'] input[type=checkbox][value="'+to_delete_ids[i]+'"]').parents('.shoutbox-history-message-alias-container').remove();
                            }
                        }
                    }
                } else{
                    location.reload(true);
                }
            });
    },
    clean_messages: function(chat_id){
        var room_name = Shoutbox.data[chat_id]['room_name'];
        var history_container = jQuery('div[data-shoutbox-id='+chat_id+'] div.shoutbox-history-container');
        var total_count = jQuery(history_container).children().size();

        if(total_count > Shoutbox.clean_target){
            jQuery.post(Shoutbox.ajaxurl, {
                action: 'shoutbox-ajax-clean',
                to_clean_room_name: room_name},
                function(data) {
                    var to_delete_count = total_count-Shoutbox.clean_target;
                    if(data.rows_affected == to_delete_count){
                        for(var current_chat_id in Shoutbox.data){
                            if(Shoutbox.data[current_chat_id]['room_name'] == room_name){
                                var current_to_delete_count = to_delete_count;
                                var current_history_container = jQuery('div[data-shoutbox-id='+current_chat_id+'] div.shoutbox-history-container');
                                for(var i=current_to_delete_count; i>0; i--){
                                    jQuery(current_history_container).children(":first").remove();
                                }
                            }
                        }
                    }else{
                        location.reload(true);
                    }
                });
        }
    },
    ban_users: function(chat_id, to_ban_ips){
        var this_chat = jQuery.find('div[class=shoutbox-container][data-shoutbox-id="'+chat_id+'"]');
        jQuery.post(Shoutbox.ajaxurl, {
            action: 'shoutbox-ajax-ban',
            to_ban_ips: to_ban_ips},
            function(data) {;
                jQuery(this_chat).find('.shoutbox-users-container input[type=checkbox]').each(function(){
                    jQuery(this).attr('checked', false);
                });
            });
    },
    transcript: function(chat_id){
        var room_name = Shoutbox.data[chat_id]['room_name'];
        jQuery.post(Shoutbox.ajaxurl, {
            action: 'shoutbox-ajax-transcript',
            room_name: room_name},
            function(data) {
                window.open(Shoutbox.stripslashes(data.transcript_url));
            });
    }
});

jQuery("div.shoutbox-delete-link a").on('click', function(e) {
    e.preventDefault();

    var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
    var this_element = jQuery(this);

    var to_delete_ids = [];
    jQuery(this).parents('.shoutbox-container').find('.shoutbox-history-container input[type=checkbox]:checked').each(function(){
        to_delete_ids.push(jQuery(this).val());
    });

    jQuery(this).fadeTo(100, 0, function() {
        if(to_delete_ids == ""){
            alert(Shoutbox.i18n.delete_what_s);
        } else{
            if (confirm(Shoutbox.i18n.delete_confirm_s)){
                Shoutbox.delete_messages(chat_id, to_delete_ids);
            }
        }
        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery("div.shoutbox-clean-link a").on('click', function(e) {
    e.preventDefault();

    var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
    var this_element = jQuery(this);

    jQuery(this).fadeTo(100, 0, function() {
        if (confirm(Shoutbox.i18n.clean_confirm_s.replace('%s',Shoutbox.clean_target))){
            Shoutbox.clean_messages(chat_id);
        }
        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery("div.shoutbox-ban-link a").on('click', function(e) {
    e.preventDefault();

    var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
    var this_element = jQuery(this);

    var to_ban_ips = [];
    jQuery(this).parents('.shoutbox-container').find('.shoutbox-users-container input[type=checkbox]:checked').each(function(){
        to_ban_ips.push(jQuery(this).val());
    });

    jQuery(this).fadeTo(100, 0, function() {
        if(to_ban_ips == ""){
            alert(Shoutbox.i18n.ban_who_s);
        } else{
            if (confirm(Shoutbox.i18n.ban_confirm_s)){
                Shoutbox.ban_users(chat_id, to_ban_ips);
            }
        }
        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery(document).on('click', "div.shoutbox-transcript-link a", function(e) {
    e.preventDefault();

    var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
    var this_element = jQuery(this);

    jQuery(this).fadeTo(100, 0, function() {
        Shoutbox.transcript(chat_id);
        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery("div.shoutbox-select-all-link a").on('click', function(e){
    e.preventDefault();
    var this_element = jQuery(this);

    jQuery(this).fadeTo(100, 0, function() {
        jQuery(this_element).parents('.shoutbox-container').find('.shoutbox-history-container input[type=checkbox]').prop('checked',!Shoutbox.toggle);
        Shoutbox.toggle = !Shoutbox.toggle;
        jQuery(this_element).fadeTo(100, 1);
    });
});