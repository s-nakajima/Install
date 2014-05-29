$(document).ready(function() {
  $("#language").change(function() {
    var url = window.location.href.split("?")[0];
    url += url.indexOf("?") > -1 ? "&" : "?"
    window.location.href = url + "language=" + $(this).val();
  });
  $("#datasource").change(function() {
    var type = $("option:selected", this).text();
    if (type === "Mysql") {
      $("#port").val(3306);
      $("#login").val("root");
      $("#schema").parent().parent().slideUp();
      $("#schema").prop("disabled", true);
    } else if (type === "Postgresql") {
      $("#port").val(5432);
      $("#login").val("postgres");
      $("#schema").prop("disabled", false).parent().parent().slideDown();
    }
  });
});
