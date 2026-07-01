import {wpbookitPluginPath} from "./const.js";
export default function debounce(func, timeout = 300){
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
}

function openPaymentWindow(url) {
  try {
    new URL(url);
    const windowFeatures = 'width=800,height=600,scrollbars=yes,resizable=yes';
    window.open(url, 'PaymentWindow', windowFeatures);
  } catch (err) {
    return false;
  }
}


function sidebarResized() {
  const sidebar = document.querySelector(".sidebar");
  if (window.innerWidth < 1200) {
      sidebar.classList.add('sidebar-mini');
  } else {
      sidebar.classList.remove('sidebar-mini');
  }
}

const createTableStatusSwitch = ( data, row, callback ) => {
    const div = document.createElement('div');
    div.classList.add("form-check", "form-switch", "form-status");
    const input = document.createElement('input');
    input.classList.add("form-check-input");
    input.setAttribute('type', 'checkbox');
    if(parseInt(data) === 1){
        input.setAttribute('checked', 'checked');
    }
    input.addEventListener('change', (e) => {
        e.preventDefault();
        callback( e, input, row );
    }  );
    div.append(input);
    return div;
}

const createAnchorWithImage = ( data, clickHandler, extraOptions = { }) => {
    const href = '#';
    const classes = 'me-3';
    const anchor = document.createElement('a');
    anchor.href = href;
    anchor.classList.add( classes );
    if(extraOptions?.tooltipTitle){
        anchor.setAttribute('data-bs-title', extraOptions.tooltipTitle);
        anchor.setAttribute('data-bs-toggle', 'tooltip');
        anchor.setAttribute('data-bs-placement', 'top');
    }
    anchor.setAttribute('data-id', data?.id);
    anchor.addEventListener('click', ( e ) => {
        e.preventDefault();
        if( anchor.classList.contains('disabled')){
            return;
        }
        clickHandler(e,anchor,data);
    } );

    jQuery(anchor).tooltip();

    if(extraOptions?.imgSrc && extraOptions?.imgAlt){
        const img = document.createElement('img');
        img.width = 18;
        img.height = 18;
        img.src = wpbookitPluginPath?.image + extraOptions.imgSrc;
        img.alt = extraOptions.imgAlt;
        img.loading = 'lazy';
        img.classList.add( 'default' );
        anchor.append(img);

        const loaderImg = document.createElement('img');
        loaderImg.width = 18;
        loaderImg.height = 18;
        loaderImg.classList.add( 'd-none','loader','spinner' )
        loaderImg.src = wpbookitPluginPath?.image + 'spinner.svg';
        loaderImg.alt = 'spinner-icon';
        loaderImg.loading = 'lazy';
        anchor.append(loaderImg);
    }

    return anchor;
};
const createTableActionColumDiv = ( ...innerEle ) => {
    // Ensure innerEle is provided and not empty
    if( innerEle && innerEle.length > 0 ){
        // Create the container div
        const div = document.createElement('div');
        div.classList.add('d-flex', 'align-items-center');
        div.append( ...innerEle );  // Append all passed elements
        return div;  // Return the created div
    }
    return ''; // Return '' if no elements are passed
};

const copyToClipboard = ( element ) => {
    if (navigator.clipboard && window.isSecureContext) {
        // Modern method with Clipboard API
        const text = element.getAttribute('value');
        navigator.clipboard.writeText(text).then(
            () => {
                showCopyToClipboardTooltip(element,window.wpbookit.dashbord_language.comman.copied +" "+text)
                hideCopyToClipboardTooltip(element);
            },
            (err) => {
                showCopyToClipboardTooltip(element,window.wpbookit.dashbord_language.comman.fail_copied+" "+text)
                hideCopyToClipboardTooltip(element);
                console.error(err);
            }
        );
    } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(element);
    }
}

const fallbackCopyTextToClipboard = ( element ) => {
    const text = element.getAttribute('value');
    const textArea = document.createElement("textarea");
    textArea.value = text;
    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showCopyToClipboardTooltip(element, window.wpbookit.dashbord_language.comman.copied +" "+text)
        hideCopyToClipboardTooltip(element)
    } catch (err) {
        showCopyToClipboardTooltip(element, window.wpbookit.dashbord_language.comman.fail_copied+" "+text)
        hideCopyToClipboardTooltip(element)
        console.error( err);
    }
    document.body.removeChild(textArea);
}

const showCopyToClipboardTooltip = ( element, message ) => {
    element.setAttribute('data-bs-original-title', message);
    jQuery(element).tooltip('show');
}
const hideCopyToClipboardTooltip = ( element ) => {
    setTimeout(function () {
        jQuery(element).tooltip('hide');
        element.setAttribute('data-bs-original-title',  window.wpbookit.dashbord_language.comman.click_to_copy);
    }, 1500);
}

const validateForm = ( config, formEle )=>{

    let isValid = true;
    if( ! formEle ){
        return isValid;
    }
    // Loop through config rules to validate fields
    Object.keys(config.rules).forEach((field) => {
        const input = formEle.find(`[name="${field}"]`);
        const rule = config.rules[field];

        // Reset any previous errors
        input.siblings(`.${config.errorClass}`).addClass('d-none');

        // Check for 'required' fields
        if ( rule.required && ! input.val() ) {
            isValid = false;
            input.siblings(`.${config.errorClass}`).removeClass('d-none');
        }
    });

    return isValid;
}

export {
    openPaymentWindow,
    sidebarResized,
    createTableStatusSwitch,
    createAnchorWithImage,
    createTableActionColumDiv,
    copyToClipboard,
    validateForm
}
