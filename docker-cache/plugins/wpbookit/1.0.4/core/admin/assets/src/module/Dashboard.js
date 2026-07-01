import ApexCharts from "apexcharts";
import { get } from "./../utils/ajax";
import flatpickr from "flatpickr";
import notificationToast from "../utils/notification-toast";

export default class Dashboard {

    constructor() {
        this.loadDashboard();
        this.renderDashbordChart();
        this.addEventListner();
        this.initFlatpickr();
    }

    loadDashboard() {
        this.dashboardLineChartElement = jQuery('#dashboard-line-chart')[0];
        this.dashboardLineChartDateFilterElement = jQuery('#dashboard-chart-date-filter')[0];
        this.flatpickrElement = jQuery('#wpb-range-flatpicker');
        this.flatpickrSubmitElement = jQuery('#flatpickr-submit')[0];
        this.flatpickrResetElement = jQuery('#flatpickr-reset');
    }

    renderDashbordChart() {
        this.dashboardLineChartInstance = new ApexCharts(this.dashboardLineChartElement, JSON.parse(this.dashboardLineChartElement.dataset.chartOption));
        this.dashboardLineChartInstance.render();
    }

    addEventListner() {
        jQuery(this.flatpickrSubmitElement).on('click', () => this.submitDashboardRange());
        jQuery(this.flatpickrResetElement).on('click', () => {
            const formatDate = (date) => {
                let year = date.getFullYear();
                let month = (date.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
                let day = date.getDate().toString().padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
        
            const today = new Date();
            const nextMonth = new Date(today);
            nextMonth.setMonth(today.getMonth() + 1);
        
            const formattedToday = formatDate(today);
            const formattedNextMonth = formatDate(nextMonth);
        
            this.flatpickrSelectedDate = [formattedToday, formattedNextMonth];
            this.flatpickrInstance.setDate(this.flatpickrSelectedDate);
            this.submitDashboardRange();
        });
        
        
    }

    initFlatpickr() {
        // Get today's date
        const today = new Date();

        // Get the date one month ago
        const lastMonth = new Date(today);
        const startdate  = String(today.getDate()).padStart(2, '0');
        const startyear = today.getFullYear();
        const startmonth = String(today.getMonth() + 1).padStart(2, '0');
        const start = `${startyear}-${startmonth}-${startdate}`;
        lastMonth.setMonth(today.getMonth() + 1);
        const enddate  = String(lastMonth.getDate()).padStart(2, '0');
        const endyear = lastMonth.getFullYear();
        const endmonth = String(lastMonth.getMonth() + 1).padStart(2, '0');
        const end = `${endyear}-${endmonth}-${enddate}`;
        const bookingsLink = document.getElementById('wpb-bookings-link');
        if (bookingsLink) {
            bookingsLink.href = `admin.php?page=wpbookit-dashboard&tab=bookings` + `&start=${start}&end=${end}`;
        } else {
            console.error('Bookings link not found');
        }
        const paymentLink = document.getElementById('wpb_payment_link');
        if (paymentLink) {
            paymentLink.href = `admin.php?page=wpbookit-dashboard&tab=payment&start=${start}&end=${end}`;
        } else {
            console.error('Payment link not found');
        }
        const customerLink = document.getElementById('wpb_customer_link');
        if (customerLink) {
            customerLink.href = `admin.php?page=wpbookit-dashboard&tab=customer&start=${start}&end=${end}`;
        } else {
            console.error('Payment link not found');
        }

        // Format the dates to YYYY-MM-DD
        const formatDate = date => date.toISOString().split('T')[0];

        this.flatpickrInstance =  this.flatpickrElement.flatpickr({
            mode: 'range',
            dateFormat: 'Y-m-d',
            defaultDate: [formatDate(lastMonth), formatDate(today)],
            onChange: (selectedDates, dateStr, instance) => {
                if (selectedDates.length == 2) {
                    this.flatpickrSelectedDate = selectedDates.map((item) => this.formatDate(item));
                    this.flatpickrSubmitElement.removeAttribute('disabled')
                    this.flatpickrResetElement.show()
                }else{
                    this.flatpickrSubmitElement.setAttribute('disabled','true')
                    this.flatpickrResetElement.hide()
                }
            },
            onReady: (selectedDates, dateStr, instance)=> {
                this.flatpickrSelectedDate = selectedDates.map((item) => this.formatDate(item));
            },
            locale:window.wpbookit.flatpicker
        });

    }
    formatDate(date) {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0'); // getMonth() returns 0-based month, so add 1
        const day = String(d.getDate()).padStart(2, '0');
    
        return `${year}-${month}-${day}`;
    }
    submitDashboardRange() {
        const startDate = this.flatpickrSelectedDate ? this.flatpickrSelectedDate[0] : null;
        const endDate = this.flatpickrSelectedDate ? this.flatpickrSelectedDate[1] : null;
        const locationId = wp.hooks.applyFilters('wpb_get_after_endDate_dashboard', null);
        
        if (startDate && endDate) {
            get('get_dashboard_apt_revenue_date', { 'start': startDate, 'end': endDate, 'location_id': locationId })
                .then(res => res.data)
                .then(res => {
                    
                    this.dashboardLineChartInstance.updateSeries(res.bookings_revenue_chart);
                    this.dashboardLineChartInstance.updateOptions(res.bookings_revenue_range);
    
                    jQuery.each( res.dashboard_fragments, function( key, value ) {
                        jQuery( key ).html(value);
                    });

                    const bookingsLink = document.getElementById('wpb-bookings-link');
                    if (bookingsLink) {
                        bookingsLink.href = `admin.php?page=wpbookit-dashboard&tab=bookings` + `&start=${startDate}&end=${endDate}`;
                    } else {
                        console.error('Bookings link not found');
                    }
                    const paymentLink = document.getElementById('wpb_payment_link');
                    if (paymentLink) {
                        paymentLink.href = `admin.php?page=wpbookit-dashboard&tab=payment&start=${startDate}&end=${endDate}`;
                    } else {
                        console.error('Payment link not found');
                    }
                    const customerLink = document.getElementById('wpb_customer_link');
                    if (customerLink) {
                        customerLink.href = `admin.php?page=wpbookit-dashboard&tab=customer&start=${startDate}&end=${endDate}`;
                    } else {
                        console.error('Payment link not found');
                    }
                });
        } else {
            console.error('Start or end date is missing');
        }
    }
    

}
