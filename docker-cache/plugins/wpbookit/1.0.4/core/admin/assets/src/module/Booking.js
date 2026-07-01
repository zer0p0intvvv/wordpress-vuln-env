// import DataTable from "datatables.net-dt";
import flatpickr from "flatpickr";
import { post, get } from "../utils/ajax";
import notificationToast from "../utils/notification-toast";
import { Offcanvas, Modal } from 'bootstrap';
import FormDataJson from "form-data-json-convert";
import importModule from "../components/importModule";

export default class Booking {
    advancedFilterFlatpikerInstance;

    bookingTimeFlatpickerInstance;
    bookingDateFlatpickerInstance;
    bookingDatatable;

    bookingFilterSettings
    bookingFilterSearch

    bookingTypeAvailableDays
    bookingSpecificDay
    bookingMaxPostBookingDays
    bookingPaymentInvoice
    days = {
        "0": "sunday",
        "1": "monday",
        "2": "tuesday",
        "3": "wednesday",
        "4": "thursday",
        "5": "friday",
        "6": "saturday",
    }



    bookingTableData

    bookingAdvancedOffcanvas

    bookingViewModel



    isFreeBookingType=false

    constructor() {
        const urlParams = new URLSearchParams(window.location.search);
        const start = urlParams.get('start');
        const end = urlParams.get('end');
        this.bookingFilterSettings = {date_from: start, date_to: end }
        jQuery(document.body).on("click", "#wpb_apply_booking_reset", this.filters_reset.bind(this));
        jQuery(document.body).on("click","#create-booking", this.RemoveClass.bind(this));
        this.bookingTypeId = document.querySelector('#wpb_booking_type');
        this.bookingDateDropDown = document.querySelector('#wpb-datepicker');
        this.timeSlotDropDown = document.querySelector('#wpb_booking_slot_time');
        this.booking_table = jQuery('#wpb-booking-tbl');
        this.advanceFilter = jQuery('#advance-filter');
        this.paymentInvoice = jQuery('#payment_invoice');
        this.addModule = jQuery('#booking-form');
        this.priceModule = jQuery('#booking_price');
        this.paginate = jQuery('#datatable_paginate');
        this.showing_msg = jQuery('#wpb-booking-paginate');
        this.paymentSection = jQuery('#wpb-payment-section');
        this.selectedDates = [];

        this.bookingDatatable = new DataTable('#wpb-booking-tbl', {
            "searching": false,
            "processing": true,
            "serverSide": true,
            "order": false,
            "paging": true,
            "lengthChange": false,
            "ajax": (data, callback, settings) => {
                if (this.bookingFilterSettings) {
                    data = { ...data, ...{ advanceFilter: this.bookingFilterSettings } }
                }
                if (this.bookingFilterSearch) {
                    data = { ...data, ...{ customer_search: this.bookingFilterSearch } }
                }
                return get('booking_list', data).then(resData => {
                    this.bookingTableData = resData.data
                    callback(resData)
                })
            },
            "columns" :  wp.hooks.applyFilters('wpb_booking_datatable_columns',[
                {
                    "data": "name",
                    "render": function (data, type, row) {
                        return `<div class="d-flex align-items-center gap-3">
                            <img class="rounded-pill img-fluid avatar-40" src="${row.profile_img}" alt="" loading="lazy">
                            <div class="media-support-info">
                                <h6 class="iq-sub-label">${data}</h6>
                                <p class="mb-0">${row.email}</p>
                            </div>
                        </div>`;

                    },
                    "name": 'booking_name',
                    "searchable": false
                },
                {
                    "data": "datetime",
                    "name": 'booking_date',
                    "searchable": false

                },
                {
                    "data": "type",
                    "name": 'booking_type',
                    "searchable": false
                },
                {
                    "data": "duration",
                    "name": 'duration',
                    "searchable": false

                },
                {
                    "data": "price",
                    "name": 'price',
                    "searchable": false,
                },
                {
                    "data": "status",
                    "orderable": false,
                    "searchable": false,
                    "render": function (data, type, row) {

                        let status = {
                            "wpb-pending": 'warning',
                            "wpb-rejected": 'danger',
                            "wpb-approved": 'success',
                            "wpb-completed": 'primary',
                            "wpb-cancelled": 'danger',
                            "wpb-failed": 'danger',
                        }

                        return `<span class="badge bg-${status[data.key]}-subtle p-2 text-${status[data.key]}">${data.label}</span>`;

                    },
                },
                {
                    "data": "actions",
                    "render": function (data, type, row) {

                        let render = '';
                        if (row.status.key === "wpb-approved") {
                            if (row.location == "Meeting URL") {
                                if (row.location_source == '' && row.zoom_error != null) {
                                    render += ` <a href="#" data-id="${row.id}" class="me-3 generate-zoom-meeting-button" data-toggle="tooltip" title="${row.zoom_error ?? ''}! Click to re-generate meeting.">
                                    <img src="${wpbookit.wpb_plugin_url}core/admin/assets/images/add-link.png" alt="checked" style="width:20px;">
                                </a>`;
                                }
                            }
                        }
                        if (row.status.key !== "wpb-cancle") {
                            render += ` <a href="#" data-id="${row.id}" class="me-3 edit-booking-button">
                            <img class="icon" src="${wpbookit.wpb_plugin_url}core/admin/assets/images/edit-icon.svg" alt="checked">
                            <svg class="spinner wpb-notification-svg" style="display:none;" height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path fill="#d3d3d3" d="M304 48c0 26.5-21.5 48-48 48s-48-21.5-48-48 21.5-48 48-48 48 21.5 48 48zm-48 368c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zm208-208c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48zM96 256c0-26.5-21.5-48-48-48S0 229.5 0 256s21.5 48 48 48 48-21.5 48-48zm12.9 99.1c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zm294.2 0c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48c0-26.5-21.5-48-48-48zM108.9 60.9c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z"></path>
                            </svg>
                        </a>`;
                        }

                     
                        render += `<a class=" detail-booking-button me-3 " onclick="window.DashbordSideBarModule.how_booking_model(${row.id})" href="#" data-id="${row.id}" data-name="${row.name}">
                            <img src="${wpbookit.wpb_plugin_url}core/admin/assets/images/user-detail.svg" alt="checked">
                        </a>`;


                        render += `<a class=" delete-booking-button me-3 " href="#" data-id="${row.id}" data-name="${row.name}">
                        <img src="${wpbookit.wpb_plugin_url}core/admin/assets/images/delete-icon.svg" alt="checked">
                        </a>`;


                        return `<div class="d-flex align-items-center">${render}</div>`;
                    },
                    "searchable": false,
                    "orderable": false,
                },
            ]),

            language: window.wpbookit.datatable_language
        });

        // Initialize tooltips
        jQuery('#wpb-booking-tbl').on('mouseenter', '[data-toggle="tooltip"]', function () {
            jQuery(this).tooltip({
                container: 'body'
            });
        });


        this.toggleDateAndTimeFields();

        this.validateField = this.validateField.bind(this);
        this.showError = this.showError.bind(this);
        this.hideError = this.hideError.bind(this);
            
        this.errorMessages = wp.hooks.applyFilters('wpb_after_booking_payment_mode',{
            booking_type: {
                selector: "#booking_type_error",
                blank: window.wpbookit.dashbord_language.validation.select_booking_type,
            },
            booking_date: {
                selector: "#date_error",
                blank: window.wpbookit.dashbord_language.validation.select_booking_date,
            },
            booking_time: {
                selector: "#time_errorz",
                blank: window.wpbookit.dashbord_language.validation.select_booking_time,
            },
            booking_customer: {
                selector: "#customer_error",
                blank: window.wpbookit.dashbord_language.validation.select_customer,
            },
            booking_status: {
                selector: "#status_error",
                blank: window.wpbookit.dashbord_language.validation.select_booking_status,
            },
            booking_payment_status: {
                selector: "#payment_status_error",
                blank: window.wpbookit.dashbord_language.validation.select_booking_payment_status,
            },
            booking_payment_mode: {
                selector: "#payment_mode_error",
                blank: window.wpbookit.dashbord_language.validation.select_booking_payment_mode,
            },
        });
        this.offcanvasElement = document.getElementById('booking-form');
        this.offcanvas = new Offcanvas(this.offcanvasElement);
        this.bookingAdvancedOffcanvas = new Offcanvas(document.querySelector('#advance-filter'));
        this.opencanvas();

        this.bookingViewModel = new Modal('#booking-detail-modal', {
            keyboard: false
        })

        this.event_Handler();

        new importModule(document.querySelector('#wpb-booking-import'),this.bookingDatatable.ajax.reload)
    }

    how_booking_model(id) {
        let bookingData = this.bookingTableData.find(item => item.id == id);
        let { type, duration, datetime, email, date_created, questions_answers, location, location_source ,...extra} = this.bookingTableData.find(item => item.id == id);
        let formattedDateTime = this.formatDateTime(datetime);
        let formatedate_created = this.formatDateTime(date_created);

        window.wp.hooks.doAction('wpb_show_booking_detail',extra);
        
        let model = jQuery(this.bookingViewModel._element)

        model.find('.wpb-booking-type').html(type)
        model.find('.wpb-booking-date-time').html(formattedDateTime)
        model.find('.wpb-booking-duration').html(duration)
        model.find('.wpb-booking-user-email').html(email)
        model.find('.wpb-booking-created').html(formatedate_created)
        model.find('.wpb-booking-questions').html('')

        
            wp.hooks.doAction('wpb_after_booking_questions',bookingData);
        
        

        if(location==false){
            jQuery(model.find('.wpb-booking-location-container')).hide()
            model.find('.wpb-booking-location').innerHTML= '';
            model.find('.wpb-booking-location-source').innerHTML= '';
        }
        jQuery(model.find('.wpb-booking-location-container')).show()
     
        model.find('.wpb-booking-location').text(location+": ");        
        model.find('.wpb-booking-location-container').removeClass('d-none');

        if(location_source == null || location_source == ''){
            model.find('.wpb-booking-location-container').addClass('d-none');
        }
        if(this.isUrl){
            var $anchor = jQuery('<a></a>')
            .attr('href', location_source)
            .attr('target', '_blank')
            .text(location_source);
            model.find('.wpb-booking-location-source').html($anchor);
        }else{
            model.find('.wpb-booking-location-source').text(location_source);
        }
      

        (questions_answers.length !== undefined) && questions_answers.forEach(function (el) {
            let ans = (el.ans === undefined || el.ans === null || el.ans === '') ? '-' : el.ans;
            let item = `   <li class="mb-3">
            <div class="align-items-center gap-1 flex-wrap">
                <h6 class="mb-0 question">${el.question}</h6>
                <span class="d-inline-block ans mt-1">${ans}</span>
            </div>
        </li>`;
            model.find('.wpb-booking-questions').append(item)
        })

        this.bookingViewModel.show()

    }
    formatDateTime(datetime) {
        
        let parts = datetime.split(' ');
        if (parts.length >= 3) {
            parts[2] += ','; 
        }
        return parts.join(' ');
    }

    event_Handler() {
        jQuery(document).ready(this.intialize_datepicker());
        jQuery(document).ready(this.addEventListeners());
        jQuery(document).ready(this.addTooltips());

        jQuery(document.body).on("click", ".dashboard-page .delete-booking-button", (e) => this.Delete_Booking(e));
        jQuery(document.body).on("click", ".dashboard-page .payment_invoice-booking-button", (e) => this.Payment_Invoice(e));
        jQuery(document.body).on("click", "#wpb_invoice_submit", (e) => this.Payment_Invoice_Print(e));
        jQuery(document.body).on("submit", "#booking-form #add-booking-form", (e) => this.SubmitbtnEventHandlers(e));
        jQuery(document.body).on("click", "#booking-form .btn-close", (e) => this.PopupCloseEvent(e));
        jQuery(document.body).on("click", ".edit-booking-button", (e) => this.EditDataeventHandlers(e));
        jQuery(document.body).on("change", "#booking-form #wpb_booking_type", (e) => this.ChangePricewithbookingtype(e));
        jQuery(document.body).on("submit", "#booking_filters_form", (e) => this.filter_Bookings(e));
        jQuery(document.body).on("click", "#wpb_apply_booking_reset", (e) => this.filters_reset(e));
        jQuery('.dt-search').on("input", _.debounce(e => {
            this.search_filter(e)
        }, 500));


        this.offcanvasElement.addEventListener('hidden.bs.offcanvas', (e) => {
            document.querySelector('#add-booking-form').reset()
            jQuery('#add-booking-form select').trigger('change')
            wp.hooks.doAction('wpb_new_booking_offcanvas_close', this)
            this.PopupCloseEvent(e)
        })
        this.offcanvasElement.addEventListener('show.bs.offcanvas', (e) => {
            if (e.relatedTarget === undefined) {
                e.target.querySelector('.offcanvas-title-edit').classList.remove('d-none')
                e.target.querySelector('.offcanvas-title-add').classList.add('d-none')
            } else {
                e.target.querySelector('.offcanvas-title-edit').classList.add('d-none')
                e.target.querySelector('.offcanvas-title-add').classList.remove('d-none')


                jQuery('#wpb_booking_type').prop("disabled", false).trigger('change');
                jQuery('#wpb_booking_slot_time').prop("disabled", false).trigger('change');
                this.timeSlotDropDown.add(new Option('No slot found', '', true, false));
                jQuery('#wpb-datepicker').prop("disabled", false).trigger('change');
                jQuery('#wpb_customer option[value="temp"]').remove();
                jQuery('#wpb_customer').prop("disabled", false).trigger('change');
                jQuery('#wpb_booking_payment_mode').prop("disabled", false).trigger('change');
            }
        })


        jQuery(this.booking_table).on('ajax_refresh',()=>{
            console.log('sample',e);
        })

    }
    Payment_Invoice_Print(e) {
        var self = this;
        e.preventDefault();
        var data = [];
        data['data_id'] = e.target.getAttribute('data-id');
        self.print_ajax(data);
    }
    print_ajax(data) {
        const pluginUrl = wpbookit.wpb_plugin_url;
        post('print_invoice_data', data).then(response => {
            jQuery(response).printArea({
                'popTitle': 'Payment Invoice',
                'extraCss': pluginUrl + '/core/admin/assets/vendor/css/bootstrap.css?v=5.2.3, ' + pluginUrl + '/core/admin/assets/vendor/css/printarea.css?',
            });
        }).catch(error => {
            console.error('Something went wrong');
        });
    }
    Payment_Invoice(e) {
        e.preventDefault();
        const __this = jQuery(e.currentTarget);
        const editButton = __this.closest('.payment_invoice-booking-button');
        const data = this.bookingDatatable.row(editButton.closest('tr')).data();
        this.paymentInvoice.find('#wpb_booking_type').text(data['name']);
        this.paymentInvoice.find('#wpb_customer_address').text('-');
        this.paymentInvoice.find('#wpb_booking_date').text(data['datetime']);
        this.paymentInvoice.find('#wpb_payment_id').text('Invoice Id #' + data['payment_id']);
        this.paymentInvoice.find('#wpb_customer_email').text(data['email']);
        this.paymentInvoice.find('#wpb_booking_status').text(data['status']['label']);
        this.paymentInvoice.find('#customer_name').text(data['name']);
        this.paymentInvoice.find('#customer_gender').text(data['gender']);
        this.paymentInvoice.find('#customer_birthdate').text(data['dob']);
        this.paymentInvoice.find('#booking_type').text(data['type']);
        this.paymentInvoice.find('#booking_duration').text(data['duration']);
        this.paymentInvoice.find('#booking_price').text(data['price']);
        this.paymentInvoice.find('#wpb_invoice_submit').attr("data-id", data['payment_id']);
    }
    search_filter(e) {
        this.bookingFilterSearch = e.target.value;
        this.bookingDatatable.ajax.reload()

    }
    filters_reset(e) {
        e.preventDefault();
        jQuery('#booking_filters_form')[0].reset();
        jQuery('#booking_filters_form select').val(null).trigger('change');

        this.bookingFilterSettings = {}
        this.bookingFilterSearch = '';
        this.bookingDatatable.ajax.reload()
        this.selectedDates = [];
        this.resetFilterDivValues();
        this.bookingAdvancedOffcanvas.hide()


    }
    RemoveClass(e){
        e.preventDefault();
        const bookingPriceElement = document.getElementById('booking_price');
        bookingPriceElement.classList.add('d-none');
    }

    intialize_datepicker() {
        const self = this;
        let TodayDate = new Date()
        this.advancedFilterFlatpikerInstance = flatpickr('#wpb-bookings-advanced-filter-flatpiker', {
            inline: true,
            dateFormat: window.wpbookit.date_format,
            mode: "range",
            onChange: function (selectedDates, dateStr, instance) {
                self.handleDateRange(selectedDates, instance);
            },
            locale:window.wpbookit.flatpicker
        });

        this.bookingDateFlatpickerInstance = flatpickr('#wpb-datepicker', {
            dateFormat: window.wpbookit.date_format,
            allowInput: true, 
            minDate: "today",
            onChange: function (selectedDates, dateStr, instance) {
                self.handleDateRange(selectedDates, instance);
                self.handleTimeSlotRange(selectedDates);
            },
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                if ((this.bookingTypeAvailableDays === undefined || this.bookingTypeAvailableDays === null)) {
                    return
                }

                if (dayElem.dateObj > TodayDate && this.days[dayElem.dateObj.getDay()] in this.bookingTypeAvailableDays && dayElem.dateObj <= new Date().fp_incr(this.bookingMaxPostBookingDays)) {
                    dayElem.classList.add('wpb-available');
                }

                let formattedDate = dayElem.dateObj.getFullYear() + '-' + this.get2DigitFmt(dayElem.dateObj.getMonth() + 1) + '-' + this.get2DigitFmt(dayElem.dateObj.getDate());
                if (this.bookingSpecificDay[formattedDate]) {
                    dayElem.classList.add('wpb-available');
                }
            },
            onReady(_, __, fp) {
                fp.calendarContainer.classList.add("wpb-booking-calender");
            },
            locale:window.wpbookit.flatpicker
        });

        document.getElementById('wpb_apply_booking_reset').addEventListener('click', function () {
            self.advancedFilterFlatpikerInstance.clear();
            self.bookingDateFlatpickerInstance.clear();
        });
    }
    get2DigitFmt(val) {
        return ('0' + val).slice(-2);
    }

    filter_Bookings(e) {
        e.preventDefault();
        let formData = FormDataJson.toJson(e.currentTarget.closest('form'))
        this.selectedDates = this.selectedDates.map((item) => this.formatbookingDate(item));
        this.bookingFilterSettings = { ...formData, date_from: this.selectedDates[0], date_to: this.selectedDates[1] }
        this.bookingDatatable.ajax.reload()
        this.bookingAdvancedOffcanvas.hide()
    }

    formatbookingDate(date) {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0'); 
        const day = String(d.getDate()).padStart(2, '0');
    
        return `${year}-${month}-${day}`;
    }

    resetFilterDivValues() {
        document.getElementById("wpb_filter_from_date").textContent = "-";
        document.getElementById("wpb_filter_to_date").textContent = "-";
    }

    getToDay() {
        const startDate = new Date();
        this.setStartEndDate(startDate, startDate);
        return [startDate, startDate];
    }

    getLast30DaysRange() {
        const endDate = new Date(); // Today's date
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - 29); // Subtract 29 days to get 30 days range
        this.setStartEndDate(startDate, endDate);
        return [startDate, endDate];
    }

    getMonthDateRange() {
        const today = new Date();
        const startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of the current month
        const endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of the current month
        this.setStartEndDate(startDate, endDate);
        return [startDate, endDate];
    }

    getLastMonthDateRange() {
        const today = new Date();
        const startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const endDate = new Date(today.getFullYear(), today.getMonth(), 0);
        this.setStartEndDate(startDate, endDate);
        return [startDate, endDate];
    }

    getLast90DaysRange() {
        const endDate = new Date(); // Today's date
        const startDate = new Date();
        startDate.setDate(startDate.getDate() - 90); // Subtract 89 days to get 90 days range
        this.setStartEndDate(startDate, endDate);
        return [startDate, endDate];
    }

    getLast6MonthsRange() {
        const today = new Date();
        const startDate = new Date(today.getFullYear(), today.getMonth() - 5, 1); // First day of the month 6 months ago
        const endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of the current month
        this.setStartEndDate(startDate, endDate);
        return [startDate, endDate];
    }

    getLast1YearRange() {
        const today = new Date();
        const startDate = new Date(today.getFullYear() - 1, today.getMonth(), today.getDate()); // Exactly 1 year ago from today
        const endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate()); // Today's date
        this.setStartEndDate(startDate, endDate);
        return [startDate, endDate];
    }

    handleTimeSlotRange(selectedDates) {
        this.timeSlotDropDown.disabled = false ;
        this.timeSlotDropDown.innerHTML = '';
    

        const defaultOption = new Option('Select a time slot', '', true, false);
        this.timeSlotDropDown.add(defaultOption);
    
        const [selectedDate] = selectedDates;
        const options = { day: '2-digit', month: 'long', year: 'numeric' };
        const dateString = selectedDate.toLocaleDateString('en-US', options);
    
        const spinner = document.querySelector('.iqwpbwm-notification-submit-svg');
        spinner.classList.remove('d-none');

        new Promise((resolve, reject) => {
            get('get_booking_timeslot_dashboard', {
                selected_date: dateString,
                bookingTypeId: this.bookingTypeId.value,
                _ajax_nonce: window.wpb_nounce,
            })
            .then(resolve)
            .catch(reject);
        })
        .then(res => {
            if (res && res.data && Array.isArray(res.data)) {
                res.data.forEach((time, index) => {
                    const option = new Option(time, time, false, false);
                    this.timeSlotDropDown.add(option);
                });
                this.toggleDateAndTimeFields();
                this.timeSlotDropDown.disabled = false;
            } else {
                console.error('Unexpected response format from server');
            }
        })
        .catch(error => {
            console.error('Error fetching timeslots:', error);
        })
        .finally(() => {
            // Hide spinner
            if (spinner) {
                spinner.classList.add('d-none');
            }
        });
    }
    
    handleDateRange(selectedDates, clickedItemId) {
        const dateRangeItems = document.querySelectorAll('.range-list li');
        dateRangeItems.forEach(item => {
            item.classList.remove('active');
        })

        const clickedItem = document.getElementById(clickedItemId);
        if (clickedItem) {
            clickedItem.classList.add('active');
        }

        const dateRangeHandlers = {
            'wpb_today_btn': this.getToDay,
            'wpb_get_last_30_days': this.getLast30DaysRange,
            'wpb_this_month_btn': this.getMonthDateRange,
            'wpb_last_month_btn': this.getLastMonthDateRange,
            'wpb_get_last_90_days': this.getLast90DaysRange,
            'wpb_get_last_6_months': this.getLast6MonthsRange,
            'wpb_get_last_1_year': this.getLast1YearRange
        };

        const dateRangeHandler = dateRangeHandlers[clickedItemId];
        if (dateRangeHandler) {
            const dateRange = dateRangeHandler.call(this);
            this.advancedFilterFlatpikerInstance.setDate(dateRange);
        } else if (selectedDates) {
            const startDate = selectedDates[0];
            const endDate = selectedDates[selectedDates.length - 1];
            this.setStartEndDate(startDate, endDate);
        }
    }

    setStartEndDate(startDate, endDate) {

        // set start date and end date
        this.selectedDates = [startDate, endDate]

        document.getElementById('wpb_filter_from_date').textContent = startDate.toLocaleDateString('en-US', { day: '2-digit', month: 'long', year: 'numeric' });
        document.getElementById('wpb_filter_to_date').textContent = endDate.toLocaleDateString('en-US', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    addEventListeners() {
        const dateRangeItems = document.querySelectorAll('.range-list li');
        dateRangeItems.forEach(item => {
            item.addEventListener('click', () => {
                this.handleDateRange(null, item.id);
            });
        });
    }

    addTooltips() {
        jQuery('[data-bs-toggle="tooltip"]').tooltip();
    }

    PopupCloseEvent() {
        this.offcanvas.hide();
        const form = this.offcanvasElement.querySelector('form');
        const errorMessage = document.querySelectorAll('.error-message');
        if (errorMessage.length > 0) {
            errorMessage.forEach(element => {
                if (element.textContent.trim() !== '') {
                    element.textContent = '';
                }
            });
        }
        if (form) {
            form.reset();
            jQuery(form).find('select').trigger('change');
        }
    }

    Delete_Booking(e) {
        e.preventDefault();
        const __this = jQuery(e.currentTarget);
        const bookingID = __this.data('id');
        const bookingName = __this.data('name');

        // Using wp.i18n for translations
        var cMessage = window.wpbookit.dashbord_language.booking.confirm_delete_boooking + bookingName + "?";
        if (confirm(cMessage) == true) {
            post('delete_booking', { 'bookingID': bookingID }).then(response => {
                const resStatus = response.status;
                this.RefreshTableeventHandlers()
                notificationToast[resStatus](response.message, resStatus.toUpperCase(), { autoClose: true });
            });
        }
    }

    RefreshTableeventHandlers() {
        this.bookingDatatable.ajax.reload()
    }


    SubmitbtnEventHandlers(e) {
        e.preventDefault();
        const _this = this;

        var booking_id = jQuery('#edit-booking-id').val();
        var formFields = {
            'booking_type': jQuery('#wpb_booking_type').val(),
            'booking_date': jQuery('#wpb-datepicker').val(),
            'booking_time': jQuery('#wpb_booking_slot_time').val(),
            'booking_customer': jQuery('#wpb_customer').val(),
            'booking_status': jQuery('#wpb_booking_status').val(),
            'booking_payment_status': jQuery('#wpb_booking_payment_status').val(),
            'booking_payment_mode': jQuery('#wpb_booking_payment_mode').val(),
        }
         formFields = wp.hooks.applyFilters('wpb_after_error_booking_payment_mode', formFields);
        // booking_payment_mode
        if(booking_id  && this.isFreeBookingType){
            delete formFields.booking_payment_mode;
            delete formFields.booking_payment_status;
        }
        let isValid = [];
        jQuery.each(formFields, function (fieldName, fieldValue) {
            if (fieldValue === undefined || fieldValue === null) return;
        
            if (_this.errorMessages[fieldName] !== undefined) {
                var error = _this.validateField(
                    fieldValue,
                    _this.errorMessages[fieldName].selector,
                    _this.errorMessages[fieldName].blank,
                    _this.errorMessages[fieldName].invalid
                );
                if (error === undefined) {
                    document.querySelector(_this.errorMessages[fieldName].selector).innerHTML = '';
                }
                isValid.push(error);
            }
        });

        if (booking_id) {

            const form = jQuery(e.currentTarget).closest('form')[0];
            const formData = new FormData(form);

            if (isValid.indexOf(false) !== -1) return false;

            jQuery('#wpb-submit-booking').prop('disabled', true);
            jQuery('.wpb-booking-submit-svg').removeClass('d-none');
            post('add_update_booking', formData).then(response => {
                const { message, status } = response.data;
                if (status === 'success') {
                    this.PopupCloseEvent();
                    this.RefreshTableeventHandlers();
                }
                notificationToast[status](message, status.toUpperCase(), { autoClose: true });
                jQuery('#wpb-submit-booking').prop('disabled', false);
                jQuery('.wpb-booking-submit-svg').addClass('d-none');
            })
                .catch(error => {
                    jQuery('#wpb-submit-booking').prop('disabled', false);
                    jQuery('.wpb-booking-submit-svg').addClass('d-none');
                    console.error('Error :', error);
                });
        } else {
            const bookingType = jQuery('#wpb_booking_type option:selected').text().trim();
            const form = jQuery(e.currentTarget).closest('form')[0];
            const formData = new FormData(form);

            if (isValid.indexOf(false) !== -1) return false;

            jQuery('#wpb-submit-booking').prop('disabled', true);
            jQuery('.wpb-booking-submit-svg').removeClass('d-none');
            formData.append('bookingType', bookingType);
            wp.hooks.doAction('wpb_after_booking_type', formData);
            post('add_update_booking', formData)
                .then(response => {
                    const { message, status } = response.data;
                    if (status === 'success') {
                        this.PopupCloseEvent();
                        this.RefreshTableeventHandlers();
                    }
                    notificationToast[status](message, status, { autoClose: true });
                    jQuery('#wpb-submit-booking').prop('disabled', false);
                    jQuery('.wpb-booking-submit-svg').addClass('d-none');
                })
                .catch(error => {
                    jQuery('#wpb-submit-booking').prop('disabled', false);
                    jQuery('.wpb-booking-submit-svg').addClass('d-none');
                    console.error('Error :', error);
                });
        }
    }

    showError(elementId, errorMessage) {
        jQuery(elementId).text(errorMessage).show();
    }

    hideError(elementId) {
        jQuery(elementId).hide();
    }

    validateField(value, errorSelector, blankErrorMessage, invalidErrorMessage) {
        if (!value) {
            this.showError(errorSelector, blankErrorMessage);
            return false;
        } else {
            this.hideError(errorSelector);
            return true;
        }
    }

    EditDataeventHandlers(e) {
        e.preventDefault();
        const __this = jQuery(e.currentTarget);
        const booking_id = __this.data('id');
        __this.find('.icon').toggle()
        __this.find('.spinner').toggle()

        this.paymentSection.removeClass('d-none');

        var divElement = document.querySelector('#wpb-payment-section');
        divElement.style.display = '';
        this.isFreeBookingType=false;

        if (booking_id) {
            get('get_booking_details', { 'booking_id': booking_id }).then(response => {
                this.offcanvas.show();
                __this.find('.icon').toggle()
               __this.find('.spinner').toggle()
                var booking_id = response.id;
                var booking_type_id = response.booking_type_id;
                var booking_date = response.booking_date;
                var booking_time = response.booking_time;
                var booking_customer_id = response.booking_customer_id;
                var booking_status = response.booking_status;
                var payment_mode = response.payment_mode;
                var payment_status = response.payment_status;
                var booking_notes = response.booking_notes;
                var booking_price = response.booking_price;
                var booking_name = response.booking_name;
                
                if(booking_price==window.wpbookit.dashbord_language.validation.free){
                    this.isFreeBookingType=true;
                }
                
                if(this.isFreeBookingType){
                    jQuery('#wpb-payment-section').hide()
                }else{
                    jQuery('#wpb-payment-section').show()
                }

                jQuery('#edit-booking-id').val(booking_id);
                jQuery('#wpb_booking_type').prop("disabled", true).val(booking_type_id).trigger('change');
                // jQuery('#wpb_booking_slot_time').prop("disabled", true).val(booking_time).trigger('change');
                jQuery('#wpb-datepicker').prop("disabled", true).val(booking_date).trigger('change');
                jQuery('#wpb_customer').append(jQuery("<option selected></option>").attr("value", 'temp').text(booking_name)).prop("disabled", true).trigger('change');
                wp.hooks.doAction('wpb_after_booking_price', response);
                jQuery('#wpb_booking_payment_mode').prop("disabled", true).val(payment_mode).trigger('change');
                jQuery('#wpb_booking_payment_status').val(payment_status).trigger('change');
                jQuery('#wpb_booking_status').val(booking_status).trigger('change');
                jQuery('#notesFormControlTextarea1').val(booking_notes);
                document.getElementById('booking_price').innerHTML = response.booking_price_html;
                document.getElementById('booking_price').classList.remove('d-none');

                this.timeSlotDropDown.add(new Option(booking_time, booking_time))
                jQuery(this.timeSlotDropDown).trigger('change')
                jQuery(document).trigger('editBooking', response);
            });
        }
    }

    ChangePricewithbookingtype(e) {
        if (jQuery('#edit-booking-id').val()) {
            return;
        }
    
        this.toggleDateAndTimeFields();
        document.getElementById('wpb-datepicker').value = '';
    
        var bookingSlotTimeSelect = document.getElementById('wpb_booking_slot_time');
        bookingSlotTimeSelect.innerHTML = '';
    
        e.preventDefault();
        const __this = jQuery(e.currentTarget);
        const booking_type_id = __this.val();
        this.updatePriceElements(__this.find(':selected').attr('data-price') ?? "-");
    
        post('select_booking', { 'booking_type_id': booking_type_id }).then(response => {
            if(response.data.length !== 0){
                const bookingPriceElement = document.getElementById('booking_price');
                bookingPriceElement.innerHTML = response.data.html_output;
            
                bookingPriceElement.classList.remove('d-none');
            }

            window.wp.hooks.doAction('wpb_new_booking_after_select_booking',response)

            this.bookingSpecificDay = response.data.specific_dates
            this.bookingTypeAvailableDays = response.data.weekly_time_slots
            this.bookingMaxPostBookingDays = response.data.how_far
            document.getElementById('booking_price').innerHTML = response.data.html_output;
            this.isFreeBookingType = response.data.tax_values.total == 'Free'; 
            wp.hooks.doAction('wpb_before_bookingSpecificDay',response);
        });
    }
    

    opencanvas() {
        const params = new URLSearchParams(window.location.search);
        if (params.has("offcanvas")) {
            this.offcanvas.show();
        }
    }
    updatePriceElements(price) {
        if (price == 0 || price.toLowerCase() === 'free') {
            this.paymentSection.addClass('d-none');
            this.isFreeBookingType = true; // Mark as free booking
        } else {
            this.paymentSection.removeClass('d-none');
            this.isFreeBookingType = false; // Mark as paid booking
        }
        this.priceModule.find('.price_text').text(price);
    }
    


    toggleDateAndTimeFields() {
        this.bookingDateDropDown.disabled = (this.bookingTypeId.value === '');
        this.timeSlotDropDown.disabled = (this.bookingDateDropDown.value === '' || this.bookingTypeId === '');
    }
    isUrl(string) {
        try { return Boolean(new URL(string)); }
        catch (e) { return false; }
    }

    generateZoomMeeting(e) {
        e.preventDefault();
        const __this = jQuery(e.currentTarget);
        const booking_id = __this.data('id');
        console.log(booking_id);
        let cMessage = "Do you want to create zoom meeting for this booking?";
        if (booking_id) {
            if (confirm(cMessage) == true) {
                get('generate_zoom_meeting_link', { 'booking_id': booking_id }).then(response => {
                    console.log(response);
                    const { message, status } = response.data;
                    if (status === 'success') {
                        this.RefreshTableeventHandlers();
                    }
                    notificationToast[status](message, status.toUpperCase(), { autoClose: true });
                });

            }
        }
    }
}
