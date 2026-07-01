// import DataTable from "datatables.net-dt";
import flatpickr from "flatpickr";
import { post, get } from "../utils/ajax";
import notificationToast from "../utils/notification-toast";
import { Offcanvas, Modal } from "bootstrap";
import FormDataJson from "form-data-json-convert";
import Calendar from "@event-calendar/core";
import TimeGrid from "@event-calendar/time-grid";
import DayGrid from "@event-calendar/day-grid";
import List from "@event-calendar/list";
import "@event-calendar/core/index.css";
import "flatpickr/dist/flatpickr.min.css";
import editIcon from "../../images/edit-icon.svg";
import deleteIcon from "../../images/delete-icon.svg";
import editIconWhite from "../../images/edit-white.svg";
import deleteIconWhite from "../../images/delete-white.svg";
import ResourceTimeGrid from "@event-calendar/resource-time-grid";
import ResourceTimeline from "@event-calendar/resource-timeline";
import calendarStyle from "@event-calendar/core/index.css?inline";
import appCss from "../css/app.css?inline";

export default class Calendars {

    advancedFilterFlatpikerInstance;

    bookingTimeFlatpickerInstance;
    bookingDateFlatpickerInstance;
    bookingDatatable;

    bookingFilterSettings;
    bookingFilterSearch;

    bookingTypeAvailableDays;
    bookingSpecificDay;
    bookingMaxPostBookingDays;
    bookingPaymentInvoice;
    days = {
        0: "sunday",
        1: "monday",
        2: "tuesday",
        3: "wednesday",
        4: "thursday",
        5: "friday",
        6: "saturday",
    };

    bookingTableData;

    bookingAdvancedOffcanvas;

    bookingViewModel;

    calendar;
    currentUserId = 0;

    constructor() {
        // console.log(appCss);
        this.bookingTypeId = document.querySelector("#wpb_booking_type");
        this.bookingDateDropDown = document.querySelector("#wpb-datepicker");
        this.timeSlotDropDown = document.querySelector("#wpb_booking_slot_time");
        this.booking_table = jQuery("#wpb-booking-tbl");
        // this.advanceFilter = jQuery("#advance-filter");
        // this.paymentInvoice = jQuery("#payment_invoice");
        this.addModule = jQuery("#booking-form");
        this.priceModule = jQuery("#booking_price");
        this.paginate = jQuery("#datatable_paginate");
        this.showing_msg = jQuery("#wpb-booking-paginate");
        this.paymentSection = jQuery("#wpb-payment-section");
        this.selectedDates = [];
        this.currentUserId = jQuery("#current_user_id").val();

        let icon = `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 22" fill="none">
                  <path d="M1.0918 8.40421H18.9157" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M14.4429 12.3097H14.4522" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M10.0054 12.3097H10.0147" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M5.55818 12.3097H5.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M14.4429 16.1962H14.4522" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M10.0054 16.1962H10.0147" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M5.55818 16.1962H5.56744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M14.0433 1V4.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M5.96515 1V4.29078" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M14.2383 2.57922H5.77096C2.83427 2.57922 1 4.21516 1 7.22225V16.2719C1 19.3263 2.83427 21 5.77096 21H14.229C17.175 21 19 19.3546 19 16.3475V7.22225C19.0092 4.21516 17.1842 2.57922 14.2383 2.57922Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>`;


        function formatDate(date, time) {
            return new Date(date + " " + time);
        }

        function formatTime(date) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
        }
        function get_booking_type_ids() {
            let booking_ids = [];
            var checkedValues = jQuery('.booking-type-checkboxes:checked').map(function () {
                booking_ids.push(this.value); //jQuery(this).val();
            }).get();
            
            return (booking_ids); //booking_ids;
        }
        let currentPopover = null;
        let statusColor = {
            "wpb-pending": 'var(--bs-warning-bg-subtle)',
            "wpb-approved": 'var(--bs-success-bg-subtle)',
            "wpb-cancelled": 'var(--bs-danger-bg-subtle)',
            "wpb-completed": 'var(--bs-info-bg-subtle)'
        }
        let status = {
            "wpb-pending": 'bg-warning',
            "wpb-approved": 'bg-success',
            "wpb-cancelled": 'bg-danger',
            "wpb-completed": 'bg-info'
        }
        let currentView = 'dayGridMonth';
        this.calendar = new Calendar({
            target: document.getElementById("event-calendar"),
            props: {
                plugins: [TimeGrid, DayGrid, List, ResourceTimeGrid, ResourceTimeline],
                options: {
                    buttonText: (text) => (
                        {
                            ...text,
                            dayGridMonth: "Month",
                            listDay: "List",
                            listMonth: "List",
                            listWeek: "List",
                            listYear: "List",
                            timeGridDay: "Day",
                            resourceTimeGridDay: "Day",
                            timeGridWeek: "Week",
                            today: "Today",
                        }
                    ),
                    headerToolbar: {
                        start: "prev,next today",
                        center: "title",
                        end: "dayGridMonth,timeGridWeek,resourceTimeGridDay,listWeek",
                    },
                    view: "dayGridMonth",
                    eventStartEditable: true,
                    lazyFetching: true,
                    loading: (isLoading) => {
                        if (isLoading) {
                            jQuery("#event-calendar").addClass('ec-loading');
                        } else {
                            jQuery("#event-calendar").removeClass('ec-loading');
                        }
                    },
                    eventContent: function (arg) {
                        
                        if (arg.view.type === 'listWeek') {
                            return {
                                html: `
                                <div class="ec-event-box ec-event-list d-flex justify-content-between flex-sm-row flex-column gap-3 ${arg["event"]["extendedProps"]["status"]}">
                                    <div class="">
                                        <div class="ec-event-time">${icon}<strong> ${arg.timeText}</strong></div>
                                        <div>${arg.event.title.html}</div>
                                    </div>
                                    <div class="ec-event-action">
                                        <div class="d-flex align-items-sm-end align-items-start justify-content-between flex-column h-100">
                                            <div class="list-event-date">
                                                <span class="event-date">${arg.event.extendedProps.list_view_date}</span>
                                                <span class="event-date event-day">${arg.event.extendedProps.dayname}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-3 mt-3">
                                                <button class="button-action edit-event bg-success" data-event='${JSON.stringify(arg.event)}'><img src="${editIconWhite}" alt="edit"></button>
                                                <button class="button-action delete-event bg-danger" data-event='${JSON.stringify(arg.event)}'><img src="${deleteIconWhite}" alt="delete"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>`,
                            };
                        } else {
                            return {
                                html: `
                                <div class="popover-container position-relative dropdown dropup">
                                    <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"></div>
                                    <div class="dropdown-menu popups p-0 shadow-none"></div>
                                </div>
                                <div class="ec-event-box ${arg["event"]["extendedProps"]["status"]}">
                                    <div class="ec-event-time">${icon}<strong> ${arg.timeText}</strong></div>
                                    <div class="ec-event-title">${arg.event.title.html}</div>
                                </div>`,
                            };
                        }
                    },
                    eventSources: [
                        {
                            events: function (fetchInfo, successCallback, failureCallback) {
                                let data = {
                                    start: 0,
                                    length: 30,
                                    booking_type: get_booking_type_ids(),
                                };
                                
                                get("calendar_booking_list", data).then((resData) => {
                                    const events = resData.data.map((event) => ({
                                        id: event.id,
                                        title: {
                                            html: `
                      <div class="${event.status.key}">
                        <div class="mb-1 booking-type event-info">${event.type}</div>
                        <div class="booking-name event-info">${event.name}</div>
                      </div>
                      `,
                                        },
                                        start: formatDate(event.date, event.start_time),
                                        end: formatDate(event.date, event.end_time),
                                        allDay: false,
                                        editable: true,
                                        extendedProps: {
                                            type: event.type,
                                            name: event.name,
                                            email: event.email,
                                            status: event.status.key,
                                            status_label: event.status.label,
                                            list_view_date: event.list_view_date,
                                            dayname: event.dayname,
                                        },
                                        backgroundColor: statusColor[event.status.key],
                                        textColor: "var(--bs-heading-color)"
                                    }));
                                    successCallback(events);
                                });
                            },
                        },
                    ],
                    filterResourcesWithEvents: true,
                    viewDidMount: (viewInfo) => {
                        const centerSection = jQuery("h2.ec-title");
                        flatpickr(centerSection, {
                            dateFormat: "Y-m-d",
                            onChange: (selectedDates, dateStr, instance) => {
                                this.calendar.setOption("date", selectedDates[0]);
                            },
                            locale:window.wpbookit.flatpicker
                        });
                    },
                    eventMouseEnter: (info) => {
                        const event = info.event;
                        const $el = jQuery(info.el).find(".popups");
                        let colorObject = {
                            "wpb-approved": 'var(--bs-success)',
                            "wpb-pending": 'var(--bs-warning)',
                            "wpb-cancelled": 'var(--bs-danger)',
                            "wpb-completed": 'var(--bs-info)'
                        }

                        let color = colorObject[event.extendedProps.status]; 

                        let badges = {
                            "wpb-approved": `<span class="badge bg-success-subtle p-2 text-success">Approved</span>`,
                            "wpb-pending": `<span class="badge bg-primary-subtle p-2 text-primary">Pending</span>`,
                            "wpb-cancelled": `<span class="badge bg-warning-subtle p-2 text-warning">Cancelled</span>`,
                            "wpb-completed": `<span class="badge bg-info-subtle p-2 text-info">Completed</span>`
                        } 

                        let status = badges[event.extendedProps.status];
                        const content = `
              <div class="popover-content p-3 rounded-3 ${event.extendedProps.status}" style="min-width: 260px;">
                <div class="popover-event-title d-flex gap-2 align-item-center justify-content-between">
                  <h6 class="popover-title mb-0" style="color: ${color};">${event.extendedProps.type}</h6>
                  <div class="d-flex align-items-center justify-content-end gap-3"> 
                    <a href="javascript:void(0)" id="edit-event" class="edit-booking-button">
                      <img src="${editIcon}" alt="edit">
                    </a>
                    <a href="javascript:void(0)" id="delete-event">
                      <img src="${deleteIcon}" alt="delete">
                    </a>
                  </div>
                </div>
                <div class="popover-event-body">
                  <div class="d-flex gap-2 align-item-center justify-content-between mb-2">
                    <span class="content-text font-size-12 fw-semibold">Name:</span>
                    <h6 class="mb-0 font-size-12 fw-semibold">${event.extendedProps.name}</h6>
                  </div>
                  <div class="d-flex gap-2 align-item-center justify-content-between mb-2">
                    <span class="content-text font-size-12 fw-semibold">Email:</span>
                    <h6 class="mb-0 font-size-12 fw-semibold">
                      ${event.extendedProps.email}
                    </h6>
                  </div>
                  <div class="d-flex gap-2 align-item-center justify-content-between">
                    <span class="content-text font-size-12 fw-semibold">Time:</span> 
                    <h6 class="mb-0 font-size-12 fw-semibold">${formatTime(event.start)}</h6>
                  </div>
                </div>
              </div>
            `;

                        $el.html(content);
                        jQuery('#edit-event').on('click', (e) => {
                            this.EditDataeventHandlers(e, event);
                            $el.html('');
                        });

                        jQuery('#delete-event').on('click', (e) => {
                            this.Delete_Booking(e, event);
                            $el.html('');
                        });
                        jQuery('.dropdown-toggle', $el.parent().parent()).trigger('click');
                    },
                    eventMouseLeave: (info) => {
                        const $el = jQuery(info.el).find(".popups");
                        jQuery('.dropdown-toggle', $el.parent().parent()).trigger('click');
                        $el.html('');
                    },
                    eventClick: (info) => {
                        jQuery('#edit-event').trigger('click');
                    },
                    nowIndicator: true,
                },
            },
        })


        this.toggleDateAndTimeFields();

        this.validateField = this.validateField.bind(this);
        this.showError = this.showError.bind(this);
        this.hideError = this.hideError.bind(this);
        this.errorMessages = {
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
        
        };
        this.offcanvasElement = document.getElementById("booking-form");
        this.offcanvas = new Offcanvas(this.offcanvasElement);
        // this.bookingAdvancedOffcanvas = new Offcanvas(
        //   document.querySelector("#advance-filter")
        // );
        this.opencanvas();

        this.bookingViewModel = new Modal("#booking-detail-modal", {
            keyboard: false,
        });

        this.event_Handler();

        /*------------------------------------- 
        Slide scroll clickble Tab
        -------------------------------------*/
        if (document.querySelectorAll(".custom-nav-slider").length) {
            const slider = document.querySelectorAll('.custom-nav-slider');

            window.slide = function (direction, e) {
                var container = e.target.closest("div").parentElement.getElementsByClassName("custom-nav-slider");
                var parent = e.target.closest("div").parentElement;
                container.innerHTML = slidescroll(container, direction, parent);
            }

            function slidescroll(container, direction, parent, is_vertical = false) {
                var scrollCompleted = 0,
                    rightArrow = (parent != null) ? parent.getElementsByClassName("right")[0] : null,
                    leftArrow = (parent != null) ? parent.getElementsByClassName("left")[0] : null,
                    maxScroll = (parent != null) ? container[0].scrollWidth - container[0].offsetWidth - 30 : null,

                    slideVar = setInterval(function () {
                        if (direction == 'left') {
                            if (is_vertical) {
                                container[0].scrollTop -= 5;
                            } else {
                                container[0].scrollLeft -= 20;
                            }
                            if (parent != null) {
                                rightArrow.style.display = "block";
                                if (container[0].scrollLeft == 0)
                                    leftArrow.style.display = "none";
                            }
                        } else {
                            if (is_vertical) {
                                container[0].scrollTop += 5;
                            } else {
                                container[0].scrollLeft += 20;
                            }
                            if (parent != null) {
                                leftArrow.style.display = "block";
                                if (container[0].scrollLeft > maxScroll)
                                    rightArrow.style.display = "none";
                            }
                        }
                        scrollCompleted += 10;
                        if (scrollCompleted >= 100) {
                            window.clearInterval(slideVar);
                        }

                    }, 40);
            }

            if (slider) {
                slider.forEach(function (element) {
                    slideDrag(element);
                });
                enableSliderNav();
            }

            function enableSliderNav() {
                slider.forEach(function (element) {
                    if (element.parentElement.querySelector(".left")) {
                        var left = element.parentElement.querySelector(".left"),
                            right = element.parentElement.querySelector(".right");

                        if (element.scrollWidth - element.clientWidth > 0) {
                            right.style.display = "block";
                            left.style.display = "block";
                        } else {
                            right.style.display = "none";
                            left.style.display = "none";
                        }
                    }
                });
            }

            function slideDrag(eslider) {
                var isDown = false;
                var startX;
                var scrollLeft;
                var maxScroll = eslider.scrollWidth - eslider.clientWidth - 20;
                var rightArrow = eslider.parentElement.getElementsByClassName("right")[0];
                var leftArrow = eslider.parentElement.getElementsByClassName("left")[0];
                eslider.addEventListener('mousedown', (e) => {
                    isDown = true;
                    eslider.classList.add('active');
                    startX = e.pageX - eslider.offsetLeft;
                    scrollLeft = eslider.scrollLeft;
                });

                eslider.addEventListener('mouseleave', () => {
                    isDown = false;
                    eslider.classList.remove('active');
                });

                eslider.addEventListener('mouseup', () => {
                    isDown = false;
                    eslider.classList.remove('active');
                });

                eslider.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - eslider.offsetLeft;
                    const walk = (x - startX) * 3; //scroll-fast
                    eslider.scrollLeft = scrollLeft - walk;
                    if (eslider.scrollLeft > maxScroll) {
                        rightArrow.style.display = "none";
                    } else {
                        if (eslider.scrollLeft == 0) {
                            leftArrow.style.display = "none";
                        } else {
                            leftArrow.style.display = "block";
                        }
                        rightArrow.style.display = "block";
                    }

                });
            }

            window.addEventListener('resize', function () {
                enableSliderNav();
            });
        }
    }

    how_booking_model(id) {
        let {
            type,
            duration,
            datetime,
            email,
            date_created,
            questions_answers,
            location,
            location_source,
        } = this.bookingTableData.find((item) => item.id == id);

        let model = jQuery(this.bookingViewModel._element);

        model.find(".wpb-booking-type").html(type);
        model.find(".wpb-booking-date-time").html(datetime);
        model.find(".wpb-booking-duration").html(duration);
     
        model.find(".wpb-booking-user-email").html(email);
        model.find(".wpb-booking-created").html(date_created);
        model.find(".wpb-booking-questions").html("");
        if (location == "online_video") {
            model.find(".wpb-booking-meeting").show();
            // wpb-booking-meeting
            if (location_source) {
                model
                    .find(".wpb-booking-meeting-link")
                    .attr("href", location_source)
                    .html(location_source);
            } else {
                model
                    .find(".wpb-booking-meeting")
                    .attr("style", "display:none !important");
            }
        } else {
            model
                .find(".wpb-booking-meeting")
                .attr("style", "display:none !important");
        }

        questions_answers.length !== undefined &&
            questions_answers.forEach(function (el) {
                let item = `   <li class="mb-3">
            <div class="align-items-center gap-1 flex-wrap">
                <h6 class="mb-0 question">${el.question}</h6>
                <span class="d-inline-block ans mt-1">${el.ans}</span>
            </div>
        </li>`;
                model.find(".wpb-booking-questions").append(item);
            });

        this.bookingViewModel.show();
    }

    event_Handler() {
        jQuery(document).ready(this.intialize_datepicker());
        jQuery(document).ready(this.addTooltips());

        jQuery(document.body).on(
            "click",
            ".dashboard-page .delete-booking-button",
            this.Delete_Booking.bind(this)
        );

        jQuery(document.body).on(
            "submit",
            "#booking-form #add-booking-form",
            this.SubmitbtnEventHandlers.bind(this)
        );
        jQuery(document.body).on(
            "click",
            "#booking-form .btn-close",
            this.PopupCloseEvent.bind(this)
        );
        jQuery(document.body).on(
            "click",
            ".edit-booking-button",
            this.EditDataeventHandlers.bind(this)
        );
        jQuery(document.body).on(
            "change",
            "#booking-form #wpb_booking_type",
            this.ChangePricewithbookingtype.bind(this)
        );

        this.offcanvasElement.addEventListener("hidden.bs.offcanvas", () => {
            document.querySelector("#add-booking-form").reset();
            jQuery("#add-booking-form select").trigger("change");
        });

        this.offcanvasElement.addEventListener("show.bs.offcanvas", (e) => {
            if (e.relatedTarget === undefined) {
                e.target
                    .querySelector(".offcanvas-title-edit")
                    .classList.remove("d-none");
                e.target.querySelector(".offcanvas-title-add").classList.add("d-none");
            } else {
                e.target.querySelector(".offcanvas-title-edit").classList.add("d-none");
                e.target
                    .querySelector(".offcanvas-title-add")
                    .classList.remove("d-none");

                jQuery("#wpb_booking_type").prop("disabled", false).trigger("change");
                jQuery("#wpb_booking_slot_time")
                    .prop("disabled", false)
                    .trigger("change");
                jQuery("#wpb-datepicker").prop("disabled", false).trigger("change");
                jQuery('#wpb_customer option[value="temp"]').remove();
                jQuery("#wpb_customer").prop("disabled", false).trigger("change");
                jQuery("#wpb_booking_payment_mode")
                    .prop("disabled", false)
                    .trigger("change");
            }
        });

        jQuery(document.body).on("click", ".refresh-btn", () => {
            jQuery(document).trigger('refreshCalendar', this.calendar);
            const __this = jQuery('.refresh-btn');
            __this.find('img').addClass('spinner-reverse');
            this.RefreshCalendareventHandlers();
            setTimeout(() => {
                __this.find('img').removeClass('spinner-reverse');
            }, 1000)
        });

        jQuery(document.body).on("click", ".print-btn", () => {
            this.print_ajax();
        });
        jQuery(document.body).on("click", ".edit-event", (e) => {
            const __this = jQuery(e.currentTarget);
            let event =  __this.data('event');
            this.EditDataeventHandlers(e, event);
        });
        jQuery(document.body).on("click", ".delete-event", (e) => {
            const __this = jQuery(e.currentTarget);
            let event =  __this.data('event');
            this.Delete_Booking(e, event);
        });

    }

    print_ajax() {
        let printContent = jQuery('#event-calendar');
        let printWindow = window.open('', '_blank', 'width=800,height=700,scrollbars=yes');
        printWindow.document.open();
        printWindow.document.write('<html xmlns="http://www.w3.org/1999/xhtml"><head><meta charset="UTF-8"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>Print Calendar</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
        printWindow.document.write(`<style>@media print { body * { visibility: hidden; } #printable-calendar, #printable-calendar * { visibility: visible; } #printable-calendar { position: absolute; left: 0; top: 0; right: 0; } }</style>`);
        printWindow.document.write(`<style type="text/css"> #printable-calendar .ec-toolbar{ display: none;}</style>`);
        printWindow.document.write(`<style type="text/css">${calendarStyle}</style>`);
        printWindow.document.write(`<style type="text/css">${appCss}</style>`);
        printWindow.document.write('</head><body>');
        printWindow.document.write('<div id="printable-calendar">' + printContent.html() + '</div>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    }


    /**
     * Initialize the datepicker for the booking date input field.
     */
    intialize_datepicker() {
        // Store a reference to the current object in a variable.
        const self = this;

        // Get the current date.
        let TodayDate = new Date();

        // Initialize the datepicker for the booking date input field.
        this.bookingDateFlatpickerInstance = flatpickr("#wpb-datepicker", {
            // Set the date format.
            dateFormat: window.wpbookit.date_format,

            // Callback function called when the selected dates change.
            onChange: function (selectedDates, dateStr, instance) {
                // Handle time slot range changes.
                self.handleTimeSlotRange(selectedDates);
            },

            // Callback function called when a day element is created.
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                // Check if the booking type available days are defined and not null.
                if (
                    this.bookingTypeAvailableDays === undefined ||
                    this.bookingTypeAvailableDays === null
                ) {
                    // Return early if they are not defined or null.
                    return;
                }

                // Check if the day element's date is after today's date,
                // if it is a day that is available for booking type,
                // and if it is within the maximum booking days.
                if (
                    dayElem.dateObj > TodayDate &&
                    this.days[dayElem.dateObj.getDay()] in
                    this.bookingTypeAvailableDays &&
                    dayElem.dateObj <=
                    new Date().fp_incr(this.bookingMaxPostBookingDays)
                ) {
                    // Add the "wpb-available" class to the day element.
                    dayElem.classList.add("wpb-available");
                }

                // Format the date and check if it is a booking specific day.
                let formattedDate =
                    dayElem.dateObj.getFullYear() +
                    "-" +
                    this.get2DigitFmt(dayElem.dateObj.getMonth() + 1) +
                    "-" +
                    this.get2DigitFmt(dayElem.dateObj.getDate());
                if (this.bookingSpecificDay[formattedDate]) {
                    // Add the "wpb-available" class to the day element.
                    dayElem.classList.add("wpb-available");
                }
            },

            // Callback function called when the calendar is ready.
            onReady(_, __, fp) {
                // Add the "wpb-booking-calender" class to the calendar container.
                fp.calendarContainer.classList.add("wpb-booking-calender");
            },
            locale:window.wpbookit.flatpicker
        });
    }


    /**
     * Formats a given number into a 2-digit string.
     *
     * @param {number} val - The number to be formatted.
     * @return {string} - The formatted number as a 2-digit string.
     */
    get2DigitFmt(val) {
        // Prepend a '0' to the number if it is less than 10.
        // This ensures that the number is always represented as a 2-digit string.
        return ("0" + val).slice(-2);
    }


    /**
     * Handles the time slot range for the selected dates.
     * 
     * @param {Array} selectedDates - The selected dates.
     */
    handleTimeSlotRange(selectedDates) {
        // Clear the time slot dropdown.
        this.timeSlotDropDown.innerHTML = "";

        // Get the first selected date.
        const [selectedDate] = selectedDates;

        // Format the date into a localized string.
        const options = { day: "2-digit", month: "long", year: "numeric" };
        const dateString = selectedDate.toLocaleDateString("en-US", options);

        // Fetch the time slots for the selected date and booking type.
        get("get_booking_timeslot_dashboard", {
            selected_date: dateString,
            bookingTypeId: this.bookingTypeId.value,
            _ajax_nonce: window.wpb_nounce,
        })
            .then((res) => {
                // If the response contains valid data.
                if (res && res.data && Array.isArray(res.data)) {
                    // Iterate over each time slot.
                    res.data.forEach((time, index) => {
                        // Create a new option element.
                        const option = new Option(time, time, index == 0, index == 0);
                        // Add the option to the time slot dropdown.
                        this.timeSlotDropDown.add(option);
                    });
                    // Toggle the date and time fields.
                    this.toggleDateAndTimeFields();
                } else {
                    // Log an error if the response format is unexpected.
                    console.error("Unexpected response format from server");
                }
            })
            .catch((error) => {
                // Log an error if there is an error fetching the timeslots.
                console.error("Error fetching timeslots:", error);
            });
    }


    /**
     * Add tooltips to elements with the data-bs-toggle="tooltip" attribute.
     *
     * This function initializes the Bootstrap tooltip plugin on elements with the
     * specified attribute, allowing them to display a tooltip when hovered over.
     */
    addTooltips() {
        // Select all elements with the data-bs-toggle="tooltip" attribute.
        const tooltipElements = jQuery('[data-bs-toggle="tooltip"]');

        // Initialize the Bootstrap tooltip plugin on the selected elements.
        tooltipElements.tooltip();
    }


    /**
     * Closes the offcanvas and clears the form fields.
     *
     * This function hides the offcanvas and clears the values of various form fields.
     * It is typically called when the user closes the offcanvas.
     */
    PopupCloseEvent() {
        // Hide the offcanvas.
        this.offcanvas.hide();

        // Clear the values of various form fields.
        jQuery("#edit-booking-id").val(""); // Clear the booking ID field
        jQuery("#add-booking-form")
            .find("input, select, textarea")
            .val(""); // Clear all input, select, and textarea fields
        jQuery("#wpb_booking_type")
            .val("")
            .trigger("change"); // Clear the booking type field and trigger a change event
        jQuery("#wpb_booking_status")
            .val("")
            .trigger("change"); // Clear the booking status field and trigger a change event
        jQuery("#wpb_customer")
            .val("")
            .trigger("change"); // Clear the customer field and trigger a change event
    }


    /**
     * Deletes a booking.
     *
     * @param {Event} e - The event object.
     * @param {Object} event - The event object containing the booking details.
     */
    Delete_Booking(e, event) {
        e.preventDefault();

        // Get the booking ID and name from the event object.
        const bookingID = event.id;
        const bookingName = event.extendedProps.name;

        // Using wp.i18n for translations.
        const { __ } = wp.i18n;

        // Construct the confirmation message using the booking name.
        var cMessage = window.wpbookit.dashbord_language.booking.confirm_delete_boooking + bookingName + "?";

        // Show a confirmation dialog and delete the booking if confirmed.
        if (confirm(cMessage) == true) {
            post("delete_booking", { bookingID: bookingID }).then((response) => {
                // Get the response status and show a notification.
                const resStatus = response.status;
                notificationToast[resStatus](
                    response.message,
                    resStatus.toUpperCase(),
                    { autoClose: true }
                );

                // Refresh the calendar events.
                this.RefreshCalendareventHandlers();
            });
        }
    }


    /**
     * Refreshes the calendar events by calling the `refetchEvents` method.
     * This method is called when a booking is deleted or when a booking type is changed.
     */
    RefreshCalendareventHandlers() {
        // Call the `refetchEvents` method of the calendar object to refresh the events.
        this.calendar.refetchEvents();
    }


    /**
     * Handles the form submission event for adding or updating a booking.
     * @param {Event} e - The form submission event.
     */
    SubmitbtnEventHandlers(e) {
        e.preventDefault(); // Prevent the default form submission behavior.
        const _this = this;

        // Get form field values.
        var booking_id = jQuery("#edit-booking-id").val();
        var formFields = {
            booking_type: jQuery("#wpb_booking_type").val(),
            booking_date: jQuery("#wpb-datepicker").val(),
            booking_time: jQuery("#wpb_booking_slot_time").val(),
            booking_customer: jQuery("#wpb_customer").val(),
            booking_status: jQuery("#wpb_booking_status").val(),
        };

        if (this.isFreeBookingType) {
            delete formFields.booking_payment_mode;
        }
        let isValid = []; // Array to store validation results.

        console.log(_this.isFreeBookingType);
        
        // Validate each form field.
        jQuery.each(formFields, function (fieldName, fieldValue) {
            var error = _this.validateField(
                fieldValue,
                _this.errorMessages[fieldName].selector,
                _this.errorMessages[fieldName].blank,
                _this.errorMessages[fieldName].invalid
            );
            if (error === undefined) {
                // Clear any error messages.
                document.querySelector(
                    _this.errorMessages[fieldName].selector
                ).innerHTML = "";
            }
            isValid.push(error); // Add validation result to the array.
        });

        if (booking_id) {
            // Handle updating an existing booking.
            _this.handleUpdateBooking(e, isValid);
        } else {
            // Handle adding a new booking.
            _this.handleAddBooking(e, isValid);
        }
    }

    /**
     * Handles the form submission event for updating a booking.
     * @param {Object} formFields - The form field values.
     * @param {Array} isValid - The array of validation results.
     */
    handleUpdateBooking(e, isValid) {
        const form = jQuery(e.currentTarget).closest("form")[0];
        const formData = new FormData(form);

        if (isValid.indexOf(false) !== -1) return false;

       jQuery("#wpb-submit-booking").prop("disabled", true);
       jQuery(".wpb-booking-submit-svg").removeClass("d-none");
       
        post("add_update_booking", formData)
            .then((response) => {
                this.handleResponse(response, "success");
            })
            .catch((error) => {
                this.handleError(error);
            });
    }

    /**
     * Handles the form submission event for adding a new booking.
     * @param {Object} formFields - The form field values.
     * @param {Array} isValid - The array of validation results.
     */
    handleAddBooking(e, isValid) {
        const bookingType = jQuery("#wpb_booking_type option:selected")
            .text()
            .trim();
        const form = jQuery(e.currentTarget).closest("form")[0];
        const formData = new FormData(form);

        if (isValid.indexOf(false) !== -1) return false;

        jQuery("#wpb-submit-booking").prop("disabled", true);
        jQuery(".wpb-booking-submit-svg").removeClass("d-none");
        formData.append("bookingType", bookingType);
        post("add_update_booking", formData)
            .then((response) => {
                this.handleResponse(response, "success");
            })
            .catch((error) => {
                this.handleError(error);
            });
    }

    /**
     * Handles the response from the server after updating or adding a booking.
     * @param {Object} response - The response object from the server.
     * @param {string} status - The status of the response.
     */
    handleResponse(response, status) {
        const { message } = response.data;
        if (status === "success") {
            this.PopupCloseEvent();
            this.RefreshCalendareventHandlers();
        }
        notificationToast[status](message, status.toUpperCase(), {
            autoClose: true,
        });
        jQuery("#wpb-submit-booking").prop("disabled", false);
        jQuery(".wpb-booking-submit-svg").addClass("d-none");
    }

    /**
     * Handles any errors that occur during the form submission process.
     * @param {Error} error - The error object.
     */
    handleError(error) {
        jQuery("#wpb-submit-booking").prop("disabled", false);
        jQuery(".wpb-booking-submit-svg").addClass("d-none");
        console.error("Error :", error);
    }


    /**
     * Displays an error message by setting the text of the element specified by 
     * elementId to errorMessage and showing it.
     * @param {string} elementId - The id of the element to display the error message on.
     * @param {string} errorMessage - The error message to display.
     */
    showError(elementId, errorMessage) {
        // Set the text of the element to the error message
        jQuery(elementId).text(errorMessage);

        // Show the element
        jQuery(elementId).show();
    }


    /**
     * Hides the element with the specified elementId.
     * 
     * @param {string} elementId - The id of the element to hide.
     */
    hideError(elementId) {
        // Hide the element
        jQuery(elementId).hide(); // Using jQuery's hide method to hide the element
    }


    /**
     * Validates a field by checking if it is blank or not.
     * If the field is blank, it displays an error message.
     * If the field is not blank, it hides any error message.
     *
     * @param {string} value - The value of the field to be validated.
     * @param {string} errorSelector - The selector for the error element.
     * @param {string} blankErrorMessage - The error message to display if the field is blank.
     * @param {string} invalidErrorMessage - The error message to display if the field is invalid.
     * @return {boolean} Returns true if the field is not blank, false otherwise.
     */
    validateField(value, errorSelector, blankErrorMessage, invalidErrorMessage) {
        if (!value) {
            // If the field is blank, display the error message.
            this.showError(errorSelector, blankErrorMessage);
            return false;
        } else {
            // If the field is not blank, hide any error message.
            this.hideError(errorSelector);
            return true;
        }
    }



    /**
     * Event handler for editing data.
     *
     * @param {Event} e - The event object.
     * @param {Object} event - The event data.
     */
    EditDataeventHandlers(e, event) {
        // Prevent the default action of the event
        e.preventDefault();

        // Get the booking ID from the event data
        const booking_id = event.id;

        // Remove the 'd-none' class from the payment section
        this.paymentSection.removeClass("d-none");

        // Get the payment section element and set its display to an empty string
        var divElement = document.querySelector("#wpb-payment-section");
        divElement.style.display = "";

        // If the booking ID is not empty
        if (booking_id) {
            // Show the offcanvas
            this.offcanvas.show();

            // Make an AJAX request to get the booking details
            get("get_booking_details", { booking_id: booking_id }).then(response => {
             this.offcanvas.show();
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
                
                this.isFreeBookingType =response.payment_status==null
                
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
                jQuery('#wpb_booking_payment_mode').prop("disabled", true).val(payment_mode).trigger('change');
                jQuery('#wpb_booking_status').val(booking_status).trigger('change');
                jQuery('#notesFormControlTextarea1').val(booking_notes);
                document.getElementById('booking_price').innerHTML = response.booking_price_html

                this.timeSlotDropDown.add(new Option(booking_time, booking_time))
                jQuery(this.timeSlotDropDown).trigger('change')
                jQuery(document).trigger('editBooking', response);
            });
        }
    }


    /**
     * Handles the event when the booking type is changed and updates the price and available time slots.
     * @param {Event} e - The event object.
     */
    ChangePricewithbookingtype(e) {
        // Toggle the date and time fields
        this.toggleDateAndTimeFields();

        // Clear the date and time picker fields
        document.getElementById("wpb-datepicker").value = "";
        var bookingSlotTimeSelect = document.getElementById(
            "wpb_booking_slot_time"
        );
        bookingSlotTimeSelect.innerHTML = "";

        e.preventDefault();

        // Get the selected booking type
        const __this = jQuery(e.currentTarget);
        const booking_type_id = __this.val();

        

        // Make a POST request to select the booking type and update the available days and time slots
        post("select_booking", { booking_type_id: booking_type_id }).then(
            (response) => {
                // Update the specific days, available days, and maximum post booking days
                this.bookingSpecificDay = response.data.specific_dates;
                this.bookingTypeAvailableDays = response.data.weekly_time_slots;
                this.bookingMaxPostBookingDays = response.data.how_far;
            }
        );
    }



    /**
     * Opens the offcanvas if the URL parameter "offcanvas" is present.
     */
    opencanvas() {
        // Get the URL parameters
        const params = new URLSearchParams(window.location.search);

        // Check if the "offcanvas" parameter is present
        if (params.has("offcanvas")) {
            // Show the offcanvas
            this.offcanvas.show();
        }
    }




    /**
     * Toggles the disabled state of the booking date and time select elements
     * based on the value of the booking type select element.
     */
    toggleDateAndTimeFields() {
        // Disable the booking date select element if the booking type select element
        // does not have a value.
        this.bookingDateDropDown.disabled = (
            this.bookingTypeId.value === ""
        );

        // Disable the time slot select element if either the booking date select
        // element does not have a value or the booking type select element does not
        // have a value.
        this.timeSlotDropDown.disabled = (
            this.bookingDateDropDown.value === "" ||
            this.bookingTypeId === ""
        );
    }
    /**
     * Handles the 'change' event of the booking type select element.
     * When the booking type select element is changed, it updates the visibility of
     * the booking type checkboxes based on the selected value.
     * Finally, it updates the resources data and refetches the events in the calendar.
     *
     * @param {Event} event - The event object.
     */
    onChangeBookingType(event) {
       
        // Get the jQuery object of the booking type select element.
        const _this = jQuery(event);

        // Check if the selected value is 'all'.
        if (_this.val() == 'all') {
            // Check if there are any booking type checkboxes.
            if (jQuery('.booking-type-checkboxes').length > 0) {
                // Check or uncheck the booking type checkboxes based on the booking type select element.
                jQuery('.booking-type-checkboxes').prop('checked', _this.prop('checked'));
            }
            else {
                // Uncheck the booking type select element.
                _this.prop('checked', false)
            }
        }
        else {
            // Check if the 'all booking types' checkbox is checked.
            jQuery('.all-booking-type-checkboxes').prop('checked',
                jQuery('.booking-type-checkboxes:checked').length ===
                jQuery('.booking-type-checkboxes').length);
        }

        // Update the resources data.
        // Refresh the events in the calendar.
        this.calendar.refetchEvents();
    }
}
