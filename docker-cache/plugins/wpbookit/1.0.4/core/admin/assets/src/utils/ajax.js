
function get(route, Dataparams,  args= {}) {
    return jQuery.ajax({
        url: window.ajaxurl,
        type: 'GET',
        data: {
            ...Dataparams,
            action: 'wpb_ajax_get',
            route_name: route
        },
        ...args
    })
}

function post(route, Dataparams,args= {}) {

    let isFormData = {
        processData: false,
        contentType: false
    }
    if (Dataparams instanceof FormData) {
        Dataparams.append('action', 'wpb_ajax_post');
        Dataparams.append('route_name', route);
        Dataparams.append('_ajax_nonce', window?.wpbookit?._ajax_nonce ?? wpb_nounce);

    } else {
        Dataparams = {
            ...Dataparams, 
            action: 'wpb_ajax_post',
            _ajax_nonce: window?.wpbookit?._ajax_nonce ?? wpb_nounce,
            route_name: route
        };
    }


    return jQuery.ajax({
        url: window.ajaxurl,
        type: 'POST',
        data: Dataparams,
        ...(Dataparams instanceof FormData)?isFormData:{},
        ...args
    })
}
export { get, post } 