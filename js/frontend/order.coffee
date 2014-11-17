jQuery ($) ->
  $(".datetime").datepicker()

  $(".dateyear").datepicker(
    changeMonth: true,
    changeYear: true,
    yearRange: "-100: +0"
    defaultDate: "-18y"
  )

  $(".maintour").click (e) ->
    e.preventDefault()
    form = $(@).parents "form"
    wishes = $("#wishes").find ":input"
    info = form.add(wishes).serialize()+"&redirect=#{$(@).data "redirect"}"
    $(".form-tourist").not( "#tourist-new" ).find(".save-new").trigger "click"
    $.ajax
      url: form.attr "action"
      data: info
      type: "POST"
      dataType: "json"
      success: (data) ->
        $.loadPage data.direction if data.msgclass is "alert-success"
        $.showMessage data.msg, data.msgclass

  $("#directions").change ->
    $.ajax
      url: "/orders/getoffers"
      data:
        id: $(@).val()
      type: "POST"
      dataType: "json"
      success: (data) ->
        orders = $("#offers").html ""
        for item in data
          $("<option></option>").val(item.id).html(item.text).appendTo orders

  $("#sinput").autocomplete(
      source: (request, response) ->
        $.ajax
          url: "/orders/search"
          dataType: "json",
          data:
            search: $("#sinput").val()
          type: "POST",
          success: ( data ) ->
            response (data)
      minLength: 3,
      select: (event, ui) ->
        $(".searchform").find("input").val ""
        $.ajax
          url: "/orders/gettourist"
          data:
            id: ui.item.value
          type: "POST"
          dataType: "json"
          success: (data) ->
            for name, value of data
              $("#new-"+name).val value
        false
  )

  $(".search").click ->
    $(".searchform").toggle()

  $(".find-tour").click ->
    $(".find-tour-list").toggle()

  $(".find-tour-item").click ->
    item = $ @
    $(".find-tour-list").hide()
    $("#order-date").val item.data "date"
    $("#tour-offer").val item.data "offer"