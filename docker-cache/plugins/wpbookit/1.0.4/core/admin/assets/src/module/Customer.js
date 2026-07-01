import notificationToast from "../utils/notification-toast";
import { get, post } from "../utils/ajax";
import flatpickr from "flatpickr";
import { Offcanvas } from 'bootstrap';
import intlTelInput from "intl-tel-input";
import importModule from "../components/importModule";


export default class Customer {
    customerDatatable;
    customerFilterSearch;
    constructor() {

        const urlParams = new URLSearchParams(window.location.search);
        const start = urlParams.get('start');
        const end = urlParams.get('end');  
        console.log("Start : ",start);
        console.log("end : ",end);
        this.addModule      = jQuery('#new-customer');
        this.genderSelect   = jQuery('#gender');
        this.toastSelect    = jQuery('#table-main .toast');
        this.tablecustomer  = jQuery('table.customer tbody');
        this.mainContent    = jQuery('.main-content');
        this.submitnbtn     = jQuery('#add-customer-form #submit-customer-button');
       
        this.eventHandlers();
        this.initFlatpickr();

        this.firstNameRegex     = /^[A-Za-z\s]+$/;
        this.lastNameRegex      = /^[A-Za-z\s]+$/;
        this.emailRegex         = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
        this.phoneNumberRegex   = /^\d+$/;

        this.showError      = this.showError.bind(this);
        this.hideError      = this.hideError.bind(this);
        this.validateField  = this.validateField.bind(this);
        jQuery('#phone').on('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        // Error messages
        this.errorMessages = {
            firstName: {
                selector: "#first-name-error",
                blank: window.wpbookit.dashbord_language.validation.first_name_required,
                invalid: window.wpbookit.dashbord_language.validation.first_name_invalid,
            },
            lastName: {
                selector: "#last-name-error",
                blank: window.wpbookit.dashbord_language.validation.last_name_required,
                invalid: window.wpbookit.dashbord_language.validation.last_name_invalid,
            },
            email: {
                selector: "#email-error",
                blank: window.wpbookit.dashbord_language.validation.email_required,
                invalid: window.wpbookit.dashbord_language.validation.email_invalid,
            },
            phoneNumber: {
                selector: "#phone-error",
                invalid: window.wpbookit.dashbord_language.validation.phone_invalid,
            }
        };
        this.offcanvasElement = document.getElementById('new-customer');
        this.offcanvas = new Offcanvas(this.offcanvasElement);
        //this.opencanvas();

        jQuery(this.offcanvasElement).on("hidden.bs.offcanvas", () => {
            jQuery('.error-message').html('')
            this.customer_country.setCountry(this.defaultCountry)
        });


        this.customerDatatable = new DataTable('table', {
            "searching": false,
            "processing": true,
            "serverSide": true,
            "order": [
                [0, 'DESC']
            ],
            "paging": true,
            "lengthChange": false,
            "ajax": (data, callback, settings) => {
                if (this.customerFilterSearch) {
                    data = { ...data, ...{ customer_search: this.customerFilterSearch } }
                }
                return get('get_customer_list', data).then(resData => {
                    callback(resData)
                })
            },
            "columns": [
                {
                    "data": "id",
                    "name": "customer-id",
                    "searchable": false
                },
                {
                    "data": "name",
                    "render": function (data, type, row) {
                        return ` <div class="d-flex align-items-center gap-3">
                            <img class="rounded-pill img-fluid avatar-40" src="${row.profile_img}" alt="" loading="lazy">
                            <div class="media-support-info">
                                <h6 class="iq-sub-label">${data}</h6>
                                <p class="mb-0">${row.email}</p>
                            </div>
                        </div>`;

                    },
                    "name": 'customer-name',
                    "searchable": false
                },
                {
                    "data": "dob",
                    "name": 'customer-dob',
                    "searchable": false

                },
                {
                    "data": "phone",
                    "name": 'customer-phone',
                    "searchable": false,
                    "orderable": false,
                    "render": function (data, type, row) {
                        if( data != '-' )
                            if(row.dialCode)
                                return "+"+row.dialCode+" "+data; 

                        return data;
                    },
                },
                {
                    "data": "gender",
                    "name": 'customer-gender',
                    "searchable": false,
                    "orderable": false,
                    "render": function (data) {
                        return `<span class="text-capitalize">${data}</span>`;
                    }

                },
                {
                    "data": "user_registred",
                    "name": 'customer-date-time',
                    "searchable": false,
                },
                {
                    "data": "actions",
                    "render": function (data, type, row) {

                        let render = '';

                        render += ` <a href="#" data-id="${row.id}" class="edit-customer-button">
                                        <img src="${wpbookit.wpb_plugin_url}core/admin/assets/images/edit-icon.svg" alt="checked">
                                    </a>`;

                        render += `<a class=" delete-customer-button" href="#" data-id="${row.id}" data-name="${row.name}">
                                        <img src="${wpbookit.wpb_plugin_url}core/admin/assets/images/delete-icon.svg" alt="checked">
                                   </a>`;

                        return `<div class="d-flex align-items-center gap-3">${render}</div>`;
                    },
                    "searchable": false,
                    "orderable": false,
                },
            ],

            language: window.wpbookit.datatable_language
        });
        jQuery('.offcanvas-end .btn-close').on('click', this.clearErrors.bind(this));

        this.customer_country= intlTelInput(document.querySelector('#phone'),{
            containerClass:'wpb-country-container',
            initialCountry: "auto",
            geoIpLookup: (success, failure)=> {
                fetch("https://ipapi.co/json")
                .then((res) =>{ return res.json(); })
                .then((data)=> {
                      this.defaultCountry= data.country_code
                      success(data.country_code);
                  })
                .catch(() =>{ failure(); });
              },
        });

        new importModule(document.querySelector('#wpb-customer-import'),this.customerDatatable.ajax.reload)
    }
    clearErrors() {
        // Iterate over the error messages and hide them
        for (const key in this.errorMessages) {
            if (this.errorMessages.hasOwnProperty(key)) {
                this.hideError(this.errorMessages[key].selector);
            }
        }
        this.hideError('#dob-error');
        this.hideError('#gender-error');
    }
    search_filter(e) {
        this.customerFilterSearch = e.target.value;
        this.customerDatatable.ajax.reload()

    } 
    eventHandlers() {
        jQuery(document.body).on("click", "#add-customer-form #submit-button", this.SubmitbtnEventHandlers.bind(this));
        jQuery(document.body).on("click", ".edit-customer-button", this.EditeventHandlers.bind(this));
        jQuery(document.body).on("click", ".offcanvas-end .btn-close", this.PopupCloseEvent.bind(this));
        jQuery(document.body).on("change", "#edit-image", this.ChangeImageeventHandlers.bind(this));
        jQuery(document.body).on("click", "#offcanvas #remove-btn", this.EditpopupRemoveeventHandlers.bind(this));
        jQuery(document.body).on("click", ".delete-customer-button", this.DeletePopupeventHandlers.bind(this));
      

        // Add customer jQuery
        jQuery(document.body).on("click","#add-customer-click", this.Addcustomerbtn.bind(this));
        jQuery(document.body).on("change", "#new-customer #add-image", this.AddImageeventHandlers.bind(this));
        jQuery(document.body).on("click", "#new-customer #remove-btn", this.AddpopupRemoveeventHandlers.bind(this));
        this.flatpickrElement = document.querySelector('#wpb-range-flatpicker');
        this.flatpickrSubmitElement = document.querySelector('#flatpickr-submit');
        this.calendarIcon = document.querySelector('#wpb-calendar-icon');
        this.calendarIcon.style.cursor = 'pointer';
        this.initFlatpickr();
        this.addEventListeners();  

        jQuery('.dt-search').on("input", _.debounce(e => {
            this.search_filter(e)
        }, 500));
            
        jQuery(document.querySelector('#new-customer')).on("hidden.bs.offcanvas", ()=>{
            jQuery('.error-message').html('')
        });
    }
    addEventListeners() {
        let call = false;
        if (this.calendarIcon) {
            this.calendarIcon.addEventListener('click', () => {
                if (!call) {
                    this.flatpickrElement._flatpickr.open(); // Open the date picker
                    call = true;
                }else{
                    this.flatpickrElement._flatpickr.close(); // Close the date picker
                    call = false;
                }

            });
        }
    }
    initFlatpickr() {
        if (this.flatpickrElement._flatpickr) {
            this.flatpickrElement._flatpickr.destroy();  // Destroys any existing instance
        }
        
        flatpickr(this.flatpickrElement, {
            dateFormat: 'Y-m-d',
            maxDate: 'today',
            allowInput: false,
            onChange: (selectedDates, dateStr, instance) => {
                if (selectedDates.length === 2) {
                    this.flatpickrSelectedDate = selectedDates.map((item) => item.toLocaleDateString());
                }
            },
            locale:window.wpbookit.flatpicker
        });
    }
    exporteventHandlers(e) {
        e.preventDefault();
        var urlParams = new URLSearchParams(window.location.search);
        var tab = urlParams.get('tab');
    
        post('export_user_table', { 'tab': tab }).then(response => {
            // Create a Blob object containing the CSV data
            var blob = new Blob([response], { type: 'text/csv' });

            // Create a download link
            var downloadLink = document.createElement('a');
            downloadLink.href = window.URL.createObjectURL(blob);
            var filename = 'customer.csv';
            downloadLink.download = filename;

            // Append the link to the body and click it to trigger download
            document.body.appendChild(downloadLink);
            downloadLink.click();

            // Clean up
            document.body.removeChild(downloadLink);
        });
    }

   
    SubmitbtnEventHandlers(e) {
        e.preventDefault();
        const _this = this;
        const __this = jQuery(e.currentTarget);
        __this.prop('disabled', false);
        __this.find('.wpb-customer-submit-svg').addClass('d-none');
        console.log(__this);
        const user_id = jQuery('input[name="edit-customer-id"]').val();
        
        var formFields = {
            'firstName' : jQuery("#first-name").val(),
            'lastName'  : jQuery("#last-name").val(),
            'email' : jQuery("#email").val(),
            'phoneNumber'   : jQuery("#phone").val(),
            'gender': jQuery("#gender").val()  // Capture gender field
        }
        
        let isValid = [];
        jQuery.each(formFields, function(fieldName, fieldValue) {
            if (fieldName !== 'gender') { // Exclude gender from the existing validation loop
                var error = _this.validateField(
                    fieldValue,
                    _this[fieldName + 'Regex'],
                    _this.errorMessages[fieldName].selector,
                    _this.errorMessages[fieldName].blank,
                    _this.errorMessages[fieldName].invalid
                );
                isValid.push(error);
            }
        });

        const phoneNumberError = this.validateField(
            formFields['phoneNumber'],
            this.phoneNumberRegex,
            this.errorMessages['phoneNumber'].selector,
            '',
            this.errorMessages['phoneNumber'].invalid
        );
        isValid.push(phoneNumberError);
     
        const phoneNumberField = formFields['phoneNumber'];
        const phoneNumberErrorElement = jQuery(this.errorMessages['phoneNumber'].selector);

        if (!this.phoneNumberRegex.test(phoneNumberField)) {
            phoneNumberErrorElement.text(this.errorMessages['phoneNumber'].invalid).show();
            isValid.push(false);
        } else {
            phoneNumberErrorElement.text('').hide();
            isValid.push(true);
        }

        if (isValid.indexOf(false) !== -1) {
            __this.prop('disabled', false);
            __this.find('.wpb-customer-submit-svg').addClass('d-none');
            return false;
        }
        
        if (!user_id) {
            if (isValid.indexOf(false) !== -1){
                __this.prop('disabled', false);
                __this.find('.wpb-customer-submit-svg').addClass('d-none');
                return false;
            }
            __this.prop('disabled', true);
            __this.find('.wpb-customer-submit-svg').removeClass('d-none');
    
            const form = jQuery(e.currentTarget).closest('form')[0];
            const formData = new FormData(form);
            formData.append('_ajax_nonce', wpbookit.nonce);
            let {dialCode,iso2}= this.customer_country.getSelectedCountryData();
            formData.append('dialCode', dialCode);
            formData.append('iso2', iso2);

            post('add_new_customer', formData)
            .then(response => {
                const resStatus = response.status;
                if(resStatus === 'success'){
                    notificationToast[resStatus](response.message, resStatus.toUpperCase(), { autoClose: true });  
                    this.addModule.offcanvas('hide');
                    this.RefreshTableeventHandlers();
                }else{
                    notificationToast[resStatus](response.message, resStatus.toUpperCase(), { autoClose: true });
                }
                __this.prop('disabled', false);
                __this.find('.wpb-customer-submit-svg').addClass('d-none');
            })
            .catch(error => {
                __this.prop('disabled', false);
                __this.find('.wpb-customer-submit-svg').addClass('d-none');
                console.error('Error :', error);
            });
        } else {
            if (isValid.indexOf(false) !== -1){
                __this.prop('disabled', false);
                __this.find('.wpb-customer-submit-svg').addClass('d-none');
                return false;
            }
    
            const __this = jQuery(e.currentTarget);
            const form = __this.closest('form')[0];
            const formData = new FormData(form);
            formData.append('_ajax_nonce', wpbookit.nonce);

            let {dialCode,iso2}= this.customer_country.getSelectedCountryData();
            formData.append('dialCode', dialCode);
            formData.append('iso2', iso2);

            post('edit_newdata_customer', formData)
            .then(response => {
                const resStatus = response.status;
                if(resStatus === 'success'){
                    notificationToast[resStatus](response.message, resStatus.toUpperCase(), { autoClose: true });
                    this.addModule.offcanvas('hide');
                    this.RefreshTableeventHandlers();
                    this.addModule.find('input[type=text], textarea').val('');
                    this.addModule.find('select').val('').trigger('change');
                    this.addModule.find('#add-image-preview').attr('src', '');
                    const imgsrc = this.addModule.find('#add-image-preview').attr('data-attr');
                    this.addModule.find('#add-image-preview').attr('src', imgsrc);
                }else{
                    notificationToast[resStatus](response.message, resStatus.toUpperCase(), { autoClose: true });
                }
                __this.prop('disabled', false);
                __this.find('.wpb-customer-submit-svg').addClass('d-none');
            })
            .catch(error => {
                __this.prop('disabled', false);
                __this.find('.wpb-customer-submit-svg').addClass('d-none');
                console.error('Error :', error);
            });
        }
    }    

    Addcustomerbtn(e) {
        e.preventDefault();
        this.addModule.find('#new-customer-label').text('Add New Customer');
        this.addModule.find('#submit-customer-button').text('Save');
        this.addModule.find('#edit-customer-id').val('');
        this.addModule.find('select').val('').trigger('change');
        this.addModule.find('input[type=text], textarea').val('');
        this.addModule.find('#add-customer-form')[0].reset();
        const imgsrc = this.addModule.find('#add-image-preview').attr('data-attr');
        this.addModule.find('#add-image-preview').attr('src', imgsrc);
        this.addModule.find('#remove-btn').addClass('d-none');
    }
    
    RefreshTableeventHandlers() {
        this.customerDatatable.ajax.reload()
    }

    getUrlParameter(name) {
        name = name.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');
        const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        const results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    EditeventHandlers(e) {
        e.preventDefault();
        const __this = jQuery(e.currentTarget);
        const editButton = __this.closest('.edit-customer-button');
        const customer = this.customerDatatable.row(editButton.closest('tr')).data();
        let {dialCode,iso2}= this.customer_country.getSelectedCountryData();
        if(customer.iso2!=undefined && customer.iso2.length!=0){
            this.customer_country.setCountry(customer.iso2)
        }else{
            this.customer_country.setCountry(iso2)
        }
        const __tablegrandparent = __this.closest('.customer');
        const user_id            = __this.data('id');
        const trName             = customer['name'];
        const [firstName, lastName] = trName.split(' ');
        const tdEmail            = customer['email'] ;
        const tdPhone            = customer['phone'];
        const tdGender           = customer['gender'];
        const tdnotes            = customer['custom_note'];
        const avatarSrc          = customer['profile_img'];
        const tddob              = customer['tddob'];
        //this.addModule.addClass('show');
        this.offcanvas.show();
        const fname = firstName ? firstName.trim() : '';
        const lname = lastName ? lastName.trim() : '';
        const email = tdEmail ? tdEmail.trim() : '';
        const phone = tdPhone ? tdPhone.trim() : '';
        const gender = tdGender ? tdGender.trim() : '';
        const notes = tdnotes ? tdnotes.trim() : '';
        const dob = tddob ? tddob.trim() : '';
        this.addModule.find('#wpb-range-flatpicker')
        .val(dob) // Set the value directl
        .flatpickr({
            dateFormat: 'Y-m-d',
            defaultDate: dob,
            maxDate: 'today',
            allowInput: true, 
            locale:window.wpbookit.flatpicker
        });
        
       // const sgender = gender.toLowerCase();
        
        this.addModule.find('#first-name').val(fname);
        this.addModule.find('#last-name').val(lname);
        this.addModule.find('#email').val(email);
        this.addModule.find('#phone').val(phone);
        this.addModule.find('#notes').val(notes);
        this.addModule.find('#edit-customer-id').val(user_id);
        this.addModule.find('#add-image-preview').attr('src', avatarSrc);
        const sgender = gender.toLowerCase();
        this.genderSelect.val(sgender).trigger('change');
        if(customer['is_customer_image']){
            this.addModule.find('#remove-btn').removeClass('d-none');
        }else{
            this.addModule.find('#remove-btn').addClass('d-none');
        }
        this.addModule.find('#new-customer-label').text('Edit Customer ' + fname +' ' + lname );
        this.addModule.find('#submit-customer-button').text('Update');
    }

    PopupCloseEvent() {
        jQuery('.offcanvas-backdrop').removeClass('show')
        this.addModule.removeClass('show');
        this.addModule.find('input[type=text], textarea').val('');
        this.addModule.find('select').val(null).trigger('change');
        const imgsrc = this.addModule.find('#add-image-preview').attr('data-attr');
        this.addModule.find('#add-image-preview').attr('src', imgsrc);
        this.clearErrors(); // Clear errors when popup is closed
    }

    ChangeImageeventHandlers(e) {
        e.preventDefault();
        const __this = jQuery(e.currentTarget);
        const preview_img = this.addModule.find('.img-fluid');
        if (__this.prop('files') && __this.prop('files')[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview_img.attr('src', event.target.result);
            };
            reader.readAsDataURL(__this.prop('files')[0]);
        }
        else {
            // Clear the src attribute when no file is selected
            var imgsrc = preview_img.attr('data-attr');
            preview_img.attr('src', imgsrc);
        }
    }

    AddImageeventHandlers(e) {
        e.preventDefault();

        const allowedExtensions = ["jpg", "jpeg", "png", "gif"]; // List of allowed extensions
        const file = e.target.files[0]; // Get the selected file
        const errorMessage = document.getElementById("image-preview-error");

        // Clear previous error messages
        errorMessage.textContent = "";

        if (file) {
            const fileExtension = file.name.split(".").pop().toLowerCase(); // Get the file extension

            // Check if the file extension is in the allowed list
            if (!allowedExtensions.includes(fileExtension)) {
                errorMessage.textContent =  window.wpbookit.dashbord_language.validation.invalid_file_type
                e.target.value = ""; // Clear the input value
                return;
            }
        }

        const __this = jQuery(e.currentTarget);
        const preview_img = this.addModule.find('#add-image-preview');
        const remove_btn = this.addModule.find('#remove-btn');
    
        if (__this.prop('files') && __this.prop('files')[0]) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview_img.attr('src', event.target.result);
                remove_btn.removeClass('d-none');
            };
            reader.readAsDataURL(__this.prop('files')[0]);
        } else {
            var imgsrc = preview_img.attr('data-attr');
            preview_img.attr('src', imgsrc);
            remove_btn.addClass('d-none');
        }
        remove_btn.one('click', function() {
            remove_btn.addClass('d-none');
            var imgsrc = preview_img.attr('data-attr');
            preview_img.attr('src', imgsrc);
        });
    }      

    validateField(value, regex, errorSelector, blankErrorMessage, invalidErrorMessage) {
        if(blankErrorMessage===undefined && (value=='' || value== null) ){
            return true;
        }
        if (!value) {
            this.showError(errorSelector, blankErrorMessage);
            return false;
        } else if (!regex.test(value)) {
            this.showError(errorSelector, invalidErrorMessage);
            return false;
        } else {
            this.hideError(errorSelector);
            return true;
        }
    }

    showError(elementId, errorMessage) {
        jQuery(elementId).text(errorMessage).show();
    }

    hideError(elementId) {
        jQuery(elementId).hide();
    }

    popupRemoveEventHandlers(module, imageId) {
        const imgsrc = module.find(imageId).attr('data-attr');
        module.find(imageId).attr('src', imgsrc);
        module.find('input[type="file"]').val(''); 
    }

    EditpopupRemoveeventHandlers(e) {
        this.popupRemoveEventHandlers(this.addModule, '#image-preview');
    }

    AddpopupRemoveeventHandlers(e) {
        this.popupRemoveEventHandlers(this.addModule, '#add-image-preview');
    }

   

    delete_user_ajax(delete_user_id) {
        post('delete_customer', { 'delete_user_id': delete_user_id }).then(response => {
            const resStatus = response.status;
            notificationToast[resStatus]( response.message, resStatus.toUpperCase(), { autoClose: true});     
            this.RefreshTableeventHandlers();
        });
    }
    DeletePopupeventHandlers(e) {
        e.preventDefault();
        const __this = jQuery(e.currentTarget);
        const __tablegrandparent = __this.closest('.customer');
        const customerName = __this.data('name');
        var urlParams = new URLSearchParams(window.location.search);
        var tab = urlParams.get('tab');
    
        // Using wp.i18n for translations
        const { __ } = wp.i18n;
    
        var cMessage = window.wpbookit.dashbord_language.customer.confirm_delete_boooking + customerName + "?";
        if (confirm(cMessage) == true) {
            const user_id = __this.data('id');
            this.delete_user_ajax(user_id);
        }
    }

}
