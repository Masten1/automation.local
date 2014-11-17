jQuery ($) ->

  $("#tourComment").click (e) ->
    e.preventDefault()
    $.post "/tours/savetourcomment", {comment: $("#tourCommentInput").val(), id: $("#tourId").val()}, (data) ->
      $.showMessage data.message, data.type
    , "json"


  vehicleopts =
    hoverClass: "hover-background"
    drop: (event, ui) ->
      tourist = ui.draggable
      container = $ @
      ttId = container.data "transport"
      touristId = tourist.data "tourist"
      touristOld = tourist.data "old"
      $.ajax
        url: "/tours/updatetransport"
        data:
          "data[touristId]": touristId
          "data[newttId]": ttId
          "data[ttId]": touristOld
        type: "POST"
        dataType: "json"
        success: (data) ->
          if data.type isnt "remove"
            tourist.hide()
            $("#vehicle-tourists-#{ttId}").append $(data.element).draggable options
            $("#transport-for-#{touristId}").html data.vehicleName
            vehicle = $("#vehicle-container-#{ttId}")
            num =  parseInt vehicle.find(".toupdate").html()
            vehicle.find(".toupdate").html num+1
            free = parseInt vehicle.find(".free").html()
            vehicle.find(".free").html num-1
          else
            vehicleId = tourist.parents(".vehicle-droppable").data "transport"
            vehicle = $("#vehicle-container-#{vehicleId}")
            tourist.hide()
            $("#vehicle-empty").append $(data.element).draggable options
            $("#transport-for-#{touristId}").html "Не выбрано"
            num =  parseInt vehicle.find(".toupdate").html()
            vehicle.find(".toupdate").html num-1
            free = parseInt vehicle.find(".free").html()
            vehicle.find(".free").html num+1


  hotelopts =
    hoverClass: "hover-background"
    drop: (event, ui) ->
      tourist = ui.draggable
      container = $ @
      thId = container.data "hotel"
      touristId = tourist.data "tourist"
      touristOld = tourist.data "oldhotel"
      $.ajax
        url: "/tours/updatehotel"
        data:
          "data[touristId]": touristId
          "data[newthId]": thId
          "data[thId]": touristOld
        type: "POST"
        dataType: "json"
        success: (data) ->
          if data.type isnt "remove"
            tourist.hide()
            $("#hotel-tourists-#{thId}").append $(data.element).draggable options
            $("#hotel-for-#{touristId}").html data.hotelName
            hotel = $("#hotel-container-#{thId}")
            num =  parseInt hotel.find(".toupdate").html()
            hotel.find(".toupdate").html num+1
          else
            hotelId = tourist.parents(".hotel-droppable").data "hotel"
            hotel = $("#hotel-container-#{hotelId}")
            tourist.hide()
            $("#hotel-empty").append $(data.element).draggable options
            $("#hotel-for-#{touristId}").html "Не выбрано"
            num =  parseInt hotel.find(".toupdate").html()
            hotel.find(".toupdate").html num-1


  $("#tourMenu").children(".btn").click ->
    item = $ @
    $("#tourMenu").children(".active").removeClass "active"
    item.addClass "active"
    $(item.data "hide").addClass "hide"
    $(item.data "show").removeClass "hide"

  options =
    helper: () ->
      $("<div></div>").addClass("tourist-drag-helper").html $(@).data "name"

  $(".tourist-drag").draggable options

  $(".transport-container, .vehicle-droppable").droppable vehicleopts
  $(".hotel-container, .hotel-droppable").droppable hotelopts

  $("#doAddVehicle").click ->
    button = $ @
    $.ajax
      url: button.data "href"
      data:
        id: $("#addTransport").val()
        tourId: button.data "id"
      type: "POST"
      dataType: "json"
      success: (data) ->
        $(data.block).appendTo( $("#availableTransports")).droppable vehicleopts
        $(data.list).appendTo( $("#allVehicles")).droppable vehicleopts

  $("#doAddHotel").click ->
    button = $ @
    $.ajax
      url: button.data "href"
      data:
        id: $("#addHotel").val()
        tourId: button.data "id"
      type: "POST"
      dataType: "json"
      success: (data) ->
        $(data.block).appendTo( $("#availableHotels")).droppable hotelopts
        $(data.list).appendTo( $("#allHotels")).droppable hotelopts

  $("body").on "click", ".removeitem", ->
    button = $ @
    $.ajax
      url: button.data "href"
      type: "POST"
      dataType: "json"
      data:
        id: button.data "id"
      success: (data) ->
        button.parent().parent().remove()
        $("#vehicle-tourists-#{button.data "id"}").remove()
        for tourist in data
          $(tourist.body).appendTo($("#vehicle-empty")).draggable options
          $("#transport-for-#{tourist.id}").html "Не выбрано"

  $("body").on "click", ".removehotel", ->
    button = $ @
    $.ajax
      url: button.data "href"
      type: "POST"
      dataType: "json"
      data:
        id: button.data "id"
      success: (data) ->
        button.parent().parent().remove()
        $("#hotel-tourists-#{button.data "id"}").remove()
        for tourist in data
          $(tourist.body).appendTo($("#hotel-empty")).draggable options
          $("#hotel-for-#{tourist.id}").html "Не выбрано"

  $("#tourTransport").on "change", ".transport-driver", ->
    select = $ @
    $.ajax
      url: "/tours/adddriver"
      type: "POST"
      dataType: "json"
      data:
        id: select.parents(".transport-container").data "transport"
        driverId: select.val()
      success: (data) ->
        $("#driverPhone").html data.phone
        $.showMessage data.message, data.class

