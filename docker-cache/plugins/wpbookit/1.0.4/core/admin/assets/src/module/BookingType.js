import notificationToast from "../utils/notification-toast";
import {
    post, get
} from "../utils/ajax";
import { Tab } from 'bootstrap'
import debounce from "../utils/helper";
import { Offcanvas } from 'bootstrap';


export default class BookingType {

    wpbBookingTypeApplyAdvancedBooking

    wpbBookingTypeNewBookingTabInstance
    wpbBookingTypeAdvanceBookingTabInstance

    wpbUploadedCoverImageElement
    wpbUploadedCoverImageDisplayElement

    questionCount

    statusChange = debounce((e) => this.changeStatus(e));

    wpbDay

    $cloneSpecificDatefield

    constructor() {
        this.addCanvas = jQuery("#add-booking-type");
        this.wpbDay = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        this.EditeventHandlers()
        this.AddeventHandlers()
        this.showweekly();
        this.showspecific();
        this.addAvailable();
        this.commonevent(this);
        this.toggleAvailability();
        this.question_counting = "1";
        jQuery(".showspecific").hide();
        this.validateField = this.validateField.bind(this);
        this.init()
        this.addEventListener()
        this.validateBookingNumber();
        this.questionCount = 1;

        this.errorMessages = {
            cover_image_id: {
                selector: "#cover_image_error",
                blank: window.wpbookit.dashbord_language.validation.select_cover_image,
            },
            title: {
                selector: "#title_error",
                blank: window.wpbookit.dashbord_language.validation.enter_title,
            },
            slug: {
                selector: "#slug_error",
                blank: window.wpbookit.dashbord_language.validation.enter_slug,
            },
            url: {
                selector: "#url_error",
                blank: window.wpbookit.dashbord_language.validation.enter_url,
            },
            duration: {
                selector: "#duration_error",
                blank: window.wpbookit.dashbord_language.validation.select_duration,
            },
            description: {
                selector: "#description_error",
                blank: window.wpbookit.dashbord_language.validation.enter_description,
            },
            weekly_date_not_selected: {
                selector: "#weekly_error",
                blank: window.wpbookit.dashbord_language.validation.enter_time_slot,
            },
            unavailable_date_not_selected: {
                selector: "#unavailable_error",
                blank: window.wpbookit.dashbord_language.validation.select_date_time,
            },
            questions_not_enter: {
                selector: "#questions_error",
                blank: window.wpbookit.dashbord_language.validation.questions_not_enter,
            },
        }
        this.offcanvasElement = document.getElementById('add-booking-type');
        this.offcanvas = new Offcanvas(this.offcanvasElement);

     
    }

    // Function to ensure TinyMCE is initialized before accessing it
    ensureTinymceInitialized(editorId, callback) {
        let self = this;
        let is_editor = false
        let editorElement = jQuery('.mce-tinymce.mce-container.mce-panel')
        if (editorElement.length === 0 || editorElement.is(':hidden')){
            is_editor = false
        }else{
            is_editor = true
        }
        if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
            callback(tinymce.get(editorId),is_editor);
        } else if(tinymce.get(editorId) ==  null){
            callback(tinymce.get(editorId),is_editor);
        } else {
            setTimeout(function () {
                self.ensureTinymceInitialized(editorId, callback);
            }, 100); // Check again after 100ms
        }
    }

    validateBookingNumber() {
        document.querySelector('.wpb-booking-type-apply-advanced-booking').addEventListener('click', () => {
            let error = false;
            const staticUrlInput = document.getElementById('static_url');
            const staticUrl = staticUrlInput.value || '';
            const videoConference = jQuery("#video_conference").is(':checked');
            const staticUrlErrorSpan = document.getElementById('static_url_error');
            const meetingLinkType = document.getElementById('meeting_link_type');
            const noLocationCheckbox = document.getElementById('no_location');
            const urlPattern = /https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w/_\.]*(\?\S+)?)?)?/;
            
            const additionalLinkTypes = wp.hooks.applyFilters('wpb_after_zoom_customMeetingLinkTypes', ['zoom']); 

            if (noLocationCheckbox.checked) {
                staticUrlErrorSpan.textContent = ""; 
                error = false;
            } else {
                if (videoConference && meetingLinkType.value == 'custom_link' && staticUrl == '') {
                    staticUrlErrorSpan.textContent = window.wpbookit.dashbord_language.validation.enter_meeting_url;
                    error = true;
                } else {
                    staticUrlErrorSpan.textContent = ""; // Clear error message
                    error = false;
                }
            }
            
            
            const addressInput = document.getElementById('address_input');
            const meetingAddress = addressInput.value || '';
            const physicalAddress = jQuery("#physical_address").is(':checked');
            const physicalAddressErrorSpan = document.getElementById('physical_address_error');
            if (physicalAddress && meetingAddress == '' ) {
                physicalAddressErrorSpan.textContent = window.wpbookit.dashbord_language.validation.enter_meeting_address;
                error = true;
            } else {
                physicalAddressErrorSpan.textContent = ""; // Clear error message
            }


        const enableGroupBooking = jQuery("#enable_group_booking").is(':checked');
        const slotsBookingNumberInput = document.getElementById('slots_booking_number');
        const slotsBookingNumber = parseInt(slotsBookingNumberInput.value) || 0;
        const groupBookingErrorSpan = document.getElementById('group_booking_error');

        if (enableGroupBooking && slotsBookingNumber < 2) {
            groupBookingErrorSpan.textContent = window.wpbookit.dashbord_language.validation.enter_meeting_address;
            error = true;
        } else {
            groupBookingErrorSpan.textContent = "";
        }

        const redirectionUrlContainer = document.getElementById('redirection_url_container');
        const redirectionUrlInput = document.getElementById('redirection_url');
        const redirectionUrlErrorSpan = document.getElementById('redirection_url_error');
        const urlPatterns = /https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w/_\.]*(\?\S+)?)?)?/;


            
        // Proceed if no errors
        if (!error) {
            this.wpbBookingTypeNewBookingTabInstance.show();
        }
            if (jQuery('input[name="location"]:checked').val() === 'phone_number') {

                var mobileNumber = jQuery('#phone_number').val();
                var mobileNumberPattern = /^[0-9]{10}$/;

                if (mobileNumberPattern.test(mobileNumber)) {
                    jQuery('#phone_number').next().text('');
                } else {
                    jQuery('#phone_number').next().text('Invalid mobile number');
                    error = true;
                }
            }

            // Other validation checks
            if (!error) {
                this.wpbBookingTypeNewBookingTabInstance.show()
            }
        });
    }
    init() {
        this.wpbBookingTypeApplyAdvancedBooking = document.querySelector('.wpb-booking-type-apply-advanced-booking');

        this.wpbBookingTypeNewBookingTabInstance = new Tab(document.querySelector('#nav-newbooking-tab'));
        this.wpbBookingTypeAdvanceBookingTabInstance = new Tab(document.querySelector('#nav-advancebooking-tab'));

        const cancelButton = document.getElementById('cancel-booking-type');
        const saveButton = document.getElementById('wpb-save-booking-type');
        const applyAdvancedButton = document.querySelector('.wpb-booking-type-apply-advanced-booking');

       var offcanvasElement = document.querySelector('#add-booking-type');

        document.querySelector('#nav-newbooking-tab').addEventListener('show.bs.tab', function () {
            cancelButton.style.display = 'block';
            saveButton.style.display = 'block';
            applyAdvancedButton.style.display = 'none';
        })
        document.querySelector('#nav-advancebooking-tab').addEventListener('show.bs.tab', function () {
            cancelButton.style.display = 'none';
            saveButton.style.display = 'none';
            applyAdvancedButton.style.display = 'block';
        })


        this.enable_group_booking = document.querySelector('#enable_group_booking');
        this.slots_per_booking_number_container = document.querySelector('#slots_per_booking_number_container')


        this.$cloneSpecificDatefield = jQuery('.dateContainer .new').first().clone();
        this.$cloneSpecificDatefield.find('input').val('')
        jQuery('.dateContainer').empty()

    
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
    showError(elementId, errorMessage) {
        jQuery(elementId).text(errorMessage).show();
    }
    hideError(elementId) {
        //jQuery(elementId).hide();
    }
    addEventListener() {
        
        jQuery('#page_layout').on('change', '.wpb-booking-type-status', (e) => this.statusChange(e))

        document.querySelector('#add-booking-type').addEventListener('hidden.bs.offcanvas', () => {
            jQuery('.error').text('');
            jQuery('#price').val(0);
            jQuery('.time_slot').not(':last-child').remove(0);
            jQuery('.weekly_day_checkbox').trigger('change');
            jQuery('input[type="time"]').val('')
            jQuery('#booking_type_id').val('');
        
            
            jQuery('.remove_weekly_slot').closest('.days_slots').remove()

            jQuery('.wpb-icon-wrapper').show();
            jQuery('#cover_image_preview-btn').hide();
            jQuery('#cover_image_preview').hide();
           
            jQuery('.time_slot input[type="time"]').each(function() {
                jQuery(this).val('').trigger('change');
                this.defaultValue = ''; 
            });
            const unavailableDates = document.querySelectorAll('input[name="unavailable_date[]"]');
            unavailableDates.forEach(input => {
                input.value = '';
            });
            const dateContainer = document.getElementById('undateContainer');

            const dateRows = dateContainer.querySelectorAll('.date-row');

            dateRows.forEach((row, index) => {
                if (index !== 0) {
                    row.remove();
                }
            });
            document.getElementById('add_unvailable_date').checked = false;
            document.querySelector('#add-booking-type-btn').addEventListener('click', function () {
                this.wpbBookingTypeNewBookingTabInstance.show(); 
            }.bind(this));
        })
        document.querySelector('#add-booking-type').addEventListener('shown.bs.offcanvas', () => {
            jQuery('.weekly_day_checkbox').trigger('change');
            
        })

        jQuery(this.slots_per_booking_number_container).toggle(!this.enable_group_booking.checked)
        jQuery(this.enable_group_booking).on('change', (e) => {
            jQuery(this.slots_per_booking_number_container).toggle(e.currentTarget.checked)
        })

        this.addCanvas.find('#meeting_link_type').on('change',function(){
            let value= jQuery(this).val();
            if(value ==null){
                value='custom_link';
            }
            jQuery(this).closest('#link_type_field').find('.meeting-link-control').hide()
            jQuery(this).closest('#link_type_field').find(`.meeting-link-control.${value}`).show()
        });

        // Add event listeners to validate when time fields are changed
        this.wpbDay.forEach(day => {
            const timeFromFields = document.getElementsByName(`${day}_time_from[]`);
            const timeToFields = document.getElementsByName(`${day}_time_to[]`);

            timeFromFields.forEach((timeFrom, index) => {
                timeFrom.addEventListener('change', () => this.validateTime(day,true));
            });

            timeToFields.forEach((timeTo, index) => {
                timeTo.addEventListener('change', () => this.validateTime(day,false));
            });
        });
    }

    EditeventHandlers() {
        var self = this;

        jQuery(document.body).on("click", "#edit-user-button", function (event) {
            event.preventDefault();
            var user_id = jQuery(this).data('id');
            self.edit_user_ajax(user_id);
        });

        jQuery(document.body).on("click", ".BookingTypeDeleteButton", function (event) {
            event.preventDefault();
            var BookingTypeID = jQuery(this).data('id');
            var name = jQuery(this).data('name');
            self.delete_booking_type(BookingTypeID, name);
        });

        jQuery(document.body).on("click", ".BookingTypeCloneButton", function (event) {
            event.preventDefault();
            var BookingTypeID = jQuery(this).data('id');
            self.clone_booking_type(BookingTypeID);

        });

        jQuery(document.body).on("click", "#add-booking-type-btn", function (event) {
            event.preventDefault();
            self.questionCount = 1;
            document.getElementById('new-booking-label').textContent = "Add New Booking Type";
            document.querySelector('.booking_type_form').reset();
            jQuery('#booking_type_id').val('');
            jQuery('#add-booking-type').offcanvas('show');
            jQuery('.time_slot input[name*="_time_to[]"]').val("09:00").trigger('change'); 
            jQuery('.time_slot input[name*="_time_from[]"]').val("18:00").trigger('change'); 
            
            jQuery('input[name="unavailable_time_to[]"]').val('');
            jQuery('input[name="unavailable_time_from[]"]').val('');
            
        });

        jQuery(document.body).on("click", ".BookingTypeEditButton", function (event) {
            event.preventDefault();
            var BookingTypeID = jQuery(this).data('id');
            const booking_type_parent = jQuery(this).closest('.card-body');
            var booking_type_name = booking_type_parent.find('h5').text();
            jQuery('#add-booking-type').find('.offcanvas-title').text('Edit ' + booking_type_name + ' booking type');
            document.getElementById('new-booking-label').textContent = "Edit Booking Type";
            self.edit_booking_type(BookingTypeID);
        });


        jQuery(document.body).on("change", "input[name=booking_type]", function (event) {
            event.preventDefault();
            self.changeBookingTypeTab(jQuery(this).val());
        });

        jQuery(document.body).on("click", ".cancel_booking_type", this.CancleBookingBtn.bind(this));
        jQuery(document.body).on("click", "#add-booking-type-btn", this.addBookingtypeBtn.bind(this));
        jQuery(document.body).on("click", "#add-booking-type-new", this.match.bind(this));

        jQuery(document).on('click', '.delete-question-btn', function (event) {
            event.preventDefault();
            jQuery(this).closest('.new-question').remove();
        });
        jQuery(document).on('change', 'input#slug', this.generateSlug.bind(this));

    }
    match(e){
        e.preventDefault();
        jQuery('#title').val('').trigger('change');
        jQuery('#slug').val('').trigger('change');
        jQuery('#duration').val('').trigger('change');
        jQuery('#how_far').val('').trigger('change');
        jQuery('#maximum_buffer').val('').trigger('change');
        jQuery('#how_far').val('').trigger('change');
        jQuery('#booking_number').val('').trigger('change');
        jQuery('#booking_threshold').val('').trigger('change');
    }
    generateSlug(e) {
        e.preventDefault();
        var slugInput = jQuery(e.currentTarget),
            slugval = slugInput.val(),
            post_id = jQuery('#booking_type_id').val(),
            filterSlug = slugval.toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text

        this.checkSlugAvailability(filterSlug, post_id, slugInput);
    }

    checkSlugAvailability(slug, post_id, slugInput) {
        get('get_booking_type_slug', { slug: slug, post_id: post_id }).then(response => {
            if (response.slug) {
                slugInput.val(response.slug);
            }
        });
    }

    addBookingtypeBtn(event) {
        event.preventDefault();
        self.questionCount = 1;
        this.addCanvas.find('input[type=text], textarea,input[type=date]').val('');
        this.addCanvas.find('input[type=checkbox], input[type=radio]').prop('checked', false);
        this.addCanvas.find('#weekly').prop('checked', true);
        this.addCanvas.find('#monday').prop('checked', true);
        this.addCanvas.find('#tuesday').prop('checked', true);
        this.addCanvas.find('#wednesday').prop('checked', true);
        this.addCanvas.find('#thursday').prop('checked', true);
        this.addCanvas.find('#friday').prop('checked', true);
        this.addCanvas.find('#saturday').prop('checked', true);
        this.addCanvas.find('#sunday').prop('checked', true);
        this.addCanvas.find('#flexCheckDefault11').prop('checked', true);
        this.addCanvas.find('#video_conference').prop('checked', true).trigger('change');
        this.addCanvas.find('.showweekly').show();
        this.addCanvas.find('.showspecific').hide();
        this.addCanvas.find('#booking_number_by').val('days').trigger('change');
        jQuery(this.enable_group_booking).trigger('change')
        this.addCanvas.find('#meeting_link_type').val('custom_link').trigger('change');
        //this.addCanvas.find('#weekly input[type=checkbox]').prop('checked', true);
        this.addCanvas.find('#cover_image_preview img').remove();
        this.addCanvas.find('.offcanvas-title').text('Create new booking type');
        this.addCanvas.find('.add_unvailable_date_div').hide();
    
        var descriptionIframe = document.getElementById('description');
        if (descriptionIframe && descriptionIframe.contentDocument) {
            var descriptionIframeDoc = descriptionIframe.contentDocument || descriptionIframe.contentWindow.document;
            descriptionIframeDoc.body.innerHTML = '';
        }

        // Clear the content of the email editor
        var emailIframe = document.getElementById('email_content_editor');
        if (emailIframe && emailIframe.contentDocument) {
            var emailIframeDoc = emailIframe.contentDocument || emailIframe.contentWindow.document;
            emailIframeDoc.body.innerHTML = '';
        }

    }
    CancleBookingBtn(event) {
        event.preventDefault();
        this.addCanvas.offcanvas('hide');

    }
    changeBookingTypeTab(currentTab) {
        switch (currentTab) {
            case "sepcific_date":
                jQuery(".showweekly").hide();
                jQuery(".showspecific").show();
                if (jQuery(".showspecific .new").length <= 1) {
                } 
                break;
                break;

            case "weekly":
                jQuery(".showweekly").show();
                jQuery(".showspecific").hide();
                break;
        }
    }
    commonevent(self) {
        checkSpecificDateRows();
        var self = this;

        jQuery('#addNewAvailableDate').on('click', function () {
            var $newRow = jQuery('.new').first().clone();
            $newRow.find('input').val('');
            jQuery('.dateContainer').append($newRow);
        });
        jQuery('.dateContainer').on('click', '.remove-row', function () {
            var $row = jQuery(this).closest('.new');
            var rowCount = jQuery('.dateContainer').find('tr').length;
            // if (rowCount > 1) {
            $row.remove();
            checkSpecificDateRows();
            //}
        });
        jQuery('.dateContainer').on('click', '.duplicate-row', function () {
            var $rowToDuplicate = jQuery(this).closest('.new');
            var $clonedRow = $rowToDuplicate.clone();
            $rowToDuplicate.after($clonedRow);
            checkSpecificDateRows();
        });
        function checkSpecificDateRows() {
            var rowCount = jQuery('.dateContainer .new').length;
        }
        jQuery('#addNewUnDate').on('click', function () {
            var jQuerynewRow = jQuery('.date-row').first().clone();
            jQuerynewRow.find('input').val('');
            jQuery('#undateContainer').append(jQuerynewRow);
        });

        jQuery('#add_unvailable_date').on('change', function () {
            var undate_length = jQuery("#undateContainer tr").length;
            if (undate_length < 1) {
                var newRow = `
                    <tr class="date-row">
                        <td>
                            <div>
                                <input class="form-control" type="date" name="unavailable_date[]" value="0">
                            </div>
                        </td>
                        <td>
                            <div class="form-group mb-0 d-flex align-items-center">
                                <input type="time" class="form-control bg-white title-text" name="unavailable_time_to[]" placeholder="08:30 AM" value="">
                                <svg class="mx-3" width="18" height="2" viewBox="0 0 6 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="18" height="2" fill="#0C112E"></rect>
                                </svg> 
                                <input type="time" class="form-control bg-white title-text" name="unavailable_time_from[]" placeholder="04:20 PM" value="">
                            </div>
                        </td>
                        <td>
                            <span type="button" class="duplicate-row small">Duplicate</span>
                        </td>
                        <td>
                            <span type="button" style="display: none;" class="text-secondary remove-row small">Remove</span>
                        </td>
                    </tr>
                `;
                jQuery('#undateContainer').append(newRow);
            }
            if (jQuery(this).prop('checked')) {
                jQuery(".add_unvailable_date_div").show();
            } else {
                jQuery(".add_unvailable_date_div").hide();
            }
        });

        jQuery('#undateContainer').on('click', '.remove-row', function () {
            // jQuery(this).closest('.date-row').remove(); 
            var $row = jQuery(this).closest('.date-row');
            var rowCount = jQuery('#undateContainer').find('tr').length;
            if (rowCount > 1) {
                $row.remove();
            }
        });
        jQuery('#undateContainer').on('click', '.duplicate-row', function () {
            var jQueryrowToDuplicate = jQuery(this).closest('.date-row');
            var jQueryclonedRow = jQueryrowToDuplicate.clone();
            jQueryrowToDuplicate.after(jQueryclonedRow);
        });

        jQuery(document.body).on('click', '.remove_weekly_slot', function () {
            jQuery(this).closest('.days_slots').remove();
        });

        jQuery(document.body).on('click', '.add_new_time_slot',  (e)=> {
            var newTimeSlotContainer = jQuery(e.target).closest('.days_slots').clone();
            newTimeSlotContainer.find('.days-slot-action').html('<div class="d-block"><a class="remove_weekly_slot fw-bold text-secondary small" type="button">' + window.wpbookit.dashbord_language.comman.remove + '</a></div>')

            let day =  newTimeSlotContainer.find('.available_day').data('type')
            newTimeSlotContainer.find(`input[name='${day}_time_to[]']`).on('change' , ()=> this.validateTime(day,false))
            newTimeSlotContainer.find(`input[name='${day}_time_from[]']`).on('change' , ()=> this.validateTime(day,true))

            jQuery(e.target).closest('.days_slots-container').find('.unavailable_day').before(newTimeSlotContainer)

        });


        jQuery('#addNewDate').on('click',  ()=> {
            let clone =this.$cloneSpecificDatefield.clone();
            clone.find('input').val('');
            jQuery('.dateContainer').append(clone);
            checkSpecificDateRows();
        });
        jQuery('.wpb-copy-button').on('click', function () {
            var text = jQuery(this).closest('.d-flex').find('.wpb-copy-text').text().trim();
            var tempTextarea = jQuery('<textarea>');
            jQuery('body').append(tempTextarea);
            tempTextarea.val(text).select();
            document.execCommand('copy');
            tempTextarea.remove();
            jQuery(this).tooltip('show');
            var button = jQuery(this);
            setTimeout(function () {
                button.tooltip('hide');
            }, 1500);
        });

        jQuery('#maximum_booking_number_container').show();

        // Show corresponding input elements based on the selected radio button
        jQuery('input[type=radio][name=location]').change(function () {
            if (this.value === 'physical_address') {
                jQuery('#address_input_field').show();
                jQuery('#phone_number_field').hide();
                jQuery('#static_url_field').hide();
            } else if (this.value === 'phone_number') {
                jQuery('#address_input_field').hide();
                jQuery('#phone_number_field').show();
                jQuery('#static_url_field').hide();
            } else if (this.value === 'online_video') {
                jQuery('#address_input_field').hide();
                jQuery('#phone_number_field').hide();
                jQuery('#static_url_field').show();
            } else {
                jQuery('#address_input_field').hide();
                jQuery('#phone_number_field').hide();
                jQuery('#static_url_field').hide();
            }
        });

        jQuery('.weekly_day_checkbox').on('change', function () {
            // Get the corresponding day type
            var dayType = jQuery(this).attr('id');

            // Get the container elements
            var availableDayContainer = jQuery('.available_' + dayType).closest('.days_slots');
            var unavailableDayContainer = jQuery('.unavailable_' + dayType);

            jQuery(this).closest('.col-sm-3.col-5').next('.days_slots-container').find('.remove_weekly_slot').toggle(jQuery(this).prop('checked'));
            if (jQuery(this).prop('checked')) {
                // Show the available day container and hide the unavailable day container
                availableDayContainer.show();
                unavailableDayContainer.hide();
            } else {
                // Show the unavailable day container and hide the available day container
                availableDayContainer.hide();
                unavailableDayContainer.show();
            }
        });


    }
    PopupCloseEvent() {
        this.addCanvas.offcanvas('hide');
        this.addCanvas.find('input[type=text], textarea').val('');
        this.addCanvas.find('select').val('');
    }
    toggleAvailability(day) {
        if (jQuery('#' + day).is(':checked')) {
            jQuery('.available_' + day).show();
            jQuery('.unavailable_' + day).hide();
        } else {
            jQuery('.available_' + day).hide();
            jQuery('.unavailable_' + day).show();
        }
    }
    showweekly() {
        jQuery(document.body).on("click", ".btn-weekly", function (event) {
            jQuery(".showweekly").show();
            jQuery(".showspecific").hide();
        });
    }
    showspecific() {
        jQuery(document.body).on("click", ".btn-sepcific", function (event) {
            jQuery(".showweekly").hide();
            jQuery(".showspecific").show();
        });
    }
    addAvailable() {
        jQuery(document.body).on("click", ".btn-addAvailable", function (event) {
            if (jQuery('.btn-addAvailable').prop('checked')) {
                jQuery(".addAvailable").show();
            } else {
                jQuery(".addAvailable").hide();
            }
        });
    }
    AddeventHandlers() {
        var self = this;
        self.questionCount = 1;
        console.log(this.errorMessages);
        
        jQuery(document.body).on("submit", ".booking_type_form", function (event) {
            var errorOccurred = false;
            event.preventDefault();
            var formData = new FormData(this);
            var formDataArray = Array.from(formData.entries());

            let isValid = [];
            var required = ["title", "url", "duration", "slug", "wpbl_location_booking_type"];

            formDataArray.forEach(function (pair) {
                var key = pair[0];
                var value = pair[1];
                if (required.includes(key)) {
                    console.log(self.errorMessages);
                    
                    var error = self.validateField(
                        value,
                        self.errorMessages[key].selector, 
                        self.errorMessages[key].blank,
                    );
                    isValid.push(error);
                    
                }
            });
        
            errorOccurred = wp.hooks.applyFilters('wpb_before_booking_type_validation_duration', errorOccurred, formData, self);

            if (isValid.indexOf(false) !== -1 || errorOccurred) {
                return false; 
            }
            
            const durationValue = document.getElementById('duration');
            const bookingNumber = parseInt(durationValue.value) || 0;
            const bookingSpan = jQuery(self.errorMessages['duration'].selector);
            if (bookingNumber <= 0) {
                bookingSpan.text(self.errorMessages['duration'].blank).show();
                errorOccurred = true;
            } else {
                bookingSpan.text('');
                errorOccurred = false;
            }

            if (isValid.indexOf(false) !== -1) return false;

            var selectedtype = document.querySelector('input[name="booking_type"]:checked').value;
            const specific_availabledataArray = [];
            var weekly_data = [];
                const specific_available_dates = document.querySelectorAll("input[name='specific_available_date[]']");
                const specific_available_timeTo = document.querySelectorAll("input[name='specific_available_time_to[]']");
                const specific_available_timeFrom = document.querySelectorAll("input[name='specific_available_time_from[]']");
                for (let i = 0; i < specific_available_dates.length; i++) {
                    const date = specific_available_dates[i].value;
                    const to = specific_available_timeTo[i].value;
                    const from = specific_available_timeFrom[i].value;
                    if (date != "" && to != "" && from != "") {
                        specific_availabledataArray.push({ date, to, from });
                    } 
                }
                var timeWiseArray = [];
                var daydata = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                daydata.forEach(day => {
                    var weekly_available_dates = document.querySelector("input[name='" + day + "']");
                    if (weekly_available_dates.checked == true  ) {
                        weekly_available_dates = day;
                        const weekly_available_timeTo = document.querySelectorAll("input[name='" + day + "_time_to[]']");
                        const weekly_available_timeFrom = document.querySelectorAll("input[name='" + day + "_time_from[]']");

                        var dayTimeRanges = [];
                        for (let i = 0; i < weekly_available_timeTo.length; i++) {
                            // Get the value of time inputs
                            var timeTo = weekly_available_timeTo[i].value;
                            var timeFrom = weekly_available_timeFrom[i].value;

                            // Check if both time inputs have values
                            if (timeTo && timeFrom) {
                                // Construct an object representing the time range
                                var timeRange = {
                                    timeTo: timeTo,
                                    timeFrom: timeFrom
                                };
                                // Push the time range object to the array
                                dayTimeRanges.push(timeRange);
                            } else {
                                
                                let validationtimeTo = self.validateField(
                                    timeTo,
                                    self.errorMessages["weekly_date_not_selected"].selector,
                                    self.errorMessages["weekly_date_not_selected"].blank,
                                );
                                let validationtimeFrom = self.validateField(
                                    timeFrom,
                                    self.errorMessages["weekly_date_not_selected"].selector,
                                    self.errorMessages["weekly_date_not_selected"].blank,
                                );

                                if (validationtimeTo === false || validationtimeFrom === false) {
                                    errorOccurred = true
                                }

                                return;
                                break;
                            }
                        }
                        if (weekly_available_dates != "" && (dayTimeRanges != "" || dayTimeRanges.length != 0)) {
                            weekly_data.push({ weekly_available_dates, dayTimeRanges });
                        } else {
                            let validationVal = self.validateField(
                                false,
                                self.errorMessages["weekly_date_not_selected"].selector,
                                self.errorMessages["weekly_date_not_selected"].blank,
                            );
                            if (validationVal === false) {
                                errorOccurred = true
                                return;
                            }
                            
                        }
                    }

                });
            const unavailabledataArray = [];
            if (jQuery('#add_unvailable_date').prop('checked') == true) {
                const unavailable_dates = document.querySelectorAll("input[name='unavailable_date[]']");
                const unavailable_timeTo = document.querySelectorAll("input[name='unavailable_time_to[]']");
                const unavailable_timeFrom = document.querySelectorAll("input[name='unavailable_time_from[]']");
                for (let i = 0; i < unavailable_dates.length; i++) {
                    const date = unavailable_dates[i].value;
                    const to = unavailable_timeTo[i].value;
                    const from = unavailable_timeFrom[i].value;
                    if (date != "" && to != "" && from != "") {
                        unavailabledataArray.push({ date, to, from });
                    } else {
                        let validationVal = self.validateField(
                            false,
                            self.errorMessages["unavailable_date_not_selected"].selector,
                            self.errorMessages["unavailable_date_not_selected"].blank,
                        );
                        if (validationVal === false) {
                            errorOccurred = true
                        }
                    }
                }
            }
            // Loop through each question element
            var questionsArray = [];

            jQuery('.new-question').each(function () {
                var questionId = jQuery(this).data('question-id');
                var question = jQuery(`#question-${questionId}`).val().trim();
                var type = jQuery(`#question-type-${questionId}`).val();
                var options = [];

                // Check if the question is empty
                if (question === '') {
                    let validationVal = self.validateField(
                        false,
                        self.errorMessages["questions_not_enter"].selector,
                        self.errorMessages["questions_not_enter"].blank,
                    );
                    if (validationVal === false) {
                        errorOccurred = true
                    }
                    return;
                }

                // Check if the type is empty
                if (type === '') {
                    let validationVal = self.validateField(
                        false,
                        self.errorMessages["questions_not_enter"].selector,
                        self.errorMessages["questions_not_enter"].blank,
                    );
                    if (validationVal === false) {
                        errorOccurred = true
                    }
                    return;
                }

                // Check if the question type is "Radio", "Checkbox", or "Dropdown"
                if (type === 'radio' || type === 'checkbox' || type === 'dropdown') {
                    var optionTextArea = jQuery(`#option-text-${questionId}`);
                    var optionText = optionTextArea.val().trim();
                    if (optionText === '') {
                        let validationVal = self.validateField(
                            false,
                            self.errorMessages["questions_not_enter"].selector,
                            self.errorMessages["questions_not_enter"].blank,
                        );
                        if (validationVal === false) {
                            errorOccurred = true
                        }
                        return;
                    } else {
                        options = optionText.split(',').map(function (option) {
                            return option.trim();
                        });
                    }
                }

                // Push the question, type, and options to the array+
               
                questionsArray.push( window.wp.hooks.applyFilters('wpb_filter_push_booking_type_question',{
                    questionId: questionId,
                    question: question,
                    type: type,
                    options: options
                }));
            });

            var data = {};
            for (const [key, value] of formData) {
                if (key !== "unavailable_date[]" && key !== "unavailable_time_to[]" && key !== "unavailable_time_from[]" && key !== "specific_available_date[]" && key !== "specific_available_time_to[]" && key !== "specific_available_time_from[]") {
                    if (value != "") {
                        data[key] = value;
                    }
                }
            }
            if (unavailabledataArray != "" && jQuery('#add_unvailable_date').prop('checked') == true) {
                data['unavailable_dates'] = JSON.stringify(unavailabledataArray);
            }
            data['specific_available_dates'] = JSON.stringify(specific_availabledataArray);
            data['weekly_data'] = JSON.stringify(weekly_data);
            if (questionsArray.length != 0) {
                data["questions"] = JSON.stringify(questionsArray);
            }

            if (!data.hasOwnProperty('charge')) {
                data['price'] = '';
            }

            
            if(JSON.parse(data.specific_available_dates).length ==0 &&JSON.parse(data.weekly_data).length==0){
                jQuery('#weekly_error,#specific_error').html(window.wpbookit.dashbord_language.validation.require_avaible_day);
                errorOccurred=true
            }
            
            let reqFormData = new FormData();

            // Convert the object into key-value pairs and append to FormData
            for (let key in data) {
                if (data.hasOwnProperty(key)) {
                    reqFormData.append(key, data[key]);
                }
            }
            reqFormData = wp.hooks.applyFilters('wpb_after_booking_type_js_background_color', reqFormData);          

            if (errorOccurred) {
                return;
            }

            self.add_booking_type_ajax(reqFormData);
        });

        this.addCanvas.find('#title').on('input',(e)=>{
            if(jQuery('#booking_type_id').val()==''){
                jQuery('#slug').val(e.target.value.replaceAll(' ', '-').toLowerCase());
            }
        })
    }


    add_booking_type_ajax(data) {
        
        jQuery('#wpb-save-booking-type').prop('disabled', true);
        jQuery('.wpb-booking-type-submit-svg').removeClass('d-none');

        post('add_booking_type', window.wp.hooks.applyFilters('wpb_add_booking_type',data))
            .then(response => {
                var message = response.data.message;
                const resStatus = response.data.status;
                if (resStatus === 'success') {
                    this.PopupCloseEvent();
                    this.addCanvas.offcanvas('hide');
                    setTimeout(function () {
                        window.location.reload()
                    }, 2200);
                }
                notificationToast[resStatus](message, resStatus.toUpperCase(), { autoClose: true });
                jQuery('#wpb-save-booking-type').prop('disabled', false);
                jQuery('.wpb-booking-type-submit-svg').addClass('d-none');
            })
            .catch(error => {
                jQuery('#wpb-save-booking-type').prop('disabled', false);
                jQuery('.wpb-booking-type-submit-svg').addClass('d-none');
                console.error('Error :', error);
            });
    }
    delete_booking_type(data, name) {
        // Using wp.i18n for translations

        var cMessage = window.wpbookit.dashbord_language.booking_type.confirm_delete_boooking_type + name + "?";
        if (confirm(cMessage) == true) {
            post('delete_booking_type', { "id": data }).then(response => {
                const message = response.data.message;
                const resStatus = response.data.status;

                notificationToast[resStatus](message, resStatus.toUpperCase());
                setTimeout(function () {
                    window.location.reload()
                }, 3000);
            })
        }
    }
    clone_booking_type(data) {
        post('clone_booking_type', { "id": data }).then(response => {
            const message = response.data.message;
            const resStatus = response.data.status;
            notificationToast[resStatus](message, resStatus.toUpperCase(), { autoClose: true });
            setTimeout(function () {
                window.location.reload()
            }, 4000);
        })

    }
    toggleLocationFields() {
        var selectedLocation = jQuery('input[name="location"]:checked').val();
        // Hide all location fields first
        jQuery('.location-fields').hide();
        // Show the specific location field based on the selected radio button
        switch (selectedLocation) {
            case 'online_video':
                jQuery('#static_url_field').show();
                break;
            case 'physical_address':
                jQuery('#address_input_field').show();
                break;
            case 'phone_number':
                jQuery('#phone_number_field').show();
                break;
            default:
                // No location selected
                break;
        }
    }
    edit_booking_type(data) {
        var self = this;
        self.questionCount = 1;
        get('get_booking_type', { id: data }).then(response => {
            window.wp.hooks.doAction('wpb_get_booking_type',response)
            var rowCount = jQuery('.dateContainer .new').length;

            if (response.data.success != false && response.data.success != true) {
                var message = response.data.message;
                this.addCanvas.offcanvas('show');
                if (response.data.id) {
                    jQuery('#booking_type_id').val(response.data.id);
                }

                jQuery('#title').val(response.data.name);
                jQuery('#slug').val(response.data.slug);
                jQuery('#url').val(response.data.url);
                jQuery('#duration').val(response.data.duration);

                // Check weekly or specific date
                if (response.data.type === 'weekly') {
                    jQuery('#weekly').prop('checked', true);
                } else {
                    jQuery('#sepcific_date').prop('checked', true);
                }

                if (response.data.type === 'sepcific_date') {
                    jQuery('.showspecific').show();
                    jQuery('.showweekly').hide();
                } else if (response.data.type === 'weekly') {
                    jQuery('.showspecific').hide();
                    jQuery('.showweekly').show();
                }


                // Cover Image
                if (response.data.meta.cover_image_id && response.data.meta.cover_image_url) {
                    jQuery('#cover_image_id').val(response.data.meta.cover_image_id);
                    jQuery('#cover_image_url').val(response.data.meta.cover_image_url);
                    if(document.querySelector('#cover_image_preview .booking-cover-image')){
                        jQuery('#cover_image_preview .booking-cover-image').html(`<img class='img-fluid' src="${response.data.meta.cover_image_url}" alt="Cover Image">`);
                    }else{
                        jQuery('#cover_image_preview').append(`<div class='booking-cover-image'><img class='img-fluid' src="${response.data.meta.cover_image_url}" alt="Cover Image"></div>`);
                    }
                    jQuery('.wpb-icon-wrapper').hide();
                    jQuery('#cover_image_preview-btn').show();
                    jQuery('#cover_image_preview').show();
                }


                // Weekly data 
                jQuery('.weekly_day_checkbox').prop('checked', false);

                if (response.data.meta.weekly_days !== undefined) {
                    jQuery.each(JSON.parse(response.data.meta.weekly_days), function (index, value) {
                        if (value === 'sunday') {
                            jQuery('#sunday').prop('checked', true);
                        } else if (value === 'monday') {
                            jQuery('#monday').prop('checked', true);
                        } else if (value === 'tuesday') {
                            jQuery('#tuesday').prop('checked', true);
                        } else if (value === 'wednesday') {
                            jQuery('#wednesday').prop('checked', true);
                        } else if (value === 'thursday') {
                            jQuery('#thursday').prop('checked', true);
                        } else if (value === 'friday') {
                            jQuery('#friday').prop('checked', true);
                        } else if (value === 'saturday') {
                            jQuery('#saturday').prop('checked', true);
                        }
                    });
                }
                jQuery('.weekly_day_checkbox').trigger('change');

                var weeklyTimeSlots = response.data.meta.weekly_time_slots != undefined ? JSON.parse(response.data.meta.weekly_time_slots) : [];
                var specificDates = response.data.meta.specific_dates != undefined ? JSON.parse(response.data.meta.specific_dates) : [];

                jQuery.each(weeklyTimeSlots, function (day, slots) {
                    var container = jQuery('.available_' + day).closest('.days_slots-container');
                    container.find('.days_slots').not('.unavailable_day').remove();

                    jQuery.each(slots, function (index, slot) {

                        var timeSlotHTML = `<div class="days_slots">
                            <div class="row align-items-center flex-nowrap days_slot-wrapper">
                                <div class="col-sm-9 col-12">
                                    <div class="available_day available_${day} ${day}_time_contiener" id="available_${day}" data-type="${day}">
                                        <div class="time_slot">
                                            <div class="form-group mb-0 d-flex align-items-center ${day}_time_new">
                                                <input type="time" class="form-control bg-white title-text" value="${slot.timeTo}" placeholder="08:30 AM" name="${day}_time_to[]" />
                                                <svg class="mx-3" width="18" height="2" viewBox="0 0 6 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="18" height="2" fill="#0C112E"></rect>
                                                </svg>
                                                <input type="time" class="form-control bg-white title-text" value="${slot.timeFrom}" placeholder="04:20 PM" name="${day}_time_from[]"/> 
                                            </div>
                                        </div>
                                    </div>
                                </div>`;

                        if (index != (slots.length-1)) {
                            timeSlotHTML += `<div class="col-sm-3 col-5 days-slot-action text-end"><div class="d-block"><a class="remove_weekly_slot fw-bold text-secondary small" type="button">Remove</a></div></div>`;
                        } else {

                            timeSlotHTML += `<div class="col-sm-3 col-5 days-slot-action text-end"><div class="d-block">
                                <a type="button" id="${day}_add_time" class="available_${day} text-secondary text-capitalize fw-bold add_new_time_slot small" data-type="${day}">+New Slot</a>
                                </div></div>`;
                        }
                        timeSlotHTML += ` </div>
                        </div>`;
                        container.prepend(timeSlotHTML);
                        container.find(`input[name='${day}_time_to[]']`).on('change' , ()=> self.validateTime(day,false))
                        container.find(`input[name='${day}_time_from[]']`).on('change' , ()=> self.validateTime(day,true))
                    });
                });
                var specificDates = JSON.parse(response.data.meta.specific_dates);

                var dateContainer = jQuery('.dateContainer');
                if (specificDates.length != 0) {
                    dateContainer.empty();
                }
                var keys = Object.keys(specificDates);
                var removeButtonHtml = '';
                if (keys.length === 1) {
                    removeButtonHtml = `<span class="text-secondary remove-row small">Remove</span>`;
                } else {
                    removeButtonHtml = `<span class="text-secondary remove-row small">Remove</span>`;
                }
                jQuery.each(specificDates, function (date, slot) {
                    var rowHtml = `
                        <tr class="new">
                            <td>
                                <div>
                                    <input class="form-control" type="date" name="specific_available_date[]" value="${slot.date}">
                                </div>
                            </td>
                            <td>
                                <div class="form-group mb-0 d-flex align-items-center">
                                    <input type="time" class="form-control bg-white title-text" placeholder="08:30 AM" name="specific_available_time_from[]" value="${slot.from}">
                                    <svg class="mx-3" width="18" height="2" viewBox="0 0 6 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="18" height="2" fill="#0C112E"></rect>
                                    </svg> 
                                    <input type="time" class="form-control bg-white title-text" placeholder="04:20 PM" name="specific_available_time_to[]" value="${slot.to}">
                                </div>
                            </td>
                            <td>
                                <span type="button" class="duplicate-row small">Duplicate</span>
                            </td>
                            <td>
                            ${removeButtonHtml}
                        </td>
                        </tr>
                    `;
                    dateContainer.append(rowHtml);
                });


                // Unavailable data
                if (response.data.unavailable == "1") {
                    jQuery('.add_unvailable_date_div').show();
                    jQuery('#add_unvailable_date').prop('checked', true);
                }

                if (response.data.unavailable == "") {
                    jQuery('.add_unvailable_date_div').hide();
                    jQuery('#add_unvailable_date').prop('checked', false);
                }

                var dateContainer = jQuery('#undateContainer');
                if (specificDates.length != 0) {
                    dateContainer.empty();
                };
                var unavailableDates = JSON.parse(response.data.meta.unavailable_dates);
                if (Object.keys(unavailableDates).length > 0) {
                    jQuery('#undateContainer .date-row').remove();
                }

                jQuery.each(JSON.parse(response.data.meta.unavailable_dates), function (index, data) {
                    var newRow = `
                        <tr class="date-row test">
                            <td>
                                <div>
                                    <input class="form-control" type="date" name="unavailable_date[]" value="${data.date}">
                                </div>
                            </td>
                            <td>
                                <div class="form-group mb-0 d-flex align-items-center">
                                    <input type="time" class="form-control bg-white title-text" name="unavailable_time_to[]" placeholder="08:30 AM" value="${data.to}">
                                    <svg class="mx-3" width="18" height="2" viewBox="0 0 6 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="18" height="2" fill="#0C112E"></rect>
                                    </svg> 
                                    <input type="time" class="form-control bg-white title-text" name="unavailable_time_from[]" placeholder="04:20 PM" value="${data.from}">
                                </div>
                            </td>
                            <td>
                                <span type="button" class="duplicate-row small">Duplicate</span>
                            </td>
                            <td>
                                <span class="text-secondary remove-row small" >Remove</span>
                            </td>
                        </tr>
                    `;
                    jQuery('#undateContainer').append(newRow);
                });

                // Fill How far data
                var how_far_Id = response.data.meta.how_far;
                if (how_far_Id) {
                    jQuery('[name="how_far"]').val(how_far_Id).trigger('change');
                } else {
                    jQuery('[name="how_far"]').val(null).trigger('change');
                }

                if(response.data.meta.background_color){
                    jQuery('#wpb-booking-type-color').val(response.data.meta.background_color).trigger('change');
                }else{
                    jQuery('#wpb-booking-type-color').val("#3745A4").trigger('change');
                }
                // Fill maximum buffer
                var maximum_buffer_Id = response.data.meta.maximum_buffer;
                if (maximum_buffer_Id) {
                    jQuery('[name="maximum_buffer"]').val(maximum_buffer_Id).trigger('change');
                } else {
                    jQuery('[name="maximum_buffer"]').val(null).trigger('change');
                }

                let maximumBooking = response.data.meta.maximum_booking;
                if (maximumBooking !== "false") {
                    jQuery('#booking_number').val(maximumBooking);
                }

                jQuery('#enable_group_booking').prop('checked', response.data.meta.enable_group_booking == 'true').trigger('change');
                if (response.data.meta.slots_per_booking_number !== "false") {
                    jQuery('#slots_booking_number').val(response.data.meta.slots_per_booking_number);
                    jQuery('#show_remaining_slot').prop('checked', response.data.meta.show_remaining_slot == 'true').trigger('change');
                }
                if (response.data.meta.booking_number_by) {
                    jQuery('#booking_number_by').val(response.data.meta.booking_number_by).trigger('change');
                }

                if (response.data.meta.booking_threshold) {
                    jQuery('#booking_threshold').val(response.data.meta.booking_threshold);
                }

               
                wp.hooks.doAction('wpb_after_booking_threshold', response.data);   

                // Location
                jQuery('input[name="location"][value="' + response.data.meta.location + '"]').prop('checked', true);
                showHideFields(response.data.meta.location);
                function showHideFields(location) {
                    jQuery('.location-fields').hide();
                    if (location === 'online_video') {
                        jQuery('#static_url_field').show();
                        jQuery('#phone_number_field').hide();
                        jQuery('#address_input_field').hide();
                        jQuery('#meeting_link_type').val(response.data.meta.meeting_link_type).trigger('change');
                        jQuery('#static_url').val(response.data.meta.location_source);
                    
                       
                        jQuery('select[name="link_type"]').val(response.data.meta.link_type).trigger('change');
                    } else if (location === 'physical_address') {
                        jQuery('#address_input_field').show();
                        jQuery('#static_url_field').hide();
                        jQuery('#phone_number_field').hide();
                        jQuery('#address_input').val(response.data.meta.location_source);
                    } else if (location === 'phone_number') {
                        jQuery('#phone_number_field').show();
                        jQuery('#address_input_field').hide();
                        jQuery('#static_url_field').hide();
                        jQuery('#phone_number').val(response.data.meta.location_source);
                    } else if (location === 'no_location') {
                        jQuery('#phone_number_field').hide();
                        jQuery('#address_input_field').hide();
                        jQuery('#static_url_field').hide();
                    }
                }


                jQuery('#flexCheckDefault11').prop('checked', response.data.meta.guest_invite === 'true').trigger('change');

               
            
                this.ensureTinymceInitialized('description', function (editor,is_editor=true) {
                    var descriptionContent = response.data.description;
                    if(is_editor === false){
                        jQuery("#wp-description-wrap").find("#description").val(descriptionContent)
                    }
                    try {
                        editor.setContent(descriptionContent);
                    } catch (error) {
                        console.log(error);
                    }
                });
                
                if (response.data.meta.email_reminder === 'true') {
                    jQuery('#conformation_email_checkbox').prop('checked', true);
                    jQuery("#email_editor_container").show();
                    this.ensureTinymceInitialized('email_content_editor', function (editor,is_editor=true) {
                        var customConfirmationEmailContent = response.data.meta.email_content_editor;
                        if(is_editor === false){
                            jQuery("#email_editor_container").find("#email_content_editor").val(customConfirmationEmailContent)
                        }
                        try {
                            editor.setContent(customConfirmationEmailContent);
                        } catch (error) {
                            console.log(error);
                        }
                    });
                }
                var formData = new FormData(jQuery(".booking_type_form"));
                var formDataArray = Array.from(formData.entries());

                var required = ["cover_image_id", "title", "slug", "url", "duration",  "description"];
                formDataArray.forEach(function (pair) {
                    var key = pair[0];
                    var value = pair[1];

                    if (required.includes(key)) {
                        var error = self.validateField(
                            value,
                            self.errorMessages[key].selector,
                            self.errorMessages[key].blank,
                        );
                    }
                });
            }

        });
    }
    changeStatus(e) {
        post('update_booking_type_status', { id: e.currentTarget.dataset.id, status: e.currentTarget.checked }).then(({ data }) => {
            notificationToast[data.status](data.message, data.status.toUpperCase(), { autoClose: true });

        }).catch(data => {
            console.log(data);
        })
    }

    setupEditor(selector, content) {
        return {
            selector: selector,
            setup: function (editor) {
                editor.on('init', function (e) {
                    editor.setContent(content);
                });
            }
        };
    }
    validateTime(day,isFromTimeUpdate) {
        const timeFromFields = document.getElementsByName(`${day}_time_from[]`);
        const timeToFields = document.getElementsByName(`${day}_time_to[]`);
        
        timeFromFields.forEach((timeFrom, index) => {
            const timeTo = timeToFields[index];

            // Convert time values to Date objects for comparison
            const fromTime = new Date(`1970-01-01T${timeFrom.value}:00`);
            const toTime = new Date(`1970-01-01T${timeTo.value}:00`);

            // Check if 'from' time is greater than or equal to 'to' time
            if (fromTime <= toTime) {
                timeFrom.min = `${timeTo.value}:00`
                if(isFromTimeUpdate){
                    timeTo.value=''
                }else{
                    timeFrom.value=''
                }
            } 

            // Report the validation state
            timeTo.reportValidity();
        });
    }

}