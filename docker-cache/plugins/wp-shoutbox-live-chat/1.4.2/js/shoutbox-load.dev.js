// Shoutbox 1.1 (hotfix) - load
var Shoutbox = jQuery.extend(Shoutbox || {}, {
    script_suffix: (Shoutbox.debug_mode == 1) ? '.dev' : '',
    private_current_name: 'Shoutbox_private_current_'+Shoutbox.user_id,
    get_script: function(url, callback, options) {
        options = jQuery.extend(options || {}, {
            crossDomain: (Shoutbox.debug_mode == 1)? true : false,
            dataType: "script",
            cache: true,
            success: callback,
            url: url
        });

        return jQuery.ajax(options);
    },
    load: function(){
        if(jQuery('div.shoutbox-container').length != 0 || (jQuery.cookie(Shoutbox.private_current_name) && jQuery.cookie(Shoutbox.private_current_name) != '{}'))
            Shoutbox.get_script(Shoutbox.url+'js/shoutbox-init'+Shoutbox.script_suffix+'.js?'+Shoutbox.version);
    }
});

//if (/(chrome|webkit)[ \/]([\w.]+)/.test(window.navigator.userAgent.toLowerCase())) {
//    // Webkit bug workaround: http://code.google.com/p/chromium/issues/detail?id=41726
//    jQuery(window).load(Shoutbox.load());
//} else{
//    jQuery(document).ready(Shoutbox.load());
// }

jQuery(window).bind("load", function() {
   Shoutbox.load()
});