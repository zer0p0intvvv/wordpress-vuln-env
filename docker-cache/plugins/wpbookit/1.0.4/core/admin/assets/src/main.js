import 'bootstrap'
import 'select2'
import 'select2/dist/css/select2.min.css'
import 'flatpickr/dist/flatpickr.min.css';
import  "intl-tel-input/build/css/intlTelInput.css";

import './css/app.css'
import './css/rtl.css'

import { Modal, Offcanvas, Tooltip } from 'bootstrap';
import Dashboard from './module/Dashboard'
import Customer from './module/Customer'
import Guest_Users from './module/Guest-Users'
import BookingType from './module/BookingType'
import Settings from './module/Settings'
import Booking from './module/Booking'
import Calendars from './module/Calendar';
import notificationToast from "./utils/notification-toast";
import { sidebarResized } from './utils/helper';
import intlTelInput from "intl-tel-input";
import { get, post } from './utils/ajax';

jQuery(function () {
    const params = new URLSearchParams(location.search);
    let DashbordSideBarModule = {
        'dashboard':Dashboard,
        'customer':Customer,
        'guest-users':Guest_Users,
        'bookings':Booking,
        'calendar':Calendars,
        'booking_type':BookingType,
        'settings':Settings,
    }
    
    if(DashbordSideBarModule[params.get('tab')|| 'dashboard']){
        window['DashbordSideBarModule'] = new (DashbordSideBarModule[params.get('tab') || 'dashboard']  )
    }
    window['notificationToast'] = notificationToast;
    window['wpbOffcanvas'] = Offcanvas;
    window['wpbmodal'] = Modal;
    window['wpb_get']=get    ;
    window['wpb_post']=post
    window['wpbintTelInput']=intlTelInput;

    // Tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl))

    jQuery('select').each(function(){
        jQuery(this).val(jQuery(this).find(':selected').val())
    })
    jQuery('select').not('.flatpickr-monthDropdown-months,.not-init-select').select2({
        width: '100%',
        minimumResultsForSearch: Infinity,

    });
    jQuery('.select2-container').addClass('wide');

    /*---------------------------------------------------------------------
    Sidebar Toggle
    --------------------------------------------------------------------*/
    window.addEventListener('resize', function () {
        sidebarResized();
    });

    let elem = document.querySelectorAll('.sidebar-toggle');
    elem.forEach(function (elem) {
        elem.addEventListener("click", function(){
            const sidebar = document.querySelector(".sidebar");
            if (sidebar.classList.contains("sidebar-mini")) {
                sidebar.classList.remove("sidebar-mini");
            } else {
                sidebar.classList.add("sidebar-mini");
            }
        });
    });


    sidebarResized()


    // Import File Uplaod 
    jQuery('#wpb_import_file').on('change', (e)=> {
        var fileName = jQuery(e.currentTarget).val().split('\\').pop();
        jQuery(e.currentTarget).next('.custom-upload').find('.title').html ( fileName)
    });

    jQuery("html").trigger('wpbookit-dashboard-loaded');

    
});
