var FormLiftEditor = {
  newFieldHtml: null,
  newFieldOptions: null,
  fieldOptions: null,
  targetField: null,
  editor: null,
  form_id: ThisFormID,
  operatingId: null,
  operation: 'switch',
  optionId: null,
  reloadCallbacks: [],

  init: function (fieldOptions) {
    /* all the form options */
    this.fieldOptions = fieldOptions
    /* get editor element */
    this.editor = document.getElementById('formlift-field-editor')
    /* activate sortable and buttons */
    /* init sortable */

    var sortables = jQuery('.formlift-sortable-fields')

    sortables.sortable({
      revert: true,
      tolerance: 'pointer',

      placeholder: 'ui-state-highlight',
      start: function (e, ui) {
        ui.placeholder.width(ui.item.width())
        ui.placeholder.height(ui.item.height())
      }
    })

    sortables.disableSelection()

    function customFieldPopupAdd () {
      var querystart = this.href.indexOf('#')
      var listArgs = this.href.substring(querystart + 1)
      listArgs = listArgs.split('=')
      var type = listArgs[1]
      FormLiftEditor.addField(type)
      formliftLightBox.close()
    }

    var customFieldOptions = jQuery('.add-custom-field')

    customFieldOptions.off()
    customFieldOptions.on('click', customFieldPopupAdd)
    this.reload()

  },

  reload: function () {

    /* init add custom fields */

    var AddCustomFieldTriggers = jQuery('.add_custom_field')

    AddCustomFieldTriggers.off('click', addCustomField)
    AddCustomFieldTriggers.on('click', addCustomField)

    /* init add options for radio/select */

    var AddCustomOptionTriggers = jQuery('.formlift-option-add')

    AddCustomOptionTriggers.off('click', addCustomOption)
    AddCustomOptionTriggers.on('click', addCustomOption)

    /* init delete options for radio/select */

    var deleteCustomOptionTriggers = jQuery('.formlift-option-delete')

    deleteCustomOptionTriggers.off('click', deleteCustomOption)
    deleteCustomOptionTriggers.on('click', deleteCustomOption)

    /* init delete field*/

    var deleteCustomFieldTriggers = jQuery('.formlift-delete-field')

    deleteCustomFieldTriggers.off('click', deleteCustomField)
    deleteCustomFieldTriggers.on('click', deleteCustomField)

    /* switch field type */

    var switchFieldTypeTriggers = jQuery('.switch-field-type')

    switchFieldTypeTriggers.off('change', switchFieldType)
    switchFieldTypeTriggers.on('change', switchFieldType)

    /* init switch width */

    var switchWidthTriggers = jQuery('.formlift-switch-width')

    switchWidthTriggers.off('click', switchWidth)
    switchWidthTriggers.on('click', switchWidth)

    for (var i = 0; i < this.reloadCallbacks.length; i++) {
      this.reloadCallbacks[i]()
    }
  },

  getFieldHtml: function () {
    this.continue = false
    var ajaxCall = jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { action: 'formlift_get_field_html', options: this.newFieldOptions, form_id: this.form_id },
      success: function (html) {
        var wrapper = document.createElement('div')
        wrapper.innerHTML = html
        FormLiftEditor.newFieldHtml = wrapper.firstChild
        if (FormLiftEditor.operation == 'switch')
          FormLiftEditor.doFieldReplace()
        else if (FormLiftEditor.operation == 'add')
          FormLiftEditor.doAddField()
      }
    })
  },

  replaceField: function (id, type) {
    var newOptions = this.fieldOptions[id]
    this.operatingId = id
    this.operation = 'switch'
    newOptions.type = type
    newOptions.display = 'on'
    this.newFieldOptions = JSON.stringify(newOptions)
    this.getFieldHtml()
  },

  doFieldReplace: function () {
    var currentField = document.getElementById('field-box-' + this.operatingId)
    this.editor.replaceChild(this.newFieldHtml, currentField)
    formliftLightBox.reload()

    /* show in lightbox */
    var newField = document.getElementById(this.operatingId + '-content')
    document.getElementById('formliftPopUpContent').innerHTML = newField.innerHTML
    /* load editor */

    var editors = jQuery(newField).find('.wp-editor')
    for (var i = 0; i < editors.length; i++) {
      wp.editor.initialize(editors[i].id, { tinymce: true, quicktags: true })
    }

    newField.innerHTML = ''
    this.reload()
  },

  addField: function (type) {
    var newID = type + '_' + Math.random().toString(36).substr(2, 16)
    this.operatingId = newID
    this.operation = 'add'
    var fieldOptions = {
      id: newID,
      name: newID,
      type: type,
      display: 'on',
      options: {}
    }
    this.fieldOptions[newID] = fieldOptions
    this.newFieldOptions = JSON.stringify(fieldOptions)
    this.getFieldHtml()
  },

  doAddField: function () {
    /* add to editor */
    this.editor.insertBefore(this.newFieldHtml, this.targetField.nextSibling)
    this.reload()
    /* open formliftLightBox */
    var url = '#source_d=' + this.operatingId + '-content'
    formliftLightBox.reload()
    formliftLightBox.init(this.operatingId, url)
    this.reload()
  },

  deleteField: function (id) {

    var result = confirm('Are you sure you want to delete this field?')

    if (result) {
      var e = document.getElementById(id)
      this.editor.removeChild(e)
    }
  },

  changeFieldWidth: function (id, newWidth) {
    if (newWidth == '1/1')
      var className = 'formlift-col formlift-span_4_of_4'
    else if (newWidth == '1/2')
      className = 'formlift-col formlift-span_1_of_2'
    else if (newWidth == '1/3')
      className = 'formlift-col formlift-span_1_of_3'
    else if (newWidth == '2/3')
      className = 'formlift-col formlift-span_2_of_3'
    else if (newWidth == '1/4')
      className = 'formlift-col formlift-span_1_of_4'
    else if (newWidth == '3/4')
      className = 'formlift-col formlift-span_3_of_4'
    document.getElementById(id).className = className
  },

  deleteOption: function (e) {
    e.parentNode.parentNode.removeChild(e.parentNode)
  },
  /* e is the option container, not the delete button in this case */
  addOption: function (e, field_id) {
    if (typeof this.fieldOptions[field_id].options == 'undefined')
      this.fieldOptions[field_id].options = {}

    this.optionId = 'option_' + Math.random().toString(36).substr(2, 16)

    this.operatingId = field_id
    /* create the div */
    var ajaxCall = jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: {
        action: 'formlift_get_option_html',
        field_id: this.operatingId,
        option_id: this.optionId,
        form_id: this.form_id
      },
      success: function (html) {
        var wrapper = document.createElement('div')
        wrapper.innerHTML = html
        var newOption = wrapper.firstChild
        e.parentNode.insertBefore(newOption, e.nextSibling)
        FormLiftEditor.fieldOptions[FormLiftEditor.operatingId].options[FormLiftEditor.optionId] = {
          value: null,
          label: null
        }
        FormLiftEditor.reload()
      }
    })
  }
}

/* reload functions */
function addCustomField () {
  FormLiftEditor.targetField = jQuery(this).parents('.formlift-col')[0]
}

function addCustomOption () {
  FormLiftEditor.addOption(this.parentNode, this.parentNode.getAttribute('data-field-id'))
}

function deleteCustomOption () {
  FormLiftEditor.deleteOption(this)
}

function deleteCustomField () {
  FormLiftEditor.deleteField(this.getAttribute('data-delete-id'))
}

function switchFieldType () {
  FormLiftEditor.replaceField(this.getAttribute('data-change-id'), this.value)
}

function switchWidth () {
  FormLiftEditor.changeFieldWidth(this.getAttribute('data-change-id'), this.value)
}