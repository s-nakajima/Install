$(document).ready(function() {
  $("#language").change(function() {
    var url = window.location.href.split("?")[0];
    url += url.indexOf("?") > -1 ? "&" : "?";
    window.location.href = url + "language=" + $(this).val();
  });

  var model = "DatabaseConfiguration";
  $("#" + model + "Datasource").change(function() {
    var type = $("option:selected", this).text();
    if (type === "Mysql") {
      $("#" + model + "Port").val(3306);
      $("#" + model + "Login").val("root");
      $("#" + model + "Schema").parent().parent().slideUp();
      $("#" + model + "Schema").prop("disabled", true);
    } else if (type === "Postgresql") {
      $("#" + model + "Port").val(5432);
      $("#" + model + "Login").val("postgres");
      $("#" + model + "Schema").prop("disabled", false).parent().parent().slideDown();
    }
  });

  var timer;
  $("#DatabaseConfigurationInitDbForm").submit(function() {
    // Show loader dialog
    var dialog = $("div.loader").dialog({
      modal: true,
      resizable: false,
      draggable: false,
      title: "Installing ...",
      minHeight: 100,
      minWidth: 150,
      close: function() {
        if (timer) {
          clearInterval(timer);
        }
      }
    }).dialog("open");
    dialog.removeClass("hidden");
    $(".ui-dialog-titlebar-close").remove();

    // Submit form
    $.ajax({
      url: "/install/init_db",
      type: "post",
      data: $(this).serialize(),
      timeout: 3600000, // 1 hour
      beforeSubmit: function(json) {
      },
      success: function() {
        clearInterval(timer);
        location.href = "/install/init_admin_user";
      },
      error: function(xhr, textStatus, errorThrown) {
        $("div.container").html(xhr.responseText);
        dialog.dialog("close");
        clearInterval(timer);
      }
    });

    // Register ping timer
    timer = setInterval(function() {
      $.ajax({
        url: "/install/ping.json",
        type: "get",
        cache: false
      });
    }, 10000);

    return false;
  });
});
