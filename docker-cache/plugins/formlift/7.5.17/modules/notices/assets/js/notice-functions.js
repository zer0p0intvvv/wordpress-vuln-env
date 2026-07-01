/**
 * Created by Adrian on 2017-06-01.
 */

function dismiss_formlift_notice (e) {
  jQuery.ajax({
    type: 'post',
    dataType: 'text',
    url: ajaxurl,
    data: { action: 'dismiss_formlift_notice', id: e.getAttribute('data-id') },
    success: function (m) {
      if (m == 'Success') {
        document.getElementById(e.getAttribute('data-id')).style.display = 'none'
      } else {
        console.log(m)
      }
    }
  })//jQuery.ajax
}