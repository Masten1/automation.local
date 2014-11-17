jQuery ($) ->
  $(".default-selector").change ->
    link = $(@).data "link"
    $(link).val $(@).val()
    $(link).change()

  $(".hotel-list").change ->
    item = $ @
    $.ajax
      url: "/orders/hotelrooms"
      data:
        id: item.val()
      type: "POST"
      success: (data) ->
        $(item.data "room").html data

  $(".vehicle-list").change ->
    item = $ @
    $.ajax
      url: "/orders/transportseats"
      data:
        id: item.val()
      type: "POST"
      success: (data) ->
        $(item.data "room").html data
