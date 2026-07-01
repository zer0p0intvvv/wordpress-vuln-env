import notificationToast from "../utils/notification-toast";
import { get, post } from "../utils/ajax";


export default class Guest_Users {
    guestSearch;
    guestDatatable;

    constructor() {
        this.eventHandlers();
    }

    eventHandlers() {
        jQuery(document).ready( this.intializeDatatable() );
        jQuery(document.body).on("click", ".delete-guest-button", (e) => this.DeletePopupeventHandlers(e));
        jQuery('.dt-search').on("input", _.debounce(e => {
            this.search_filter(e)
        }, 500));
    }

    intializeDatatable(){
        this.guestDatatable = new DataTable('table', {
            "searching": false,
            "processing": true,
            "serverSide": true,
            "order": [
                [0, 'DESC']
            ],
            "paging": true,
            "lengthChange": false,
            "ajax": (data, callback, settings) => {
                if (this.guestSearch) {
                    data = { ...data, ...{ guest_search: this.guestSearch } }
                }
                return get('get_guest_list', data).then(resData => {
                    callback(resData)
                })
            },
            "columns": [
                {
                    "data": "id",
                    "name": "id",
                    "searchable": false
                },
                {
                    "data": "guest_name",
                    "name": 'guest_name',
                    "searchable": false
                },
                {
                    "data": "guest_email",
                    "name": 'guest_email',
                    "searchable": false,
                },
                {
                    "data": "guest_phone_number",
                    "name": 'guest_phone_number',
                    "searchable": false,
                    "render": function (data, type, row) {
                        return data ? data : '-';
                    }
                },
                {
                    "data": "",
                    "render": function (data, type, row) {

                        let render = '';

                        render += `<a class="delete-guest-button" href="#" data-id="${row.id}" data-name="${row.guest_name}">
                                        <img src="${wpbookit.wpb_plugin_url}core/admin/assets/images/delete-icon.svg" alt="checked">
                                   </a>`;

                        return `<div class="d-flex align-items-center">${render}</div>`;
                    },
                    "searchable": false,
                    "orderable": false,
                },
            ],
            language: window.wpbookit.datatable_language
        });
    }

    DeletePopupeventHandlers(e) {
        e.preventDefault();
        const __this = jQuery(e.currentTarget);
        const guestName = __this.data('name');
        var urlParams = new URLSearchParams(window.location.search);
        var tab = urlParams.get('tab');
    
        // Using wp.i18n for translations
        var cMessage = window.wpbookit.dashbord_language.guest.confirm_delete_guest + guestName + "?";
        if (confirm(cMessage) == true) {
            const user_id = __this.data('id');
            this.delete_user_ajax(user_id);
        }
    }

    
    delete_user_ajax(delete_user_id) {
        post('delete_guest_user', { 'guest_id': delete_user_id }).then(response => {
            const resStatus = response.status;
            notificationToast[resStatus]( response.message, resStatus.toUpperCase(), { autoClose: true});     
            this.guestDatatable.ajax.reload()
        });
    }

    search_filter(e) {
        this.guestSearch = e.target.value;
        this.guestDatatable.ajax.reload()
    }
}