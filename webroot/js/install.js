$(document).ready(function() {
  $('#language').change(function() {
    var url = window.location.href.split('?')[0];
    url += url.indexOf('?') > -1 ? '&' : '?';
    window.location.href = url + 'language=' + $(this).val();
  });

  var model = 'DatabaseConfiguration';
  $('#' + model + 'Datasource').on('change', function() {
    var type = $('option:selected', this).text();
    if (type === 'Mysql') {
      $('#' + model + 'Port').val(3306);
      $('#' + model + 'Login').val('root');
      $('#' + model + 'Schema').parent().parent().slideUp();
      $('#' + model + 'Schema').prop('disabled', true);
    } else if (type === 'Postgresql') {
      $('#' + model + 'Port').val(5432);
      $('#' + model + 'Login').val('postgres');
      $('#' + model + 'Schema').prop('disabled', false).
          parent().parent().slideDown();
    }
  });

  // Hook submit
  var timer;
  $('#DatabaseConfigurationInitDbForm').on('submit', function(event) {
    event.preventDefault();
    event.stopPropagation();
    var dialog = $('div.loader').dialog({
      modal: true,
      resizable: false,
      draggable: false,
      title: 'Installing ...',
      minHeight: 100,
      minWidth: 150,
      close: function() {
        if (timer) {
          clearInterval(timer);
        }
      }
    });
    dialog.dialog('open');
    dialog.removeClass('hidden');
    $('.ui-dialog-titlebar-close').remove();

    // Submit form
    $.ajax({
      url: $('#DatabaseConfigurationInitDbForm').attr('action'),
      type: 'post',
      data: $(this).serialize(),
      timeout: 3600000, // 1 hour
      beforeSubmit: function() {
      },
      success: function() {
        clearInterval(timer);
        location.href = $('#DatabaseConfigurationInitDbForm')
                          .attr('action')
                          .replace(/init_db$/, 'init_admin_user');
      },
      error: function(xhr) {
        var dom = $.parseHTML(xhr.responseText);
        if ($('div.alert').length) {
          $('div.alert').html($(dom).filter('div.alert').html());
        } else {
          $('#DatabaseConfigurationInitDbForm').before(
              '<div class="alert alert-danger alert-dismissable">' +
              $(dom).filter('div.alert').html() +
              '/<div> '
          );
        }
        dialog.dialog('close');
        clearInterval(timer);
      }
    });

    // Register ping timer
    timer = setInterval(function() {
      $.ajax({
        url: '/install/ping.json',
        type: 'get',
        cache: false
      });
    }, 10000);

    return false;
  });
});
