/**
 * CodeDropz Uploader
 * Copyright 2018 Glen Mongaya
 * CodeDrop Drag&Drop Uploader
 * @version 1.3.9.6
 * @author CodeDropz, Glen Don L. Mongaya
 * @license The MIT License (MIT)
 */

// CodeDropz Drag and Drop Plugin
!function(){let e=function(e){let t=document.querySelector("form.wpcf7-form");if(t){let a=new FormData;a.append("action","_wpcf7_check_nonce"),a.append("_ajax_nonce",dnd_cf7_uploader.ajax_nonce),fetch(dnd_cf7_uploader.ajax_url,{method:"POST",body:a}).then(e=>e.json()).then(({data:e,success:t})=>t&&(dnd_cf7_uploader.ajax_nonce=e)).catch(console.error)}var r=this;let d={handler:r,color:"#000",background:"",server_max_error:"Uploaded file exceeds the maximum upload size of your server.",max_file:r.dataset.max?r.dataset.max:10,max_upload_size:r.dataset.limit?r.dataset.limit:"10485760",supported_type:r.dataset.type?r.dataset.type:"jpg|jpeg|JPG|png|gif|pdf|doc|docx|ppt|pptx|odt|avi|ogg|m4a|mov|mp3|mp4|mpg|wav|wmv|xls",text:"Drag & Drop Files Here",separator:"or",button_text:"Browse Files",on_success:""},o=Object.assign({},d,e);var n=r.dataset.name+"_count_files";localStorage.setItem(n,1);var s=dnd_upload_cf7_unique_id();s||(s=function(e=20){let t=new Uint8Array(16);crypto.getRandomValues(t),t[6]=15&t[6]|64,t[8]=63&t[8]|128;let a=Array.from(t,e=>e.toString(16).padStart(2,"0")).join("");return a.replace(/^(.{8})(.{4})(.{4})(.{4})(.{12})$/,"$1-$2-$3-$4-$5")}(),localStorage.setItem("dnd_wpcf7_session_id",JSON.stringify({value:s,savedAt:Date.now()})));let l=`
            <div class="codedropz-upload-handler">
                <div class="codedropz-upload-container">
                <div class="codedropz-upload-inner">
                    <${dnd_cf7_uploader.drag_n_drop_upload.tag}>${o.text}</${dnd_cf7_uploader.drag_n_drop_upload.tag}>
                    <span>${o.separator}</span>
                    <div class="codedropz-btn-wrap"><a class="cd-upload-btn" href="#">${o.button_text}</a></div>
                </div>
                </div>
                <span class="dnd-upload-counter"><span>0</span> ${dnd_cf7_uploader.dnd_text_counter} ${parseInt(o.max_file)}</span>
            </div>
        `,p=document.createElement("div");p.classList.add("codedropz-upload-wrapper"),o.handler.parentNode.insertBefore(p,o.handler),p.appendChild(o.handler),o.supported_type=o.supported_type.replace(/[^a-zA-Z0-9| ]/g,"");let i=o.handler.closest("form"),c=o.handler.closest(".codedropz-upload-wrapper"),u=i.querySelector('input[type="submit"], button[type="submit"]');o.handler.insertAdjacentHTML("afterend",l),["drag","dragstart","dragend","dragover","dragenter","dragleave","drop"].forEach(function(e){c.querySelector(".codedropz-upload-handler").addEventListener(e,function(e){e.preventDefault(),e.stopPropagation()})}),["dragover","dragenter"].forEach(function(e){c.querySelector(".codedropz-upload-handler").addEventListener(e,function(e){c.querySelector(".codedropz-upload-handler").classList.add("codedropz-dragover")})}),["dragleave","dragend","drop"].forEach(function(e){c.querySelector(".codedropz-upload-handler").addEventListener(e,function(e){c.querySelector(".codedropz-upload-handler").classList.remove("codedropz-dragover")})}),c.querySelector(".cd-upload-btn").addEventListener("click",function(e){e.preventDefault(),o.handler.value=null,o.handler.click()}),c.querySelector(".codedropz-upload-handler").addEventListener("drop",function(e){m(e.dataTransfer.files,"drop")}),o.handler.addEventListener("change",function(e){m(this.files,"click")}),/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)&&r.removeAttribute("accept");var m=function(e,t){if(0==e.length)return;var a=new FormData;a.append("action","dnd_codedropz_upload"),a.append("type",t),a.append("security",dnd_cf7_uploader.ajax_nonce),a.append("form_id",r.dataset.id),a.append("upload_name",r.dataset.name),a.append("upload_folder",s);let d=o.handler.querySelector(".has-error"),l=c.querySelector(".codedropz-upload-handler");for(let p of(d&&d.remove(),e)){if(void 0!==a.delete&&a.delete("upload-file"),Number(localStorage.getItem(n))>o.max_file){if(!c.querySelector("span.has-error-msg")){var u=dnd_cf7_uploader.drag_n_drop_upload.max_file_limit,m=document.createElement("span");m.className="has-error-msg",m.textContent=u.replace("%count%",o.max_file),l.parentNode.insertBefore(m,l.nextSibling)}return!1}let g=f.createProgressBar(p);var v=!1;if(p.size>o.max_upload_size){let h=document.getElementById(g),y=document.createElement("span");y.classList.add("has-error"),y.textContent=dnd_cf7_uploader.drag_n_drop_upload.large_file,h.querySelector(".dnd-upload-details").appendChild(y),v=!0}if(regex_type=RegExp("(.*?).("+o.supported_type+")$"),!1!==v||regex_type.test(p.name.toLowerCase())||(document.querySelector("#"+g+" .dnd-upload-details").insertAdjacentHTML("beforeend",'<span class="has-error">'+dnd_cf7_uploader.drag_n_drop_upload.inavalid_type+"</span>"),v=!0),localStorage.setItem(n,Number(localStorage.getItem(n))+1),!1===v){a.append("upload-file",p);var x=new XMLHttpRequest;let $=document.getElementById(g),S=$.querySelector(".dnd-progress-bar"),b=$.querySelector(".dnd-upload-details"),q=i.querySelector('input[type="submit"], button[type="submit"]');x.open(i.getAttribute("method"),o.ajax_url),x.onreadystatechange=function(){if(4===this.readyState){if(200===this.status){var e=JSON.parse(this.responseText);e.success?(f.setProgressBar(g,100),"function"==typeof o.on_success&&o.on_success.call(this,r,g,e)):(S.remove(),b.insertAdjacentHTML("beforeend",'<span class="has-error">'+e.data+"</span>"),q&&(q.classList.remove("disabled"),q.removeAttribute("disabled")),$.classList.remove("in-progress"))}else S.remove(),b.insertAdjacentHTML("beforeend",'<span class="has-error">'+o.server_max_error+"</span>"),q&&(q.classList.remove("disabled"),q.removeAttribute("disabled")),$.classList.remove("in-progress")}},x.upload.addEventListener("progress",function(e){if(e.lengthComputable){var t=parseInt(100*(e.loaded/e.total));f.setProgressBar(g,t-1)}},!1),x.send(a)}}},f={createProgressBar:function(e){var t=c.querySelector(".codedropz-upload-handler"),a="dnd-file-"+Math.random().toString(36).substr(2,9),r=`
                    <div class="dnd-upload-image">
                        <span class="file"></span>
                    </div>
                    <div class="dnd-upload-details">
                        <span class="name"><span>${e.name}</span><em>(${f.bytesToSize(e.size)})</em></span>
                        <a href="#" title="${dnd_cf7_uploader.drag_n_drop_upload.delete.title}" class="remove-file" data-storage="${n}">
                        <span class="dnd-icon-remove"></span>
                        </a>
                        <span class="dnd-progress-bar"><span></span></span>
                    </div>
                `,d=document.createElement("div");return d.id=a,d.className="dnd-upload-status",d.innerHTML=r,t.parentNode.insertBefore(d,t.nextSibling),a},setProgressBar:function(e,t){let a=document.getElementById(e),r=a.querySelector(".dnd-progress-bar");if(r){u&&f.disableBtn(u);let d=t*r.offsetWidth/100;a.classList.add("in-progress"),100==t?(r.querySelector("span").style.width="100%",r.querySelector("span").textContent=`${t}% `):(r.querySelector("span").style.width=d+"px",r.querySelector("span").textContent=`${t}% `),100==t&&(a.classList.add("complete"),a.classList.remove("in-progress"))}return!1},bytesToSize:function(e){return 0===e?"0":fileSize=(kBytes=e/1024)>=1024?(kBytes/1024).toFixed(2)+"MB":kBytes.toFixed(2)+"KB"},disableBtn:function(e){e&&(e.classList.add("disabled"),e.disabled=!0)}}};document.addEventListener("click",function(e){if(e.target.classList.contains("dnd-icon-remove")){e.preventDefault();var t=e.target,a=t.closest(".dnd-upload-status"),r=t.closest(".codedropz-upload-wrapper"),d=t.parentElement.getAttribute("data-storage"),o=Number(localStorage.getItem(d)),n=dnd_upload_cf7_unique_id();if(a.classList.contains("in-progress")||a.querySelector(".has-error"))return a.remove(),localStorage.setItem(d,o-1),!1;t.classList.add("deleting"),t.textContent=dnd_cf7_uploader.drag_n_drop_upload.delete.text+"...";var s=new XMLHttpRequest;s.open("POST",dnd_cf7_uploader.ajax_url),s.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),s.onload=function(){if(200===this.status){var e=JSON.parse(this.responseText);if(e.success)a.remove(),localStorage.setItem(d,o-1),r.querySelectorAll(".dnd-upload-status").length<=1&&r.querySelector(".has-error-msg")&&r.querySelector(".has-error-msg").remove(),r.querySelector(".dnd-upload-counter span").textContent=Number(localStorage.getItem(d))-1;else{let t=a.querySelector(".dnd-upload-details");if(t){let n=document.createElement("span");n.classList.add("has-error-msg"),n.textContent=e.data,t.appendChild(n)}}}},s.send("path="+a.querySelector('input[type="hidden"]').value+"&action=dnd_codedropz_upload_delete&security="+dnd_cf7_uploader.ajax_nonce+"&upload_folder="+n),document.querySelectorAll(".has-error-msg").forEach(function(e){e.remove()})}}),HTMLElement.prototype.CodeDropz_Uploader=e}();
// END: CodeDropz Uploader function

// Custom JS hook event
var dnd_upload_cf7_event = function(target, name, data) {
	// Create a custom event with the specified name and data
	var event = new CustomEvent('dnd_upload_cf7_' + name, {
		bubbles: true,
		detail: data
	});
	target.dispatchEvent(event);
}

// Get unique id. (reset after 24hours)
function dnd_upload_cf7_unique_id() {
	const item = localStorage.getItem('dnd_wpcf7_session_id');
	if ( ! item ) {
		return null;
	}

	// Parse item
	const data = JSON.parse( item );

	// Compare date
	if ( Date.now() - data.savedAt > ( 24 * 60 * 60 * 1000 ) ) {
		localStorage.removeItem('dnd_wpcf7_session_id');
		return null;
	}

	return data.value;
}

// BEGIN: initialize upload
document.addEventListener('DOMContentLoaded', function() {

	// Fires when an Ajax form submission has completed successfully, and mail has been sent.
    document.addEventListener( 'wpcf7mailsent', function( event ) {

        // Get form
        const form = event.target;

        // Get input type file element
        var inputFile = form.querySelectorAll('.wpcf7-drag-n-drop-file');
        var status = form.querySelectorAll('.dnd-upload-status');
        var counter = form.querySelector('.dnd-upload-counter span');
        var error = form.querySelectorAll('span.has-error-msg');

        // Reset upload list for multiple fields
        if ( inputFile.length > 0 ) {
            inputFile.forEach( function(input) {
                localStorage.setItem( input.getAttribute('data-name') + '_count_files', 1 ); // Reset file counts
            });
        }

        // Remove status / progress bar
        if (status) {
            status.forEach(function(statEl){
                statEl.remove();
            });
        }

        if (counter) {
            counter.textContent = '0';
        }

        if (error) {
            error.forEach(function(errEl){
                errEl.remove();
            });
        }

    }, false );

	window.initDragDrop = function () {

		// Get text object options/settings from localize script
		var TextOJB = dnd_cf7_uploader.drag_n_drop_upload;
        var fileUpload = document.querySelectorAll('.wpcf7-drag-n-drop-file');

        fileUpload.forEach(function(Upload) {

            // Support Multiple Fileds
            Upload.CodeDropz_Uploader({
                'color': '#fff',
                'ajax_url': dnd_cf7_uploader.ajax_url,
                'text': TextOJB.text,
                'separator': TextOJB.or_separator,
                'button_text': TextOJB.browse,
                'server_max_error': TextOJB.server_max_error,
                'on_success': function(input, progressBar, response) {

                    // Progressbar Object
                    var progressDetails = document.querySelector('.codedropz-upload-wrapper #' + progressBar);
                    var form = input.closest('form');
                    var span = form.querySelector('.wpcf7-acceptance');
                    var checkboxInput = ( span ? span.querySelector('input[type="checkbox"]') : '' );

                    // Remove 'required' error message
                    const requiredMessage = input.closest('.codedropz-upload-wrapper').nextElementSibling;
                    if( requiredMessage && requiredMessage.classList.contains('wpcf7-not-valid-tip') ){
                        requiredMessage.remove();
                    }

                    // If it's complete remove disabled attribute in button
                    if ( ( span && span.classList.contains('optional') ) || ! span || checkboxInput.checked || form.classList.contains('wpcf7-acceptance-as-validation')) {
                        setTimeout(function(){
                            const submitButton = form.querySelector('button[type=submit], input[type=submit]');
                            if( submitButton ){
								submitButton.classList.remove('disabled');
                                submitButton.removeAttribute('disabled');
                            }
                        }, 1);
                    }

                    // Append hidden input field
                    var detailsElement = progressDetails.querySelector('.dnd-upload-details');
                    var inputHTML = '<span><input type="hidden" name="' + input.dataset.name + '[]" value="' + response.data.path + '/' + response.data.file + '"></span>';
                    detailsElement.insertAdjacentHTML('beforeend', inputHTML);

                    // Update counter
                    var filesCounter = ( Number( localStorage.getItem( input.dataset.name + '_count_files' ) ) - 1);
                    var counterElement = input.closest('.codedropz-upload-wrapper').querySelector('.dnd-upload-counter span');
                    counterElement.textContent = filesCounter;

					// Add custom event
					dnd_upload_cf7_event( progressDetails, 'success', response );
                }
            });

        });

	}

	window.initDragDrop();

	// Usage: Custom js hook after success upload
	/*document.addEventListener( 'dnd_upload_cf7_success', function( event ) {
		console.log(event.detail);
	});*/

});