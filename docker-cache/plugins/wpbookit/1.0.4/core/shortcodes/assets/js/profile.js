import 'bootstrap';
import flatpickr from "flatpickr";
import { get, post } from './../../../admin/assets/src/utils/ajax';
import notificationToast from '../../../admin/assets/src/utils/notification-toast';
import 'add-to-calendar-button';
import intlTelInput from 'intl-tel-input';

import './../../../admin/assets/src/css/rtl.css';
import '../css/profile.css';
import 'bootstrap/dist/css/bootstrap-grid.min.css';
import {Modal} from 'bootstrap';

class Profile {
    shortcodeElement
    constructor(shortcodeElement) {
        this.shortcodeElement = shortcodeElement
        this.loadProfile();
        this.initFlatpickr();
        this.initEventHandlers();

        this.firstNameRegex = /^[A-Za-z]+$/;
        this.lastNameRegex = /^[A-Za-z]+$/;
        this.emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
        this.phoneRegex = /^\d{10}$/;
        this.pass1Regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;
        this.pass2Regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;
        this.genderRegex = /^(male|female|other)$/i;

        this.showError = this.showError.bind(this);
        this.hideError = this.hideError.bind(this);
        this.validateField = this.validateField.bind(this);
        this.profilePreview = this.profilePreview.bind(this);

        this.errorMessages = {
            firstName: {
                selector: "#first-name-error",
                blank: window.wpbookit.first_name_blank,
                invalid:window.wpbookit.cancel_booking_confirmation
            },
            lastName: {
                selector: "#last-name-error",
                blank: window.wpbookit.last_name_blank,
                invalid:window.wpbookit.first_name_validation
            },
            email: {
                selector: "#email-address-error",
                blank: window.wpbookit.email_blank,
                invalid:window.wpbookit.email_validation
            },
            phone: {
                selector: "#phone-error",
                invalid:window.wpbookit.phone_number_validation
            },
            gender: {
                selector: "#gender-error",
                invalid:window.wpbookit.gender_validation
            },
            pass1: {
                selector: "#pass-1-error",
                blank: window.wpbookit.password_blank,
                invalid:window.wpbookit.password_strength
            },
            pass2: {
                selector: "#pass-2-error",
                blank: window.wpbookit.confirm_password_blank,
                invalid: window.wpbookit.password_strength
            },
        };

        this.customer_country= intlTelInput(shortcodeElement.querySelector('#phone'),{
            containerClass:'wpb-country-container',
            initialCountry: "auto",
            geoIpLookup: (success, failure)=> {
                
                if(shortcodeElement.querySelector("#phone").dataset.iso2){
                    success(shortcodeElement.querySelector("#phone").dataset.iso2)
                }
                
                fetch("https://ipapi.co/json")
                .then((res) =>{ return res.json(); })
                .then((data)=> {
                      this.defaultCountry= data.country_code
                      success(data.country_code);
                  })
                .catch(() =>{ failure(); });
              },
        });
        window['wpbmodal'] = Modal  

    }

    loadProfile() {
        this.flatpickrElement = document.querySelector('#wpb-profile-flatpickr');
        this.submitButton = document.querySelector('#edit-profile-submit');
        this.userProfile = document.querySelector("#profile-image-preview");
        this.avatar = document.querySelector('#avatar');
    }

    initEventHandlers() {
        if (this.avatar) {
            this.avatar.addEventListener('change', this.profilePreview);
        }
        jQuery(document.body).on("click", "#wpb-edit-profile-form #submit-button", this.submitProfileData.bind(this));
        jQuery(this.shortcodeElement).on('click','.show-pass-toggle',(e)=>this.toggle_pass_icon(e))
        jQuery(this.shortcodeElement).on('click','.btn-close-icon-white',(e)=>this.cancleBooking(e))
    }
    toggle_pass_icon(event){
        let InpurGroup = event.currentTarget.closest('.input-group')
        let inputField = jQuery(InpurGroup).find('input')
        let type = inputField.attr('type') === 'password' ? 'text' : 'password';
        inputField.attr('type', type);

        jQuery(InpurGroup).find('.show-pass').toggle()
        jQuery(InpurGroup).find('.hide-pass').toggle()
    }

    profilePreview(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            this.userProfile = document.querySelector(".profile-image-preview");
            reader.onload = (e) => {
                this.userProfile.setAttribute('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    submitProfileData(event) {
        event.preventDefault();
        const form = jQuery(event.currentTarget).closest('form')[0];
        const formData = new FormData(form);
        const _this = this;

        var formFields = {
            'firstName': jQuery("#first_name").val(),
            'lastName': jQuery("#last_name").val(),
            'email': jQuery("#email_address").val(),
            'phone': jQuery("#phone").val(),
            'gender': jQuery("#gender").val(),
            'pass1': jQuery("#pass1").val(),
            'pass2': jQuery("#pass2").val(),
        };

        let isValid = [];
        jQuery.each(formFields, function (fieldName, fieldValue) {
            if (fieldName !== 'pass1' && fieldName !== 'pass2') {
                var error = _this.validateField(
                    fieldValue,
                    _this[fieldName + 'Regex'],
                    _this.errorMessages[fieldName].selector,
                    _this.errorMessages[fieldName].blank,
                    _this.errorMessages[fieldName].invalid
                );
                isValid.push(error);
            }

            if (fieldName === 'pass1' || fieldName === 'pass2') {
                const regex = _this[fieldName + 'Regex'];
                if (fieldValue && !regex.test(fieldValue)) {
                    _this.showError(_this.errorMessages[fieldName].selector, _this.errorMessages[fieldName].invalid);
                    isValid.push(false);
                } else {
                    _this.hideError(_this.errorMessages[fieldName].selector);
                    isValid.push(true);
                }
            }
        });

        // Check if passwords match
        if (formFields['pass1'] !== formFields['pass2']) {
            this.showError(this.errorMessages['pass2'].selector, window.wpbookit.confirm_change_password_not_match);
            isValid.push(false);
        } else {
            this.hideError(this.errorMessages['pass2'].selector);
        }

        // If any field is invalid, stop the form submission
        if (isValid.indexOf(false) !== -1) return false;
        let {dialCode,iso2}= this.customer_country.getSelectedCountryData();
        formData.append('dialCode', dialCode);
        formData.append('iso2', iso2);
        post('edit_profile_data', formData)
            .then(res => {
                const { message, status } = res.data;
                notificationToast.success(message, status, { autoClose: true });
            })
            .catch(error => {
                console.error('Error submitting profile data:', error);
            });
    }

    showError(elementId, errorMessage) {
        jQuery(elementId).text(errorMessage).show();
    }

    hideError(elementId) {
        jQuery(elementId).hide();
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

    initFlatpickr() {
        flatpickr(this.flatpickrElement, {
            dateFormat: 'Y-m-d',
            allowInput: true, 
            defaultDate: null, 
            maxDate:this.flatpickrElement.getAttribute('max'),
            onChange: (selectedDates) => {
                if (selectedDates.length == 2) {
                    this.flatpickrSelectedDate = selectedDates.map((item) => item.toLocaleDateString());
                }
            },
            locale:window.wpbookit.flatpicker
        });
    }

    shadow() {
        setTimeout(function () {
            // Select the shadow host element
            const shadowHost = document.querySelector('add-to-calendar-button');

            // Access the shadow root
            const shadowRoot = shadowHost.shadowRoot;

            // Select the 'atcb-bgoverlay' element within the shadow root
            const atcbBgOverlay = shadowRoot.querySelector('#atcb-bgoverlay');

            // Set the 'part' attribute
            atcbBgOverlay.setAttribute('part', 'atcb-bgoverlay');
        }, 5000)
    }
    cancleBooking(e){
        if (window.confirm(window.wpbookit.cancel_booking)) {
            
            post('cancle_booking_appointment', { id:e.currentTarget.getAttribute('data-id') }).then(res => {
                let { status, message } = res;
                notificationToast[status](message, status.toUpperCase(), { autoClose: true });
                if (status == 'success') {
                    setTimeout(() => {
                        window.location.reload()
                    }, 2000);
                }
            })
        }
        
    }
}

jQuery(function () {
    new Profile(document.querySelector('.wpb-profile-shortcode'))
});
