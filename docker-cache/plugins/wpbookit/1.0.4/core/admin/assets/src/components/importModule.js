import { post } from "../utils/ajax"
import notificationToast from "../utils/notification-toast"

export default class importModule{
    offcanvasElement 
    modeulRefreshfn

    constructor (element,modeulRefreshfn) {
        this.offcanvasElement = element
        this.modeulRefreshfn = modeulRefreshfn
        this.add_eventlistener()   
    }
    add_eventlistener() {
        jQuery(this.offcanvasElement.querySelector('form')).on('submit',(e)=>this.importData(e))
        jQuery(this.offcanvasElement).on('hidden.bs.offcanvas',(e)=>this.resetForm(e))
    }
    resetForm(e){
        this.offcanvasElement.querySelector('form').reset()
        jQuery(this.offcanvasElement).find('.import-data-log').addClass('d-none');
        console.log(e);
    }

    importData(e){
        e.preventDefault()

        let submitBtn = jQuery(e.currentTarget).find('[type="submit"]')
        submitBtn.attr('disabled','disabled')
    
        
        submitBtn.find('.loader').removeClass('d-none')

        var formData = new FormData(e.currentTarget);

        post('wpb_import',formData)
        .then(res=>{
            const  {status,message}=  res
            jQuery(e.currentTarget).find('.import-data-log').removeClass('d-none');
            if (res.total) {
                jQuery(e.currentTarget).find('#total_rows').text(res.total)
            }
            if (res.importedData) {
                jQuery(e.currentTarget).find('#total_imported_rows').text(res.importedData)
            }
            if (res.emailNotFound) {
                jQuery(e.currentTarget).find('#email_not_found').text(res.emailNotFound)
            }
            if (res.nameNotFound) {
                jQuery(e.currentTarget).find('#name_not_found').text(res.nameNotFound)
            }
            if(status=='success'){   
                this.modeulRefreshfn()
            }
            notificationToast[status](
                message,
                status.toUpperCase(),
                { autoClose: true }
            );
            submitBtn.find('.loader').addClass('d-none')
            submitBtn.removeAttr('disabled')
        })
    }

}
