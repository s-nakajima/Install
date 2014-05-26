$(document).ready(function(){
  $('#language').change(function(){
    var url = window.location.href.split('?')[0];
    url += url.indexOf('?') > -1 ? '&' : '?'
    window.location.href = url + 'language=' + $(this).val();
  });
});