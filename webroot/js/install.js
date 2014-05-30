$(document).ready(function() {
  $("#language").change(function() {
    var url = window.location.href.split("?")[0];
    url += url.indexOf("?") > -1 ? "&" : "?"
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
});
