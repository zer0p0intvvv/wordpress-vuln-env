import {
    get,
    post
} from "../utils/ajax";
import notificationToast from "../utils/notification-toast";
import { Offcanvas } from 'bootstrap';
import FormDataJson from "form-data-json-convert";
import "flatpickr/dist/flatpickr.min.css";
import { createTableStatusSwitch, createAnchorWithImage, createTableActionColumDiv, copyToClipboard, validateForm } from "../utils/helper.js"

export default class Settings {
    offlinePaymentModeOffcanvas;

    constructor() {

        this.addModule = jQuery('#email-options');

        this.EditEventListner();
        this.showdetails();
        this.EnableListner();

        this.offcanvasElement = document.getElementById('email-options');
        this.siteLogo = document.querySelector('#site_logo');
        this.offcanvas = new Offcanvas(this.offcanvasElement);

        this.guestEmailAddressField = document.querySelector('.require-guest-email-address-field');
        this.offlinePaymentModeOffcanvas = document.querySelector('#payment_gateway-options');
        this.offlinePaymentModeOffcanvasInstance = new Offcanvas(this.offlinePaymentModeOffcanvas);

        // Initialize the booking redirect section
        this.initializeBookingRedirectSection();

        this.siteLogoPreview = this.siteLogoPreview.bind(this);

        this.wpbUploadedSiteLogoImageDisplayElement = document.querySelector('#site_logo_image_preview');

        if (this.siteLogo) {
            this.siteLogo.addEventListener('change', (e) => this.siteLogoPreview(e));
        }
        this.addEventHandlers();


        wp.hooks.doAction(
            'setting_panel_class_init',
            this,
            {
                get,
                post,
                notificationToast,
                Offcanvas,
                createTableStatusSwitch,
                createAnchorWithImage,
                createTableActionColumDiv,
                copyToClipboard,
                validateForm
            }
        );

    }

    addEventHandlers() {
        var self = this;
        jQuery(document.body).on("submit", "#general_setting_form", function (event) {
            event.preventDefault();
            let data = FormDataJson.toJson('#general_setting_form');
            self.add_general_setting_ajax(data);
        });

        jQuery(document.body).on("submit", "#wpb-import-booking", function (event) {
            event.preventDefault();
        
            const fileInput = event.currentTarget.querySelector('#wpb_upload_file');
            const file = fileInput.files[0];
        
            // Check if file is selected
            if (!file) {
                jQuery(fileInput).addClass('is-invalid');
                return;
            }
        
            // Validate the file type
            const allowedTypes = ['text/csv', 'application/vnd.ms-excel'];
            const fileType = file.type;
            const fileExtension = file.name.split('.').pop().toLowerCase();
        
            if (!allowedTypes.includes(fileType) || fileExtension !== 'csv') {
                jQuery(fileInput).addClass('is-invalid');
                return;
            }
        
            // If valid, proceed with form submission
            jQuery(fileInput).removeClass('is-invalid');
        
            let submitBtn = jQuery(event.currentTarget).find('[type="submit"]');
            submitBtn.attr('disabled', 'disabled');
            submitBtn.find('.loader').removeClass('d-none');
        
            var formData = new FormData(event.currentTarget);
            post('wpb_import', formData)
                .then(res => {
                    const { status, message } = res;
                    jQuery(event.currentTarget).find('.import-data-log').removeClass('d-none');
                    notificationToast[status](
                        message,
                        status.toUpperCase(),
                        { autoClose: true }
                    );
                    submitBtn.find('.loader').addClass('d-none');
                    submitBtn.removeAttr('disabled');
                });
        });
        jQuery(document.body).on("submit", "#save-woo-payment-gateway-name", function (event) {
            event.preventDefault();
            let submit_btn = event.target.querySelector('[type="submit"]')
            let loader = event.target.querySelector('.spinner')

            loader.classList.remove('d-none')
            submit_btn.setAttribute('disabled', true)

            let data = FormDataJson.toJson(event.target);

            post('save_woocommerce_payment_gateway', { data }).then(res => {
                loader.classList.add('d-none')
                submit_btn.removeAttribute('disabled')
                notificationToast[res.status](res.message, res.status.toUpperCase(), { autoClose: true });
            });
        });


        jQuery(document.body).on("submit", "#theme_settings_form", function (event) {
            event.preventDefault();
            var formElement = document.getElementById('theme_settings_form');
            var formData1 = new FormData(formElement);

            var data = {};
            for (const [key, value] of formData1) {
                if (value != "") {
                    data[key] = value;
                }
            }

            let reqFormData1 = new FormData();
            // Convert the object into key-value pairs and append to FormData
            for (let key in data) {
                if (data.hasOwnProperty(key)) {
                    reqFormData1.append(key, data[key]);
                }
            }
            reqFormData1.append('site_logo', document.querySelector('#site_logo').files[0]);
        });

        jQuery('#wpb-custom-code').on('submit', (e) => {
            e.preventDefault();
            let submit_btn = jQuery(e.originalEvent.submitter);

            submit_btn.find('.spinner').removeClass('d-none');
            submit_btn.attr('disabled', true);
            let formData = FormDataJson.toJson(e.currentTarget);

            post('save_custome_code', formData).then(res => {
                let { status, message } = res.data;
                submit_btn.find('.spinner').addClass('d-none');
                submit_btn.attr('disabled', false);
                notificationToast[status](message, status.toUpperCase(), { autoClose: true });
            });
        });

        jQuery('#wpb-payment-gateway-form').on('submit', (e) => {
            e.preventDefault();

            // Clear previous errors
            jQuery('.error').text('');

            // Get form data
            let paymentModeName = jQuery('#payment_mode_name').val().trim();
            let paymentModeDesc = jQuery('#payment_mode_desc').val().trim();

            // Validation flags
            let isValid = true;

            // Validate Method
            if (paymentModeName === '') {
                jQuery('#payment_mode_name').addClass('is-invalid');
                isValid = false;
            }else{
                jQuery('#payment_mode_name').removeClass('is-invalid');
            }

            // Validate Descriptions
            if (paymentModeDesc === '') {
                jQuery('#payment_mode_desc').addClass('is-invalid');
                isValid = false;
            }else{
                jQuery('#payment_mode_desc').removeClass('is-invalid');
            }

            // If the form is valid, proceed with the submission
            if (isValid) {
                let submit_btn = jQuery(e.originalEvent.submitter);

                submit_btn.find('.spinner').removeClass('d-none');
                submit_btn.attr('disabled', true);

                let formData = FormDataJson.toJson(e.currentTarget);

                post('add_offline_payment_list', formData).then(res => {
                    let { status, message } = res.data;
                    submit_btn.find('.spinner').addClass('d-none');
                    submit_btn.attr('disabled', false);
                    this.offlinePaymentModeDatatable.ajax.reload();
                    this.offlinePaymentModeOffcanvasInstance.hide();
                    notificationToast[status](message, status.toUpperCase(), { autoClose: true });
                });
            }
        });

        // Initialize the event listeners for the booking redirect radios
        this.initializeBookingRedirectRadios();

        jQuery('.wpb-copy-button').on('click', function () {
            var input = jQuery(this).closest('.d-flex').find('input');

            // Create a temporary input element
            var tempInput = jQuery('<input>');
            jQuery('body').append(tempInput);
            tempInput.val(input.val()).select();

            // Copy the value to the clipboard
            document.execCommand('copy');

            // Remove the temporary input element
            tempInput.remove();

            // Show tooltip saying "Copied"
            jQuery(this).tooltip('show');

            // Hide the tooltip after a short delay
            var button = jQuery(this);
            setTimeout(function () {
                button.tooltip('hide');
            }, 1500);
        });

        // Initialize tooltips
        jQuery('[data-toggle="tooltip"]').tooltip({
            trigger: 'manual' // Set the trigger to manual to prevent tooltip from showing on hover
        });

        jQuery("#offline-payments-tab").one('show.bs.tab', e => {
            this.offlinePaymentModeDatatable = new DataTable('#offline-payments-table', {
                "searching": false,
                "order": [
                    [0, 'DESC']
                ],
                "processing": true,
                "serverSide": true,
                "paging": false,
                'info': false,
                "lengthChange": false,
                "ajax": (data, callback, settings) => {
                    if (this.customerFilterSearch) {
                        data = { ...data, ...{ customer_search: this.customerFilterSearch } };
                    }
                    return get('get_payment_gateways_list', data).then(resData => {
                        callback(resData);
                    });
                },
                "columns": [
                    {
                        "data": "id",
                        "searchable": false,
                        "orderable": true
                    },
                    {
                        "data": "name",
                        "searchable": false,
                        "orderable": false
                    },
                    {
                        "data": "desc",
                        "searchable": false,
                        "orderable": false
                    },
                    {
                        "data": "status",
                        "searchable": false,
                        "orderable": false,
                        'render': (data, type, row) => {
                            let div = document.createElement('div');
                            div.classList.add("form-check", "form-switch", "form-status");

                            let input = document.createElement('input');
                            input.classList.add("form-check-input");
                            input.setAttribute('data-id', row.id);
                            input.setAttribute('type', 'checkbox');
                            input.setAttribute('checked', data);

                            input.addEventListener('change', this.payment_mode_change_status);

                            div.append(input);
                            return div;
                        }
                    },
                    {
                        "data": "actions",
                        "render": (data, type, row) => {
                            let edit_anchor = document.createElement('a');
                            edit_anchor.href = '#';
                            edit_anchor.classList.add('me-3');
                            edit_anchor.setAttribute('data-id', row.id);
                            edit_anchor.addEventListener('click', e => {
                                this.edit_payment_mode(row.id);
                            });

                            let edit_img = document.createElement('img');
                            edit_img.src = `${wpbookit.wpb_plugin_url}core/admin/assets/images/edit-icon.svg`;
                            edit_img.alt = 'edit-icon';
                            edit_anchor.append(edit_img);

                            let delete_anchor = document.createElement('a');
                            delete_anchor.href = '#';
                            delete_anchor.classList.add('me-3');
                            delete_anchor.setAttribute('data-id', row.id);
                            let delete_ = _.debounce((e) => this.delete_payment_mode(e), 1500);
                            delete_anchor.addEventListener('click', e => {
                                e.preventDefault();
                                delete_(row.id);
                            });

                            let delete_img = document.createElement('img');
                            delete_img.src = `${wpbookit.wpb_plugin_url}core/admin/assets/images/delete-icon.svg`;
                            delete_img.alt = 'delete-icon';
                            delete_anchor.append(delete_img);

                            let div = document.createElement('div');
                            div.classList.add("d-flex", "align-items-center");

                            div.append(edit_anchor, delete_anchor);
                            return div;
                        },
                        "searchable": false,
                        "orderable": false,
                    }
                ],
                language: window.wpbookit.datatable_language,
            });
        });
        jQuery('#site_logo_preview_btn').on('click', (e) => {
            jQuery('.site_logo_preview').remove();
            jQuery('#old_site_logo').val('');
            jQuery('#site_logo').val('');
            jQuery('#site_logo_image_preview #site_logo_preview_btn').hide();
        });
        // console.log(this.offlinePaymentModeOffcanvas);
        jQuery(this.offlinePaymentModeOffcanvas).on('hidden.bs.offcanvas', (e) => {
            this.offlinePaymentModeOffcanvas.querySelector('#payment_gateway_id').value = '';
            this.offlinePaymentModeOffcanvas.querySelector('#payment_mode_name').value = '';
            this.offlinePaymentModeOffcanvas.querySelector('#payment_mode_desc').value = '';
        });
        jQuery(this.offlinePaymentModeOffcanvas).on('show.bs.offcanvas', (e) => {
            if (e.relatedTarget) {
                jQuery("#edit-offline-payment-mode").addClass('d-none');
                jQuery("#add-offline-payment-mode").removeClass('d-none');

                jQuery("#wpb-submit-payment-mode .wpb-update-btn").addClass('d-none');
                jQuery("#wpb-submit-payment-mode .wpb-add-btn").removeClass('d-none');
            } else {
                jQuery("#edit-offline-payment-mode").removeClass('d-none');
                jQuery("#add-offline-payment-mode").addClass('d-none');

                jQuery("#wpb-submit-payment-mode .wpb-update-btn").removeClass('d-none');
                jQuery("#wpb-submit-payment-mode .wpb-add-btn").addClass('d-none');
            }
        });


        // Example of how to listen for the custom event in another script or plugin
        jQuery(document).on('bookingModelUpdated', function (event, id, bookingData) {

            if (bookingData) {
                var location_source = bookingData.zoom_link;
                if (jQuery('.wpb-booking-meeting-link').length > 0) {
                    jQuery('.wpb-booking-meeting-link').attr('href', location_source).html(location_source);
                }
            }
        });


        // Change Event For Booking Type Change URL
        jQuery(document).on('change', '#link_type_field_input', function () {
            var t_this = jQuery(this);
            var this_parents = t_this.parents('#link_type_field');
            var online_video = this_parents.find('input[name="online_video"]');
            this_parents.find('.zoom-logo').remove();

            var zoom_icon = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" height="26" viewBox="0 0 114 26" width="114"><path d="m23.6977 25.2924h-20.10301c-1.32885 0-2.58954-.6978-3.202853-1.8892-.698493-1.3617-.4429462-2.9956.630343-4.068l13.98692-13.97375h-10.01743c-2.7599 0-4.99167-2.22968-4.99167-4.987h18.5186c1.3288 0 2.5895.69784 3.2028 1.88927.6986 1.36164.443 2.9956-.6303 4.0679l-13.98691 13.97378h11.60181c2.7599 0 4.9917 2.2297 4.9917 4.987zm79.5603-25.2924c-2.879 0-5.4691 1.24249-7.241 3.23389-1.7883-1.9914-4.3781-3.23389-7.2401-3.23389-5.3497 0-9.7108 4.56149-9.7108 9.88887v15.40353c2.7598 0 4.9915-2.2297 4.9915-4.987v-10.46757c0-2.5701 1.9933-4.74871 4.5487-4.85083 2.692-.10213 4.9237 2.05945 4.9237 4.73169v10.58671c0 2.7573 2.2317 4.987 4.9915 4.987v-15.45457c0-2.5701 1.9935-4.74871 4.5485-4.85083 2.692-.10213 4.924 2.05945 4.924 4.73169v10.58671c0 2.7573 2.232 4.987 4.991 4.987v-15.40353c-.017-5.32738-4.378-9.88887-9.727-9.88887zm-54.3805 12.8334c0 7.0806-5.7583 12.8335-12.8455 12.8335-7.0871 0-12.8454-5.7529-12.8454-12.8335 0-7.0805 5.7753-12.8334 12.8454-12.8334 7.0702 0 12.8455 5.7529 12.8455 12.8334zm-4.9917 0c0-4.32315-3.5265-7.8464-7.8538-7.8464-4.3272 0-7.8538 3.52325-7.8538 7.8464 0 4.3233 3.5266 7.8465 7.8538 7.8465 4.3273 0 7.8538-3.5232 7.8538-7.8465zm32.6758 0c0 7.0806-5.758 12.8335-12.8451 12.8335-7.0877 0-12.8458-5.7529-12.8458-12.8335 0-7.0805 5.7757-12.8334 12.8458-12.8334 7.0696 0 12.8451 5.7529 12.8451 12.8334zm-4.9915 0c0-4.32315-3.5264-7.8464-7.8536-7.8464-4.3273 0-7.8541 3.52325-7.8541 7.8464 0 4.3233 3.5268 7.8465 7.8541 7.8465 4.3272 0 7.8536-3.5232 7.8536-7.8465z" fill="#0b5cff"/></svg>`;
            if (jQuery(this).val() == 'zoom') {
                online_video.hide();
                if (this_parents.find('.booking-url').length === 0) {
                    online_video.before(`<span class="zoom-logo d-flex ms-2 align-items-center">${zoom_icon}</span>`);
                } else {
                    this_parents.find('.booking-url').show();
                }
            } else {
                this_parents.find('.zoom-logo').remove();
                online_video.show();
                this_parents.find('.booking-url').hide();
            }
        });

    }


    payment_mode_change_status(e) {
        let id = (e.target.dataset.id - 1);
        post('update_payment_mode_status', { "id": id, 'value': e.target.checked }).then((response) => {
            const { message, status } = response.data;
            notificationToast[status](message, status.toUpperCase(), { autoClose: true });
        }).catch(res => {
            const { message, status } = res.responseJSON.data;
            notificationToast[status](message, status.toUpperCase(), { autoClose: true });

            jQuery(e.target).prop('checked', !e.target.checked).trigger('change');

        }).catch(res => {
        });
    }
    edit_payment_mode(id) {
        this.offlinePaymentModeOffcanvasInstance.show();
        let { name, desc } = this.offlinePaymentModeDatatable.row((id - 1)).data();
        this.offlinePaymentModeOffcanvas.querySelector('#payment_gateway_id').value = id;

        this.offlinePaymentModeOffcanvas.querySelector('#payment_mode_name').value = name;
        this.offlinePaymentModeOffcanvas.querySelector('#payment_mode_desc').value = desc;

    }
    delete_payment_mode(id) {
        get('delete_payment_mode', { "id": id }).then((response) => {
            const { message, status } = response.data;
            notificationToast[status](message, status.toUpperCase(), { autoClose: true });
            this.offlinePaymentModeDatatable.ajax.reload();
        }).catch(res => {
            const { message, status } = res.responseJSON.data;
            notificationToast[status](message, status.toUpperCase(), { autoClose: true });
        });
    }

    siteLogoPreview(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            this.siteLogo = document.querySelector(".site_logo_preview");
            this.siteLogo.classList.remove('d-none');
            reader.onload = (e) => {
                this.siteLogo.setAttribute('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);

            let parentEle = document.createElement('div');
            parentEle.classList.add(['site-logo-image']);

            this.wpbUploadedSiteLogoImageDisplayElement.prepend(parentEle);
            let removeBtn = document.querySelector('#site_logo_preview_btn');
            removeBtn.style.display = 'inline-block';
        }
    }

    add_general_setting_ajax(data) {

        jQuery('.general_setting_sub_button').prop('disabled', true);
        jQuery('.wpb-general-settings-submit-svg').removeClass('d-none');

        post('add_general_setting', data)
            .then(response => {
                const { message, status } = response.data;
                jQuery('.general_setting_sub_button').prop('disabled', false);
                jQuery('.wpb-general-settings-submit-svg').addClass('d-none');
                notificationToast[status](message, status.toUpperCase(), { autoClose: true });
            })
            .catch(error => {
                jQuery('.general_setting_sub_button').prop('disabled', false);
                jQuery('.wpb-general-settings-submit-svg').addClass('d-none');
                console.error('Error :', error);
            });
    }
    EditEventListner() {
        var self = this;
        jQuery(document.body).on("submit", ".wpb_email_options_form", function (event) {
            event.preventDefault();
            jQuery('#email-options').addClass('wpb-side-panel-loder');
            jQuery('.email_sub_button').prop('disabled', true);
            jQuery('.wpb-email-submit-svg').removeClass('d-none');
            var formData = new FormData(this);
            var data = [];
            data['heading'] = formData.get('email_heading');

            if (typeof tinymce !== 'undefined' && tinymce.get('emails_content')) {
                var content = tinymce.get('emails_content').getContent(); // Get HTML content
                data['content'] = content;
            } else {
                // Fallback to plain text content (not recommended for HTML)
                data['content'] = formData.get('emails_content');
            }
            data['email_id'] = formData.get('email_id');
            var reminderValue = formData.get('email_reminder');
            data['reminder'] = reminderValue.trim() === '' ? null : reminderValue;
            self.edit_email_ajax(data);
        });
    }

    showdetails() {
        var self = this;
        jQuery(document.body).on("click", "a[name='advance-email-options']", function (event) {
            var data = [];
            jQuery('#email-options').addClass('wpb-side-panel-loder');
            data['id'] = jQuery(this).attr("data-id");
            self.get_email_ajax(data);
        });
    }

    EnableListner() {
        var self = this;
        var checkboxes = document.querySelectorAll('[id="data-status"]');

        if (checkboxes) {
            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('click', function () {
                    var data = [];
                    data['email_id'] = this.getAttribute('data-id');
                    console.log(data['email_id']);
                    data['status'] = this.checked ? 1 : 0;
                    self.update_email_status(data);
                });
            });
        }
    }

    edit_email_ajax(data) {
        post('edit_email_details', data).then(response => {
            const resStatus = response.status;
            if (resStatus === "success") {
                const emailDetail = response.data;

                var trElement = jQuery('tr[data-id="' + emailDetail.email_id + '"]');

                trElement.find('h6[name="recipient"]').text(emailDetail.recipient);
                jQuery('.email_sub_button').prop('disabled', false);
                jQuery('.wpb-email-submit-svg').addClass('d-none');
                jQuery('#email-options').removeClass('wpb-side-panel-loder');
                this.closeOffcanvas();
                notificationToast[resStatus](response.message, resStatus.toUpperCase(), { autoClose: true });
            } else {
                jQuery('.email_sub_button').prop('disabled', false);
                jQuery('.wpb-email-submit-svg').addClass('d-none');
                notificationToast[resStatus](response.message, resStatus.toUpperCase(), { autoClose: true });
            }
        }).catch(error => {
            console.error('Error fetching email details:', error);
            jQuery('.email_sub_button').prop('disabled', false);
            jQuery('.wpb-email-submit-svg').addClass('d-none');
        });

    }


    get_email_ajax(data) {
        post('get_email_details', data).then(response => {
            var emailDetails = response.data[0];

            if (emailDetails.email_dynamic_keys.length !== undefined && emailDetails.email_dynamic_keys.length > 0) {
                this.createButtons(emailDetails.email_dynamic_keys);
            }

            var mergedValue = emailDetails.emails_title + ' - ' + emailDetails.role;
            document.getElementById('email_subject').value = emailDetails.emails_subject;
            document.querySelector('input[name="email_id"]').value = emailDetails.id;

            var emails_content = emailDetails.emails_content;
            var editor = tinymce.get('emails_content');
            editor.setContent(emails_content);

            if (emailDetails.is_reminder == '1') {
                document.querySelector('select[name="email_reminder"]').value = emailDetails.reminder;
                jQuery(document.querySelector('select[name="email_reminder"]')).change();
                jQuery('.reminder-div').show();
            } else {
                jQuery('.reminder-div').hide();
            }
            var emailTypeSelect = document.getElementById('email_type');
            var emailTypeOptions = emailTypeSelect.options;
            for (var i = 0; i < emailTypeOptions.length; i++) {
                if (emailTypeOptions[i].value === emailDetails.type) {
                    emailTypeOptions[i].selected = true;
                    break;
                }
            }

            jQuery('#email-options').removeClass('wpb-side-panel-loder');

        }).catch(error => {
            console.error('Error fetching email details:', error);
        });
    }

    update_email_status(data) {
        post('email_status_update', data).then(response => {
            const resStatus = response.status;
            notificationToast[resStatus](response.message, resStatus.toUpperCase(), { autoClose: true });
        }).catch(error => {
            console.error('Error fetching email details:', error);
        });
    }

    closeOffcanvas() {
        this.offcanvas.hide();
    }



    toggleBookingRedirectSection(bookingRedirect) {
        var bookingRedirectSection = document.getElementById('booking_redirect_section');
        if (bookingRedirect && bookingRedirect.value === 'specific_page') {
            bookingRedirectSection.classList.remove('d-none');
        } else {
            bookingRedirectSection.classList.add('d-none');
        }
    }

    initializeBookingRedirectRadios() {
        var self = this;
        var bookingRedirectRadios = document.querySelectorAll('.booking_redirect');

        // Initial check for all radio buttons
        bookingRedirectRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                self.toggleBookingRedirectSection(radio);
            });
        });

        // Trigger change event on page load to set the initial state
        var checkedRadio = document.querySelector('.booking_redirect:checked');
        if (checkedRadio) {
            self.toggleBookingRedirectSection(checkedRadio);
        }
    }

    initializeBookingRedirectSection() {
        // Ensure the initial state is set correctly
        this.initializeBookingRedirectRadios();
    }

    createButtons(email_dynamic_keys) {
        var self = this;
        var container = document.getElementById('email_dynamic_keys');
        container.innerHTML = '';

        email_dynamic_keys.forEach(function (key) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-primary btn-sm mb-1 me-1';
            button.setAttribute('data-toggle', 'tooltip');
            button.setAttribute('title', 'Copied');
            button.value = key;
            button.textContent = key;
            button.addEventListener('click', function () {
                self.copyToClipboard(key);
            });
            container.appendChild(button);
        });
    }

    copyToClipboard(text) {
        const el = document.createElement('textarea');
        el.value = text;
        document.getElementById('email-options').appendChild(el);
        el.select();
        document.execCommand('Copy');
        document.getElementById('email-options').removeChild(el);
        jQuery(this).tooltip('show');
        var button = jQuery(this);
        setTimeout(function () {
            button.tooltip('hide');
        }, 1500);
    }

    initializeFlatpickr() {
        const _this = this;
        var months = window.wpbookit.flatpicker.months.longhand;
        var weekDays = window.wpbookit.flatpicker.weekdays.shorthand;

        _this.calendarContainer.empty();
        _this.yearLabel.text(_this.year);

        months.forEach(function(month, index) {
            var parentDiv = jQuery('<div>', { class: 'col' }),
                monthDiv = jQuery('<div>', { class: 'month' }),
                header = jQuery('<div>', { class: 'month-header', text: month }),
                weekDaysList = jQuery('<div>', { class: 'week-days' }),
                daysContainer = jQuery('<div>', { class: 'days' }),
                firstDay = new Date(_this.year, index, 1).getDay(),
                lastDate = new Date(_this.year, index + 1, 0).getDate();

            // Example usage:
            const yesterday = new Date();
            const today = yesterday.setDate(yesterday.getDate() - 1)

            // WeekDays
            for (let i = 0; i < weekDays.length; i++){
                weekDaysList.append(jQuery('<div>', { class: 'week-day', text: weekDays[i] }));
            }

            // Fill in empty days at the start
            for (var i = 0; i < firstDay; i++) {
                daysContainer.append(jQuery('<div>', { class: 'day not-available' }));
            }

            // Add days of the month
            for (var day = 1; day <= lastDate; day++) {
                var fullDate = _this.year + '-' + String(index + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                var dayDiv = jQuery('<div>', {
                    class: new Date(fullDate) < today  ? 'day date-over' : 'day',
                    text: day,
                    'data-date': fullDate
                });
                daysContainer.append(dayDiv);
            }

            parentDiv.append(monthDiv)
            monthDiv.append(header).append(weekDaysList).append(daysContainer);
            jQuery(_this.calendarContainer).append(parentDiv);
        });
    }


    updateCalendarWithStoredDates() {
        const _this = this;
        this.calendarContainer.find('.day').each(function() {
            const dayElement = jQuery(this);
            const date = dayElement.data('date');
            if (_this.storedDates[_this.year] && _this.storedDates[_this.year].has(date)) {
                dayElement.addClass('selected');
            } else {
                dayElement.removeClass('selected');
            }
        });
    }
}
