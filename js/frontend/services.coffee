jQuery ($) ->
  $(".tourist-accordion").accordion
    heightStyle: "content"
    collapsible: true

  $(".service-drag").draggable
    revert: () ->
      $(@).data("uiDraggable").originalPosition
    revertDuration: 1

  $("#orderContainer").on "click", ".save-service", (e) ->
    e.preventDefault()
    item = $ @
    form = item.parents "form"
    container = item.data "container"
    servicePrice = form.find(".service-price").find("input").val()
    serviceComment = form.find(".service-comment").find("input").val()
    if servicePrice isnt "0" and serviceComment is ""
      alert "Введите комментарий"
    else
      $.ajax
        url: form.attr "action"
        type: "POST"
        dataType: "JSON"
        data: form.serialize()
        success: (data) ->
          if data.type is "alert-success"
            $("#priceValue").html data.price if not $("#priceValue").hasClass "fixed"
          if container?
            $(container).append data.body
          $.showMessage data.message, data.type
          if form.attr("action") is "/orders/addaservice"
            form.find(":input").each ->
              $(@).val "" if $(@).attr("type") isnt "hidden"

  $("#orderContainer").on "click", ".delete-service", (e) ->
    e.preventDefault()
    item = $ @
    container = item.data "container"
    $.ajax
      url: item.attr "href"
      data:
        id: item.data "id"
      type: "POST"
      dataType: "JSON"
      success: (data) ->
          $("#priceValue").html data.price if data.type is "alert-success" and not $("#priceValue").hasClass "fixed"
          $(container).fadeOut("slow").remove() if container?
          $.showMessage data.message, data.type

  $(".tourist-block").droppable
    hoverClass: "ui-state-active"


  $(".tourist-block").on "drop", (event, ui) ->
    service = ui.draggable
    header = $ @
    container = $ "#"+header.attr("id")+"+.service-list"

    $.ajax
      url: "/orders/addservice"
      type: "POST"
      data:
        "data[touristId]": header.data "id"
        "data[serviceId]": service.data "id"
        "data[orderId]": $("#orderId").val()
      dataType: "json"
      success: (data) ->
        if data.type is "alert-success"
          container.append data.body
          $("#priceValue").html data.price if not $("#priceValue").hasClass "fixed"
          header.find("small").append ", "+data.name

        $.showMessage data.message, data.type

  $(".tourist-add-all").droppable
    hoverClass: "ui-state-active"
    drop: (event, ui) ->
      $(".tourist-block").trigger "drop", ui

  $("#recalculateCost").click ->
    $.ajax
      url: "/orders/recalculate"
      data:
        id: $("#orderId").val()
      dataType: "json"
      type: "GET"
      success: (data) ->
        $("#priceValue").html data.price if data.price?
        $.showMessage data.message, data.type