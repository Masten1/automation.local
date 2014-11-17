jQuery ($) ->
  $("body").on "click", ".msglink", (e) ->
    link = $ @
    e.preventDefault()

    if link.hasClass "confirm"
      if confirm "Вы уверены, что хотите удалить запись?"
        $.ajax
          url: link.attr "href"
          type: "GET"
          dataType: "json"
          success: (data) ->
            $.showMessage data.msg, data.msgclass
            $(link.data "record").fadeOut "slow" if data.action is "remove"
    else
      $.ajax
        url: link.attr "href"
        type: "GET"
        dataType: "json"
        success: (data) ->
          $.showMessage data.msg, data.msgclass
          $(link.data "record").remove() if data.action is "remove"

  $("body").on "click", ".savelink", (e) ->
    link = $ @
    field = $ link.data "fieldname"
    e.preventDefault()
    $.ajax
      url: link.attr "href"
      type: "POST"
      data: field.find(":input").serialize()
      dataType: "json"
      success: (data) ->
        $(data.toappend).appendTo $(link.data "record")
        field.find(":input").val ""
        $.showMessage data.msg, data.msgclass

  $("body").on "click", ".savecomment", (e) ->
    e.preventDefault()
    item = $ @
    form = item.parent "form"
    $.ajax
      url: form.attr "action"
      data: form.serialize()
      type: "POST"
      success: (data) ->
        form.find(":input").each ->
          $(@).val "" if $(@).attr("type") isnt "hidden" and !$(@).hasClass("noclear")
        $(data).prependTo $(item.data "container")

  $("body").on "click", ".mainId", ->
    item = @
    $(".mainId").removeProp "checked"
    item.checked = true

  $("#logout").click ->
    $.post "/user/logout", ->
      window.location.hash = ""
      window.location.href = "/"


