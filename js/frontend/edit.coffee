jQuery ($) ->
  $(".datetime").datepicker(
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true,
      yearRange: "-100: +0"
  )

  $(".submit").click (event) ->
    button = $ @
    event.preventDefault()
    form = button.parents "form"
    info = form.serialize()+"&redirect=#{button.data "redirect"}"
    $.ajax
      url: form.attr "action"
      data: info
      dataType: "json"
      type: "POST"
      success: (data) ->
        if data.msgclass is "alert-success"
          $.loadPage data.direction
          $.showMessage(data.msg, data.msgclass)
        else
          for field, message of $.parseJSON(data.msg)
            fieldcont = $ "#div-id-#{field}"
            errorlabel = $("<label></label>").addClass("error-label").html message
            fieldcont.addClass("has-error").append errorlabel




