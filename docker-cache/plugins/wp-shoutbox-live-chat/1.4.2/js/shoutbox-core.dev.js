// Shoutbox 1.00 - core
jQuery.fn.Shoutbox_insert_at_caret = function(myValue) {

    return this.each(function() {

        if (document.selection) {

        this.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
        this.focus();

        } else if (this.selectionStart || this.selectionStart == '0') {
            var startPos = this.selectionStart;
            var endPos = this.selectionEnd;
            var scrollTop = this.scrollTop;
            this.value = this.value.substring(0, startPos)+ myValue+ this.value.substring(endPos,this.value.length);
            this.focus();
            this.selectionStart = startPos + myValue.length;
            this.selectionEnd = startPos + myValue.length;
            this.scrollTop = scrollTop;
        } else {
            this.value += myValue;
            this.focus();
        }
    });
};

var Shoutbox = jQuery.extend(Shoutbox || {}, {
    last_timestamp:  0,
    rooms : [],
    private_queue: {},
    private_current: {},
    private_count: 0,
    audio_support: 0,
    play_audio: 0,
    audio_element: document.createElement('audio'),
    update_users_limit: Math.floor( Shoutbox.inactivity_timeout / Shoutbox.timeout_refresh_users),
    update_users_counter: 0,
    private_queue_name: 'Shoutbox_private_queue_'+Shoutbox.user_id,
    random_string: function(length) {
        var iteration = 0;
        var string = "";
        var randomNumber;

        while(iteration < length){
                randomNumber = (Math.floor((Math.random() * 100)) % 94) + 33;
                if ((randomNumber >=33) && (randomNumber <=47)) continue;
                if ((randomNumber >=58) && (randomNumber <=64)) continue;
                if ((randomNumber >=91) && (randomNumber <=96)) continue;
                if ((randomNumber >=123) && (randomNumber <=126)) continue;
                iteration++;
                string += String.fromCharCode(randomNumber);
        }
        return 'Shoutbox_'+string;
    },
    is_user_inactive: function(){
        return (Shoutbox.update_users_counter == Shoutbox.update_users_limit && Shoutbox.user_status != 0)? true: false;
    },
    is_private_eq: function(private1, private2){
        return ((private1['private_from'] == private2['private_from']) && (private1['private_to'] == private2['private_to'])
                ||
                (private1['private_to'] == private2['private_from']) && (private1['private_from'] == private2['private_to']))? true: false;
    },
    spawn_private_chat: function(chat_id, username_him, state){
        var chat_data = {};
        chat_data['room_name'] = chat_id;
        chat_data['userlist_position'] = 'top';
        chat_data['scroll_enable'] = 1;
        chat_data['avatars'] = 1;
        chat_data['him'] = username_him;

        if(state == 'o')
            chat_data['state'] = 'o';
        else
            chat_data['state'] = 'm';

        Shoutbox.data[chat_id] = chat_data;

        var string = '<div class="shoutbox-container shoutbox-container-private" data-shoutbox-id="'+chat_id+'"><div class="shoutbox-container-private-titlebar"><div class="shoutbox-container-private-title">'+Shoutbox.i18n.private_title_s+'</div><div class="shoutbox-container-private-close"><a href="" title="'+Shoutbox.i18n.private_close_s+'">x</a></div><div class="shoutbox-container-private-minimize-restore"><a href="" title="';

        if(state == 'o')
            string += Shoutbox.i18n.private_minimize_s;
        else
            string += Shoutbox.i18n.private_restore_s;

        string += '">'+((state == 'o')?'-':'o')+'</a></div></div><div class="shoutbox-users-container shoutbox-users-container-top"></div><div class="shoutbox-history-container" style="height: 300px;"></div>';

        if (Shoutbox.user_status == 0)
            string += '<div class="shoutbox-links"><div class="shoutbox-left-link shoutbox-transcript-link"><a title="'+Shoutbox.i18n.fetch_transcript_s+'" href="">'+Shoutbox.i18n.transcript_s+'</a></div></div>';

        string += '<div class="shoutbox-alias-container"><input class="shoutbox-alias" type="text" autocomplete="off" maxlength="20" value="'+Shoutbox.user_name+'" readonly="readonly" /></div><textarea class="shoutbox-message"></textarea></div>';

        jQuery('body').append(string);

        var private_chat_element = jQuery('div[data-shoutbox-id="'+chat_id+'"]');

        Shoutbox.private_right_position(private_chat_element);

        Shoutbox.private_bottom_position(private_chat_element, state);
    },
    private_right_position: function(private_chat_element){
        var right_position = private_chat_element.outerWidth(true) * Shoutbox.private_count;
        Shoutbox.private_count++;

        private_chat_element.css('right', right_position);
    },
    private_bottom_position: function(private_chat_element, state){
        if(state == 'o'){
            jQuery(private_chat_element).animate({bottom: 0}, 500);
        } else if(state == 'm'){
            jQuery(private_chat_element).animate({bottom: -(jQuery(private_chat_element).outerHeight(true)-jQuery(private_chat_element).find('div.shoutbox-history-container').position().top)}, 500);
        }
    },
    update_private_cookie: function(cookie_name, cookie_value){
        jQuery.cookie(cookie_name, jQuery.toJSON(cookie_value), {path: Shoutbox.cookiepath, domain: Shoutbox.cookie_domain});
    }
    ,
    preg_quote: function(str){
        var specials = new RegExp("[.*+?|()\\[\\]{}\\\\]", "g");
        return str.replace(specials, "\\$&");
    },
    stripslashes: function(str){
        str = str.replace(/\\'/g,'\'');
        str = str.replace(/\\"/g,'"');
        str = str.replace(/\\\\/g,'\\');
        str = str.replace(/\\0/g,'\0');
        return str;
    },
    flag_html: function(single_user){
        return (single_user.c != null && single_user.m != null)?' <img class="shoutbox-flags" title="'+single_user.m+'" src="'+Shoutbox.quick_flag_url+'/'+single_user.c+'.gif" />':'';
    },
    update_rooms: function(){
        Shoutbox.rooms = [];
        for(var chat_id in Shoutbox.data)
            if(jQuery.inArray(Shoutbox.data[chat_id]['room_name'], Shoutbox.rooms) == -1)
                Shoutbox.rooms.push(Shoutbox.data[chat_id]['room_name']);
    },
    update_sound_state: function(){
        if(Shoutbox.play_audio == 0)
            jQuery("div.shoutbox-sound-link a").css('text-decoration','line-through');
        else
            jQuery("div.shoutbox-sound-link a").css('text-decoration','none');
    },
    user_status_class: function(user_status){
        var user_status_class = '';

        if(user_status == 0)
            user_status_class = 'shoutbox-admin';
        else if(user_status == 1)
            user_status_class = 'shoutbox-loggedin';
        else if(user_status == 2)
            user_status_class = 'shoutbox-guest';

        return user_status_class;
    },
    is_private_allowed: function(user_status){
        if  (
            user_status == 0
            ||
            (user_status == 1 &&
            Shoutbox.loggedin_initiate_private == 1)
            ||
            (user_status == 2 &&
            Shoutbox.guests_initiate_private == 1)
            ){
                return true;
        }else{
            return false;
        }
    },
    single_message_html: function(single_message, avatars, sys_mes){
        if(sys_mes == false){
            var alias = Shoutbox.stripslashes(single_message.alias);
            var status_class = Shoutbox.user_status_class(single_message.status);
        }else if(sys_mes == true){
            alias = Shoutbox.i18n.notice_s;
            status_class = 'shoutbox-notice';
        }

        var message_with_smile = Shoutbox.stripslashes(single_message.message);
        for (var smile in Shoutbox['smilies']){
            var replace = '<div class="shoutbox-smile shoutbox-smile-'+Shoutbox['smilies'][smile]+'" title="'+smile+'"></div>';
            message_with_smile = message_with_smile.replace(new RegExp(Shoutbox.preg_quote(smile), 'g'), replace);
        }
        var string = '<div class="shoutbox-history-message-alias-container '+status_class+'"><div class="shoutbox-history-header">';

        if(avatars == 1 && single_message.avatar != false)
            string += Shoutbox.stripslashes(single_message.avatar);

        string += '<div class="shoutbox-history-alias">';

        if(alias == Shoutbox.user_name || sys_mes == true  || Shoutbox.no_participation == 1)
            string += alias;
        else
            string += '<a href="" title="'+Shoutbox.i18n.reply_to_s.replace('%s', alias)+'">'+alias+'</a>';

        string += '</div>';

        string += '<div class="shoutbox-history-timestring">'+single_message.timestring+'</div></div><div class="shoutbox-history-message">'+message_with_smile+'</div>';

        if(Shoutbox.user_status == 0)
            string += '<div class="shoutbox-history-links">';

        if(Shoutbox.user_status == 0 && sys_mes == false)
            string += '<input class="shoutbox-to-delete-boxes" type="checkbox" name="shoutbox-to-delete[]" value="'+single_message.id+'" />';

        if(Shoutbox.user_status == 0)
            string += '</div>';

        string += '</div>';
        return string;
    },
    check_username: function(chat_id, username_check, username_status_element){

        if (typeof(Shoutbox.data[chat_id]['username_timeout']) != 'undefined')
            clearTimeout(Shoutbox.data[chat_id]['username_timeout']);

        Shoutbox.data[chat_id]['username_timeout'] = setTimeout(function(){
            jQuery.ajax({
                type: 'POST',
                url: Shoutbox.ajaxurl,
                data: {action: 'shoutbox-ajax-username-check', username_check: username_check},
                cache: false,
                dataType: 'json',

                success: function(data){
                    if(Shoutbox.no_participation == 0 && data.no_participation == 1)
                        location.reload(true);

                    jQuery(username_status_element).html('');
                    if(data.username_invalid == 1) {
                        jQuery(username_status_element).addClass('shoutbox-error');
                        jQuery(username_status_element).html(Shoutbox.i18n.username_invalid_s);
                    }else if(data.username_bad_words == 1){
                        jQuery(username_status_element).addClass('shoutbox-error');
                        jQuery(username_status_element).html(Shoutbox.i18n.username_bad_words_s);
                    }else if(data.username_exists == 1){
                        jQuery(username_status_element).addClass('shoutbox-error');
                        jQuery(username_status_element).html(Shoutbox.i18n.username_exists_s);
                    } else if(data.username_blocked == 1){
                        jQuery(username_status_element).addClass('shoutbox-error');
                        jQuery(username_status_element).html(Shoutbox.i18n.username_blocked_s);
                    }else if(data.username_exists == 0 || data.username_blocked == 0 || data.username_invalid == 0){
                        jQuery(username_status_element).html('');
                        Shoutbox.user_name = data.username;
                        jQuery('input.shoutbox-alias').val(data.username);
                    }
                },
                beforeSend: function(){
                    jQuery(username_status_element).html('');
                    jQuery(username_status_element).removeClass('shoutbox-error');
                    jQuery(username_status_element).html(Shoutbox.i18n.username_check_wait_s);
                }
            });
            delete Shoutbox.data[chat_id]['username_timeout'];
        }, 1500);
    },
    update_messages: function(){
        jQuery.post(Shoutbox.ajaxurl, {
                action: 'shoutbox-ajax-update-messages',
                last_timestamp: Shoutbox.last_timestamp,
                rooms: Shoutbox.rooms
            },
            function(data){
                if((Shoutbox.no_participation == 0 && data.no_participation == 1)
                    ||
                    (Shoutbox.no_participation == 1 && data.no_participation == 0))
                    location.reload(true);

                if(data.success == 1){
                    var updates = data.messages;
                    jQuery('div.shoutbox-container').each(function(){
                        var already_notified = 0;
                        var chat_id = jQuery(this).attr('data-shoutbox-id');
                        var room_name = Shoutbox.data[chat_id]['room_name'];
                        var avatars = Shoutbox.data[chat_id]['avatars'];
                        var scroll_enable = Shoutbox.data[chat_id]['scroll_enable'];
                        var history_container = jQuery(this).find('.shoutbox-history-container');
                        var state = Shoutbox.data[chat_id]['state'];

                        for(var i=0;typeof(updates[i])!='undefined';i++){
                            if(room_name == updates[i].room){
                                if(updates[i].alias != 'Shoutbox'){
                                    if( already_notified == 0
                                        &&
                                        Shoutbox.play_audio == 1
                                        &&
                                        Shoutbox.user_name != Shoutbox.stripslashes(updates[i].alias)
                                        &&
                                        Shoutbox.last_timestamp != 0) {
                                        Shoutbox.audio_element.play();
                                        already_notified = 1;
                                    }

                                    if( Shoutbox.last_timestamp != 0
                                        &&
                                        typeof(state) != 'undefined'
                                        && state == 'm'){
                                        jQuery(this).find('div.shoutbox-container-private-minimize-restore a').click();
                                    }

                                    jQuery(history_container).append(Shoutbox.single_message_html(updates[i], avatars, false));
                                } else if(Shoutbox.last_timestamp != 0){
                                    var sys_mes = jQuery.parseJSON(Shoutbox.stripslashes(updates[i].message));
                                    for (var mes_room_name in sys_mes){
                                        if(sys_mes[mes_room_name]['private_to'] == Shoutbox.user_name && sys_mes[mes_room_name]['type'] == 'INV'){ // Receiver
                                            if(Shoutbox.is_private_allowed(updates[i].status)){
                                                Shoutbox.private_queue[mes_room_name] = sys_mes[mes_room_name];
                                                Shoutbox.update_private_cookie(Shoutbox.private_queue_name, Shoutbox.private_queue);

                                                var invitation_received = jQuery.extend(true, {}, updates[i]);
                                                invitation_received.message = Shoutbox.i18n.invitation_received_s.replace('%s', sys_mes[mes_room_name]['private_from']);

                                                jQuery(history_container).append(Shoutbox.single_message_html(invitation_received, 0, true));
                                            }
                                        } else if(sys_mes[mes_room_name]['private_from'] == Shoutbox.user_name){ // Sender
                                            var private_queue_found = false;
                                            for (var pq_room_name in Shoutbox.private_queue){
                                                if(Shoutbox.is_private_eq(Shoutbox.private_queue[pq_room_name], sys_mes[mes_room_name])){
                                                    Shoutbox.spawn_private_chat(pq_room_name, sys_mes[mes_room_name]['private_to'], 'o');
                                                    Shoutbox.update_rooms();

                                                    delete Shoutbox.private_queue[pq_room_name];
                                                    Shoutbox.update_private_cookie(Shoutbox.private_queue_name, Shoutbox.private_queue);

                                                    Shoutbox.private_current[pq_room_name] = Shoutbox.data[pq_room_name];
                                                    Shoutbox.update_private_cookie(Shoutbox.private_current_name, Shoutbox.private_current);
                                                    private_queue_found = true;
                                                }
                                            }

                                            if(private_queue_found == false){
                                                if(Shoutbox.is_private_allowed(updates[i].status)){
                                                    Shoutbox.spawn_private_chat(mes_room_name, sys_mes[mes_room_name]['private_to'], 'o');
                                                    Shoutbox.update_rooms();

                                                    Shoutbox.private_current[mes_room_name] = Shoutbox.data[mes_room_name];
                                                    Shoutbox.update_private_cookie(Shoutbox.private_current_name, Shoutbox.private_current);

                                                    var invitation_sent = jQuery.extend(true, {}, updates[i]);
                                                    invitation_sent.message = Shoutbox.i18n.invitation_sent_s.replace('%s', sys_mes[mes_room_name]['private_to']);

                                                    jQuery(history_container).append(Shoutbox.single_message_html(invitation_sent, 0, true));
                                                }else{
                                                    alert(Shoutbox.i18n.not_allowed_to_initiate_s);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if(scroll_enable == 1)
                            jQuery(history_container).animate({scrollTop: jQuery(history_container)[0].scrollHeight}, 500);
                    });

                    Shoutbox.last_timestamp = updates[updates.length-1].unix_timestamp;
                }

                if(!Shoutbox.is_user_inactive())
                    Shoutbox.update_messages();
            },
            'json'
        );
    },
    update_users: function(){
        Shoutbox.update_users_counter++;
        if(Shoutbox.is_user_inactive()){
            clearInterval(Shoutbox.users_interval);
            jQuery('div.shoutbox-container').html('<div style="shoutbox-bootom-notice">'+Shoutbox.i18n.dropped_inactivity_s+'</div>');
        }else{
            jQuery.post(Shoutbox.ajaxurl, {
                    action: 'shoutbox-ajax-update-users',
                    rooms: Shoutbox.rooms},
                    function(data){
                        if((Shoutbox.no_participation == 0 && data.no_participation == 1)
                        ||
                        (Shoutbox.no_participation == 1 && data.no_participation == 0))
                            location.reload(true);

                        if(typeof(Shoutbox.users_interval) == 'undefined'){
                            Shoutbox.users_interval = setInterval(function(){
                                Shoutbox.update_users();
                            }, Shoutbox.timeout_refresh_users * 1000);
                        }

                        var users = data.users;
                        jQuery("div.shoutbox-container").each(function(){
                            var chat_id = jQuery(this).attr('data-shoutbox-id');
                            var userlist_position = Shoutbox.data[chat_id]['userlist_position'];
                            var room_name = Shoutbox.data[chat_id]['room_name'];
                            var string = '';

                            for(var i=0;typeof(users[i])!='undefined';i++){
                                if(room_name == users[i].room){
                                    if(Shoutbox.user_status == 0){
                                        var checked_ids = [];
                                        jQuery(this).find('.shoutbox-users-container input[type=checkbox]:checked').each(function(){
                                            checked_ids.push(jQuery(this).attr('data-user-id'));
                                        });
                                    }
                                    var alias = Shoutbox.stripslashes(users[i].alias);

                                    string += '<div class="shoutbox-single-user '+Shoutbox.user_status_class(users[i].status)+'">';

                                    if(alias == Shoutbox.user_name || Shoutbox.no_participation == 1)
                                        string += alias;
                                    else{
                                        if(Shoutbox.user_status == 0)
                                            string += '<input class="shoutbox-to-ban-boxes" type="checkbox" name="shoutbox-to-ban[]" value="'+users[i].ip+'" data-user-id="'+users[i].id+'"'+((jQuery.inArray(users[i].id, checked_ids) == 0) ? ' checked="checked"':'')+'/>';

                                        string += '<a href="" title="'+Shoutbox.i18n.private_with_s.replace('%s',alias)+'">'+alias+'</a>';
                                    }

                                    string += Shoutbox.flag_html(users[i]);

                                    string += '</div>';

                                    if(userlist_position == 'top')
                                        string += ', ';
                                }
                            }
                            jQuery(this).find('.shoutbox-users-container').html((userlist_position == 'top') ? string.substring(0, string.length-2): string);
                        });
                    },
                    'json'
                );
        }
    },
    new_message: function(chat_id, message_text, sys_mes){
        var room_name = Shoutbox.data[chat_id]['room_name'];
        Shoutbox.update_users_counter = 0;

        jQuery.post(
            Shoutbox.ajaxurl,{
                action: 'shoutbox-ajax-new-message',
                sys_mes: sys_mes,
                message: message_text,
                room: room_name
            },
            function(data) {
                if(Shoutbox.no_participation == 0 && data.no_participation == 1)
                    location.reload(true);
            });
    }
});

if(Shoutbox.audio_element.canPlayType){
    if(Shoutbox.audio_element.canPlayType('audio/ogg; codecs="vorbis"')) {
        Shoutbox.audio_element.setAttribute('src', Shoutbox.url+'/sounds/message-sound.ogg');
        Shoutbox.audio_element.setAttribute('preload', 'auto');
        Shoutbox.audio_support = 1;
    } else if(Shoutbox.audio_element.canPlayType('audio/mpeg;')){
        Shoutbox.audio_element.setAttribute('src', Shoutbox.url+'sounds/message-sound.mp3');
        Shoutbox.audio_element.setAttribute('preload', 'auto');
        Shoutbox.audio_support = 1;
    } else if(Shoutbox.audio_element.canPlayType('audio/wav; codecs="1"')){
        Shoutbox.audio_element.setAttribute('src', Shoutbox.url+'sounds/message-sound.wav');
        Shoutbox.audio_element.setAttribute('preload', 'auto');
        Shoutbox.audio_support = 1;
    }
}

if(Shoutbox.audio_support == 1){
    if(jQuery.cookie('Shoutbox_sound_state'))
        Shoutbox.play_audio = jQuery.cookie('Shoutbox_sound_state');
    else
        Shoutbox.play_audio = Shoutbox.audio_enable;
}

if(Shoutbox.no_participation == 0){
    if(jQuery.cookie(Shoutbox.private_queue_name) != null)
        Shoutbox.private_queue = jQuery.parseJSON(jQuery.cookie(Shoutbox.private_queue_name));

    if(jQuery.cookie(Shoutbox.private_current_name) != null)
        Shoutbox.private_current = jQuery.parseJSON(jQuery.cookie(Shoutbox.private_current_name));

    for (var Shoutbox_pc_room_name in Shoutbox.private_current)
        Shoutbox.spawn_private_chat(Shoutbox_pc_room_name, Shoutbox.private_current[Shoutbox_pc_room_name]['him'], Shoutbox.private_current[Shoutbox_pc_room_name]['state']);
}

Shoutbox.update_rooms();

if(Shoutbox.audio_support){
    jQuery("div.shoutbox-sound-link").css('display','block');
    Shoutbox.update_sound_state();
}

Shoutbox.update_users();

jQuery(document).on('keypress', "textarea.shoutbox-message", function(e) {
    code = e.keyCode ? e.keyCode : e.which;
    if(code.toString() == 13) {
        e.preventDefault();

        var message_text = jQuery.trim(jQuery(this).val());
        if(message_text != ''){
            var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
            jQuery(this).val('');
            Shoutbox.new_message(chat_id, message_text, false);
        }
    }
});

jQuery("input.shoutbox-send-button").on('click', function(e) {
    e.preventDefault();

    var textarea = jQuery(this).siblings('textarea.shoutbox-message');
    var message_text = jQuery.trim(jQuery(textarea).val());
    if(message_text != ''){
        var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
        jQuery(textarea).val('');
        Shoutbox.new_message(chat_id, message_text, false);
    }
    jQuery(this).prev().focus();
});

jQuery("div.shoutbox-smile").on('click', function() {
    var input_textarea = jQuery(this).parents('.shoutbox-container').find('.shoutbox-message');
    var this_element = jQuery(this);

    jQuery(this).fadeTo(100, 0.3, function() {
        jQuery(input_textarea).Shoutbox_insert_at_caret(jQuery(this_element).attr('title')).trigger('change');
        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery(document).on('click', "div.shoutbox-history-alias a", function(e) {
    e.preventDefault();
    var input_textarea = jQuery(this).parents('.shoutbox-container').find('.shoutbox-message');
    var this_element = jQuery(this);

    jQuery(this).fadeTo(100, 0, function() {
        jQuery(input_textarea).Shoutbox_insert_at_caret('@'+jQuery(this_element).text()+': ');
        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery(document).on('click', "div.shoutbox-single-user a", function(e) {
    e.preventDefault();

    var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
    var this_element = jQuery(this);

    jQuery(this).fadeTo(100, 0, function() {
        var mes_room_name = Shoutbox.random_string(12);
        var sys_mes = jQuery.parseJSON('{"'+mes_room_name+'":{"private_from":"'+Shoutbox.user_name+'","private_to":"'+jQuery(this_element).text()+'"}}');
        var sys_mes_type = 'INV';
        var question = Shoutbox.i18n.private_invite_confirm_s.replace('%s',jQuery(this_element).text());

        for (var pq_room_name in Shoutbox.private_queue){
            if(Shoutbox.is_private_eq(Shoutbox.private_queue[pq_room_name], sys_mes[mes_room_name]))
                sys_mes_type = 'ACK';

            question = Shoutbox.i18n.private_accept_confirm_s.replace('%s',jQuery(this_element).text());
            break;
        }

        if (confirm(question)){
            sys_mes[mes_room_name]['type'] = sys_mes_type;
            Shoutbox.new_message(chat_id, jQuery.toJSON(sys_mes), true);
        }
        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery(document).on('click', "div.shoutbox-container-private-close a", function(e) {
    e.preventDefault();
    var this_element = jQuery(this);
    var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');

    jQuery(this).fadeTo(100, 0, function() {
        delete Shoutbox.data[chat_id];

        delete Shoutbox.private_current[chat_id];
        Shoutbox.update_private_cookie(Shoutbox.private_current_name, Shoutbox.private_current);
        Shoutbox.private_count--;

        Shoutbox.update_rooms();
        jQuery(this).parents('.shoutbox-container').remove();
        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery(document).on('click', "div.shoutbox-container-private-minimize-restore a", function(e) {
    e.preventDefault();
    var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
    var private_chat_element = jQuery(this).parents('.shoutbox-container');
    var state = Shoutbox.data[chat_id]['state'];
    var this_element = jQuery(this);

    jQuery(this).fadeTo(100, 0, function() {
        if(state == 'o'){
            jQuery(this_element).attr('title',Shoutbox.private_restore);
            Shoutbox.data[chat_id]['state'] = 'm';
            Shoutbox.private_current[chat_id]['state'] = 'm';
            Shoutbox.update_private_cookie(Shoutbox.private_current_name, Shoutbox.private_current);
            jQuery(this_element).text('o');

            Shoutbox.private_bottom_position(private_chat_element, 'm');
        } else if(state == 'm'){
            jQuery(this_element).attr('title',Shoutbox.private_minimize);
            Shoutbox.data[chat_id]['state'] = 'o';
            Shoutbox.private_current[chat_id]['state'] = 'o';
            Shoutbox.update_private_cookie(Shoutbox.private_current_name, Shoutbox.private_current);
            jQuery(this_element).text('-');

            Shoutbox.private_bottom_position(private_chat_element, 'o');
        }
        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery("div.shoutbox-sound-link a").on('click', function(e) {
    e.preventDefault();
    var this_element = jQuery(this);

    jQuery(this).fadeTo(100, 0, function() {
        if(Shoutbox.play_audio == 1)
            Shoutbox.play_audio = 0;
            else
            Shoutbox.play_audio = 1;

        jQuery.cookie('Shoutbox_sound_state', Shoutbox.play_audio, {path: Shoutbox.cookiepath, domain: Shoutbox.cookie_domain});

        Shoutbox.update_sound_state();

        jQuery(this_element).fadeTo(100, 1);
    });
});

jQuery("div.shoutbox-scroll-link a").on('click', function(e) {
    e.preventDefault();

    var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
    var scroll_enable = Shoutbox.data[chat_id]['scroll_enable'];
    var this_element = jQuery(this);

    jQuery(this).fadeTo(100, 0, function() {
        if(scroll_enable == 0){
            Shoutbox.data[chat_id]['scroll_enable'] = 1;
            jQuery(this_element).css('text-decoration','none');
        } else{
            Shoutbox.data[chat_id]['scroll_enable'] = 0;
            jQuery(this_element).css('text-decoration','line-through');
        }
        jQuery(this_element).fadeTo(100, 1);
    });
});

if(Shoutbox.user_status == 0 || Shoutbox.allow_change_username == 1){
    jQuery("input.shoutbox-alias").on('keyup change', function(){
        var username_check = jQuery.trim(jQuery(this).val());
        if(username_check != ''){
            var chat_id = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-id');
            var username_status_element = jQuery(this).parents('.shoutbox-container').find('span.shoutbox-username-status');

            Shoutbox.check_username(chat_id, username_check, username_status_element);
        }
    });
}

if(Shoutbox.user_status != 0){
    jQuery('textarea.shoutbox-message').on('keyup change input paste', function() {
        var counter = jQuery(this).parents('.shoutbox-container').attr('data-shoutbox-counter');
        if(counter == 1){
            var counter_element = jQuery(this).parents('.shoutbox-container').find('span.shoutbox-counter');
            var count = jQuery(this).val().length;
            var available = Shoutbox.message_maximum_number_chars - count;
            if(available <= 25 && available >= 0)
                jQuery(counter_element).addClass('shoutbox-warning');
            else
                jQuery(counter_element).removeClass('shoutbox-warning');

            if(available < 0)
                jQuery(counter_element).addClass('shoutbox-exceeded');
            else
                jQuery(counter_element).removeClass('shoutbox-exceeded');

            jQuery(counter_element).html(available);
        }
    });
}

Shoutbox.update_messages();