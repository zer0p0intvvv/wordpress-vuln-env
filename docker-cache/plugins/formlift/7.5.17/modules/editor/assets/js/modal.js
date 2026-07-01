/* 
Grab a container via the ID of the container and load that content into the box.
Display the box in the correct position of the screen.
close the thickbox and put the content back where it came from.

formliftPopUpOverlay
formliftPopUpWindow
formliftPopUpTitle
formliftPopUpContent
*/

var formliftLightBox = {
  /* constants */
  overlayId: 'formliftPopUpOverlay',
  windowId: 'formliftPopUpWindow',
  contentId: 'formliftPopUpContent',
  titleId: 'formliftPopUpTitle',
  /* variable */
  content: '',
  title: '',
  source: '',

  /* inititalize the PopUp*/
  init: function (title, queryArgs) {
    this.title = title
    this.getSourceId(queryArgs)
    this.pullContent()
    this.showPopUp()
  },

  getSourceId: function (queryArgs) {
    var querystart = queryArgs.indexOf('#')
    var listArgs = queryArgs.substring(querystart + 1)
    listArgs = listArgs.split('=')
    this.source = listArgs[1]
  },

  close: function () {
    //remove editor if any
    var editors = jQuery('#' + this.contentId).find('.wp-editor')
    for (var i = 0; i < editors.length; i++) {
      wp.editor.remove(editors[i].id)
    }

    this.pushContent()
    this.hidePopUp()
  },

  /* Switch the content In the source and target between */
  pullContent: function () {
    var target = document.getElementById(this.contentId)
    var source = document.getElementById(this.source)
    while (source.hasChildNodes()) {
      target.appendChild(source.firstChild)
    }

  },

  pushContent: function () {
    var target = document.getElementById(this.contentId)
    var source = document.getElementById(this.source)

    while (target.hasChildNodes()) {
      source.appendChild(target.firstChild)
    }
  },

  /* Load the PopUp onto the screen */
  showPopUp: function () {

    //init editor if any
    var editors = jQuery('#' + this.contentId).find('.wp-editor')
    for (var i = 0; i < editors.length; i++) {
      wp.editor.initialize(editors[i].id, { tinymce: true, quicktags: true })
    }

    jQuery('#' + this.titleId).html(this.title)
    jQuery('#' + this.overlayId).fadeIn()
    jQuery('#' + this.windowId).fadeIn()
  },

  /* Close the PopUp */
  hidePopUp: function () {
    jQuery('#' + this.overlayId).css('display', 'none')
    jQuery('#' + this.windowId).css('display', 'none')
  },

  reload: function () {
    jQuery('.formlift_trigger_popup').click(
      function () {
        //console.log(this.href);
        formliftLightBox.init(this.title, decodeURIComponent(this.href))
      }
    )

    jQuery('#formliftCloseButton').click(
      function () {
        formliftLightBox.close()
      }
    )

    jQuery('#formliftPopUpSaveButton').click(
      function () {
        formliftLightBox.close()
      }
    )
  }
}

jQuery(document).ready(function () {
  formliftLightBox.reload()
})