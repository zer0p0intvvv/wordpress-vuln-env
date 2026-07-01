/* 
 * (c) M2TransMod,
 * Gerard Revestraat 49, 205EN Haarlem
 * sidney.vandestouwe@m2transmod.com
 */

    var $emailHasFocus = false;
    var $phoneHasFocus = false;
    function stopRKey(evt) { 
      var evt = (evt) ? evt : ((event) ? event : null); 
      var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
      if ((evt.keyCode == 13) && ($emailHasFocus || $phoneHasFocus))  {return false;} else {return true;} 
    } 
    document.onkeypress = stopRKey;

    var $SMSNotificationGetActive = false;
    var rawFile, file;
    function getSMSNotificationFile() {
        // we add ?dummy=.... to the url to stop avoid caching the file issues
        file = (document.getElementById("stc_js_folders_hidden").value).split("|")[1];
        file = file.replace("status.txt", document.getElementById("stc_subscriber_id_hidden").value+"-status.txt") + "?dummy="+Date.now();
        rawFile = new XMLHttpRequest();
        rawFile.open("GET",file);
        rawFile.onload = stc_file_load_handler;
        rawFile.send();
    }

    function stc_file_load_handler() {
        if(this.status == 200) {
             // success!
            if (document.getElementById("stc-mobile-phone-label") != null) {
                $response = this.responseText.split("|");
                $str = document.getElementById("stc-mobile-phone-label").innerHTML.slice(0, document.getElementById("stc-mobile-phone-label").innerHTML.lastIndexOf(":")+1);
                document.getElementById("stc-mobile-phone-label").innerHTML = $str + " " + $response[0];
            }
            $SMSNotificationGetActive = false
        } else {
            alert("Error reading status file:" + file);
        }
        // reschedule the check for status update
        setTimeout(getSMSNotificationStatus, 5000);
    }

    function getSMSNotificationStatus() {
        if (!$SMSNotificationGetActive) {
                $SMSNotificationGetActive = true;
                getSMSNotificationFile();
        }
    }


    var toggler = document.getElementsByClassName("stc-caret");
    var i;
    for (i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", function() {
            this.parentElement.querySelector(".stc-nested").classList.toggle("stc-active");
            this.classList.toggle("stc-caret-down");
        })
    };
    toggler = document.getElementsByClassName("stc-caret-u");
    i;
    for (i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", function() {
            this.parentElement.querySelector(".stc-nested-u").classList.toggle("stc-active-u");
            this.classList.toggle("stc-caret-down-u");
        })
    };

jQuery(function($){

    var input = document.querySelector("#stc_mobile_phone");
    var errorMsg = document.querySelector("#error-msg");
    var errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
    var iti;

    if (input) {
        iti = window.intlTelInput(input, {
            initialCountry: "auto",
            geoIpLookup: function(success, failure) {
                    $.getJSON('https://freegeoip.app/json/', function(data) {
                            var countryCode = (data && data.country_code) ? data.country_code : "us";
                            success(countryCode);
                    });
            },
            utilsScript: (document.getElementById("stc_js_folders_hidden").value).split("|")[0],
        });
        // on blur: validate
        input.addEventListener('blur', function() {
            reset();
            if (input.value.trim()) {
                if (iti.isValidNumber()) {
                    document.querySelector("#stc_mobile_phone_hiddden").value = iti.getNumber();
                } else {
                    input.classList.add("error");
                    var errorCode = iti.getValidationError();
                    errorMsg.innerHTML = errorMap[errorCode];
                    errorMsg.classList.remove("hide");
                }
            }
        });

        var reset = function() {
            input.classList.remove("error");
            errorMsg.innerHTML = "";
            errorMsg.classList.add("hide");
            document.querySelector("#stc_mobile_phone_hiddden").value = "";
        };

        // on keyup / change flag: reset
        input.addEventListener('change', reset);
        input.addEventListener('keyup', reset);
    }

    var $updateMe = false;
    $('#stc-unsubscribe-checkbox').click( function() {
        if( $(this).prop('checked') == true ) {
            if ($('#stc-update-btn').is(":visible")) {$updateMe = true;} else {$updateMe = false;}
            $('.stc-categories').hide();
            $('#stc-subscribe-btn').hide();
            $('#stc-update-btn').hide();
            $('#stc-unsubscribe-btn').show();
        } else {
            $('.stc-categories').show();
            if (!$updateMe) {$('#stc-subscribe-btn').show();} else { $('#stc-subscribe-btn').hide();}
            $('#stc-unsubscribe-btn').hide();
            if ($updateMe) {$('#stc-update-btn').show();} else {$('#stc-update-btn').hide();}
        }
    });

    $('#stc-all-categories').click( function() {
        if( $(this).prop('checked') == true ) {
            $('div.stc-categories-checkboxes').hide();
            $('div.stc-categories-checkboxes input[type=checkbox]').each(function () {
                $(this).prop('checked', true);
            });
        } else {
            $('div.stc-categories-checkboxes').show();
            $('div.stc-categories-checkboxes input[type=checkbox]').each(function () {
                $(this).prop('checked', false);
            });
        }

    });
                
    $('div.stc-notification-checkboxes input[type=checkbox]').click(function(cb) {
        switch (cb.target.id) {
            case "Sun" :                                
            case "Mon" :                                
            case "Tue" :                                
            case "Wed" :                                
            case "Thu" :                                
            case "Fri" :                                
            case "Sat" :
                $('div.stc-notification-checkboxes').find($('#Daily')).prop('checked', false);
                $('div.stc-notification-checkboxes').find($('#Hourly')).prop('checked', false);
                $('div.stc-notification-checkboxes').find($('#STC')).prop('checked', false);
                break;
            case "Daily" :
                $('div.stc-notification-checkboxes input[type=checkbox]').each(function() {
                    $(this).prop('checked', false);
                });
                $('div.stc-notification-checkboxes').find($('#Daily')).prop('checked', true);
                break;
            case "Hourly" :
                $('div.stc-notification-checkboxes input[type=checkbox]').each(function() {
                    $(this).prop('checked', false);
                });
                $('div.stc-notification-checkboxes').find($('#Hourly')).prop('checked', true);
                break;
            case "STC":
                $('div.stc-notification-checkboxes input[type=checkbox]').each(function() {
                    $(this).prop('checked', false);
                });
                $('div.stc-notification-checkboxes').find($('#STC')).prop('checked', true);
                break;
        }
        if( $(this).prop('checked') == true ) {
        } else {
        }
    });

    // email field is changed so we need to adapt all other stuff
    $('#stc-email').change( function() {
        data = {
                action: 'stc_get_results',
                email: $('#stc-email').val(),
                original_id: $('#stc_original_subscriber_id_hidden').val()
        };
        var ajaxurl = (document.getElementById("stc_js_folders_hidden").value).split("|")[2]; //"<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
        // ask the server to fill in the details for the new email adress
        $.post(ajaxurl , data, function (response) {
            // parse the JSON results
            xObj = JSON.parse(response);
            if (xObj.user_state == 'exsist' ) {
                // check if the unsubscribe checkbox is checked
                if ($('#stc-unsubscribe-checkbox').prop('checked')) {
                    $updateMe = true;
                    $('#stc-subscribe-btn').hide();
                    $('#stc-update-btn').hide();
                    $('#stc-unsubscribe-btn').show();
                } else {
                    $updateMe = false;
                    $('#stc-subscribe-btn').hide();
                    $('#stc-update-btn').show();
                    $('#stc-unsubscribe-btn').hide();
                }
            } else if (xObj.user_state == 'new' ) {
                // we have a unknown potential new subscriber email and therefore reset the form
                $updateMe = false;
                $('div.stc-categories-checkboxes').show();
                $('#stc-unsubscribe-checkbox').prop('checked', false);
                $('#stc-all-categories').prop('checked', false);
                $('#stc-subscribe-btn').show();
                $('#stc-update-btn').hide();
                $('#stc-unsubscribe-btn').hide();
                $('.stc-categories').show();
            } else {
                $('#stc-email').val($('#stc-email').val() + " : " + script_vars.approvalStr);
                $('#stc-subscribe-btn').hide();
                $('#stc-update-btn').hide();
                $('#stc-unsubscribe-btn').hide();
            }
            // set the mobile phone field to the contents of the current subscriber
            if (iti) {
                iti.setNumber(xObj.stc_mobile_phone);
                document.querySelector("#stc_mobile_phone_hiddden").value = xObj.stc_mobile_phone;
                // set the sms subscriber status status in the label of the mobile phone number field
                $str = document.getElementById("stc-mobile-phone-label").innerHTML.slice(0, document.getElementById("stc-mobile-phone-label").innerHTML.lastIndexOf(":")+1);
                document.getElementById("stc-mobile-phone-label").innerHTML = $str + " " + xObj.stc_mobile_phone_status;
            }
            document.querySelector("#stc_subscriber_id_hidden").value = xObj.stc_subscriber_id;
            // set the keywords to the contents of the current subscriber
            $('#stc-keywords').val(xObj.keywords);
            // set the  stc-area checkboxes to the values as last set
            $('div.stc-area-checkboxes input[type=checkbox]').each(function () {
                $(this).prop('checked', false);
                if (xObj.user_state == 'exsist') {
                    $currentCheckBox = $(this);
                    if (xObj.search_areas) xObj.search_areas.forEach(function(area) { 
                        if (area.name == $currentCheckBox.val()) {
                                $currentCheckBox.prop('checked', true);
                        }
                    });
                }
            });
            // set the  stc-area checkboxes to the values as last set
            $('div.stc-notification-checkboxes input[type=checkbox]').each(function () {
                $(this).prop('checked', false);
                if (xObj.user_state == 'exsist') {
                    $currentCheckBox = $(this);
                    if (xObj.notifications) xObj.notifications.forEach(function(area) { 
                        if (area.name == $currentCheckBox.val()) {
                            $currentCheckBox.prop('checked', true);
                        }
                    });
                }
            });
            // set the category checkboxes to the values as last set
            $('div.stc-categories-checkboxes input[type=checkbox]').each(function () {
                $(this).prop('checked', false);
                if (xObj.user_state == 'exsist') {
                    $currentCheckBox = $(this);
                    if (xObj.categories) xObj.categories.forEach(function(cat) { 
                        if (cat.term_id == $currentCheckBox.val()) {
                            $currentCheckBox.prop('checked', true);
                        }
                    });
                }
            });
        });
    });
    $('#stc-email').focusin(function() {$emailHasFocus = true;});
    $('#stc-email').focusout(function() {$emailHasFocus = false;});
    $('#stc_mobile_phone').focusin(function() {$phoneHasFocus = true;});
    $('#stc_mobile_phone').focusout(function() {$phoneHasFocus = false;});

    // when the treeview is ready we unfold the treeview if required by the admin settings 
    $(document).ready( function () {
        // start to check the notification status
        $SMSNotificationGetActive = false;
        if (input) setTimeout(getSMSNotificationStatus, 1000);

        toggler = document.getElementsByClassName("stc-caret-u");
        for (i = 0; i < toggler.length; i++) {
            toggler[i].parentElement.querySelector(".stc-nested-u").classList.toggle("stc-active-u");
            toggler[i].classList.toggle("stc-caret-down-u");
        };
    });
})

