jQuery ($) ->
  $(".datepick").datepicker
    dateFormat: "dd.mm.yy"
    onSelect: (date, ui) ->
      cdate = new Date()
      $("#ctime").val(date+" "+cdate.getHours()+":"+cdate.getMinutes() )

  $(".payments").on "click", ".delete-payment", (e) ->
    e.preventDefault()
    item = $ @
    container = item.parents "tr"
    $.ajax
      url: item.attr "href"
      data:
        id: item.data "id"
      type: "POST"
      dataType: "JSON"
      success: (data) ->
        $(".rest").html data.rest
        $(".payment").html data.price
        $(container).fadeOut("slow").remove() if container?
        $.showMessage data.message, data.type


