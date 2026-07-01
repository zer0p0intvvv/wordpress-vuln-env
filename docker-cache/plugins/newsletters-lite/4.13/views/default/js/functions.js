(function ($) {
    $.fn.newsletters_subscribe_form = function () {
        var $form = this,
            $submit = $form.find(':submit'),
            $fields = $form.find('.newsletters-fieldholder :input'),
            $fieldholders = $form.find('.newsletters-fieldholder'),
            $selectfields = $form.find('select'),
            $filefields = $form.find(':file'),
            $errorfields = $form.find('.has-error'),
            $errors = $form.find('.newsletters-field-error'),
            $wrapper = $form.parent(),
            $loading = $form.find('.newsletters-loading-wrapper'),
            $scroll = $form.find('input[name="scroll"]'),
            $progress = $form.find('.newsletters-progress'),
            $progressbar = $form.find('.newsletters-progress .progress-bar'),
            $progresspercent = $form.find('.newsletters-progress .sr-only'),
            $postpageclasses = '.newsletters-management, .entry-content, .post-entry, .entry, .page-content, .page-entry',
            $postpagecontainer = $form.closest($postpageclasses),
            $recaptcha_id,
            $recaptcha_element,
            $recaptcha_loaded = false;
        var isTurnstile = (newsletters.has_captcha && newsletters.captcha === 'turnstile');
        var turnstileWidgetId = null;           // will hold widget id

        // Handle form submission
        $form.on('submit', function (e) {
            $($form).trigger('newsletters_subscribe_form_submit');

            // reCAPTCHA handling
            if (typeof grecaptcha !== 'undefined' && newsletters.has_captcha && newsletters.captcha === 'recaptcha') {
                if (newsletters.recaptcha_type === 'v3') {
                    // reCAPTCHA v3
                    e.preventDefault();
                    grecaptcha.ready(function () {
                        grecaptcha.execute(newsletters.recaptcha_sitekey, { action: 'subscribe' }).then(function (token) {
                            $form.find('input[name="g-recaptcha-response"]').val(token);
                            $form.trigger('newsletters_subscribe_form_submitted');
                            if ($form.hasClass('newsletters-subscribe-form-ajax')) {
                                $form.ajaxSubmit(); // Trigger AJAX submission manually
                            } else {
                                $form.off('submit').submit(); // Regular submission
                            }
                        });
                    });
                    return false;
                } else if (newsletters.recaptcha_type === 'invisible') {
                    // reCAPTCHA v2 invisible
                    if (typeof $recaptcha_id !== 'undefined') {
                        var token = grecaptcha.getResponse($recaptcha_id);
                        if (!token) {
                            grecaptcha.execute($recaptcha_id);
                            return false;
                        }
                    }
                }
            }

            $loading.show();
            if (typeof $filefields !== 'undefined' && $filefields.length > 0) {
                $progress.show();
            }

            if (typeof $errors !== 'undefined') { $errors.slideUp(); }
            if (typeof $errorfields !== 'undefined') { $errorfields.removeClass('has-error'); }
            $submit.prop('disabled', true);
            $fields.attr('readonly', true);

            if ($.isFunction($.fn.select2) && typeof $selectfields !== 'undefined' && $selectfields.length > 0) {
                $selectfields.select2('destroy');
                $selectfields.attr('readonly', true);
                $selectfields.select2();
            }

            $($form).trigger('newsletters_subscribe_form_submitted');
        });

        $fields.on('focus click', function () {
            $(this).removeClass('newsletters_fielderror').nextAll('div.newsletters-field-error').slideUp().parent().removeClass('has-error');
        });

        if ($.isFunction($.fn.select2) && typeof $selectfields !== 'undefined' && $selectfields.length > 0) {
            $selectfields.select2();
        }

        if (!$form.hasClass('form-inline') && $form.hasClass('form-twocolumns')) {
            $form.wrap('<div class="container"></div>');
            $divs = $postpagecontainer.find($form).find('.newsletters-fieldholder:not(.newsletters_submit, .hidden)');
            for (var i = 0; i < $divs.length; i += 2) {
                $divs.slice(i, i + 2).wrapAll('<div class="row"></div>');
            }
            jQuery($divs).wrap('<div class="col-md-6"></div>');
            $postpagecontainer.find($form).find('.newsletters-progress').addClass('col-md-12');
        }

        if ($form.hasClass('newsletters-subscribe-form-ajax')) {
            if ($.isFunction($.fn.ajaxForm)) {
                $form.ajaxForm({
                    url: newsletters_ajaxurl + 'action=wpmlsubscribe&security=' + newsletters.ajaxnonce.subscribe,
                    data: (function () {
                        var formvalues = $form.serialize();
                        return formvalues;
                    })(),
                    type: 'POST',
                    cache: false,
                    beforeSend: function () {
                        var percentVal = '0%';
                        $progressbar.width(percentVal);
                        $progresspercent.html(percentVal);
                        $($form).trigger('newsletters_subscribe_form_before_ajax');
                    },
                    uploadProgress: function (event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        $progressbar.width(percentVal);
                        $progresspercent.html(percentVal);
                        $($form).trigger('newsletters_subscribe_form_upload_progress');
                    },
                    success: function (response) {
                        if ($('.newsletters-subscribe-form', $('<div/>').html(response)).length > 0) {
                            $wrapper.html($(response).find('.newsletters-subscribe-form'));
                        } else {
                            $wrapper.parent().find('.newsletters-form-styling_beforeform').remove();
                            $wrapper.parent().find('.newsletters-form-styling_afterform').remove();
                            $wrapper.html(response);
                        }

                        $wrapper.find('.newsletters-subscribe-form').newsletters_subscribe_form();

                        if (typeof $scroll !== 'undefined' && $scroll.val() == 1) {
                            var targetOffset = ($wrapper.offset().top - 50);
                            $('html,body').animate({ scrollTop: targetOffset }, 500);
                        }

                        $($form).trigger('newsletters_subscribe_form_success_ajax');
                    },
                    complete: function () {
                        var percentVal = '100%';
                        $progressbar.width(percentVal);
                        $progresspercent.html(percentVal);

                        $($form).trigger('newsletters_subscribe_form_complete_ajax');
                    }
                });
            }
        }

        var recaptcha_callback = function () {
            if (newsletters.has_captcha && newsletters.captcha === 'recaptcha' && newsletters.recaptcha_type !== 'v3' && $recaptcha_loaded == false) {
                $recaptcha_element = $form.find('.newsletters-recaptcha-challenge');

                if (typeof grecaptcha !== 'undefined') {
                    var recaptcha_options = {
                        sitekey: newsletters.recaptcha_sitekey,
                        theme: newsletters.recaptcha_theme,
                        //type: 'image',
                        size: (newsletters.recaptcha_type === 'invisible' ? 'invisible' : 'normal'),
                        callback: function () {
                            if (newsletters.recaptcha_type === 'invisible') {
                                $form.submit();
                            }
                        },
                        'expired-callback': function () {
                            if (typeof $recaptcha_id !== 'undefined') {
                                grecaptcha.reset($recaptcha_id);
                            }
                        }
                    };

                    if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.render !== 'undefined') {
                        // Check if this element has already been rendered by looking for child elements
                        if ($recaptcha_element.children().length === 0) {
                            $recaptcha_id = grecaptcha.render($recaptcha_element[0], recaptcha_options);
                            $recaptcha_loaded = true;
                        }
                    }
                }
            }
        }

        var turnstile_callback = function () {
            if (isTurnstile && typeof turnstile !== 'undefined' && !turnstileWidgetId) {
                var el = $form.find('.newsletters-turnstile-challenge')[0];
                if (el) {
                    turnstileWidgetId = turnstile.render(el, {
                        sitekey: el.getAttribute('data-sitekey'),
                        theme: el.getAttribute('data-theme') || 'light',
                        size: 'normal',                 // checkbox-style widget
                        callback: function (token) {
                            // copy Cloudflare’s hidden field to your own, if present
                            $form.find('input[name="cf-turnstile-response"]').val(token);
                        }
                    });
                }
            }
        };
        $(window).on('load', turnstile_callback);
        turnstile_callback();

        $(window).on('load', recaptcha_callback);
        recaptcha_callback();

        $form.trigger('newsletters_subscribe_form_after_create');
        return $form;
    };

    $(function () {
        $('.newsletters-subscribe-form').each(function () {
            $(this).trigger('newsletters_subscribe_form_before_create');
            $(this).newsletters_subscribe_form();
        });
    });
})(jQuery);