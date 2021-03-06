// Generated by CoffeeScript 1.6.3
(function() {
  jQuery(function($) {
    $.datepicker.regional['ru'] = {
      closeText: 'Закрыть',
      prevText: '&#x3c;Пред',
      nextText: 'След&#x3e;',
      currentText: 'Сегодня',
      monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
      monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
      dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
      dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
      dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
      weekHeader: 'Не',
      dateFormat: 'dd.mm.yy',
      firstDay: 1,
      isRTL: false,
      showMonthAfterYear: false,
      yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['ru']);
    $("body").on("click", ".glyphicon-calendar", function() {
      return $(this).prev("input").datepicker("show");
    });
    $.loadPage = function(href) {
      window.location.hash = href;
      $("#contentblocker").show();
      return $.ajax({
        url: href,
        type: "GET",
        success: function(data) {
          $("#contentblocker").hide();
          return $("#main").html(data);
        }
      });
    };
    $.showMessage = function(text, type) {
      var message;
      message = $("<div></div>").addClass("alert").addClass(type).html(text).appendTo($("#messageBox"));
      return window.setTimeout(function() {
        return message.fadeOut("slow");
      }, 1500);
    };
    $(".form-signin").submit(function() {
      var form;
      form = $(this);
      $.ajax({
        url: "/user/login",
        type: "POST",
        data: form.serialize(),
        dataType: "json",
        success: function(data) {
          if (data.type === "success") {
            return location.href = "/";
          } else {
            return $.showMessage(data.message, data.type);
          }
        }
      });
      return false;
    });
    $("body").on("change", "#tourFilter", function() {
      var item;
      item = $(this);
      return $.ajax({
        url: "/orders/result",
        type: "POST",
        data: {
          "filter[tourId]": item.val()
        },
        success: function(data) {
          return $("#result").html(data);
        }
      });
    });
    return $("body").on("click", ".save-new", function(e) {
      var form, link;
      e.preventDefault();
      link = $(this);
      form = link.parents("form");
      return $.ajax({
        url: form.attr("action"),
        data: form.serialize(),
        type: "POST",
        dataType: "json",
        success: function(data) {
          var container, errors, field;
          if (data.action === "append") {
            $(data.toappend).appendTo(link.data("record"));
            form.find(":input").each(function() {
              if ($(this).attr("type") !== "hidden" || this.id === "new-id") {
                return $(this).val("");
              }
            });
          }
          if (data.action === "append-update") {
            $(data.toappend).appendTo(link.data("record"));
            form.find(":input").each(function() {
              if ($(this).attr("type") !== "hidden") {
                return $(this).val("");
              }
            });
            $(".rest").html(data.rest);
            $(".payment").html(data.price);
          }
          if (data.action === "validate") {
            errors = $.parseJSON(data.errors);
            container = $("#tourist-" + data.form);
            for (field in errors) {
              container.find("[name='tourist[" + field + "]']").parent(".form-group").addClass("has-error");
            }
          }
          return $.showMessage(data.msg, data.msgclass);
        }
      });
    });
  });

}).call(this);
