jQuery ($) ->
  $.datepicker.regional['ru'] =

    closeText: 'Закрыть'
    prevText: '&#x3c;Пред'
    nextText: 'След&#x3e;'
    currentText: 'Сегодня'
    monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
                 'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь']
    monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
                      'Июл','Авг','Сен','Окт','Ноя','Дек']
    dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота']
    dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт']
    dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб']
    weekHeader: 'Не'
    dateFormat: 'dd.mm.yy'
    firstDay: 1
    isRTL: false
    showMonthAfterYear: false
    yearSuffix: ''

  $.datepicker.setDefaults $.datepicker.regional['ru']

  $("body").on "click", ".glyphicon-calendar", ->
    $(@).prev("input").datepicker "show"


  $.loadPage = (href) ->
      window.location.hash = href
      $("#contentblocker").show()
      $.ajax
        url: href
        type: "GET"
        success: (data) ->
          $("#contentblocker").hide()
          $("#main").html data

  $.showMessage = (text, type) ->
    message = $("<div></div>").addClass("alert").addClass(type).html(text).appendTo $("#messageBox")
    window.setTimeout(
      -> message.fadeOut "slow"
      1500
    )

  $(".form-signin").submit ->
    form = $ @
    $.ajax
      url: "/user/login"
      type: "POST"
      data: form.serialize()
      dataType: "json"
      success: (data) ->
        if data.type is "success"
          location.href = "/"
        else
          $.showMessage data.message, data.type
    false

  $("body").on "change", "#tourFilter", ->
    item = $ @
    $.ajax
      url: "/orders/result"
      type: "POST"
      data:
        "filter[tourId]": item.val()
      success: (data) ->
        $("#result").html data


  $("body").on "click", ".save-new", (e) ->
    e.preventDefault()
    link = $ @
    form = link.parents "form"
    $.ajax
      url: form.attr "action"
      data: form.serialize()
      type: "POST"
      dataType: "json"
      success: (data) ->
        if data.action is "append"
          $(data.toappend).appendTo link.data "record"
          form.find(":input").each ->
            $(@).val "" if $(@).attr("type") isnt "hidden" or @id is "new-id"

        if data.action is "append-update"
          $(data.toappend).appendTo link.data "record"
          form.find(":input").each ->
            $(@).val "" if $(@).attr("type") isnt "hidden"
          $(".rest").html data.rest
          $(".payment").html data.price

        if data.action is "validate"
          errors = $.parseJSON data.errors
          container = $ "#tourist-#{data.form}"
          for field of errors
            container.find("[name='tourist[#{field}]']").parent(".form-group").addClass("has-error")

        $.showMessage data.msg, data.msgclass

