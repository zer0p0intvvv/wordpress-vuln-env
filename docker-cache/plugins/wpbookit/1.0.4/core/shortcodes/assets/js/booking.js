import './../css/booking.css'
import './../../../admin/assets/src/css/rtl.css';
import { get, post } from './../../../admin/assets/src/utils/ajax';
import flatpickr from "flatpickr";
import 'flatpickr/dist/flatpickr.min.css';
import { Collapse, Modal, Offcanvas, Tab } from 'bootstrap';
import notificationToast from '../../../admin/assets/src/utils/notification-toast';
import 'add-to-calendar-button';
import FormDataJson from 'form-data-json-convert';
class Booking_Shotcode {

    shortCodeElement
    bookingTypeId
    bookingTypeAvailableDays
    bookingSpecificDay

    bookingModelInstance
    bookingFormElement

    bookingTimeSlot
    bookingDate

    bookingMaxPreBookingDays
    bookingMaxPostBookingDays

    bookingModelCloseBtn
    bookingModelSubmitBtn

    bookingShortcodeNextBtn

    tabs
    booking_detail_collapse_instance
    currentActiveTab = 0
    
    post;
    get;
    FormDataJson;

    days = {
        "0": "sunday",
        "1": "monday",
        "2": "tuesday",
        "3": "wednesday",
        "4": "thursday",
        "5": "friday",
        "6": "saturday",
    }



    constructor(element) {
        this.shortCodeElement = element
        this.init()
        this.addEventListener()

        this.post= post
        this.get= get
        this.FormDataJson= FormDataJson

        window['wpbNotificationToast'] = notificationToast;
        window['wpbOffcanvas'] = Offcanvas;
        window['wpbModal'] = Modal
        this.phoneNumberRegex = /^[\d+]+$/;
        jQuery('#wpb_user_phone_number').on('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9+]/g, ''); 
        });
    }
    get2DigitFmt(val) {
        return ('0' + val).slice(-2);
    }
    async init() {

        this.bookingTypeId = this.shortCodeElement.querySelector('.wpb-booking-type-id')?.value
        if (this.bookingTypeId == undefined) {
            this.bookingConfirm();
            return;
        }
        
        document.querySelector('.add-btn-close').addEventListener('click', this.RemoveErrorShortCode);
        document.querySelector('.wpb-close-model-btn').addEventListener('click', this.RemoveErrorShortCode);
        this.bookingTypeAvailableDays = JSON.parse(this.shortCodeElement.querySelector('.wpb-booking-available-days').value)
        this.bookingSpecificDay = JSON.parse(this.shortCodeElement.querySelector('.wpb-booking-sepcific-date-days').value ?? {})

        this.bookingMaxPostBookingDays = this.shortCodeElement.querySelector('.wpb-booking-max-post-booking-days').value;

        this.bookingModelCloseBtn = this.shortCodeElement.querySelector('.wpb-close-model-btn')
        this.bookingModelSubmitBtn = this.shortCodeElement.querySelector('.wpb-submit-model-btn')


        let flatargs = {
            inline: true,
            allowInput: true,
            formats: 'd-m-Y',
            onChange: (selectedDates, dateStr, instance) => {
                this.OnSelectDate(selectedDates, dateStr, instance)
            },
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                if (!dayElem.classList.contains('flatpickr-disabled')) {
                    if (this.days[dayElem.dateObj.getDay()] in this.bookingTypeAvailableDays && dayElem.dateObj <= new Date().fp_incr(this.bookingMaxPostBookingDays)) {
                        dayElem.classList.add('wpb-available');
                    } else {
                        dayElem.classList.add('flatpickr-disabled');
                    }
                }
                let formattedDate = dayElem.dateObj.getFullYear() + '-' + this.get2DigitFmt(dayElem.dateObj.getMonth() + 1) + '-' + this.get2DigitFmt(dayElem.dateObj.getDate());
                if (this.bookingSpecificDay[formattedDate]) {
                    dayElem.classList.add('wpb-available');
                    dayElem.classList.remove('flatpickr-disabled');
                    if(!(dayElem.dateObj <= new Date().fp_incr(this.bookingMaxPostBookingDays))){
                        dayElem.classList.add('flatpickr-disabled');
                    }
                }

            },
            onReady: () => {
                let year_input = jQuery(this.shortCodeElement).find('.numInput.cur-year')

                if (year_input.attr('max') == year_input.attr('min')) {
                    jQuery(this.shortCodeElement).find('.numInputWrapper .arrowUp,.numInputWrapper .arrowDown').hide()
                }
            },
            locale: window.wpbookit.flatpicker
        };
        flatargs['minDate'] = new Date()
        if (this.bookingMaxPostBookingDays != "-1") {
            flatargs['maxDate'] = new Date().fp_incr(this.bookingMaxPostBookingDays)
        }
        jQuery('.wpb-inline-flatpickr').flatpickr(flatargs);
        this.shortCodeElement.querySelector('.flatpickr-day.wpb-available')?.click()

        this.bookingModelInstance = new Modal(this.shortCodeElement.querySelector('.confirm-booking'), { 'backdrop': true, 'focus': true })
        this.bookingFormElement = this.shortCodeElement.querySelector('.wpb-shortcode-booking-form')



        this.registerTab = this.shortCodeElement.querySelector('#wpb-new-customer-tab')
        this.loginTab = this.shortCodeElement.querySelector('#wpb-already-customer-tab')
        this.shortCodeElement.querySelector('.confirm-booking').addEventListener('show.bs.modal', (e) => {
            jQuery(e.target).find('[name="wpb_payments_gateways"]').first().prop('checked', true).trigger('change')
            if(jQuery(this.shortCodeElement).find('.pagination-item').not('.disabled').length==1){
                jQuery(this.shortCodeElement).find('.wpb-submit-model-btn').removeClass('d-none')
                jQuery(this.shortCodeElement).find('.wpb-next-btn').addClass('d-none')
            }
        })
        this.shortCodeElement.querySelector('.confirm-booking').addEventListener('hide.bs.modal', (e) => {
            jQuery(this.bookingFormElement).trigger("reset");
            this.currentActiveTab=0
            window.wpbookit.extra_fields.tabs.filter(el => el['condition']).forEach(tab => {
                jQuery(tab.condition.element).trigger('change')
            });

            jQuery(this.shortCodeElement).find('.wpb-submit-model-btn').addClass('d-none')
            jQuery(this.shortCodeElement).find('.wpb-next-btn').removeClass('d-none')
        
            jQuery(this.shortCodeElement).find('.wpb-prev-btn').addClass('d-none')
            jQuery(this.shortCodeElement).find('.wpb-close-model-btn').removeClass('d-none')
            
        })

        this.getContainerWidth()

        this.booking_detail_collapse_instance = new Collapse(this.shortCodeElement.querySelector('#wpb_booking_detail_collapse'))

        this.tabs = window.wpbookit.extra_fields.tabs

        this.form = jQuery(this.shortCodeElement).find('.wpb-shortcode-booking-form');
        this.validation_rules = {
            rules: {},
            messages: {}
        };
        
        for (const key in window.wpbookit?.extra_fields?.validation_rules) {
            if (window.wpbookit?.extra_fields?.validation_rules.hasOwnProperty(key)) {
                this.validation_rules.rules[key] = window.wpbookit?.extra_fields?.validation_rules[key].rules;
                this.validation_rules.messages[key] = window.wpbookit?.extra_fields?.validation_rules[key].messages;
            }
        }

        jQuery.validator.addMethod("customEmail", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value);
        }, window.wpbookit.valid_email);
    }

    RemoveErrorShortCode() {
        var errorElements = [
            'wpb-user-first-name-error',
            'wpb-user-last-name-error',
            'wpb_user_email-error',
            'wpb_user_phone_number-error',
            'wpb-user-full-name-error',
            'wpb_user_password-error',
            'wpb_login_user_email-error',
            'wpb_login_user_password-error'
        ];
    
        errorElements.forEach(function(id) {
            var element = document.getElementById(id);
            if (element) {
                element.remove();
            }
        });
    }
    

    resetErrors() {
        Object.values(this.errorMessages).forEach(errorConfig => {
            this.hideError(errorConfig.selector);   
        });
    }
    
    addEventListener() {
        jQuery(this.shortCodeElement.querySelector('.booking-slots-time')).on('click', '.btn', (e) => this.openModel(e))

        this.bookingFormElement?.addEventListener('sumbit', (e) => this.onFormSubmit(e))
        this.bookingModelSubmitBtn?.addEventListener('click', (e) => this.onFormSubmit(e))

        this.bookingModelCloseBtn?.addEventListener('click', () => this.bookingModelInstance.hide())

        const cancelButton = this.shortCodeElement.querySelector('#wpb_close_button');
        const cancelModalButton = this.shortCodeElement.querySelector('#close_booking_button');
        if (cancelButton) {
            cancelButton.addEventListener('click', () => {
                this.resetErrors();
                this.bookingModelInstance.hide();
            });
        }
        if(cancelModalButton){
            cancelModalButton.addEventListener('click', () =>{
                this.resetErrors();
            })
        }

        this.loginTab?.addEventListener('show.bs.tab', (event) => {
            this.shortCodeElement.querySelector('.wpb-user-booking-with').value = 'wpb-login';
        })
        this.registerTab?.addEventListener('show.bs.tab', (event) => {
            this.shortCodeElement.querySelector('.wpb-user-booking-with').value = 'wpb-register';
        })

        jQuery(window).resize(() => {
            this.getContainerWidth();
        });
        jQuery(this.shortCodeElement).find('.wpb-next-btn').on('click', (e) => this.nextTab(e))
        jQuery(this.shortCodeElement).find('.wpb-prev-btn').on('click', (e) => this.prevTab(e))

        window.wpbookit.extra_fields.tabs.filter(el => el['condition']).forEach(tab => {
            jQuery(tab.condition.element).on('change', (e) => {
                if (jQuery(e.currentTarget).attr('type') == 'checkbox') {
                    jQuery(`#${tab.tab},#${tab.tab}_pagination`).toggleClass('disabled', !(e.currentTarget.checked == tab.condition.value))
                } else {
                    jQuery(`#${tab.tab},#${tab.tab}_pagination`).toggleClass('disabled', !(jQuery(e.currentTarget).val() == tab.condition.value))
                }
                let isOnlyOneTab= jQuery(this.shortCodeElement).find('.pagination-item').not('.disabled').length==1;
                
                jQuery(this.shortCodeElement).find('.wpb-submit-model-btn').toggleClass('d-none',!isOnlyOneTab)
                jQuery(this.shortCodeElement).find('.wpb-next-btn').toggleClass('d-none',isOnlyOneTab)
                
            })
            jQuery(tab.condition.element).trigger('change')
        });
    }
    async nextTab(e,isRecuring=false) {
   
        
        e.preventDefault()
        
        let tab = this.currentActiveTab + 1;
       
  
       
        if((!isRecuring)  && wp.hooks.applyFilters('wpb_load_next_tab',false,e)){
            return ;
        }
        
        this.form.validate({
            ...this.validation_rules,
            onsubmit : false,
            errorPlacement: function(error, element) {
                wp.hooks.doAction('wpb_booking_validate_error_trigger',{error, element});
                element.parent().next('.error-message').html(error)
            }
        });

        if(this.form.valid() !== true){
            return 
        }


        if (this.tabs[tab]?.condition) {
            if (jQuery(this.shortCodeElement).find(this.tabs[tab]?.condition.element).attr('type') == 'checkbox') {
                if (jQuery(this.shortCodeElement).find(this.tabs[tab]?.condition.element).is(":checked")  != this.tabs[tab]?.condition.value){
                    this.currentActiveTab= this.currentActiveTab+1;
                    this.nextTab(e,true)
                    return
                }
            } else {
                if ((jQuery(this.shortCodeElement).find(this.tabs[tab]?.condition.element).val() != this.tabs[tab]?.condition.value)) {
                    this.currentActiveTab= this.currentActiveTab+1;
                    this.nextTab(e,true)
                    return
                }
            }
        }
        await window.wp.hooks.applyFilters('wpb_before_change_tab',new Promise(function(resolve, reject) {
                resolve("resolve promise")
        }),this.currentActiveTab);
        
        
        if ((this.tabs.length - 1) <= tab) {
            jQuery(this.shortCodeElement).find('.wpb-submit-model-btn').removeClass('d-none')
            jQuery(this.shortCodeElement).find('.wpb-next-btn').addClass('d-none')
        }
        
        if(tab>0){
            jQuery(this.shortCodeElement).find('.wpb-prev-btn').removeClass('d-none')
            jQuery(this.shortCodeElement).find('.wpb-close-model-btn').addClass('d-none')
        }
       
        this.currentActiveTab=tab
        jQuery(this.shortCodeElement).find('.wpb-tab,.pagination-item').removeClass('active')
        jQuery(this.shortCodeElement).find(`#${this.tabs[tab].tab},#${this.tabs[tab].tab}_pagination`).addClass('active')

    }
    prevTab(e) {
        e.preventDefault()
        let tab = this.currentActiveTab -1;
        if(tab<0){
            return;
        }
        
        if(this.currentActiveTab == 1){
            jQuery(this.shortCodeElement).find('.wpb-prev-btn').addClass('d-none')
            jQuery(this.shortCodeElement).find('.wpb-close-model-btn').removeClass('d-none')
        }
        
        if (tab < (this.tabs.length) ) {
            jQuery(this.shortCodeElement).find('.wpb-submit-model-btn').addClass('d-none')
            jQuery(this.shortCodeElement).find('.wpb-next-btn').removeClass('d-none')
        }
        
       
        jQuery(this.shortCodeElement).find('.wpb-tab,.pagination-item').removeClass('active')
        if (this.tabs[tab]?.condition) {
            if (jQuery(this.shortCodeElement).find(this.tabs[tab]?.condition.element).attr('type') == 'checkbox') {
                if (jQuery(this.shortCodeElement).find(this.tabs[tab]?.condition.element).is(":checked")  != this.tabs[tab]?.condition.value){
                    this.currentActiveTab= this.currentActiveTab-1;
                    this.prevTab(e)
                    return
                }
            } else {
                if ((jQuery(this.shortCodeElement).find(this.tabs[tab]?.condition.element).val() != this.tabs[tab]?.condition.value)) {
                    this.currentActiveTab= this.currentActiveTab-1;
                    this.prevTab(e)
                    return
                }
            }

        }
        
        this.currentActiveTab= tab
        jQuery(this.shortCodeElement).find(`#${this.tabs[tab].tab},#${this.tabs[tab].tab}_pagination`).addClass('active')
    }



    onFormSubmit(event) {
        event.preventDefault();
        const _this = this;

        const form = event.target.closest('form');
        const requiredFields = {};
        const fields = form.querySelectorAll('[required]');


        fields.forEach(field => {
            let tabContent = field.closest('.tab-pane.fade');
            if (tabContent !== null) {
                if (tabContent.classList.contains('active')) {
                    requiredFields[field.name] = field.value.trim();
                }
            } else {
                requiredFields[field.name] = field.value.trim();
            }

        });


        let FormSubmit = Object.fromEntries(new FormData(event.target.closest('form')))

        FormSubmit = { ...FormSubmit, 'date': this.bookingDate, timeslot: this.bookingTimeSlot, bookingTypeId: this.bookingTypeId }

        if(this.current_booking_tab_invalid()){
            return
        }
        
        this.form.validate({
            ...this.validation_rules,
            onsubmit : false,
            errorPlacement: function(error, element) {
                element.parent().next('.error-message').html(error)
              }
        });

        if(this.form.valid() !== true ){
            return 
        }

        this.bookingModelSubmitBtn.querySelector('.spinner').classList.remove('d-none');
        this.bookingModelSubmitBtn.disabled = true

        post('new_booking', window.wp.hooks.applyFilters('wpb_filter_submit_booking_formdata',FormSubmit) ).then((res) => {
            console.log("response : ", res);
            this.bookingModelSubmitBtn.querySelector('.spinner').classList.add('d-none');
            this.bookingModelSubmitBtn.disabled = false


            if (wp.hooks.hasAction('wpb_init_' + (FormSubmit?.wpb_payments_gateways || ''))) {
                wp.hooks.doAction('wpb_init_' + (FormSubmit?.wpb_payments_gateways || ''), { res, data: FormSubmit });
            }
            if (res.status == 'success') {
                // Clear the form fields
                this.bookingModelInstance.hide();
                notificationToast[res.status](res.message, res.status.toUpperCase(), { autoClose: true });
                jQuery(document).trigger("checkout_process", res);

                (!wp.hooks.applyFilters('wpb_payment_gateway_load_inline_module', { status: false, data: FormSubmit })?.status) && setTimeout(() => {
                    if (res.woo_payment_redirect !== undefined && res.woo_payment_redirect !== '') {
                        console.log("drgedrer", res.woo_payment_redirect);
                        window.location.href = res.woo_payment_redirect;
                    } else if (res.payment_redirect !== undefined && res.payment_redirect !== '') {
                        window.location.href = res.payment_redirect
                    } else if (res.data.redirect_url !== undefined && res.data.redirect_url !== '') {
                        window.location.href = res.data.redirect_url
                    } else if (res.data.redirect_url == 'success') {
                        var url = new URL(window.location.href);
                        url.searchParams.set('booking_confirmation', res.data.booking_id);
                        window.location.replace(url.toString());
                    }
                }, 2000);
                jQuery("body").trigger("update_checkout");
            } else {
                notificationToast[res.status](res.message, res.status.toUpperCase(), { autoClose: true });
            }
        })

    }
    OnSelectDate(Dates) {
        const [selectedDate] = Dates;

        const options = { day: '2-digit', month: 'long', year: 'numeric' };
        const dateString = selectedDate.toLocaleDateString('en-US', options);
        const slotsContainer = this.shortCodeElement.querySelector(".booking-slots-time");


        slotsContainer.innerHTML = `<div class="spinner-border " role="status">
  <span class="visually-hidden">Loading...</span>
</div>`

        this.bookingDate = dateString;

        get('get_booking_timeslot', {
            selected_date: dateString,
            bookingTypeId: this.bookingTypeId,
            _ajax_nonce: window.wpb_nounce,
        })
            .then(({ data }) => {
                if (data.trim() === "") {
                    slotsContainer.innerHTML =window.wpbookit.no_slots_available ;
                } else {
                    slotsContainer.innerHTML = data;
                }
            })
    }
    openModel(e) {
        this.bookingTimeSlot = e.currentTarget.querySelector('.date-time-slot').dataset.time_slot;
        this.shortCodeElement.querySelector('.wpb-selected-timestap').innerHTML = e.currentTarget.querySelector('.date-time-slot').dataset.date_formated
        let count = false
        if (count = e.currentTarget.querySelector('.remain_table')?.dataset?.remainSlotLabel) {
            this.shortCodeElement.querySelector('.wpb-available-seat-count').innerHTML = count
        }
        wp.hooks.doAction('wpb_show_booking_model',e.currentTarget);
        this.booking_detail_collapse_instance.show()
        this.bookingModelInstance.show()

        jQuery(this.shortCodeElement).find('.wpb-tab').removeClass('active').first().addClass('active')
        jQuery(this.shortCodeElement).find('.pagination-item').removeClass('active').first().addClass('active')
    }

    bookingConfirm() {
        this.bookNewMeetingBtn = this.shortCodeElement.querySelector('.book_new_meeting');
        this.bookCancleBtn = this.shortCodeElement.querySelector('.cancel_meeting');

        this.bookNewMeetingBtn.addEventListener('click', () => {
            let currentUrl = window.location.href;
            let newUrl = currentUrl.split('?')[0];
            window.location.href = newUrl;
        });
        this.bookCancleBtn.addEventListener('click', (e) => this.cancleBooking(e))

    }
    cancleBooking(e) {
        if (window.confirm(window.wpbookit.confirm_delete_msg)) {

            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('booking_confirmation');

            post('cancle_booking_appointment', { id }).then(res => {
                let { status, message } = res;
                notificationToast[status](message, status.toUpperCase(), { autoClose: true });
                if (status == 'success') {
                    setTimeout(() => {
                        let currentUrl = window.location.href;
                        let newUrl = currentUrl.split('?')[0];
                        window.location.href = newUrl;
                    }, 2000);
                }
            })
        }
    }

    formatDate(date) {
        const options = {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            hour12: true
        };
        return new Intl.DateTimeFormat('en-US', options).format(date);
    }

    getContainerWidth() {
        if (this.shortCodeElement.clientWidth < 851) {
            this.shortCodeElement.classList.add('content-width-small');
        } else {
            this.shortCodeElement.classList.remove('content-width-small');
        }
    }

    async getStaffId() {
        try {
            const response = await get('get_booking_type', { id: this.bookingTypeId });
            if (response.success) {
                return response.data.meta.staff;
            } else {
                throw new Error("Failed to retrieve booking type details.");
            }
        } catch (error) {
            return null;
        }
    }

    current_booking_tab_invalid(){
        return false;
    }
}
jQuery(function () {
    new (wp.hooks.applyFilters('iqwp_booking_shortcode_class',Booking_Shotcode))(document.querySelector('.wpb-booking-shortcode'))
});
