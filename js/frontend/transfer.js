// Generated by CoffeeScript 1.6.3
(function() {
  jQuery(function($) {
    $(".default-selector").change(function() {
      var link;
      link = $(this).data("link");
      $(link).val($(this).val());
      return $(link).change();
    });
    $(".hotel-list").change(function() {
      var item;
      item = $(this);
      return $.ajax({
        url: "/orders/hotelrooms",
        data: {
          id: item.val()
        },
        type: "POST",
        success: function(data) {
          return $(item.data("room")).html(data);
        }
      });
    });
    return $(".vehicle-list").change(function() {
      var item;
      item = $(this);
      return $.ajax({
        url: "/orders/transportseats",
        data: {
          id: item.val()
        },
        type: "POST",
        success: function(data) {
          return $(item.data("room")).html(data);
        }
      });
    });
  });

}).call(this);