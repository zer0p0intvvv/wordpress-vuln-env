function formliftSubmitV2 (formObject) {
  if (typeof formlift_ajax_object === 'undefined') {
    alert('Ajax Url is undefined.')
    return false
  }
  var form = jQuery(formObject)
  if (form.data('submitting') === true)
    return false
  form.data('submitting', true)
  var formId = form.attr('data-id')
  var loader = jQuery('#wait-' + formId)
  loader.css('display', 'inline-block')
  var successMsg = jQuery('#success-' + formId)
  successMsg.css('display', 'none')
  var errorMsg = jQuery('#error-' + formId)
  errorMsg.css('display', 'none')
  var formData = new FormData(formObject)
  var request = new XMLHttpRequest()
  request.open('POST', formlift_ajax_object.ajax_url, true)
  request.onreadystatechange = function () {
    if (request.readyState === 4 && request.status === 200) {
      loader.css('display', 'none')
      jQuery('.formlift-error-response').attr('class', 'formlift-error-response formlift-no-error')
      try {
        var response = JSON.parse(request.responseText)
      } catch (e) {
        alert(request.responseText)
      }
      if (typeof response['url'] !== 'undefined') {
        successMsg.css('display', 'inline-block')
        if (typeof response['xid'] !== 'undefined') {
          var xid = document.createElement('INPUT')
          xid.type = 'hidden'
          xid.name = 'inf_form_xid'
          xid.value = response['xid']
          formObject.appendChild(xid)
        }
        jQuery(formObject).attr('action', response['url'])
        jQuery(formObject).attr('onsubmit', '')
        jQuery('.remove-on-submit').attr('disabled', 'disabled')
        formObject.submit()
      } else if (typeof response['msg'] !== 'undefined') {
        console.log(response['msg'])
        successMsg.css('display', 'inline-block')
      } else {
        errorMsg.css('display', 'inline-block')
        if (document.getElementsByClassName('g-recaptcha').length > 0) {
          grecaptcha.reset()
        }
        for (var id in response) {
          if (response.hasOwnProperty(id)) {
            try {
              var node = document.getElementById('error-' + id + '-' + formId)
              node.setAttribute('class', 'formlift-error-response')
              node.innerHTML = response[id]
            } catch (err) {
              console.log(response[id])
              alert(response[id])
            }
          }
        }
      }
      form.data('submitting', false)
      return false
    } else if (request.readyState === XMLHttpRequest.DONE && request.status !== 200) {
      loader.css('display', 'none')
      alert('Please contact your system administrator. This form is unable to function due to restrictions placed on your wp-ajax.php file or another unknown error.')
    }
  }
  request.send(formData)
  return false
}