function copy_shortcode (id) {
  var short_code_input = jQuery(id)
  short_code_input.select()

  try {
    var successful = document.execCommand('copy')
  } catch (err) {
    console.log('unable to copy')
  }
}