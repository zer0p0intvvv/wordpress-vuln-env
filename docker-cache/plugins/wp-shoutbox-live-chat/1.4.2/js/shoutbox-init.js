// Shoutbox 1.00 - init
var Shoutbox = jQuery.extend(Shoutbox || {}, {
    data: []
});

jQuery.post(
    Shoutbox.ajaxurl,
    {action: 'shoutbox-ajax-init'},
    function(data) {
        Shoutbox = jQuery.extend(true, Shoutbox || {}, data.js_vars);

        jQuery("div.shoutbox-container").each(function(){
            var height = jQuery(this).attr('data-shoutbox-height');
            var userlist = jQuery(this).attr('data-shoutbox-userlist');
            var userlist_position = jQuery(this).attr('data-shoutbox-userlist-position');
            var smilies = jQuery(this).attr('data-shoutbox-smilies');
            var send_button = jQuery(this).attr('data-shoutbox-send-button');
            var loggedin_visible = jQuery(this).attr('data-shoutbox-loggedin-visible');
            var guests_visible = jQuery(this).attr('data-shoutbox-guests-visible');
            var counter = jQuery(this).attr('data-shoutbox-counter');

            var string = '';

            if(
            Shoutbox.user_status == 0
            ||
            (Shoutbox.user_status < 2 && loggedin_visible == 1)
            ||
            (Shoutbox.user_status == 2 && guests_visible == 1)
            ){
                string += '<div class="shoutbox-top">';
                    if(userlist == 1){
                        if(userlist_position == 'right')
                            string += '<div class="shoutbox-users-container shoutbox-users-container-right" style="height:'+height+'px;">';
                        else if (userlist_position == 'top')
                            string += '<div class="shoutbox-users-container shoutbox-users-container-top">';
                        else if (userlist_position == 'left')
                            string += '<div class="shoutbox-users-container shoutbox-users-container-left" style="height:'+height+'px;">';

                        string += '</div>';
                    }

                    string += '<div class="shoutbox-history-container" style="height:'+height+'px;"></div></div>';

                string += '  <div class="shoutbox-links">';
                    if (Shoutbox.user_status == 0){
                        string += '<div class="shoutbox-left-link shoutbox-ban-link"><a title="'+Shoutbox.i18n.add_blocklist_s+'" href="">'+Shoutbox.i18n.ban_s+'</a></div>';
                        string += '<div class="shoutbox-left-link shoutbox-transcript-link"><a title="'+Shoutbox.i18n.fetch_transcript_s+'" href="">'+Shoutbox.i18n.transcript_s+'</a></div>';
                    }

                    if (Shoutbox.user_status == 0)
                        string += '<div class="shoutbox-right-link shoutbox-select-all-link"><a title="'+Shoutbox.i18n.all_toggle_s+'" href="">'+Shoutbox.i18n.toggle_s+'</a></div><div class="shoutbox-right-link shoutbox-delete-link"><a title="'+Shoutbox.i18n.delete_selected_s+'" href="">'+Shoutbox.i18n.delete_s+'</a></div><div class="shoutbox-right-link shoutbox-clean-link"><a title="'+Shoutbox.i18n.clean_all_except_s.replace('%s', Shoutbox.clean_target)+'" href="">'+Shoutbox.i18n.clean_s+'</a></div>';

                    string += '<div class="shoutbox-right-link shoutbox-scroll-link"><a style="text-decoration: none;" title="'+Shoutbox.i18n.scroll_toggle_s+'" href="">'+Shoutbox.i18n.scroll_s+'</a></div><div style="display: none;" class="shoutbox-right-link shoutbox-sound-link"><a title="'+Shoutbox.i18n.sound_toggle_s+'" href="">'+Shoutbox.i18n.sound_s+'</a></div></div>';

                if(Shoutbox.no_participation == 1){
                    if(Shoutbox.ip_blocked == 1){
                        string += '<div class="shoutbox-bootom-notice">'+Shoutbox.i18n.ip_banned_s+'</div>';
                    } else if(Shoutbox.must_login == 1){
                        string += '<div class="shoutbox-bootom-notice">'+Shoutbox.i18n.must_login_s+'</div>';
                    }
                }else {
                    //string += '<div class="shoutbox-alias-container"><input class="shoutbox-alias" type="text" autocomplete="off" maxlength="20" value="'+Shoutbox.user_name+'"';

                        //if(Shoutbox.user_status != 0 && Shoutbox.allow_change_username == 0)
                        //    string += 'readonly="readonly"';

                        //string += '/>';

                        if(Shoutbox.user_status != 0 && counter == 1)
                            string += '<span class="shoutbox-counter">'+Shoutbox.message_maximum_number_chars+'</span>';
                        string += '<span class="shoutbox-username-status"></span></div>';

                    if(Shoutbox.adsense_content != '')
                        string += '<div class="shoutbox-adsense">'+Shoutbox.adsense_content+'</div>';

                    string += '<textarea class="shoutbox-message" placeholder="Ваше сообщение"></textarea>';

                    if(smilies == 1){
                        string += '<div class="shoutbox-smilies-container">';
                        for(var smile in Shoutbox['smilies'])
                            string += '<div class="shoutbox-smile-container shoutbox-smile" title="'+smile+'"><img src="/wp-content/plugins/shoutbox/img/'+Shoutbox['smilies'][smile]+'" alt="'+Shoutbox['smilies'][smile]+'"/></div>'
                        string += '</div>';
                    }

                    if(send_button == 1)
                        string += '<input class="shoutbox-send-button" type="button" value="'+Shoutbox.i18n.send_s+'">';
                }
            }

            jQuery(this).append(string);

            var loading = jQuery('div.shoutbox-loading');
            if(loading.is(':visible'))
                loading.hide();

            var chat_data = {};
            chat_data['room_name'] = jQuery(this).attr('data-shoutbox-room-name');
            chat_data['userlist_position'] = userlist_position;
            chat_data['avatars'] = jQuery(this).attr('data-shoutbox-avatars');
            chat_data['scroll_enable'] = 1;

            Shoutbox.data[jQuery(this).attr('data-shoutbox-id')] = chat_data;
        });

        Shoutbox.get_script(Shoutbox.url+'js/jquery.json'+Shoutbox.script_suffix+'.js?'+Shoutbox.version, function(){
            Shoutbox.get_script(Shoutbox.url+'js/shoutbox-core'+Shoutbox.script_suffix+'.js?'+Shoutbox.version);
        });

        if(Shoutbox.user_status == 0)
            Shoutbox.get_script(Shoutbox.url+'js/shoutbox-power'+Shoutbox.script_suffix+'.js?'+Shoutbox.version);
});